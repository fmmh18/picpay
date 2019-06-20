<?php

if (file_exists(ROOT . '.env')) {
    $dotenv = Dotenv\Dotenv::create(ROOT);
    $dotenv->load();
}

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    ],
];
