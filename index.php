<?php

require_once __DIR__ . '/Infra/UserDAO.php';
require_once __DIR__ . '/Infra/PostDAO.php';

use Infra;

//シングルフロントコントローラー
$requestMethod = $_SERVER['REQUEST_METHOD'];
if ($requestMethod !== 'GET' && $requestMethod !== 'POST') 
    clientError("本システムではGET/POST以外対応していません。");

$path = null;
if ($requestMethod === 'GET') $path = $_GET['path'] ?? null; 
else if ($requestMethod === 'POST') $path = $_POST['path'] ?? null;

if (is_null($path)) ClientErrorAcction("pathクエリ/パラメータが指定されていません。");
else if ($path === "home") HomeAction();

function ClientErrorAcction($errorMsg) {
    require_once __DIR__ . '/Views/error.phtml';
}

function HomeAction() {
    require_once __DIR__ . '/Views/home.phtml';
}