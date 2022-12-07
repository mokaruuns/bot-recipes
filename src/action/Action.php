<?php

namespace Bot\action;

interface Action
{
    public function execute(int $user_id, array $args): void;

    public function getNames(): array;

    public function setParams(array $params): void;

    public function getParams(): array;

}