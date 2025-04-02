<?php

require_once 'data.php';

class Backend {
    private $connection;
    private $dbName;
    private $user;
    private $pass;
    private $table;
    private $data;

    function connect($dbName, $user, $pass) {
        // Store DB parameters
        $this->dbName = $dbName;
        $this->user = $user;
        $this->pass = $pass;

        // Create connection
        $this->connection = new PDO("mysql:host=localhost;port=3306;dbname=$dbName", $user, $pass);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check connection
        if ($this->connection->errorCode() > 0) {
            throw new PDOException("Connection failed with error code " . $this->connection->errorCode() . " for " . $dbName . " " . $user . " " . $pass);   
        }
        else {
            $this->data = new Data();
        }
    }

    function fetchData($table) {
        $this->table = $table;

        // Check if table exists
        $stmt = $this->connection->prepare("SHOW TABLES LIKE :table");
        $stmt->bindParam(':table', $this->table);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            throw new PDOException("Table $table does not exist in the database.");
        }

        // Fetch data from the table
        $stmt = $this->connection->query("SELECT * FROM $table");

        // Get Column Count
        $this->data->columnCount = $stmt->columnCount();

        // Get Column Names
        $columnNames = [];
        for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $meta = $stmt->getColumnMeta($i);
            $columnNames[] = $meta['name'];
        }

        $this->data->columnNames = $columnNames;

        // Fetch Data
        $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->data->tableData = $tableData;

        // Get Row Count
        $this->data->rowCount = count($tableData);
    }

    function fetchLine($table, $key, $id) {
        $this->table = $table;

        // Fetch data from the table
        $stmt = $this->connection->query("SELECT * FROM $table WHERE $key = $id LIMIT 1");

        // Get Column / Row Count
        $this->data->columnCount = $stmt->columnCount();
        $this->data->rowCount = 1;

        // Get Column Names
        $columnNames = [];
        for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $meta = $stmt->getColumnMeta($i);
            $columnNames[] = $meta['name'];
        }

        $this->data->columnNames = $columnNames;

        // Fetch Data
        $tableData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->data->tableData = $tableData;

        // Return the data
        $result = "";
        foreach ($this->data->tableData[0] as $cell) {
            $result .= $cell . " ";
        }
        return $result;
    }

    function render() {
        echo "<h3>Table: $this->table</h3>";
        echo "<p>Row Count: ".$this->data->rowCount."</p>";
        echo "<p>Column Count: ".$this->data->columnCount."</p>";

        echo "<table class='table table-bordered'>";
        echo "<thead>";
        echo "<tr>";
        foreach ($this->data->columnNames as $column) {
            echo "<th>".htmlentities($column)."</th>";
        }
        echo "</tr></thead><tbody>";

        $key = $this->data->columnNames[0];
        foreach ($this->data->tableData as $row) {
            echo "<tr onclick=\"clickRow('".$key."', ".$row[$key].", '".$this->dbName."', '".$this->table."', '".$this->user."', '".$this->pass."')\">";
            foreach ($row as $cell) {
                echo "<td>".htmlentities($cell)."</td>";
            }
            echo "</tr>";
        }

        echo "</tbody></table>";
    }

}

?>