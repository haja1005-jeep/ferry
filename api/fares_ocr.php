<?php
// POST {photo_id} -> Claude 비전으로 운임표 사진을 읽어 "초안"을 돌려줍니다 (저장하지 않음).
// 사람이 admin 화면에서 검수한 뒤 r=fares 로 저장합니다.
function handle_fares_ocr($method)
{
    global $config;
    if ($method !== 'POST') fail('method_not_allowed', 405);
    require_admin();
    if ($config['anthropic_key'] === '') fail('ocr_not_configured', 501);

    $d = body_json();
    $pid = isset($d['photo_id']) ? (int)$d['photo_id'] : 0;
    $s = db()->prepare('SELECT file_name FROM terminal_photos WHERE id = ?');
    $s->execute(array($pid));
    $row = $s->fetch();
    if (!$row) fail('photo_not_found', 404);

    $path = upload_dir() . '/' . $row['file_name'];
    $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);

    $tool = array(
        'name' => 'save_fares',
        'description' => '운임표 사진에서 읽은 운임 목록',
        'input_schema' => array(
            'type' => 'object',
            'properties' => array(
                'fares' => array(
                    'type' => 'array',
                    'items' => array(
                        'type' => 'object',
                        'properties' => array(
                            'route_name' => array('type' => 'string', 'description' => '행선지/항로. 표에 없으면 빈 문자열'),
                            'category'   => array('type' => 'string', 'enum' => array('person', 'vehicle', 'other')),
                            'item_name'  => array('type' => 'string', 'description' => '표에 적힌 구분 그대로 (예: 어른, 학생, 경차, 승용차, 오토바이)'),
                            'price'      => array('type' => 'integer', 'description' => '원 단위 정수'),
                            'note'       => array('type' => 'string', 'description' => '비고. 없으면 빈 문자열'),
                        ),
                        'required' => array('category', 'item_name', 'price'),
                    ),
                ),
                'unreadable' => array('type' => 'string', 'description' => '읽기 어렵거나 불확실한 부분 설명'),
            ),
            'required' => array('fares'),
        ),
    );

    $payload = array(
        'model' => $config['anthropic_model'],
        'max_tokens' => 4096,
        'tools' => array($tool),
        'tool_choice' => array('type' => 'tool', 'name' => 'save_fares'),
        'messages' => array(array('role' => 'user', 'content' => array(
            array('type' => 'image', 'source' => array(
                'type' => 'base64', 'media_type' => $mime, 'data' => base64_encode(file_get_contents($path)))),
            array('type' => 'text', 'text' => '이 사진은 한국 여객선 운임표입니다. 사람(person) 운임, 차량(vehicle) 운임, 그 밖의 화물·자전거·오토바이 등(other) 운임을 모두 읽어 save_fares 로 반환하세요. 사진에 보이는 값만 쓰고 추측하지 마세요. 불확실하면 unreadable 에 적으세요.'),
        ))),
    );

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_HTTPHEADER => array(
            'content-type: application/json',
            'x-api-key: ' . $config['anthropic_key'],
            'anthropic-version: 2023-06-01',
        ),
        CURLOPT_POSTFIELDS => json_encode($payload),
    ));
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $j = json_decode($res, true);
    if ($code !== 200 || !is_array($j)) fail('ocr_api_error', 502);

    foreach ($j['content'] as $block) {
        if ($block['type'] === 'tool_use') {
            json_out(array(
                'photo_id'   => $pid,
                'fares'      => isset($block['input']['fares']) ? $block['input']['fares'] : array(),
                'unreadable' => isset($block['input']['unreadable']) ? $block['input']['unreadable'] : '',
            ));
        }
    }
    fail('ocr_no_result', 502);
}
