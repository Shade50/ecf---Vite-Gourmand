<?php

namespace App\Service;

use MongoDB\Client;
use MongoDB\Database;

class MongoDBService
{
    private Database $database;

    public function __construct()
    {
        $client = new Client(
            $_ENV['APP_MONGODB_URL'] ?? 'mongodb://127.0.0.1:27017'
        );

        $this->database = $client->selectDatabase(
            $_ENV['APP_MONGODB_DB'] ?? 'vite_gourmand_stats'
        );
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }
}