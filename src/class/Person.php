<?php

class Person
{

    private int $id;
    private string $last_message;
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
            'last_message' => $this->last_message,
            'vk_id' => $this->vk_id
        );
    }

    public function setPerson(array $person): void
    {
        $this->id = $person['id'];
        $this->last_message = $person['last_message'];
        $this->vk_id = $person['vk_id'];
    }


    public function createPersonReal(): bool
    {
        $query = "INSERT INTO " . $this->table_name . " SET vk_id=:vk_id, last_message=:last_message";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":vk_id", $this->vk_id);
        $stmt->bindParam(":last_message", $this->last_message);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }


    public function createPerson(): bool
    {
        $res = $this->readPerson();
        if ($res) {
            return $res['id'];
        } else {
            return $this->createPersonReal();
        }
    }


    public function readPerson(): array
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