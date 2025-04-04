<?php
    session_start();

    if(isset($_POST['submit'])) {

        if (strlen($_POST['host']) > 0 && strlen($_POST['port']) > 0 && strlen($_POST['dbName']) > 0 && strlen($_POST['table']) > 0 && strlen($_POST['userName']) > 0) {
            $_SESSION['host'] = $_POST['host'];
            $_SESSION['port'] = $_POST['port'];
            $_SESSION['dbName'] = $_POST['dbName'];
            $_SESSION['table'] = $_POST['table'];
            $_SESSION['userName'] = $_POST['userName'];
            $_SESSION['passWord'] = $_POST['passWord'] ?? "";
            $_SESSION['load'] = 'load';
        } 
        else {
            $_SESSION['error'] = "Missing host, port, database name, table or username";
            unset($_SESSION['success']);
            unset($_SESSION['load']);
        }

        header("Location: index.php");
        return;
    }
    else if (isset($_POST['refresh'])) {

        if (strlen($_POST['table']) > 0) {
            $_SESSION['table'] = $_POST['table'];
            $_SESSION['load'] = 'load';
        }
        else {
            $_SESSION['error'] = "Missing table name";
            unset($_SESSION['host']);
            unset($_SESSION['port']);
            unset($_SESSION['dbName']);
            unset($_SESSION['table']);
            unset($_SESSION['userName']);
            unset($_SESSION['passWord']);
            unset($_SESSION['success']);
            unset($_SESSION['load']);
        }

        header("Location: index.php");
        return;
    }

    if (isset($_SESSION['load'])) {

        $url = 'http://localhost/dbviewer/server/server.php/getall';
        $params = [
            'host' => $_SESSION['host'],
            'port' => $_SESSION['port'],
            'db' => $_SESSION['dbName'],
            'table' => $_SESSION['table'],
            'user' => $_SESSION['userName'],
            'pass' => $_SESSION['passWord']
        ];
        $url .= '?' . http_build_query($params);

        try {
            $response = file_get_contents($url);

            $_SESSION['success'] = "Fetched data from the database successfully";
            unset($_SESSION['error']);
        } 
        catch (PDOException $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
            unset($_SESSION['success']);
            unset($_SESSION['load']);

            unset($_SESSION['host']);
            unset($_SESSION['port']);
            unset($_SESSION['dbName']);
            unset($_SESSION['table']);
            unset($_SESSION['userName']);
            unset($_SESSION['passWord']);
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="actions.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <title>DB Viewer</title>
</head>
<body>
    <div id="content" class="container">
        <h1>Welcome to DB Viewer</h1>

        <?php

        if (isset($_SESSION['error'])) {
            echo "<p class='text-danger'>".htmlentities($_SESSION['error'])."</p><br>";
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo "<p class='text-success'>".htmlentities($_SESSION['success'])."</p>";
            unset($_SESSION['success']);
        }

        if (isset($_SESSION['load'])) {
            echo $response;
        ?>
        <p>
            <form action="index.php" method="POST">
                <label for="table">Table:</label>
                <input type="text" name="table" id="table" value="<?= $_SESSION['table'] ?>">
                <input type="submit" class="btn btn-primary" name="refresh" value="Refresh">
            </form>
        </p>
        <p>
            <a href='reset.php' type='button' class='btn btn-primary'>Reset and Back</a>
        </p>
        <?php 
        }
        else { ?>

        <p class="text-info">Please enter the database and table name, username and optionally password.</p><br>
        <p>
            <form action="index.php" method="POST">
                <label for="host">Host:</label>
                <input type="text" name="host" id="host" value="localhost"><br>
                <label for="port">Port:</label>
                <input type="text" name="port" id="port" value="3306"><br><br>
                <label for="name">DB Name:</label>
                <input type="text" name="dbName" id="dbName" placeholder="Enter the database name"><br>
                <label for="userName">User Name:</label>
                <input type="text" name="userName" id="userName" placeholder="Enter your username"><br>
                <label for="passWord">Password:</label>
                <input type="text" name="passWord" id="passWord" placeholder="Enter your password"><br><br>
                <label for="table">Table:</label>
                <input type="text" name="table" id="table" placeholder="Enter the table to display"><br><br>
                <input type="submit" class="btn btn-primary" name="submit" value="Submit">
            </form>
        </p>

        <?php } ?>
    </div>
</body>
</html>