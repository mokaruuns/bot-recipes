<?php

namespace Bot\action;

use Bot\class\Database;
use Bot\class\Dish;
use Exception;
use VK\Client\VKApiClient;
use VK\Exceptions\Api\VKApiMessagesCantFwdException;
use VK\Exceptions\Api\VKApiMessagesChatBotFeatureException;
use VK\Exceptions\Api\VKApiMessagesChatUserNoAccessException;
use VK\Exceptions\Api\VKApiMessagesContactNotFoundException;
use VK\Exceptions\Api\VKApiMessagesDenySendException;
use VK\Exceptions\Api\VKApiMessagesKeyboardInvalidException;
use VK\Exceptions\Api\VKApiMessagesPrivacyException;
use VK\Exceptions\Api\VKApiMessagesTooLongForwardsException;
use VK\Exceptions\Api\VKApiMessagesTooLongMessageException;
use VK\Exceptions\Api\VKApiMessagesTooManyPostsException;
use VK\Exceptions\Api\VKApiMessagesUserBlockedException;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

class GetDishById implements Action
{

    private VKApiClient $vkApi;
    private ActionStorage $actionStorage;

    public function __construct(VKApiClient $vkApi)
    {
        $this->vkApi = $vkApi;
    }


    /**
     * @throws VKApiMessagesPrivacyException
     * @throws VKApiMessagesDenySendException
     * @throws VKApiMessagesTooLongMessageException
     * @throws VKApiMessagesChatUserNoAccessException
     * @throws VKApiMessagesTooManyPostsException
     * @throws VKApiMessagesChatBotFeatureException
     * @throws VKClientException
     * @throws VKApiMessagesCantFwdException
     * @throws VKApiMessagesUserBlockedException
     * @throws VKApiException
     * @throws VKApiMessagesKeyboardInvalidException
     * @throws VKApiMessagesContactNotFoundException
     * @throws VKApiMessagesTooLongForwardsException
     * @throws Exception
     */
    public function execute(int $user_id, array $args): void
    {
        $db = new Database();
        $conn = $db->getConnection();
        $dish = new Dish($conn);

        if ($args[0] == null) {
            $random_dish = $dish->getRandom();
            $id = $random_dish['id'];
            $ingredients = $dish->getIngredientsNames($id);
            $message = $this->getBody($user_id, $random_dish, $ingredients);
        } else {
            $id = intval($args[0]);
            $d = $dish->getById($id);
            $ingredients = $dish->getIngredientsNames($id);
            $message = $this->getBody($user_id, $d, $ingredients);
        }
        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => $message,
            "random_id" => random_int(0, 1000000)
        ]);
    }

    public function getNames(): array
    {
        return array("id");
    }

    public function getDescription(): string
    {
        $result = "Получить блюдо по id." . PHP_EOL;
        $result .= "Пример: id 1" . PHP_EOL;
        $result .= "Если не указать номер блюда, то будет получено случайное блюдо." . PHP_EOL;
        $result .= "Пример: id" . PHP_EOL;
        return $result;
    }

    public function setActionStorage(ActionStorage $actionStorage): void
    {
        $this->actionStorage = $actionStorage;
    }

    public function sendError(int $user_id, string $message): void
    {
        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => $message,
            "random_id" => random_int(0, 1000000)
        ]);
    }


    private function getBody(int $user_id, array $dish, array $ingredients): string
    {
        $recipes = implode(PHP_EOL . PHP_EOL, $dish['recipe']);
        $message = "Вот что я нашел: " . PHP_EOL;
        $message .= "- Название: " . $dish['name'] . PHP_EOL . PHP_EOL;
        $message .= "- Ингредиенты: " . PHP_EOL . implode(PHP_EOL, $ingredients) . PHP_EOL . PHP_EOL;
        $message .= "- Рецепт приготовления: " . PHP_EOL . $recipes . PHP_EOL . PHP_EOL;
        $message .= "- url: " . $dish['url'];
        return $message;


    }
}