<?php

require_once realpath(dirname(__FILE__)) . '/../src/class/Dish.php';
require_once realpath(dirname(__FILE__)) . '/../src/class/Ingredient.php';
require_once realpath(dirname(__FILE__)) . '/../src/config/Database.php';
require_once realpath(dirname(__FILE__)) . '/../src/action/predict.php';


function testGetRandomDish(): void
{
    $start = microtime(true);
    echo 'testGetRandomDish - ';
    $res = getRandomDish();
    echo $res['name'] . PHP_EOL;
    echo microtime(true) - $start . PHP_EOL;
}


testGetRandomDish();


function testGetSimilarIngredients(): void
{
    $start = microtime(true);
    echo 'testGetSimilarIngredients - ';
    $db = new Database();
    $conn = $db->getConnection();
    $ingredient = new Ingredient($conn);
    $ingredient->setIngredient(["ingredient" => "лук"]);
    $res = $ingredient->getSimilarIngredientsId();
//    foreach ($res as $ingredient) {
//        echo $ingredient['ingredient'] . PHP_EOL;
//    }
    echo microtime(true) - $start . PHP_EOL;
}

testGetSimilarIngredients();


function testGiveDishByIngredients(): void
{
    $start = microtime(true);
    echo 'testGiveRecipeByIngredientsId - ';
    $db = new Database();
    $conn = $db->getConnection();

    $ingredients = ['мука', 'масло', 'соль','перец', 'молоко', 'яйцо', 'сахар'];
    $ingredients = getManySimilarIngredients($ingredients);
    echo json_encode($ingredients) . PHP_EOL;
    $dish = new Dish($conn);
    $res = $dish->getDishByIngredientsId($ingredients);

    foreach ($res as $recipe_id) {
        $dish->getDishById($recipe_id);
        echo $dish->getDish()['name'] . PHP_EOL;
    }
    echo microtime(true) - $start . PHP_EOL;
}


testGiveDishByIngredients();

