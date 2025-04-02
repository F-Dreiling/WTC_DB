<?php
    session_start();
    
    require_once 'backend.php';

    function connect(&$backend) {

        if (isset($_SESSION['dbName']) && isset($_SESSION['table']) && isset($_SESSION['userName'])) {

            try {
                $backend->connect($_SESSION['dbName'], $_SESSION['userName'], $_SESSION['passWord']);
                $backend->fetchData($_SESSION['table']);

                $_SESSION['success'] = "Connected to the database successfully!";
                unset($_SESSION['error']);
            }
            catch (PDOException $e) {
                $_SESSION['error'] = "Error: " . $e->getMessage();
                unset($_SESSION['success']);
                unset($_SESSION['load']);

                unset($_SESSION['dbName']);
                unset($_SESSION['table']);
                unset($_SESSION['userName']);
                unset($_SESSION['passWord']);

                header("Location: index.php");
                return;
            }

        }

    }

    if(isset($_POST['submit'])) {

        if (isset($_POST['dbName']) && isset($_POST['table']) && isset($_POST['userName'])) {
            $_SESSION['dbName'] = $_POST['dbName'];
            $_SESSION['table'] = $_POST['table'];
            $_SESSION['userName'] = $_POST['userName'];
            $_SESSION['passWord'] = $_POST['passWord'] ?? "";
            $_SESSION['load'] = 'load';

            header("Location: index.php");
            return;
        } 
        else {
            $_SESSION['error'] = "Please enter the database name, table, username, and optionally password.";
            unset($_SESSION['success']);
            unset($_SESSION['load']);
        }

    }

    if (isset($_SESSION['load'])) {
        $backend = new Backend();
        connect($backend);
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
            echo "<p class='text-danger'>".htmlentities($_SESSION['error'])."</p>";
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo "<p class='text-success'>".htmlentities($_SESSION['success'])."</p>";
            unset($_SESSION['success']);
        }

        if (isset($_SESSION['load'])) {
            $backend->render();
            echo "<br><p><a href='reset.php' type='button' class='btn btn-primary'>Reset and Back</a></p>";
        }
        else { ?>

        <p class="text-info">Please enter the database and table name, username and optionally password.</p>
        <p class="text-warning">If the database host is not <b>localhost</b> or the port is not <b>3306</b>, please change it in the backend.</p><br>
        <p>
            <form action="index.php" method="POST">
                <label for="name">DB Name:</label>
                <input type="text" name="dbName" id="dbName" placeholder="Enter the database name"><br>
                <label for="userName">User Name:</label>
                <input type="text" name="userName" id="userName" placeholder="Enter your username"><br>
                <label for="passWord">Password:</label>
                <input type="text" name="passWord" id="passWord" placeholder="Enter your password"><br><br>
                <label for="passWord">Table:</label>
                <input type="text" name="table" id="table" placeholder="Enter the table to display"><br><br>
                <input type="submit" class="btn btn-primary" name="submit" value="Submit">
            </form>
        </p>

        <?php } ?>
    </div>
</body>
</html>