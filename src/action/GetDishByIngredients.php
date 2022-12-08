<?php

namespace Bot\action;


use Bot\class\Database;
use Bot\class\Dish;
use Bot\class\Helper;
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

class GetDishByIngredients implements Action
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
     * @throws \Exception
     */
    public function execute(int $user_id, array $args): void
    {

        $db = new Database();
        $conn = $db->getConnection();
        $helper = new Helper($conn);
        $ingredients = $helper->getManySimilarIngredients($args);
        $dishesId = $helper->getDishIdByIngredientsId($ingredients);

        $message = [];
        $dish = new Dish($conn);
        foreach ($dishesId as $dishId) {
            $d = $dish->getById($dishId);
            $message[] = "id " . $d['id'] . " - " . $d['name'] . PHP_EOL . $d['url'];
        }
        if (count($message) == 0) {
            $this->sendError($user_id, "По вашему запросу ничего не найдено");
            return;
        }
        $message = "Вот что я нашел: " . PHP_EOL . implode(PHP_EOL . PHP_EOL, $message);
        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => $message,
            "random_id" => random_int(0, 1000000)
        ]);
    }

    public function getNames(): array
    {
        return array("ing", "поиск", "ингредиенты");
    }

    public function getDescription(): string
    {
        $message = "Поиск блюд по ингредиентам" . PHP_EOL;
        $message .= "Выводятся все блюда, которые можно приготовить из указанных ингредиентов" . PHP_EOL;
        $message .= "Пример: ing картофель, мясо" . PHP_EOL;
        $message .= "Пример: ing картофель, мясо, молоко, сахар, масло, перец, лук, чеснок, мука, яйцо, сливки";
        return $message;
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
}