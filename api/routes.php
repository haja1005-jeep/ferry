<?php
// /ferry/api/routes.php

function api_routes($pdo)
{
    $sql = "SELECT 
                route_code,
                route_name_ko,
                route_name_en,
                origin_terminal_code,
                destination_name,
                via_islands,
                region,
                operator_name,
                route_type,
                distance_km,
                sailing_time_min,
                season_type,
                memo
            FROM routes
            WHERE use_yn = 'Y'
            ORDER BY region, route_name_ko";

    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll();

    echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

?>
