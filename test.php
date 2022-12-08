<?php

function getCommandAndArgs(string $text): array
{
    $text = mb_strtolower($text);
    $args = preg_split('/\s+/', $text, limit: 2);
    $command = array_shift($args);
    echo $command . "_";
    echo implode(" ", $args) . "_";
    $args = preg_split('/\s*[,.]\s*/', $args[0] ?? "");
    return ["command" => $command, "args" => $args];
}


echo json_encode(getCommandAndArgs("help"));
