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
        $helps = [];
        if ($args[0] == null) {
            foreach ($this->actionStorage->getActions() as $action) {
                $helps[] = $this->getHelp($action) . PHP_EOL;
            }
        } else {
            $action = $this->actionStorage->getAction($args[0]);
            $helps[] = $this->getHelp($action) . PHP_EOL;
        }
        $message = implode(PHP_EOL, $helps);
        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => $message,
            "random_id" => random_int(0, 1000000)
        ]);
    }

    public function getHelp(Action $action): string
    {
        $actionNames = implode(" | ", $action->getNames());
        $description = $action->getDescription();
        return $actionNames . PHP_EOL . PHP_EOL . $description;

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