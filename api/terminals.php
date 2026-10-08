<?php
function handle_terminals($method)
{
    $pdo = db();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($method === 'GET') {
        if ($id) {
            $stmt = $pdo->prepare('SELECT * FROM terminals WHERE id = ?');
            $stmt->execute(array($id));
            $t = $stmt->fetch();
            if (!$t) fail('not_found', 404);
            $p = $pdo->prepare('SELECT id, kind, file_name, caption FROM terminal_photos WHERE terminal_id = ? ORDER BY id');
            $p->execute(array($id));
            $f = $pdo->prepare('SELECT id, route_name, category, item_name, price, note, source FROM fares WHERE terminal_id = ? ORDER BY route_name, FIELD(category,"person","vehicle","other"), price');
            $f->execute(array($id));
            $t['photos'] = $p->fetchAll();
            $t['fares']  = $f->fetchAll();
            json_out($t);
        }
        $rows = $pdo->query('SELECT id, name, city, address, lat, lng, phone FROM terminals ORDER BY name')->fetchAll();
        json_out($rows);
    }

    require_admin();

    if ($method === 'POST') {
        $d = body_json();
        $name = trim(isset($d['name']) ? $d['name'] : '');
        if ($name === '' || !isset($d['lat'], $d['lng']) || !is_numeric($d['lat']) || !is_numeric($d['lng'])) {
            fail('name, lat, lng are required');
        }
        $vals = array($name, trim(isset($d['city']) ? $d['city'] : ''), trim(isset($d['address']) ? $d['address'] : ''),
                      (float)$d['lat'], (float)$d['lng'], trim(isset($d['phone']) ? $d['phone'] : ''), isset($d['memo']) ? $d['memo'] : '');
        if ($id) {
            $vals[] = $id;
            $pdo->prepare('UPDATE terminals SET name=?, city=?, address=?, lat=?, lng=?, phone=?, memo=? WHERE id=?')->execute($vals);
        } else {
            $pdo->prepare('INSERT INTO terminals (name, city, address, lat, lng, phone, memo) VALUES (?,?,?,?,?,?,?)')->execute($vals);
            $id = (int)$pdo->lastInsertId();
        }
        json_out(array('id' => $id));
    }

    if ($method === 'DELETE') {
        if (!$id) fail('id required');
        $p = $pdo->prepare('SELECT file_name FROM terminal_photos WHERE terminal_id = ?');
        $p->execute(array($id));
        foreach ($p->fetchAll() as $row) {
            @unlink(upload_dir() . '/' . $row['file_name']);
        }
        $pdo->prepare('DELETE FROM terminals WHERE id = ?')->execute(array($id));
        json_out(array('ok' => true));
    }

    fail('method_not_allowed', 405);
}
