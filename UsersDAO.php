<?php

namespace Infra;

use PDO;
use Generator;

// #region DTO
// Create処理用DTO
class CreateUserDTO {
    public function __construct(
        public string $Name,
        public string $PasswordHash,
        public bool $IsAdmin,
    ) {}
}

// Update処理用DTO
class UpdateUserDTO {
    public function __construct(
        public int $Id,
        public string $Name,
        public string $PasswordHash,
    ){}
}

// Read用のDTO
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

// #endregion

class UsersDAO {
    private PDO $_pdo;

    public function __construct(PDO $pdo) {
        $this->_pdo = $pdo;
    }

    // #region Create系処理
    public function Create(CreateUserDTO $createUserDto) {
        $query = '
            INSERT INTO users (name, password_hash, is_admin, is_exists)
            VALUES(:name, :password_hash, :is_admin, :is_exists);
        ';

        $stmt = $this->_pdo->prepare($query);
        // $userDto を $createUserDto に修正
        $stmt->bindValue(':name', $createUserDto->Name, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $createUserDto->PasswordHash, PDO::PARAM_STR);
        $stmt->bindValue(':is_admin', $createUserDto->IsAdmin, PDO::PARAM_BOOL); 
        $stmt->bindValue(':is_exists', true, PDO::PARAM_BOOL);
        $stmt->execute();
    }
    // #endregion

    // #region Read系処理
    public function ReadById(int $id) : ?ReadUserDTO {
        $query = '
            SELECT * FROM users
            WHERE id = :id;
        ';

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null; 

        return new ReadUserDTO(
            (int)$row['id'],
            (string)$row['name'],
            (string)$row['sign_up_day'],
            (string)$row['password_hash'],
            (bool)$row['is_admin'],
            (bool)$row['is_exists']
        );
    }
    // #endregion

    // #region Update系処理
    public function Update(UpdateUserDTO $updateUserDTO) {
        $query = '
            UPDATE users SET 
                name = :name, 
                password_hash = :password_hash
            WHERE ID = :id;
        '; // password_hash の後ろの不要なカンマを削除

        $stmt = $this->_pdo->prepare($query);
        // 重複していた $$ を修正
        $stmt->bindValue(':name', $updateUserDTO->Name, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $updateUserDTO->PasswordHash, PDO::PARAM_STR);
        $stmt->bindValue(':id', $updateUserDTO->Id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // #endregion

    // #region Delete処理
    public function SoftDelete(int $id) {
        $query = '
            UPDATE users SET 
                is_exists = 0
            WHERE ID = :id;
        ';

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function HardDelete(int $id) {
        $query = '
            DELETE FROM users WHERE ID = :id;
        ';

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // #endregion
}