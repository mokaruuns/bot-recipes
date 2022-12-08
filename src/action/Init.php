<?php

namespace Bot\action;

use Bot\config\Database;
use VK\Client\VKApiClient;

class Init implements Action
{
    private VKApiClient $vkApi;
    private ActionStorage $actionStorage;

    public function __construct(VKApiClient $vkApi)
    {
        $this->vkApi = $vkApi;
    }

    public function execute(int $user_id, array $args): void
    {
        $db = new Database();

        $db->fillDatabase();

        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => "База данных заполнена",
            "random_id" => random_int(0, 1000000)
        ]);
    }

    public function getNames(): array
    {
        return array("init");
    }

    public function getDescription(): string
    {
        return "Заполнить базу данных";
    }

    public function setActionStorage(ActionStorage $actionStorage): void
    {
        $this->actionStorage = $actionStorage;
    }
}