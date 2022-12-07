<?php


require_once realpath(dirname(__FILE__)) . "/vendor/autoload.php";
require_once realpath(dirname(__FILE__)) . '/config.php';

use VK\CallbackApi\Server\VKCallbackApiServerHandler;

$handler = new ServerHandler();
$data = json_decode(file_get_contents("php://input"));
$handler->parse($data);