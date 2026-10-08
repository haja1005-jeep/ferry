<?php
// 라우터: /api/index.php?r=<resource>
require __DIR__ . '/lib.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Token');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$r = isset($_GET['r']) ? $_GET['r'] : '';
$m = $_SERVER['REQUEST_METHOD'];

try {
    switch ($r) {
        case 'config':
            json_out(array('kakao_js_key' => $config['kakao_js_key'], 'ocr_enabled' => $config['anthropic_key'] !== ''));
        case 'terminals':
            require __DIR__ . '/terminals.php';
            handle_terminals($m);
        case 'photos':
            require __DIR__ . '/photos.php';
            handle_photos($m);
        case 'fares':
            require __DIR__ . '/fares.php';
            handle_fares($m);
        case 'fares_ocr':
            require __DIR__ . '/fares_ocr.php';
            handle_fares_ocr($m);
        default:
            fail('unknown resource: use r=config|terminals|photos|fares|fares_ocr', 404);
    }
} catch (PDOException $e) {
    fail('db_error', 500);
}
