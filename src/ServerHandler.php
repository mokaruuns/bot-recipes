<?php
declare(strict_types=1);

namespace Bot;

require_once "config.php";

use Bot\action\ActionStorage;
use Bot\action\Start;
use VK\CallbackApi\Server\VKCallbackApiServerHandler;
use VK\Client\VKApiClient;

class ServerHandler extends VKCallbackApiServerHandler
{
    private VKApiClient $vkApi;
    private ActionStorage $actionStorage;

    public function __construct()
    {
        $this->vkApi = new VKApiClient("5.131");
        $this->actionStorage = new ActionStorage(
            new Start($this->vkApi)
        );

    }

    function confirmation(int $group_id, ?string $secret)
    {
        if ($secret === GROUP_SECRET && $group_id === GROUP_ID) {
            echo API_CONFIRMATION_TOKEN;
        }
    }

    public function messageNew(int $group_id, ?string $secret, array $object)
    {
        if ($secret != GROUP_SECRET) {
            echo "nok";
            return;
        }
        $message = $object["message"];
        $text = $message->text;
        $args = preg_split("/\s+/", $text);
        $user_id = $message->from_id;

        $command = $this->actionStorage->getAction(array_shift($args));
        if ($command != null) {
            $command->execute($user_id, $args);
        } else {
            $this->vkApi->messages()->send(BOT_TOKEN, [
                "user_id" => $user_id,
                "random_id" => random_int(0, PHP_INT_MAX),
                "message" => "Command not found!",
            ]);
        }

        echo "ok";
    }
}