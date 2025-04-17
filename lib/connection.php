<?php
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '10.0.0.115') {
    define('DB_HOST', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', 'politica');
} else {
    define('DB_HOST', '100.42.189.93');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'S3nh@sb@nc0');
    define('DB_DATABASE', 'politica');
}

$con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
mysqli_set_charset($con,'utf8');

if (!$con) {
    die('Não foi possível conectar: ' . mysqli_error());
}

#mysqli_close($con);