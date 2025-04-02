<?php

require_once 'backend.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    parse_str($input, $params);

    if (!empty($params['id']) && is_numeric($params['id'])) {
        $key = htmlspecialchars($params['key']);
        $id = htmlspecialchars($params['id']);
        $db = htmlspecialchars($params['db']);
        $table = htmlspecialchars($params['table']);
        $user = htmlspecialchars($params['user']);
        $pass = htmlspecialchars($params['pass']);

        $backend = new Backend();
        $backend->connect($db, $user, $pass);
        $response = $backend->fetchLine($table, $key, $id);
    }
    else {
        $response = "Invalid ID received.";
    }

    header('Content-Type: text/plain');
    echo $response;
    exit;
}

?>