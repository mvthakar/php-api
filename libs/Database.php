<?php

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    public static function instance(): Database
    {
        if (Database::$instance == null)
            Database::$instance = new Database();

        return Database::$instance;
    }
    
    public function __construct()
    {
        $this->pdo = new PDO(
            "mysql:host=localhost;port=3306;dbname=app_db",
            "root",
            ""
        );
    }

    private function executeAndGetStatement(string $query, ?array $params = null): PDOStatement
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($params);

        return $statement;
    }

    public function execute(string $query, ?array $params = null)
    {
        $this->executeAndGetStatement($query, $params);
    }

    public function get(string $query, ?array $params = null): mixed
    {
        $statement = $this->executeAndGetStatement($query, $params);
        $result = $statement->fetch(PDO::FETCH_OBJ);

        return $result;
    }

    public function getAll(string $query, ?array $params = null): mixed
    {
        $statement = $this->executeAndGetStatement($query, $params);
        $result = $statement->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    public function exists(string $query, ?array $params = null): bool
    {
        $result = $this->get($query, $params);
        return $result->count > 0;
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }
}