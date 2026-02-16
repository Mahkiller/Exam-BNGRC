<?php
/**
 * Router for PHP Built-in Server
 * Used with: php -S localhost:8000 -t public router.php
 * 
 * This file routes all requests to public/index.php
 * while allowing static files to be served normally
 */

// Get the requested file path
$requested_file = __DIR__ . '/public' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If it's a real file or directory (static assets), serve it
if (is_file($requested_file) || is_dir($requested_file)) {
    return false; // Let the server handle it
}

// Otherwise, route to index.php for Flight to handle
require_once __DIR__ . '/public/index.php';
