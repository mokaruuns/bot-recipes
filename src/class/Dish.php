<?php

namespace Bot\class;

use PDO;

class Dish
{
    private int $id;
    private string $name;
    private string $url;
    private int $count_ingredients;

    private array $recipe;
    private array $images_url;

    private PDO $conn;

    private string $table_name = "dishes";
    private string $table_name_dish_ingredient = "dish_ingredient";

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function get(): array
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'count_ingredients' => $this->count_ingredients,
            'recipe' => $this->recipe,
            'images_url' => $this->images_url
        );
    }

    public function set(array $dish): void
    {
        $this->name = mb_strtolower($dish['name']);
        $this->url = $dish['url'];
        $this->count_ingredients = $dish['count_ingredients'];
        $this->recipe = $dish['recipe'];
        $this->images_url = $dish['images_url'];
    }

    private function insertReal(): int
    {
        $query = "INSERT INTO " . $this->table_name . " (name, url, count_ingredients, recipe, images_url) VALUES (:name, :url, :count_ingredients, :recipe, :images_url)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":url", $this->url);
        $stmt->bindParam(":count_ingredients", $this->count_ingredients);
        $recipe = json_encode($this->recipe);
        $images_url = json_encode($this->images_url);
        $stmt->bindParam(":recipe", $recipe);
        $stmt->bindParam(":images_url", $images_url);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return $this->id;
        }
        return 0;
    }

    public function insert(): int
    {
        $res = $this->getByName();
        if ($res) {
            $this->id = $res['id'];
        } else {
            $this->id = $this->insertReal();
        }
        return $this->id;
    }


    public function getRandom(): array
    {

        $query = "SELECT * FROM $this->table_name TABLESAMPLE system_rows(1);";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $result['recipe'] = json_decode($result['recipe']);
        $result['images_url'] = json_decode($result['images_url']);
        return $result;
    }

    private function getByName()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE name = :name";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $result['recipe'] = json_decode($result['recipe']);
            $result['images_url'] = json_decode($result['images_url']);
        }
        return $result;
    }

    public function insertDishIngredient(int $ingredient_id): bool
    {
        $query = "INSERT INTO " . $this->table_name_dish_ingredient . " (dish_id, ingredient_id) VALUES (:dish_id, :ingredient_id)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":dish_id", $this->id);
        $stmt->bindParam(":ingredient_id", $ingredient_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


    public function getById(int $id): array
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $result['recipe'] = json_decode($result['recipe']);
        $result['images_url'] = json_decode($result['images_url']);
        return $result;
    }

    public function getIngredientsNames($id): array
    {
        $query = "SELECT i.ingredient FROM " . $this->table_name_dish_ingredient . " di INNER JOIN ingredients i ON di.ingredient_id = i.id WHERE di.dish_id = :dish_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dish_id", $id);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $names = array();
        foreach ($result as $item) {
            $names[] = $item['ingredient'];
        }
        return $names;
    }
}