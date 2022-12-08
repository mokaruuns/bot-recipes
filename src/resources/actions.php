<?php

define('START_KEYBOARD', [
    [
        [
            'action' => [
                'type' => 'text',
                'payload' => json_encode(['action' => START_ACTION]),
                'label' => START_ACTION_BUTTON_TEXT
            ],
            'color' => 'primary'
        ]
    ]
]);
const START_ACTION = "start";
const START_ACTION_BUTTON_TEXT = "Начать";
