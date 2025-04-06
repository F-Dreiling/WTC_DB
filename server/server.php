<?php

require_once 'backend.php';

$backend = new Backend();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ( $requestMethod === 'POST' && $requestUri === '/dbviewer/server/server.php/getone' ) {
    $input = file_get_contents('php://input');
    parse_str($input, $params);

    if ( !empty($params['id']) && is_numeric($params['id']) ) {
        $key = htmlspecialchars($params['key']);
        $id = htmlspecialchars($params['id']);
        $host = htmlspecialchars($params['host']);
        $port = htmlspecialchars($params['port']);
        $db = htmlspecialchars($params['db']);
        $table = htmlspecialchars($params['table']);
        $user = htmlspecialchars($params['user']);
        $pass = htmlspecialchars($params['pass']);

        try {
            $backend->connect($host, $port,$db, $user, $pass);
            $backend->fetchOne($table, $key, $id);
            $response = $backend->oneToString();
        }
        catch (PDOException $e) {
            $response = "Error: " . $e->getMessage() . ", for " . $host . " " . $port . " " . $db . " " . $user . " " . $table . " " . $key . " " . $id;     
        }
    }
    else {
        $response = "Error: Invalid ID received";
    }

    header('Content-Type: text/plain');
    echo $response;
    exit;
}

else if ( $requestMethod === 'GET' && $requestUri === '/dbviewer/server/server.php/getall' ) {

    if ( !empty($_GET['db']) && !empty($_GET['table']) && !empty($_GET['user']) ) {
        $host = htmlspecialchars($_GET['host']) ?? "localhost";
        $port = htmlspecialchars($_GET['port']) ?? "3306";
        $db = htmlspecialchars($_GET['db']);
        $table = htmlspecialchars($_GET['table']);
        $user = htmlspecialchars($_GET['user']);
        $pass = htmlspecialchars($_GET['pass']);

        try {
            $backend->connect($host, $port, $db, $user, $pass);
            $backend->fetchAll($table);
            $response = $backend->render();
        }
        catch (PDOException $e) {
            $response = "Error: " . $e->getMessage() . ", for " . $host . " " . $port . " " . $db . " " . $user . " " . $table;
        }
    }
    else {
        $response = "Error: Invalid Data received";
    }

    header('Content-Type: text/html');
    echo $response;
    exit;
}

else if ( $requestMethod === 'GET' && $requestUri === '/dbviewer/server/server.php/getjson' ) {

    if ( !empty($_GET['db']) && !empty($_GET['table']) && !empty($_GET['user']) ) {
        $host = htmlspecialchars($_GET['host']) ?? "localhost";
        $port = htmlspecialchars($_GET['port']) ?? "3306";
        $db = htmlspecialchars($_GET['db']);
        $table = htmlspecialchars($_GET['table']);
        $user = htmlspecialchars($_GET['user']);
        $pass = htmlspecialchars($_GET['pass']);

        try {
            $backend->connect($host, $port, $db, $user, $pass);
            $backend->fetchAll($table);
            $response = json_encode($backend->data->jsonSerialize());
        }
        catch (PDOException $e) {
            $response = json_encode(["error" => "Error: " . $e->getMessage() . ", for " . $host . " " . $port . " " . $db . " " . $user . " " . $table]);
        }
    }
    else {
        $response = json_encode(["error" => "Error: Invalid Data received"]);
    }

    header('Content-Type: application/json');
    echo $response;
    exit;
}

else {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: text/plain');
    echo "Invalid Request on ".$requestUri;
    exit;
}

?>