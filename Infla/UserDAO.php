<?php

namespace Infra;

use \PDO;

class ReadUserDTO {
    public function __construct(
        public int $Id,
        public string $Name,
        public string $SignUpDay,
        public string $PasswordHash,
        public bool $IsAdmin,
        public bool $IsExists
    ){}
}

class UserDAO {
    public function __construct(
        private PDO $_pdo
    ) {}

    public function Create(string $name, string $passwordHash, bool $isAdmin): void {
        $query = "
            INSERT INTO users (name, password_hash, is_admin, is_exists) 
            VALUES (:name, :password_hash, :is_admin, 1);
        ";

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':name', $name, PDO::PARAM_STR);
        $stmt->BindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->BindValue(':is_admin', $isAdmin, PDO::PARAM_BOOL);
        $stmt->Execute();
    }

    public function Update(int $id, string $name, string $passwordHash, bool $isAdmin): void {
        $query = "
            UPDATE users SET name = :name, password_hash = :password_hash, is_admin = :is_admin
            WHERE id = :id;
        ";

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':name', $name, PDO::PARAM_STR);
        $stmt->BindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->BindValue(':is_admin', $isAdmin, PDO::PARAM_BOOL);
        $stmt->BindValue(':id', $id, PDO::PARAM_INT);
        $stmt->Execute();
    }

    public function SoftDelete(int $id): void {
        $query = "
            UPDATE users SET is_exists = 0
            WHERE id = :id;
        ";

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':id', $id, PDO::PARAM_INT);
        $stmt->Execute();
    }

    public function HardDelete(int $id): void {
        $query = "
            DELETE FROM users WHERE id = :id;
        ";

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':id', $id, PDO::PARAM_INT);
        $stmt->Execute();
    }
}