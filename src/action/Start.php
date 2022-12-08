<?php

namespace Bot\action;

use VK\Client\VKApiClient;


class Start implements Action
{
    private VKApiClient $vkApi;
    private ActionStorage $actionStorage;


    public function __construct(VKApiClient $vkApi)
    {
        $this->vkApi = $vkApi;
    }

    public function execute(int $user_id, array $args): void
    {

        $message = "Привет! Я бот, который поможет тебе найти рецепт блюда по его ингредиентам." . PHP_EOL;
        $message .= "Для получения списка всех команд введите 'help'" . PHP_EOL;
        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => $message,
            "random_id" => random_int(0, 1000000)
        ]);
    }

    public function getNames(): array
    {
        return array("start", "старт", "начать", "начало", "привет");
    }

    public function getDescription(): string
    {
        return "Начало работы с ботом";
    }

    public function setActionStorage(ActionStorage $actionStorage): void
    {
//         TODO: Implement setActionStorage() method.
    }

    public function sendError(int $user_id, string $message): void
    {
        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => $message,
            "random_id" => random_int(0, 1000000)
        ]);
    }
}