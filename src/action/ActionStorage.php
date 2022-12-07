<?php

namespace Bot\action;

class ActionStorage
{
    private array $actions = [];

    public function __construct(...$actions)
    {
        $this->actions = $actions;
    }

    public function getAction(string $name): ?Action
    {
        foreach ($this->actions as $action) {
            if (in_array($name, $action->getNames())) {
                return $action;
            }
        }
        return null;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function setActions(array $actions): void
    {
        $this->actions = $actions;
    }

    public function addAction(Action $action): void
    {
        $this->actions[] = $action;
    }

    public function removeAction(Action $action): void
    {
        $this->actions = array_diff($this->actions, [$action]);
    }

}