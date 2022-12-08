<?php

namespace Bot\class;

require_once realpath(dirname(__FILE__)) . '/Dish.php';
require_once realpath(dirname(__FILE__)) . '/Ingredient.php';

use PDO;
use PDOException;

class Database
{

    private PDO $conn;
    private string $host = "localhost";
    private string $port = "5432";
    private string $db_name = "bot";
    private string $username = "postgres";
    private string $password = "marsel974";


    function __construct()
    {
        try {
            $this->conn = new PDO("pgsql:host=$this->host;port=$this->port;dbname=$this->db_name;user=$this->username;password=$this->password");
            return $this->conn;
        } catch (PDOException $exception) {
//            echo "Connection error: " . $exception->getMessage();
            die();
        }
    }

    function dropTables(): void
    {
        $query = "DROP TABLE IF EXISTS dishes, dish_ingredient, ingredients, users, user_dish, user_ingredient, user_dish_ingredient";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

    }


    public function getConnection(): ?PDO
    {
        return $this->conn;
    }


    private function createDishesTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS dishes (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            url VARCHAR(255),
            count_ingredients SMALLINT NOT NULL,
            recipe json NOT NULL,
            images_url json 
      )";
        $this->conn->exec($sql);
    }

    private function createIngredientsTable(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS ingredients (
            id SERIAL PRIMARY KEY,
            ingredient VARCHAR(255) NOT NULL
        )";
        $this->conn->exec($query);
    }


    private function createDishIngredientTable(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS dish_ingredient (
            id SERIAL PRIMARY KEY,
            dish_id int NOT NULL,
            ingredient_id int NOT NULL
        )";
        $this->conn->exec($query);
    }

    private function createPersonsTable(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS persons (
            id SERIAL PRIMARY KEY,
            vk_id int NOT NULL,
            chat_position int NOT NULL
        )";
        $this->conn->exec($query);
    }

    private function createLikedDishesTable(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS liked_dishes (
            id SERIAL PRIMARY KEY,
            person_id int NOT NULL,
            dish_id int NOT NULL
        )";
        $this->conn->exec($query);
    }

    private function createCurrentIngredientsTable(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS current_ingredients (
            id SERIAL PRIMARY KEY,
            person_id int NOT NULL,
            ingredient_id int NOT NULL
        )";
        $this->conn->exec($query);
    }

    private function addExtension(): void
    {
        $query = "CREATE EXTENSION IF NOT EXISTS tsm_system_rows";
        $this->conn->exec($query);
    }

    public function fillDatabase(): void
    {
        $this->addExtension();
        $this->createDishesTable();
        $this->createIngredientsTable();
        $this->createDishIngredientTable();
        $this->createPersonsTable();
        $this->createLikedDishesTable();
        $this->createCurrentIngredientsTable();
        $this->fillDishesTable();
    }

    public function insertFullDish($name, $url, $recipes, $images, $ingredients): void
    {
        $dish = new Dish($this->conn);
        $dish->set(["name" => $name, "url" => $url, "count_ingredients" => count($ingredients), "recipe" => $recipes, "images_url" => $images]);
        $dish->insert();
        foreach ($ingredients as $ingredient_name) {
            $ingredient = new Ingredient($this->conn);
            $ingredient->set(["ingredient" => $ingredient_name]);
            $ingredient->insert();
            $dish->insertDishIngredient($ingredient->getIngredientId());
        }
    }

    private function fillDishesTable(): void
    {
        $row = 1;

        if (($handle = fopen("recipes.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 20000, "~")) !== FALSE) {
                $name = $data[1];
                $url = $data[2];

                $ingredients = explode("', '", substr($data[3], 2, -2));

                $images = explode("', '", substr($data[4], 2, -2));

                $recipe = explode("', '", substr($data[5], 2, -2));

                $this->insertFullDish($name, $url, $recipe, $images, $ingredients);
                $row++;
                if ($row % 500 == 0) {
                    echo "processed: " . $row . PHP_EOL;
                }
            }
            fclose($handle);
        }
    }
}

