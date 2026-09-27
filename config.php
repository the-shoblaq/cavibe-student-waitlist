<?php
declare(strict_types=1);
$dsn='mysql:host='.(getenv('DB_HOST')?:'localhost').';dbname='.(getenv('DB_NAME')?:'cavibe_waitlist').';charset=utf8mb4';
try{$pdo=new PDO($dsn,getenv('DB_USER')?:'root',getenv('DB_PASS')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
catch(PDOException $e){http_response_code(500);exit('Database connection failed.');}
