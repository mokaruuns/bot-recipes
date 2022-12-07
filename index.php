<?php
declare(strict_types=1);


use Bot\ServerHandler;

require_once "vendor/autoload.php";
require_once "config.php";


$handler = new ServerHandler();
$data = json_decode(file_get_contents("php://input"));
$handler->parse($data);