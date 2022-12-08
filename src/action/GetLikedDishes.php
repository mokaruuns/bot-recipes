<?php

namespace Bot\action;

use Bot\class\Database;
use Bot\class\Dish;
use Bot\class\Person;
use VK\Client\VKApiClient;

class GetLikedDishes implements Action
{

    private $vkApi;

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
        return array("liked", "избранное", "ld");
    }

    public function getDescription(): string
    {
        $message = "Выводит список избранных блюд" . PHP_EOL;
        $message .= "liked - выводит список избранных блюд";
        return $message;
    }

    public function setActionStorage(\Bot\action\ActionStorage $actionStorage): void
    {
        // TODO: Implement setActionStorage() method.
    }

    public function sendError(int $user_id, string $message): void
    {
        // TODO: Implement sendError() method.
    }
}
