<?php

declare(strict_types=1);

namespace App\Db;

use App\Db\Exception\DbException;
use App\Db\Exception\UniqueDbException;
use PDO;
use PDOException;

class DbProvider
{
    private PDO $pdo;

    private const UNIQUE_CONSTRAINT_CODE = 23000;

    public function __construct(
        private readonly string $dbPath,
    ) {
        $this->pdo = new PDO("sqlite:" . dirname(__DIR__, 2) . '/' . $this->dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function fetchAll(string $query, array $params = []): array
    {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($params);
            $response = $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new DbException($e->errorInfo[2], $e->errorInfo[0], $e);
        }

        if ($response === false) {
            return [];
        }

        return $response;
    }

    public function execute(string $query, array $params = []): int
    {
        try {
            $statement = $this->pdo->prepare($query);
            $statement->execute($params);
            return $statement->rowCount();
        } catch (PDOException $e) {
            if ((int) $e->errorInfo[0] === self::UNIQUE_CONSTRAINT_CODE) {
                throw new UniqueDbException($e->errorInfo[2], (int) $e->errorInfo[0], $e);
            }

            throw new DbException($e->errorInfo[2], (int) $e->errorInfo[0], $e);
        }
    }

    public function insert(string $query, array $params = []): int
    {
        try {
            $pdo = $this->pdo;
            $statement = $pdo->prepare($query);
            $statement->execute($params);
            return (int) $pdo->lastInsertId();
        } catch (PDOException $e) {
            if ((int) $e->errorInfo[0] === self::UNIQUE_CONSTRAINT_CODE) {
                throw new UniqueDbException($e->errorInfo[2], (int) $e->errorInfo[0], $e);
            }

            throw new DbException($e->errorInfo[2], (int) $e->errorInfo[0], $e);
        }
    }
}
