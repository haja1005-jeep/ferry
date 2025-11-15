<?php
// api/timetables.php

function api_timetables($pdo, $terminalCode = null, $date = null)
{
    if (!$terminalCode) {
        http_response_code(400);
        echo json_encode(array(
            'error'   => 'missing_terminal',
            'message' => 'terminal parameter is required'
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    if (!$date) {
        $date = date('Y-m-d');
    }

    // 요일 구하기 (0:일 ~ 6:토)
    $w = date('w', strtotime($date));
    $weekdayType = '매일';
    if ($w == 0) {
        $weekdayType = '일·공휴일';
    } elseif ($w == 6) {
        $weekdayType = '토요일';
    } else {
        $weekdayType = '평일';
    }

    $sql = "SELECT t.route_code, r.route_name_ko,
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

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':terminal'    => $terminalCode,
        ':weekdayType' => $weekdayType
    ));

    $rows = $stmt->fetchAll();

    echo json_encode(array(
        'date'        => $date,
        'weekdayType' => $weekdayType,
        'terminal'    => $terminalCode,
        'timetables'  => $rows
    ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>