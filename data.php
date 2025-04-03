<?php

class Data implements JsonSerializable {
    public $tableName = "";
    public $rowCount = 0;
    public $columnCount = 0;
    public $columnNames = [];
    public $tableData = [];

    public function jsonSerialize(): mixed {
        return [
            'tableName' => $this->tableName,
            'columnCount' => $this->columnCount,
            'columnNames' => $this->columnNames,
            'tableData' => $this->tableData,
            'rowCount' => $this->rowCount
        ];
    }
}

?>