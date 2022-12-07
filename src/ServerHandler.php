<?php
declare(strict_types=1);

namespace Bot;

require_once "config.php";

use Bot\action\ActionStorage;
use Bot\action\Start;
use Exception;
use VK\CallbackApi\Server\VKCallbackApiServerHandler;
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

class ServerHandler extends VKCallbackApiServerHandler
{
    private VKApiClient $vkApi;
    private ActionStorage $actionStorage;

    public function __construct()
    {
        $this->vkApi = new VKApiClient("5.131");
        $this->actionStorage = new ActionStorage(
            new Start($this->vkApi)
        );

    }

    function confirmation(int $group_id, ?string $secret)
    {
        if ($secret === GROUP_SECRET && $group_id === GROUP_ID) {
            echo API_CONFIRMATION_TOKEN;
        }
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
    public function messageNew(int $group_id, ?string $secret, array $object)
    {
        if ($secret != GROUP_SECRET) {
            echo "nok";
            return;
        }
        $message = $object["message"];
        $text = $message->text;
        $args = preg_split("/\s+/", $text);
        $user_id = $message->from_id;

        $command = $this->actionStorage->getAction(array_shift($args));
        if ($command != null) {
            $command->execute($user_id, $args);
        } else {
            $this->vkApi->messages()->send(BOT_TOKEN, [
                "user_id" => $user_id,
                "random_id" => random_int(0, PHP_INT_MAX),
                "message" => "Command not found!",
            ]);
        }

        echo "ok";
    }
}