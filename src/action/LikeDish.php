<?php

namespace Bot\action;

use VK\Client\VKApiClient;

class LikeDish implements Action
{

    public function __construct(VKApiClient $vkApi)
    {
        $this->vkApi = $vkApi;
    }

    public function execute(int $user_id, array $args): void
    {
        // TODO: Implement execute() method.
    }

    public function getNames(): array
    {
        return array("like", "лайк");
    }

    public function getDescription(): string
    {
        $message = "Добавляет блюдо в избранное по его id" . PHP_EOL;
        $message .= "like [id блюда] - добавляет блюдо в избранное";
        return $message;
    }

    public function setActionStorage(ActionStorage $actionStorage): void
    {
        // TODO: Implement setActionStorage() method.
    }

    public function sendError(int $user_id, string $message): void
    {
        // TODO: Implement sendError() method.
    }
}