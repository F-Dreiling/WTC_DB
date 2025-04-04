<?php

require_once 'data.php';

class Backend {
    private $connection;
    private $host;
    private $port;
    private $dbName;
    private $user;
    private $pass;
    private $table;
    private $data;

    function connect($host, $port, $dbName, $user, $pass) {
        // Store DB parameters
        $this->host = $host;
        $this->port = $port;
        $this->dbName = $dbName;
        $this->user = $user;
        $this->pass = $pass;

        // Create connection
        $this->connection = new PDO("mysql:host=$host;port=$port;dbname=$dbName", $user, $pass);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check connection
        if ($this->connection->errorCode() > 0) {
            throw new PDOException("Connection failed with error code " . $this->connection->errorCode());   
        }
        else {
            $this->data = new Data();
        }
    }

    function fetchOne($table, $key, $id) {
        $this->table = $table;
        $this->data->tableName = $table;

        // Check if table exists
        $stmt = $this->connection->prepare("SHOW TABLES LIKE :table");
        $stmt->bindParam(':table', $this->table);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            throw new PDOException("Table $table does not exist in the database.");
        }

        // Fetch data from the table
        $stmt = $this->connection->query("SELECT * FROM $table WHERE $key = $id LIMIT 1");

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
        $this->data->rowCount = 1;

        // Return the data
        $result = "";
        foreach ($this->data->tableData[0] as $cell) {
            $result .= $cell . " ";
        }
        return $result;
    }

    function fetchAll($table) {
        $this->table = $table;
        $this->data->tableName = $table;

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

    function render() {
        $result = "<h3>Table: ".$this->data->tableName."</h3>";
        $result .= "<p>Row Count: ".$this->data->rowCount."</p>";
        $result .= "<p>Column Count: ".$this->data->columnCount."</p>";

        $result .= "<table class='table table-bordered'>";
        $result .= "<thead>";
        $result .= "<tr>";
        
        foreach ($this->data->columnNames as $column) {
            $result .= "<th>".htmlentities($column)."</th>";
        }
        $result .= "</tr></thead><tbody>";
        
        $key = $this->data->columnNames[0];
        foreach ($this->data->tableData as $row) {
            $result .= "<tr onclick=\"clickRow('".$key."', ".$row[$key].", '".$this->host."', '".$this->port."', '".$this->dbName."', '".$this->table."', '".$this->user."', '".$this->pass."')\">";
            foreach ($row as $cell) {
                $result .= "<td>".htmlentities($cell)."</td>";
            }
            $result .= "</tr>";
        }

        $result .= "</tbody></table>";

        return $result;
    }

}

?>