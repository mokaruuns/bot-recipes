<?php

namespace Bot\action;

use Bot\class\Dish;
use Bot\config\Database;
use VK\Client\VKApiClient;

class GetRandomDish implements Action
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
        $conn = $db->getConnection();
        $dish = new Dish($conn);
        $random_dish = $dish->getRandom();
        $message = "Сегодня в меню: " . PHP_EOL . $random_dish['name'] . PHP_EOL . "Ссылка на рецепт: " . PHP_EOL . $random_dish['url'];
        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => $message,
            "random_id" => random_int(0, 1000000)
        ]);
    }

    public function getNames(): array
    {
        return array("random", "блюдо", "rd");
    }

    public function getDescription(): string
    {
        return "Получить случайное блюдо";
    }

    public function setActionStorage(ActionStorage $actionStorage): void
    {
//         TODO: Implement setActionStorage() method.
    }
}