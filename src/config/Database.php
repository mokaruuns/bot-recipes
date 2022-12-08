<?php

namespace Bot\config;

use PDO;

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
            echo "Connection error: " . $exception->getMessage();
            die();
        }
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

    public function fillDatabase(): void
    {
        $this->createDishesTable();
        $this->createIngredientsTable();
        $this->createDishIngredientTable();
        $this->fillDishesTable();
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

                insertFullDish($this->conn, $name, $url, $recipe, $images, $ingredients);
                $row++;
                if ($row % 500 == 0) {
                    echo "processed: " . $row . PHP_EOL;
                }
            }
            fclose($handle);
        }
    }
}