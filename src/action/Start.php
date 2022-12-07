<?php

namespace Bot\action;

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

class Start implements Action
{
    private array $params;
    private VKApiClient $vkApi;


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
     * @throws VKApiMessagesKeyboardInvalidException
     * @throws VKApiException
     * @throws VKApiMessagesContactNotFoundException
     * @throws VKApiMessagesTooLongForwardsException
     * @throws \Exception
     */
    public function execute(int $user_id, array $args): void
    {

        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => "Привет, я бот!",
            "random_id" => random_int(0, 1000000)
        ]);
    }

    public function getNames(): array
    {
        return array("start", "старт");
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

}