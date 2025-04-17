<?php

namespace App\Services;

use MongoDB\Client;

class MongoLogger
{
    protected $collection;

    public function __construct()
    {
        $client = new Client(env('MONGO_URI', 'mongodb://127.0.0.1:27017'));
        $this->collection = $client->{env('DB_MONGO_DATABASE', 'logs')}->activity_logs;
    }

    public function log($action, $userId = null, $details = [])
    {
        $this->collection->insertOne([
            'action' => $action,
            'user_id' => $userId,
            'details' => $details,
            'created_at' => now(),
        ]);
    }
}
