<?php

require_once __DIR__ . '/PostsDAO.php';
require_once __DIR__ . '/UsersDAO.php';

// 名前空間やクラス名が実際の定義（Infra名前空間など）に合わせてある前提
use Infra\UsersDAO;
use Infra\PostsDAO;
use Infra\UpdateUserDTO;
use Infra\CreateUserDTO;
use Infra\CreatePostDTO;

$dbcs = 'mysql:host=localhost;dbname=online_forum;charset=utf8';
$dbUser = 'root';
$dbPass = '';

$pdo = new PDO($dbcs, $dbUser, $dbPass);

// インスタンス化の変数名・クラス名を修正
$postsDao = new PostsDAO($pdo);
$usersDao = new UsersDAO($pdo);

//$user = new CreateUserDTO("arai", password_hash("hogehoge", PASSWORD_DEFAULT), false);
//$usersDao->Create($user);

$post = new CreatePostDTO(8, "hello!!!");
$postsDao->Create($post);