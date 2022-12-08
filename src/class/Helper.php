<?php

namespace Bot\class;

use PDO;

class Helper
{

    private string $table_name = "dishes";
    private string $table_name_dish_ingredient = "dish_ingredient";

    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getDishByIngredientsIdInclude(array $ingredients): array
    {
        $query = "SELECT dish_id FROM " . $this->table_name_dish_ingredient . " WHERE ingredient_id IN (" . implode(',', $ingredients) . ") GROUP BY dish_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getDishByIngredientsIdExclude(array $ingredients): array
    {
        $query = "SELECT dish_id FROM " . $this->table_name_dish_ingredient . " WHERE ingredient_id NOT IN (" . implode(',', $ingredients) . ") GROUP BY dish_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getDishByIngredientsId(array $ingredients): array
    {
        $include = $this->getDishByIngredientsIdInclude($ingredients);
        $exclude = $this->getDishByIngredientsIdExclude($ingredients);
        return array_diff($include, $exclude);
    }


    function getManySimilarIngredients($conn, array $ingredients): array
    {
        $ingredient = new Ingredient($conn);
        $res = [];
        foreach ($ingredients as $ingredientName) {
            $ingredient->set(["ingredient" => $ingredientName]);
            $res = array_merge($res, $ingredient->getSimilarIngredientsId());
        }
        return $res;
    }

}