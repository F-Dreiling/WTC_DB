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

            if ($response === false || $response === "") {
                throw new Exception("Error fetching data from the server");
            }
            else if (substr($response, 0, 5) === "Error") {
                throw new Exception($response);
            }

            $_SESSION['success'] = "Fetched data from the database successfully";
            unset($_SESSION['error']);
        } 
        catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
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
    <script>
        let sessionData = <?php echo json_encode($_SESSION); ?>;
    </script>
    <script src="actions.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    
    <title>DB Viewer</title>
</head>
<body>
    <div id="content" class="container d-flex flex-column align-items-center text-center mt-4 mb-2">
        <h1 class="w-75">Welcome to DB Viewer</h1>

        <?php if (isset($_SESSION['error'])) : ?>
            <p class='text-danger w-75'><?= htmlentities($_SESSION['error']); ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <p class='text-success w-75'><?= htmlentities($_SESSION['success']); ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['load'])): ?>

        <div class="w-75">
            <?= $response; ?>
        </div>

        <div class="d-flex justify-content-between align-items-center gap-2 w-75">
            <a href='reset.php' type='button' class='btn btn-secondary'>&lt;&lt; Reset</a>

            <form action="index.php" method="POST">
                <label for="table">Table:</label>
                <input type="text" name="table" id="table" value="<?= htmlentities($_SESSION['table']); ?>" style="width: 33%">
                <input type="submit" class="btn btn-secondary" name="refresh" value="Refresh">
            </form>

            <input onclick='printJson()' type='button' class='btn btn-secondary' value='Print JSON'>
        </div>

        <?php else: ?>

        <p class="text-warning w-75">Please enter the database and table name, username and optionally password.</p>

        <div class="w-75">
            <form action="index.php" method="POST" class="d-flex flex-column gap-3">
                <div class="row">
                    <div class="col-md-6">
                        <label for="host">Host:</label>
                        <input type="text" name="host" id="host" class="form-control" value="localhost">
                    </div>
                    <div class="col-md-6">
                        <label for="port">Port:</label>
                        <input type="text" name="port" id="port" class="form-control" value="3306">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="userName">User Name:</label>
                        <input type="text" name="userName" id="userName" class="form-control" placeholder="Enter your username">
                    </div>
                    <div class="col-md-6">
                        <label for="passWord">Password:</label>
                        <input type="text" name="passWord" id="passWord" class="form-control" placeholder="Enter your password">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="dbName">DB Name:</label>
                        <input type="text" name="dbName" id="dbName" class="form-control" placeholder="Enter the database">
                    </div>
                    <div class="col-md-6">
                        <label for="table">Table:</label>
                        <input type="text" name="table" id="table" class="form-control" placeholder="Enter the table">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <input type="submit" class="btn btn-secondary w-100" name="submit" value="Submit">
                    </div>
                </div>
            </form>
        </div>

        <?php endif; ?>
    </div>
</body>
</html>