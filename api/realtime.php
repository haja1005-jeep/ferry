<?php
// api/realtime.php

function api_realtime($pdo, $terminalCode = null)
{
    if (!$terminalCode) {
        http_response_code(400);
        echo json_encode(array(
            'error'   => 'missing_terminal',
            'message' => 'terminal parameter is required'
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    // 터미널 한글 이름 가져오기
    $stmt = $pdo->prepare("SELECT name_ko FROM terminals WHERE terminal_code = :code");
    $stmt->execute(array(':code' => $terminalCode));
    $terminal = $stmt->fetch();

    if (!$terminal) {
        http_response_code(404);
        echo json_encode(array(
            'error' => 'terminal_not_found'
        ), JSON_UNESCAPED_UNICODE);
        return;
    }

    $nameKo = $terminal['name_ko'];

    // 오늘 날짜 기준 최신 snapshot
    $sqlSnapshot = "SELECT MAX(snapshot_time) AS latest
                    FROM realtime_operations
                    WHERE DATE(snapshot_time) = CURDATE()";
    $latest = $pdo->query($sqlSnapshot)->fetchColumn();

    if (!$latest) {
        echo json_encode(array(
            'terminal'      => $terminalCode,
            'terminal_name' => $nameKo,
            'snapshot_time' => null,
            'items'         => array()
        ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return;
    }

    $sql = "SELECT snapshot_time, departure_time, ship_name,
                   route_name, direction, origin,
                   route_type, operation_type, reason,
                   status, license_route
            FROM realtime_operations
            WHERE snapshot_time = :snapshot
              AND (origin LIKE :name OR route_name LIKE :name)
            ORDER BY departure_time";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':snapshot' => $latest,
        ':name'     => '%' . $nameKo . '%'
    ));
    $rows = $stmt->fetchAll();

    echo json_encode(array(
        'terminal'      => $terminalCode,
        'terminal_name' => $nameKo,
        'snapshot_time' => $latest,
        'items'         => $rows
    ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>