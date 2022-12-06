<?php

class Ingredient
{
    private int $id;
    private string $ingredient;

    private PDO $conn;

    private string $table_name = "ingredients";

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getIngredient(): array
    {
        return array(
            'ingredient' => $this->ingredient
        );
    }

    public function setIngredient(array $ingredient): void
    {
        $this->ingredient = mb_strtolower($ingredient['ingredient']);
    }

    private function insertIngredientReal(): int
    {
        $query = "INSERT INTO " . $this->table_name . " (ingredient) VALUES (:ingredient)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ingredient", $this->ingredient);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return 0;
    }

    public function insertIngredient(): bool
    {
        $res = $this->getIngredientByName();
        if ($res) {
            $this->id = $res['id'];
        } else {
            $this->id = $this->insertIngredientReal();
        }
        return $this->id;
    }

    public function getIngredientByName()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ingredient = :ingredient";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ingredient", $this->ingredient);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getIngredientId(): int
    {
        return $this->id;
    }

}