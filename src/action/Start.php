<?php

namespace Bot\action;

use VK\Client\VKApiClient;


class Start implements Action
{
    private array $params;
    private VKApiClient $vkApi;


    public function __construct(VKApiClient $vkApi)
    {
        $this->vkApi = $vkApi;
    }

    public function execute(int $user_id, array $args): void
    {

        $this->vkApi->messages()->send(BOT_TOKEN, [
            "peer_id" => $user_id,
            "message" => "Привет, я бот!",
            "random_id" => random_int(0, 1000000),
            "keyboard" => json_encode($this->getKeyboard(), JSON_UNESCAPED_UNICODE)
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

    public function getKeyboard(): array
    {
        return [
            "one_time" => false,
            "buttons" => [
                [
                    [
                        "action" => [
                            "type" => "text",
                            "payload" => json_encode(["button" => "start"]),
                            "label" => "Начать"
                        ],
                        "color" => "primary"
                    ]
                ]
            ]
        ];
    }

}