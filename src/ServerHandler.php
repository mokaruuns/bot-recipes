<?php

namespace Bot;

require_once "config.php";

use VK\CallbackApi\Server\VKCallbackApiServerHandler;
use VK\Client\VKApiClient;

class ServerHandler extends VKCallbackApiServerHandler
{
    private VKApiClient $vkApi;

    public function __construct()
    {
        $this->vkApi = new VKApiClient("5.131");

    }

    function confirmation(int $group_id, ?string $secret)
    {
        if ($secret === GROUP_SECRET && $group_id === GROUP_ID) {
            echo API_CONFIRMATION_TOKEN;
        }
    }

    public function messageNew(int $group_id, ?string $secret, array $object)
    {
        $message = $object["message"];
        $text = $message->text;
        $user_id = $message->from_id;


        $this->vkApi->messages()->send(BOT_TOKEN, [
            "user_id" => $user_id,
            "random_id" => random_int(0, PHP_INT_MAX),
            "message" => "Command not found!"
        ]);

        echo "ok";
    }
}