<?php

require_once realpath(dirname(__FILE__)) . '/../src/class/Dish.php';
require_once realpath(dirname(__FILE__)) . '/../src/class/Recipe.php';
require_once realpath(dirname(__FILE__)) . '/../src/class/Ingredient.php';
require_once realpath(dirname(__FILE__)) . '/../src/config/Database.php';


function testGetRandomDish(): void
{
    echo 'testGetRandomDish - ';
    $conn = new Database();
    $conn = $conn->getConnection();
    $dish = new Dish($conn);
    $dish->getRandomDish();
    echo $dish->getDish()['name'] . PHP_EOL;
}

//testGetRandomDish();


function testInsertRecipesJson(): void
{
    echo 'testInsertRecipesTableJson - ';
    $db = new Database();
    $conn = $db->getConnection();
    $recipe = new Recipe($conn);
    $recipe->setRecipe(["recipe" => ["test1", "test2", "test3"], "images_url" => ["test1.ing", "test2.ing", "test3.ing"]]);
    $recipe->insertRecipe();

}

$db = new Database();
$db->fillDatabase();

