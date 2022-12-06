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

    public function getDish(): array
    {
        return array(
            'name' => $this->name,
            'url' => $this->url,
            'count_ingredients' => $this->count_ingredients,
            'recipe' => $this->recipe,
            'images_url' => $this->images_url
        );
    }

    public function setDish(array $dish): void
    {
        $this->name = mb_strtolower($dish['name']);
        $this->url = $dish['url'];
        $this->count_ingredients = $dish['count_ingredients'];
        $this->recipe = json_decode($dish['recipe']);
        $this->images_url = json_decode($dish['images_url']);
    }


    private function insertDishReal(): int
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

        $this->id = $res['id'];
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

    public function insertDishIngredient(int $id): bool
    {
        $query = "INSERT INTO " . $this->table_name_dish_ingredient . " (dish_id, ingredient_id) VALUES (:dish_id, :ingredient_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":dish_id", $this->id);
        $stmt->bindParam(":ingredient_id", $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getDishByIngredientsIdInclude(array $ingredients): array
    {
        $query = "SELECT dish_id FROM " . $this->table_name_dish_ingredient . " WHERE ingredient_id IN (" . implode(',', $ingredients) . ") GROUP BY dish_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getDishByIngredientsIdExclude(array $ingredients): array
    {
        $query = "SELECT dish_id FROM " . $this->table_name_dish_ingredient . " WHERE ingredient_id NOT IN (" . implode(',', $ingredients) . ") GROUP BY dish_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getDishByIngredientsId(array $ingredients): array
    {
        $include = $this->getDishByIngredientsIdInclude($ingredients);
        $exclude = $this->getDishByIngredientsIdExclude($ingredients);
        return array_diff($include, $exclude);
    }

    public function getDishById(mixed $recipe_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $recipe_id);

        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->setDish($res);
        return $res;
    }
}