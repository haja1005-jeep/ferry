<?php
// api/terminals.php
// 정식 스키마 기반 터미널 API

function api_terminals($pdo, $region = null)
{
    // region 컬럼이 아직 테이블에 없으므로 WHERE 적용 X
    // 필요한 경우 나중에 ALTER TABLE로 region 추가 가능

    $sql = "SELECT 
                terminal_code,
                name_ko,
                name_en,
                city,
                eup_myeon,
                ri,
                terminal_type,
                road_addr,
                jibun_addr,
                lat,
                lng,
                phone,
                has_parking,
                parking_note,
                source_url,
                memo,
                created_at,
                updated_at
            FROM terminals
            WHERE use_yn = 'Y'
            ORDER BY city, name_ko";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    // 표시용 종합 주소 만들기
    foreach ($rows as &$r) {
        $addr = [];

        if (!empty($r['road_addr'])) {
            $addr[] = $r['road_addr'];
        }
        if (!empty($r['jibun_addr'])) {
            $addr[] = "(" . $r['jibun_addr'] . ")";
        }

        $r['address_display'] = implode(' ', $addr);
    }

    echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

 ?>