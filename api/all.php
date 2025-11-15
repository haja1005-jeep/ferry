<?php
// api/all.php
// 지도 로딩용: 터미널 + 항로 + (선택) 특정 터미널 시간표 & 실시간

function api_all($pdo, $terminalCode = null, $region = null, $date = null)
{
    // 1) 터미널 목록
    if ($region) {
        $sqlTerm = "SELECT terminal_code, name_ko, name_en,
                           city, eup_myeon, ri,
                           lat, lng, address, phone,
                           homepage, region, memo
                    FROM terminals
                    WHERE use_yn = 'Y'
                      AND region = :region
                    ORDER BY city, name_ko";
        $stmt = $pdo->prepare($sqlTerm);
        $stmt->execute(array(':region' => $region));
    } else {
        $sqlTerm = "SELECT terminal_code, name_ko, name_en,
                           city, eup_myeon, ri,
                           lat, lng, address, phone,
                           homepage, region, memo
                    FROM terminals
                    WHERE use_yn = 'Y'
                    ORDER BY city, name_ko";
        $stmt = $pdo->query($sqlTerm);
    }
    $terminals = $stmt->fetchAll();

    // 2) 항로 목록
    $sqlRoutes = "SELECT route_code, route_name_ko, route_name_en,
                         origin_terminal_code, destination_name,
                         via_islands, region
                  FROM routes
                  WHERE use_yn = 'Y'";
    $routesStmt = $pdo->query($sqlRoutes);
    $routes = $routesStmt->fetchAll();

    $result = array(
        'terminals' => $terminals,
        'routes'    => $routes
    );

    // 3) 특정 터미널 지정 시: 오늘 시간표 + 실시간도 같이
    if ($terminalCode) {
        if (!$date) {
            $date = date('Y-m-d');
        }

        // timetables 부분 쿼리 재사용
        $w = date('w', strtotime($date));
        $weekdayType = '매일';
        if ($w == 0) {
            $weekdayType = '일·공휴일';
        } elseif ($w == 6) {
            $weekdayType = '토요일';
        } else {
            $weekdayType = '평일';
        }

        $sqlTT = "SELECT t.route_code, r.route_name_ko,
                         t.season, t.weekday_type,
                         t.trip_no, t.depart_time, t.arrive_time,
                         t.depart_terminal_code, t.arrive_place,
                         t.notes
                  FROM timetables t
                  JOIN routes r ON t.route_code = r.route_code
                  WHERE t.depart_terminal_code = :terminal
                    AND t.use_yn = 'Y'
                    AND (t.weekday_type = '매일' OR t.weekday_type = :weekdayType)
                  ORDER BY t.depart_time";

        $stmtTT = $pdo->prepare($sqlTT);
        $stmtTT->execute(array(
            ':terminal'    => $terminalCode,
            ':weekdayType' => $weekdayType
        ));
        $ttRows = $stmtTT->fetchAll();

        // realtime 부분도 그대로
        $stmtTerm = $pdo->prepare("SELECT name_ko FROM terminals WHERE terminal_code = :code");
        $stmtTerm->execute(array(':code' => $terminalCode));
        $terminal = $stmtTerm->fetch();
        $nameKo = $terminal ? $terminal['name_ko'] : null;

        $sqlSnapshot = "SELECT MAX(snapshot_time) AS latest
                        FROM realtime_operations
                        WHERE DATE(snapshot_time) = CURDATE()";
        $latest = $pdo->query($sqlSnapshot)->fetchColumn();

        $rtRows = array();
        if ($latest && $nameKo) {
            $sqlRT = "SELECT snapshot_time, departure_time, ship_name,
                             route_name, direction, origin,
                             route_type, operation_type, reason,
                             status, license_route
                      FROM realtime_operations
                      WHERE snapshot_time = :snapshot
                        AND (origin LIKE :name OR route_name LIKE :name)
                      ORDER BY departure_time";
            $stmtRT = $pdo->prepare($sqlRT);
            $stmtRT->execute(array(
                ':snapshot' => $latest,
                ':name'     => '%' . $nameKo . '%'
            ));
            $rtRows = $stmtRT->fetchAll();
        }

        $result['focus_terminal'] = $terminalCode;
        $result['timetables'] = array(
            'date'        => $date,
            'weekdayType' => $weekdayType,
            'terminal'    => $terminalCode,
            'timetables'  => $ttRows
        );
        $result['realtime'] = array(
            'terminal'      => $terminalCode,
            'terminal_name' => $nameKo,
            'snapshot_time' => $latest,
            'items'         => $rtRows
        );
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>