<?php
declare(strict_types=1);

namespace Bot;

require_once "config.php";

use Bot\action\ActionStorage;
use Bot\action\Help;
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
            new Start($this->vkApi),
            new Help($this->vkApi)
        );
        $this->actionStorage->init($this->actionStorage);


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
        $commandAndArgs = $this->getCommandAndArgs($text);
        $action = $this->actionStorage->getAction($commandAndArgs["command"]);
        if ($action) {
            $action->execute($message->peer_id, $commandAndArgs["args"]);
        } else {
            $this->vkApi->messages()->send(BOT_TOKEN, [
                "peer_id" => $message->peer_id,
                "message" => "Неизвестная команда",
                "random_id" => random_int(0, 1000000)
            ]);
        }

        echo "ok";
    }

    private function getCommandAndArgs(string $text): array
    {
        $text = mb_strtolower($text);
        $args = preg_split('/\s+/', $text, limit: 2);
        $command = array_shift($args);
        $args = preg_split('/\s*[,.]\s*/', $args[0] ?? "");
        return ["command" => $command, "args" => $args];
    }
}