#DB設計

#CREATE DATABASE online_forum;
#use online_forum;

# 接続文字列の例(php用)
# $dbcs = 'mysql:host=localhost;dbname=tennis;charset=utf8';

#ユーザーテーブル
CREATE TABLE users(
    id INT AUTO_INCREMENT, 
    name VARCHAR(60) NOT NULL, 
    sign_up_day DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    password_hash VARCHAR(255) NOT NULL,
    is_admin TINYINT NOT NULL,
    is_exists TINYINT NOT NULL,

    PRIMARY KEY(id)
);

#投稿テーブル
CREATE TABLE posts(
    id INT AUTO_INCREMENT, 
    user_id INT NOT NULL,
    post_date_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    content VARCHAR(500) NOT NULL,
    is_exists TINYINT NOT NULL,

    PRIMARY KEY(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);

#投稿一覧データ取得
SELECT 
    posts.id AS id, 
    users.id AS user_id, 
    users.name AS user_name, 
    posts.post_date_time AS post_date_time, 
    posts.content AS content, 
    posts.is_exists 
FROM posts 
INNER JOIN users ON posts.user_id = users.id 
ORDER BY post_date_time DESC;