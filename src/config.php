<?php

return [
  'db' => [
      'dsn' => env('DB_DSN', 'pgsql:dbname=postgres;host=127.0.0.1;port=5432'),
      'username' => env('DB_USERNAME', 'postgres'),
      'password' => env('DB_PASSWORD', ''),
  ],
  'appName' => 'Mototechna',
];
