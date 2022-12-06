<?php

require_once realpath(dirname(__FILE__)) . '/../class/Dish.php';
require_once realpath(dirname(__FILE__)) . '/../class/Ingredient.php';

function insertFullDish($conn, $name, $url, $recipes, $images, $ingredients): void
{
    $dish = new Dish($conn);
    $dish->setDish(["name" => $name, "url" => $url, "count_ingredients" => count($ingredients), "recipe" => $recipes, "images_url" => $images]);
    $dish->insertDish();

    foreach ($ingredients as $ingredient_name) {
        $ingredient = new Ingredient($conn);
        $ingredient->setIngredient(["ingredient" => $ingredient_name]);
        $ingredient->insertIngredient();
        $dish->insertDishIngredient($ingredient->getIngredientId());
    }
}