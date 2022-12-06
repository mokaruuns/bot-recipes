<?php

class Recipe
{

    private int $id;
    private array $recipe;
    private array $images_url;

    private $conn;

    private $table_name = "recipes";
    private $table_name_ricipe_ingredient = "recipe_ingredient";


    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getRecipe(): array
    {
        return array(
            'recipe' => $this->recipe,
            'images_url' => $this->images_url
        );
    }

    public function setRecipe(array $recipe): void
    {
        $this->recipe = $recipe['recipe'];
        $this->images_url = $recipe['images_url'];
    }


    public function insertRecipe(): int
    {
        $query = "INSERT INTO " . $this->table_name . " (recipe, images) VALUES (:recipe, :images)";
        $recipe = json_encode($this->recipe);
        $images = json_encode($this->images_url);
        $stmt = $this->conn->prepare($query);
        if ($stmt->execute(array(':recipe' => $recipe, ':images' => $images))) {
            $this->id = $this->conn->lastInsertId();
        }
        return $this->id;
    }

    public function insertRecipeIngredient(int $id): bool
    {
        $query = "INSERT INTO " . $this->table_name_ricipe_ingredient . " (recipe_id, ingredient_id) VALUES (:recipe_id, :ingredient_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":recipe_id", $this->id);
        $stmt->bindParam(":ingredient_id", $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getRecipeId(): int
    {
        return $this->id;
    }


}