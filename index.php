<?php
    require_once 'backend.php';

    if(isset($_POST['submit'])) {
        if (isset($_POST['dbName']) && isset($_POST['userName'])) {
            $dbName = $_POST['dbName'];
            $table = $_POST['table'];
            $userName = $_POST['userName'];
            isset($_POST['passWord']) ? $passWord = $_POST['passWord'] : $passWord = "";

            try {
                $backend = new Backend();
                $backend->connect($dbName, $userName, $passWord);
                $backend->fetchData($table);
            } catch (PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        } 
        else {
            $error = "Please enter the database name, username, and password.";
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
    <title>Document</title>
</head>
<body>
    <div id="content" class="container">
        <h1>Welcome to the DB</h1>

        <?php
        
        if (isset($_POST['submit'])) {
            if (isset($error)) {
                echo "<p class='text-danger'>$error</p>";
                unset($error);
            }
            else {
                echo "<p class='text-success'>Connection successful!</p>";
                $backend->render();
            }
        }
        else { ?>

        <p class="text-info">Please enter the database name, username, and password to connect. If you don't have a password, leave it blank.</p>
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
                <input type="submit" name="submit" value="Submit">
            </form>
        </p>

        <?php } ?>
    </div>
</body>
</html>