<?php

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
        $this->recipe = json_decode($dish['recipe']);
        $this->images_url = json_decode($dish['images_url']);
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
            return $this->conn->lastInsertId();
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

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getByName()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE name = :name";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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


    public function getDishById(mixed $recipe_id): array
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $recipe_id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}