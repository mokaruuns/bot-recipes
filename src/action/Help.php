<?php

namespace Bot\action;

use VK\Client\VKApiClient;

class Help implements Action
{
    private VKApiClient $vkApi;
    private ActionStorage $actionStorage;

    public function __construct(VKApiClient $vkApi)
    {
        $this->vkApi = $vkApi;
    }

    public function execute(int $user_id, array $args): void
    {
        if ($args[0] == null) {
            $actionNames = implode(PHP_EOL, $this->getNames());
            $description = $this->getDescription();
        } else {
            $action = $this->actionStorage->getAction($args[0]);
            $actionNames = implode(PHP_EOL, $action->getNames());
            $description = $action->getDescription();
        }
        $message = "Список команд:" . PHP_EOL . $actionNames . PHP_EOL . "Описание: " . PHP_EOL . $description;
        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => $message,
            "random_id" => random_int(0, 1000000)
        ]);
    }

    public function getNames(): array
    {
        return array("help", "помощь");
    }

    public function getDescription(): string
    {
        return "Для получения информации о команде введите: help <команда>";
    }


    public function setActionStorage(ActionStorage $actionStorage): void
    {
        $this->actionStorage = $actionStorage;
    }
}