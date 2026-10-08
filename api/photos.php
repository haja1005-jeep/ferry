<?php
function handle_photos($method)
{
    require_admin();
    $pdo = db();

    if ($method === 'POST') {
        $tid  = isset($_POST['terminal_id']) ? (int)$_POST['terminal_id'] : 0;
        $kind = isset($_POST['kind']) ? $_POST['kind'] : 'terminal';
        if (!$tid || !in_array($kind, array('terminal', 'fare_sheet', 'timetable'), true)) fail('terminal_id/kind invalid');
        if (empty($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) fail('upload_failed');
        $f = $_FILES['photo'];
        if ($f['size'] > 10 * 1024 * 1024) fail('file_too_large');

        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $f['tmp_name']);
        $exts = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp');
        if (!isset($exts[$mime])) fail('only jpeg/png/webp allowed');

        $name = bin2hex(random_bytes(12)) . '.' . $exts[$mime];
        if (!move_uploaded_file($f['tmp_name'], upload_dir() . '/' . $name)) fail('save_failed', 500);

        $caption = isset($_POST['caption']) ? $_POST['caption'] : null;
        $pdo->prepare('INSERT INTO terminal_photos (terminal_id, kind, file_name, caption) VALUES (?,?,?,?)')
            ->execute(array($tid, $kind, $name, $caption));
        json_out(array('id' => (int)$pdo->lastInsertId(), 'file_name' => $name));
    }

    if ($method === 'DELETE') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $s = $pdo->prepare('SELECT file_name FROM terminal_photos WHERE id = ?');
        $s->execute(array($id));
        $row = $s->fetch();
        if (!$row) fail('not_found', 404);
        @unlink(upload_dir() . '/' . $row['file_name']);
        $pdo->prepare('DELETE FROM terminal_photos WHERE id = ?')->execute(array($id));
        json_out(array('ok' => true));
    }

    fail('method_not_allowed', 405);
}
