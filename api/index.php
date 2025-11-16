<?php

// /ferry/api/index.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/config.php';

$resource = isset($_GET['resource']) ? $_GET['resource'] : '';
$terminal = isset($_GET['terminal']) ? $_GET['terminal'] : null;
$date     = isset($_GET['date']) ? $_GET['date'] : null;
$region   = isset($_GET['region']) ? $_GET['region'] : null;

switch ($resource) {
    case 'terminals':
        require_once __DIR__ . '/terminals.php';
        api_terminals($pdo, $region);
        break;

    case 'routes':
        require_once __DIR__ . '/routes.php';
        api_routes($pdo);
        break;

    case 'timetables':
        require_once __DIR__ . '/timetables.php';
        api_timetables($pdo, $terminal, $date);
        break;

    case 'realtime':
        require __DIR__ . '/realtime.php';
        api_realtime($pdo, $terminal);
        break;

    case 'all':
        require_once __DIR__ . '/all.php';
        api_all($pdo, $terminal, $region, $date);
        break;

    default:
        echo json_encode([
            'error'   => 'invalid_resource',
            'message' => 'use ?resource=terminals|routes|timetables|all'
        ], JSON_UNESCAPED_UNICODE);
}
?>