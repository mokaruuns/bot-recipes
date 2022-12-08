<?php

namespace Bot\class;
use PDO;

class Person
{

    private int $id;
    private int $chat_position;
    private int $vk_id;


    private PDO $conn;

    private string $table_name = "persons";

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getPerson(): array
    {
        return array(
            'id' => $this->id,
            'chat_position' => $this->chat_position,
            'vk_id' => $this->vk_id
        );
    }

    public function setPerson(array $person): void
    {
        $this->id = $person['id'];
        $this->chat_position = $person['chat_position'];
        $this->vk_id = $person['vk_id'];
    }


    public function insertReal(): int
    {
        $query = "INSERT INTO " . $this->table_name . " SET vk_id=:vk_id, chat_position=:chat_position";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":vk_id", $this->vk_id);
        $stmt->bindParam(":chat_position", $this->chat_position);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return 0;
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
}