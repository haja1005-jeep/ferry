<?php
// api/config.php
// DB 접속 정보 설정

$DB_HOST = 'localhost';
$DB_NAME = 'im4u798';
$DB_USER = 'im4u798';
$DB_PASS = 'dbi73043365k!!';
$DB_CHARSET = 'utf8mb4';

$options = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
);

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}",
        $DB_USER,
        $DB_PASS,
        $options
    );
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'error'   => 'db_connection_failed',
        'message' => $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
    exit;
}
?>