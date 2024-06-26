<?php
    class Database {
        private $pdo;
        private const HOST = 'localhost';
        private const DBNAME = 'gym_db';
        private const USERNAME = 'root';
        private const PASSWORD = '';
        
        public function __construct($host = self::HOST, $dbname = self::DBNAME, $username = self::USERNAME, $password = self::PASSWORD) {
            $dsn = "mysql:host=$host;dbname=$dbname";
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        public function select($table, $conditions = [], $sorting = []) {
            $sql = "SELECT * FROM $table";
            if (!empty($conditions)) {
                $fields = array_keys($conditions);
                $placeholders = array_map(function($field) {return "$field = :$field";}, $fields);
                $sql .= " WHERE " . implode(' AND ', $placeholders);
            }
            if (!empty($sorting)) {
                $fields = array_map(function($field) {return "`$field`";}, $sorting);
                $sql .= " ORDER BY " . implode(', ', $fields);
            }
            $request = $this->pdo->prepare($sql);
            $request->execute($conditions);
            return $request->fetchAll(PDO::FETCH_ASSOC); 
        }
        

        public function insert($table, $data) {
            $fields = array_keys($data);
            $placeholders = array_map(function($field) {return ":$field";}, $fields);
            $sql = "INSERT INTO $table(".implode(', ', $fields).") VALUES(".implode(', ', $placeholders).")";
            $request = $this->pdo->prepare($sql);
            $request->execute($data);
        }

        public function update($table, $data, $conditions) {
            $fields = array_keys($data);
            $placeholders = array_map(function($field) {return "$field = :$field";}, $fields);
            $sql = "UPDATE $table SET ".implode(", " ,$placeholders);
            $conditionFields = array_keys($conditions);
            $conditionPlaceholders = array_map(function($field) {return "$field = :$field";}, $conditionFields);
            $sql .= " WHERE ".implode(' AND ', $conditionPlaceholders);
            $request = $this->pdo->prepare($sql);
            $request->execute(array_merge($data, $conditions));
        }

        public function delete($table, $conditions) {
            $fields = array_keys($conditions);
            $placeholders = array_map(function($field) {return "$field = :$field";}, $fields);
            $sql = "DELETE FROM $table WHERE ".implode(' AND ', $placeholders);
            $request = $this->pdo->prepare($sql);
            $request->execute($conditions);
        }


        public function getPdo() {
            return $this->pdo;
        }
    }

    $db = new Database();

?>