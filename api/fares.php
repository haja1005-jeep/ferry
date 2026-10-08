<?php
// POST {terminal_id, fares:[{route_name, category, item_name, price, note, source, photo_id}]}
// 해당 터미널의 운임 전체를 교체합니다.
function handle_fares($method)
{
    if ($method !== 'POST') fail('method_not_allowed', 405);
    require_admin();
    $d   = body_json();
    $tid = isset($d['terminal_id']) ? (int)$d['terminal_id'] : 0;
    if (!$tid || !isset($d['fares']) || !is_array($d['fares'])) fail('terminal_id and fares required');

    $pdo = db();
    $pdo->beginTransaction();
    $pdo->prepare('DELETE FROM fares WHERE terminal_id = ?')->execute(array($tid));
    $ins = $pdo->prepare('INSERT INTO fares (terminal_id, route_name, category, item_name, price, note, source, photo_id) VALUES (?,?,?,?,?,?,?,?)');
    foreach ($d['fares'] as $f) {
        $cat = isset($f['category']) ? $f['category'] : '';
        $item = trim(isset($f['item_name']) ? $f['item_name'] : '');
        if (!in_array($cat, array('person', 'vehicle', 'other'), true) || $item === '' || !isset($f['price']) || !is_numeric($f['price'])) {
            $pdo->rollBack();
            fail('invalid fare row: ' . $item);
        }
        $ins->execute(array(
            $tid,
            isset($f['route_name']) && $f['route_name'] !== '' ? $f['route_name'] : null,
            $cat, $item, (int)$f['price'],
            isset($f['note']) && $f['note'] !== '' ? $f['note'] : null,
            (isset($f['source']) && $f['source'] === 'ai') ? 'ai' : 'manual',
            isset($f['photo_id']) && $f['photo_id'] ? (int)$f['photo_id'] : null,
        ));
    }
    $pdo->commit();
    json_out(array('ok' => true, 'count' => count($d['fares'])));
}
