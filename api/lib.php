<?php
$config = require __DIR__ . '/config.php';

function json_out($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function fail($msg, $code = 400)
{
    json_out(array('error' => $msg), $code);
}

function db()
{
    global $config;
    static $pdo = null;
    if (!$pdo) {
        $pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_pass'], array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ));
    }
    return $pdo;
}

function require_admin()
{
    global $config;
    $token = isset($_SERVER['HTTP_X_ADMIN_TOKEN']) ? $_SERVER['HTTP_X_ADMIN_TOKEN'] : '';
    if ($config['admin_token'] === '' || !hash_equals($config['admin_token'], $token)) {
        fail('unauthorized', 401);
    }
}

function body_json()
{
    $data = json_decode(file_get_contents('php://input'), true);
    return is_array($data) ? $data : array();
}

function upload_dir()
{
    return dirname(__DIR__) . '/uploads';
}
