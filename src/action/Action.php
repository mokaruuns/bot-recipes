<?php

namespace Bot\action;

use VK\Client\VKApiClient;

interface Action
{
    public function __construct(VKApiClient $vkApi);

    public function execute(int $user_id, array $args): void;

    public function getNames(): array;

    public function getDescription(): string;

    public function setActionStorage(ActionStorage $actionStorage): void;

}