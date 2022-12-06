<?php

require_once realpath(dirname(__FILE__)) . '/../class/Dish.php';
require_once realpath(dirname(__FILE__)) . '/../class/Recipe.php';
require_once realpath(dirname(__FILE__)) . '/../class/Ingredient.php';

function insertFullDish($conn, $name, $url, $recipes, $images, $ingredients): void
{
    $dish = new Dish($conn);
    $dish->setDish(["name" => $name, "url" => $url, "count_ingredients" => count($ingredients)]);
    $dish->insertDish();

    $recipe = new Recipe($conn);
    $recipe->setRecipe(["recipe" => $recipes, "images_url" => $images]);
    $recipe->insertRecipe();

    $dish->insertDishRecipe($recipe->getRecipeId());

    foreach ($ingredients as $ingredient_name) {
        $ingredient = new Ingredient($conn);
        $ingredient->setIngredient(["ingredient" => $ingredient_name]);
        $ingredient->insertIngredient();
        $recipe->insertRecipeIngredient($ingredient->getIngredientId());
    }
}