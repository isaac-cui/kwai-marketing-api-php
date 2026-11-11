<?php
declare(strict_types=1);
header('Content-Type: application/json');
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/redirect') {
    header('Location: /target', true, 307);
    echo '{"status":307}';
} elseif ($path === '/target') {
    echo '{"unexpected":"redirect was followed"}';
} else {
    echo json_encode([
        'method' => $_SERVER['REQUEST_METHOD'],
        'token' => $_SERVER['HTTP_ACCESS_TOKEN'] ?? null,
        'body' => file_get_contents('php://input'),
        'query' => $_GET,
    ], JSON_THROW_ON_ERROR);
}
