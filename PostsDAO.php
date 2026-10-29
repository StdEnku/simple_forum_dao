<?php

namespace Infra;

use PDO;
use Generator;

// #region DTO
class CreatePostDTO {
    public function __construct(
        public int $UserId,
        public string $Content // string に修正
    ){}
}

class UpdatePostDTO {
    public function __construct(
        public int $Id,
        public int $UserId,
        public string $Content // string に修正
    ){}
}
// #endregion

class PostsDAO {
    private PDO $_pdo;

    public function __construct(PDO $pdo) {
        $this->_pdo = $pdo;
    }

    // #region Create系処理
    public function Create(CreatePostDTO $createPostDTO) {
        $query = '
            INSERT INTO posts (user_id, content, is_exists) 
            VALUES (:user_id, :content, :is_exists);
        ';

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':user_id', $createPostDTO->UserId, PDO::PARAM_INT);
        $stmt->bindValue(':content', $createPostDTO->Content, PDO::PARAM_STR);
        $stmt->bindValue(':is_exists', true, PDO::PARAM_BOOL);
        $stmt->execute();
    }
    // #endregion

    // #region Update系処理
    public function Update(UpdatePostDTO $postUpdateDTO) {
        $query = '
            UPDATE posts SET 
                user_id = :user_id, 
                content = :content 
            WHERE ID = :id;
        '; 

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':user_id', $postUpdateDTO->UserId, PDO::PARAM_INT);
        $stmt->bindValue(':content', $postUpdateDTO->Content, PDO::PARAM_STR);
        $stmt->bindValue(':id', $postUpdateDTO->Id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // #endregion

    // #region Delete系処理
    public function SoftDelete(int $id) {
        $query = '
            UPDATE posts SET is_exists = 0
            WHERE ID = :id;
        ';

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function HardDelete(int $id) {
        $query = '
            DELETE FROM posts WHERE id = :id;
        ';

        $stmt = $this->_pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // #endregion
}