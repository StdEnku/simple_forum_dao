<?php

namespace Infra;

use PDO;
use Generator;

// #region DTO
class CreatePostDTO {
    public function __construct(
        public int $UserId,
        public string $Content
    ){}
}

class UpdatePostDTO {
    public function __construct(
        public int $Id,
        public int $UserId,
        public string $Content 
    ){}
}

class ReadPostAndUserInfoDTO {
    public function __construct(
        public int $Id,
        public int $UserId,
        public string $UserName,
        public string $PostDateTime,
        public string $Content,
        public bool $IsExists
    ) {}
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

    // #region Read系処理
    public function ReadPostAndUserInfo(bool $isPostDateTimeDesc = true, ?int $limit = null, ?int $offset = null): Generator {
        $isLimitNull = $limit === null;
        $isOffsetNull = $offset === null;

        $sortStmt = $isPostDateTimeDesc ? 'DESC' : 'ASC';
        $limitStmt = $isLimitNull ? "" : "LIMIT :limit";
        $offsetStmt = $isOffsetNull ? "" : "OFFSET :offset";

        $query = "
            SELECT 
                posts.id AS id, 
                users.id AS user_id, 
                users.name AS user_name, 
                posts.post_date_time AS post_date_time, 
                posts.content AS content, 
                posts.is_exists AS is_exists
            FROM posts 
            INNER JOIN users ON posts.user_id = users.id 
            ORDER BY post_date_time {$sortStmt}
            {$limitStmt} {$offsetStmt};
        ";

        $stmt = $this->_pdo->prepare($query);
        if (!$isLimitNull) $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        if (!$isOffsetNull) $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        foreach($stmt as $row) {
            yield new ReadPostAndUserInfoDTO(
                $row['id'],
                $row['user_id'],
                $row['user_name'],
                $row['post_date_time'],
                $row['content'],
                $row['is_exists']
            );
        } 
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