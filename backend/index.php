<?php

declare(strict_types=1);

require_once __DIR__ . "/inc/bootstrap.php";
set_exception_handler("ErrorHandler::handleException");

header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if ($parts[3] != "users") {
    http_response_code(404);
    exit;
}

$id = $parts[4] ?? null;

$database = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);

$database->getConnection();
$gateway = new UserModel($database);

$controller = new UserController($gateway);
$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);
