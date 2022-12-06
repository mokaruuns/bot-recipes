<?php

class Dish
{
    private int $id;
    private string $name;
    private string $url;
    private int $count_ingredients;

    private PDO $conn;

    private string $table_name = "dishes";
    private string $table_name_dish_recipe = "dish_recipe";

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getDish(): array
    {
        return array(
            'name' => $this->name,
            'url' => $this->url,
            'count_ingredients' => $this->count_ingredients
        );
    }

    public function setDish(array $dish): void
    {
        $this->name = mb_strtolower($dish['name']);
        $this->url = $dish['url'];
        $this->count_ingredients = $dish['count_ingredients'];
    }


    private function insertDishReal(): int
    {
        $query = "INSERT INTO " . $this->table_name . " (name, url, count_ingredients) VALUES (:name, :url, :count_ingredients)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":url", $this->url);
        $stmt->bindParam(":count_ingredients", $this->count_ingredients);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return 0;
    }

    public function insertDish(): bool
    {
        $res = $this->getDishByName();
        if ($res) {
            $this->id = $res['id'];
        } else {
            $this->id = $this->insertDishReal();
        }
        return $this->id;
    }


    public function getRandomDish(): void
    {

        $query = "SELECT * FROM $this->table_name TABLESAMPLE system_rows(1);";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->setDish($res);
    }

    private function getDishByName()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE name = :name";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);

        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res;
    }

    public function insertDishRecipe(int $id): bool
    {
        $query = "INSERT INTO " . $this->table_name_dish_recipe . " (dish_id, recipe_id) VALUES (:dish_id, :recipe_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dish_id", $this->id);
        $stmt->bindParam(":recipe_id", $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getDishId(): int
    {
        return $this->id;
    }
}