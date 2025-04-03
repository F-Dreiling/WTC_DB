<?php

require_once 'backend.php';

$backend = new Backend();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ( $requestMethod === 'POST' && $requestUri === '/dbviewer/server.php/getone' ) {
    $input = file_get_contents('php://input');
    parse_str($input, $params);

    if ( !empty($params['id']) && is_numeric($params['id']) ) {
        $key = htmlspecialchars($params['key']);
        $id = htmlspecialchars($params['id']);
        $db = htmlspecialchars($params['db']);
        $table = htmlspecialchars($params['table']);
        $user = htmlspecialchars($params['user']);
        $pass = htmlspecialchars($params['pass']);

        try {
            $backend->connect($db, $user, $pass);
            $response = $backend->fetchOne($table, $key, $id);
        }
        catch (PDOException $e) {
            $response = "Connection failed with error code " . $e->getCode() . " for " . $db . " " . $user . " " . $pass;
        }
    }
    else {
        $response = "Invalid ID received";
    }

    header('Content-Type: text/plain');
    echo $response;
    exit;
}

else if ( $requestMethod === 'GET' && $requestUri === '/dbviewer/server.php/getall' ) {

    if ( !empty($_GET['db']) && !empty($_GET['table']) && !empty($_GET['user']) ) {
        $db = htmlspecialchars($_GET['db']);
        $table = htmlspecialchars($_GET['table']);
        $user = htmlspecialchars($_GET['user']);
        $pass = htmlspecialchars($_GET['pass']);

        try {
            $backend->connect($db, $user, $pass);
            $backend->fetchAll($table);
            $response = $backend->renderReturn();
        }
        catch (PDOException $e) {
            $response = "<p>Connection failed with error code " . $e->getCode() . " for " . $db . " " . $user . " " . $pass."</p>";
        }
    }
    else {
        $response = "<p>Invalid Data received</p>";
    }

    header('Content-Type: text/html');
    echo $response;
    exit;
}

else if ( $requestMethod === 'GET' && $requestUri === '/dbviewer/server.php/getjson' ) {
    header('Content-Type: application/json');
    echo "";
    exit;
}

else {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: text/plain');
    echo "Invalid Request on ".$requestUri;
    exit;
}

?>