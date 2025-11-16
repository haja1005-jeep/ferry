<?php
/<?php
// /ferry/api/all.php

function api_all($pdo, $terminalCode = null, $region = null, $date = null)
{
    // 1) 터미널 목록
    if ($region) {
        $sqlTerm = "SELECT 
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
                      AND region = :region
                    ORDER BY city, name_ko";
        $stmt = $pdo->prepare($sqlTerm);
        $stmt->execute(['region' => $region]);
    } else {
        $sqlTerm = "SELECT 
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
        $stmt = $pdo->query($sqlTerm);
    }
    $terminals = $stmt->fetchAll();

    // 표시용 주소 필드 추가
    foreach ($terminals as &$t) {
        $addr = [];
        if (!empty($t['road_addr']))  $addr[] = $t['road_addr'];
        if (!empty($t['jibun_addr'])) $addr[] = '(' . $t['jibun_addr'] . ')';
        $t['address_display'] = implode(' ', $addr);
    }

    // 2) 항로 목록
    $sqlRoutes = "SELECT 
                        route_code,
                        route_name_ko,
                        route_name_en,
                        origin_terminal_code,
                        destination_name,
                        via_islands,
                        region
                  FROM routes
                  WHERE use_yn = 'Y'";
    $routesStmt = $pdo->query($sqlRoutes);
    $routes = $routesStmt->fetchAll();

    $result = [
        'terminals' => $terminals,
        'routes'    => $routes
    ];

    // 3) 특정 터미널 focus 시, 오늘 시간표 추가
    if ($terminalCode) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        $w = date('w', strtotime($date));
        if ($w == 0) {
            $weekdayType = '일요일';
        } elseif ($w == 6) {
            $weekdayType = '토요일';
        } else {
            $weekdayType = '평일';
        }

        $sqlTT = "SELECT 
                        t.route_code,
                        r.route_name_ko,
                        t.season,
                        t.weekday_type,
                        t.trip_no,
                        t.depart_time,
                        t.arrive_time,
                        t.depart_terminal_code,
                        t.arrive_place,
                        t.notes
                  FROM timetables t
                  JOIN routes r ON t.route_code = r.route_code
                  WHERE t.depart_terminal_code = :terminal
                    AND t.use_yn = 'Y'
                    AND (
                        t.weekday_type = '매일'
                        OR t.weekday_type = :weekdayType
                    )
                  ORDER BY t.depart_time";

        $stmtTT = $pdo->prepare($sqlTT);
        $stmtTT->execute([
            ':terminal'    => $terminalCode,
            ':weekdayType' => $weekdayType
        ]);
        $ttRows = $stmtTT->fetchAll();

        $result['focus_terminal'] = $terminalCode;
        $result['timetables'] = [
            'date'        => $date,
            'weekdayType' => $weekdayType,
            'terminal'    => $terminalCode,
            'timetables'  => $ttRows
        ];
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

?>