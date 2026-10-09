<?php
declare(strict_types=1);
$envFile=__DIR__.'/.env';
if(is_readable($envFile)){
    foreach(file($envFile,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){
        $line=trim($line);
        if($line===''||$line[0]==='#'||strpos($line,'=')===false)continue;
        [$k,$v]=array_map('trim',explode('=',$line,2));
        $v=trim($v,"\"'");
        if(getenv($k)===false){putenv("$k=$v");$_ENV[$k]=$v;}
    }
}
$port=getenv('DB_PORT')?:'3306';
$dsn='mysql:host='.(getenv('DB_HOST')?:'localhost').';port='.$port.';dbname='.(getenv('DB_NAME')?:'cavibe_waitlist').';charset=utf8mb4';
try{$pdo=new PDO($dsn,getenv('DB_USER')?:'root',getenv('DB_PASS')?:'',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}
catch(PDOException $e){http_response_code(500);exit('Database connection failed.');}
