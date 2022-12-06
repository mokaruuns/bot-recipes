<?php

require_once realpath(dirname(__FILE__)) . '/../class/Dish.php';
require_once realpath(dirname(__FILE__)) . '/../class/Ingredient.php';

function getRandomDish(): array
{
    $db = new Database();
    $conn = $db->getConnection();
    $dish = new Dish($conn);
    $dish->getRandomDish();
    return $dish->getDish();
}

function getSimilarIngredients(string $ingredientNames): array
{
    $db = new Database();
    $conn = $db->getConnection();
    $ingredientNames = new Ingredient($conn);
    $ingredientNames->setIngredient(["ingredient" => $ingredientNames]);
    return $ingredientNames->getSimilarIngredientsId();
}

function getManySimilarIngredients(array $ingredients): array
{
    $db = new Database();
    $conn = $db->getConnection();
    $ingredient = new Ingredient($conn);
    $res = [];
    foreach ($ingredients as $ingredientName) {
        $ingredient->setIngredient(["ingredient" => $ingredientName]);
        $res = array_merge($res, $ingredient->getSimilarIngredientsId());
    }
    return $res;
}

