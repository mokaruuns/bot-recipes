<?php

class Ingredient
{
    private int $id;
    private string $ingredient;

    private array $similar_ingredients;

    private PDO $conn;

    private string $table_name = "ingredients";

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function get(): array
    {
        return array(
            'ingredient' => $this->ingredient
        );
    }

    public function set(array $ingredient): void
    {
        $this->ingredient = mb_strtolower($ingredient['ingredient']);
    }

    private function insertReal(): int
    {
        $query = "INSERT INTO " . $this->table_name . " (ingredient) VALUES (:ingredient)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":ingredient", $this->ingredient);

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

    public function getByName()
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

    public function getSimilarIngredientsId(): array
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE ingredient LIKE :ingredient";

        $stmt = $this->conn->prepare($query);

        $ingredient = "%" . $this->ingredient . "%";
        $stmt->bindParam(":ingredient", $ingredient);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

}