<?php

namespace Bot\class;

use PDO;

class Person
{

    private int $id;
    private int $chat_position;
    private int $vk_id;

    private array $liked_dishes;

    private array $current_ingredients;


    private PDO $conn;

    private string $table_name = "persons";

    private string $liked_dishes_table_name = "liked_dishes";

    private string $current_ingredients_table_name = "current_ingredients";

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function get(): array
    {
        return array(
            'id' => $this->id,
            'chat_position' => $this->chat_position,
            'vk_id' => $this->vk_id
        );
    }

    public function set(array $person): void
    {
        $this->id = $person['id'];
        $this->chat_position = $person['chat_position'];
        $this->vk_id = $person['vk_id'];
    }


    public function changeChatPosition(int $chat_position): void
    {
        $this->chat_position = $chat_position;
        $this->update();
    }


    public function insertReal(): int
    {
        $query = "INSERT INTO " . $this->table_name . " (vk_id, chat_position) VALUES (:vk_id, :chat_position)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":vk_id", $this->vk_id);

        $stmt->bindParam(":chat_position", $this->chat_position);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }


    public function insert(): bool
    {
        $res = $this->getById();
        if ($res) {
            return $res['id'];
        } else {
            return $this->insertReal();
        }
    }


    public function getById(): array
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE vk_id = :vk_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":vk_id", $this->vk_id);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPersonId(): int
    {
        return $this->id;
    }

    public function setPersonVKid(int $vk_id): void
    {
        $this->vk_id = $vk_id;
    }

    public function addLikedDish(int $dish_id): void
    {
        $query = "INSERT INTO " . $this->liked_dishes_table_name . " (person_id, dish_id) VALUES (:person_id, :dish_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":person_id", $this->id);

        $stmt->bindParam(":dish_id", $dish_id);

        $stmt->execute();
    }

    public function getLikedDishesId(): array
    {
        $query = "SELECT * FROM " . $this->liked_dishes_table_name . " WHERE person_id = :person_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":person_id", $this->id);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCurrentIngredient(int $ingredient_id): void
    {
        $query = "INSERT INTO " . $this->current_ingredients_table_name . " (person_id, ingredient_id) VALUES (:person_id, :ingredient_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":person_id", $this->id);

        $stmt->bindParam(":ingredient_id", $ingredient_id);

        $stmt->execute();
    }

    public function getCurrentIngredients(): array
    {
        $query = "SELECT * FROM " . $this->current_ingredients_table_name . " WHERE person_id = :person_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":person_id", $this->id);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeCurrentIngredient(int $ingredient_id): void
    {
        $query = "DELETE FROM " . $this->current_ingredients_table_name . " WHERE person_id = :person_id AND ingredient_id = :ingredient_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":person_id", $this->id);

        $stmt->bindParam(":ingredient_id", $ingredient_id);

        $stmt->execute();
    }

    private function update(): void
    {
        $query = "UPDATE " . $this->table_name . " (chat_position) VALUES (:chat_position) WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":chat_position", $this->chat_position);

        $stmt->bindParam(":id", $this->id);

        $stmt->execute();

    }
}