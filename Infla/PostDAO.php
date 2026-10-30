<?php

namespace Infra;

use \PDO;
use \Generator;

class ReadPostWithUserDTO {
    public function __construct(
        public int $Id,
        public int $UserId,
        public string $UserName,
        public string $PostDateTime,
        public string $Content,
        public bool $IsExists
    ) {}
}

class PostDAO {
    public function __construct(
        private PDO $_pdo
    ) {}

    public function Create(int $userId, string $content): void {
        $query = "
            INSERT INTO posts (user_id, content, is_exists) 
            VALUES (:user_id, :content, 1);
        ";

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->BindValue(':content', $content, PDO::PARAM_STR);
        $stmt->Execute();
    }

    public function ReadPostWithUser(bool $isPostDateTimeDesc = true, ?int $limit = null, ?int $offset = null): Generator {
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

        $stmt = $this->_pdo->Prepare($query);
        if (!$isLimitNull) $stmt->BindValue(':limit', $limit, PDO::PARAM_INT);
        if (!$isOffsetNull) $stmt->BindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->Execute();

        foreach($stmt as $row) {
            yield new ReadPostWithUserDTO(
                (int)$row['id'],
                (int)$row['user_id'],
                (string)$row['user_name'],
                (string)$row['post_date_time'],
                (string)$row['content'],
                (bool)$row['is_exists']
            );
        } 
    }
    // #endregion

    public function Update(int $id, int $userId, string $content): void {
        $query = "
            UPDATE posts SET 
                user_id = :user_id, 
                content = :content 
            WHERE id = :id;
        "; 

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->BindValue(':content', $content, PDO::PARAM_STR);
        $stmt->BindValue(':id', $id, PDO::PARAM_INT);
        $stmt->Execute();
    }

    public function SoftDelete(int $id) {
        $query = "
            UPDATE posts SET 
                is_exists = 0
            WHERE id = :id;
        ";

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':id', $id, PDO::PARAM_INT);
        $stmt->Execute();
    }
    

    public function HardDelete(int $id) {
        $query = "
            DELETE FROM posts WHERE id = :id;
        ";

        $stmt = $this->_pdo->Prepare($query);
        $stmt->BindValue(':id', $id, PDO::PARAM_INT);
        $stmt->Execute();
    }
}