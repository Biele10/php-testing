<?php

class ProductGateway
{

    private PDO $conn;

    public function __construct(Database $database)
    {

    $this->conn = $database->getConnection();

    }


    public function getAll(): array
    {

        $sql = "SELECT * FROM product";

        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {

            $row["is_available"] = (bool) $row["is_available"];

            $data[] = $row;


        }

        return $data;
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO product (name, size, is_available) VALUES (:name, :size, :is_available)";

        $stmt = $this->conn->prepare($sql);     // prepare is used to prevent sql injection by telling the database that data entered onwards is DATA, not SQL code

        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);      // assigns the name value as a string

        $stmt->bindValue(":size", $data["size"], PDO::PARAM_INT);

        $stmt->bindValue(":is_available", (bool) ($data["is_available"] ?? false), PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();     // returns a string
    }

    public function verifyAccountExists(int $id): array | false
    {
        $sql = "SELECT * FROM product WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row)
        {
            return $row;
        }

        else
        {
            return false;
        }
    }


    public function deleteItem(int $id): bool
    {

        $accountValidity = $this->verifyAccountExists($id);

        if (!$accountValidity)
        {
            return false;
        }

        $sql = "DELETE FROM product WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return true;


    }

    public function updateItem(array $data): bool
    {
        $accountValidity = $this->verifyAccountExists($data['id']);

        if (! $accountValidity)
        {
            return false;
        }


        $sql = "UPDATE product SET name = :newName, size = :newSize, is_available = :newAvailability WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":newName", $data["name"], PDO::PARAM_STR);

        $stmt->bindValue(":newSize", $data["size"], PDO::PARAM_INT);

        $stmt->bindValue(":newAvailability", $data['is_available'], PDO::PARAM_BOOL);

        $stmt->bindValue(":id", $data["id"], PDO::PARAM_INT);

        $stmt->execute();

        return true;


    }

    public function verifyToken(string $token): ?bool 
    {

        echo("we are here");

        $sql = "SELECT valid FROM token WHERE token = :token";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":token", $token, PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false)
        {
            echo("\nthis");
            return null;
        }
        else
        {
            if ($row['valid'] == true)
            {
                echo("THIS WOKSODKSAODKSD");
                return true;
            }
            else if ($row['valid'] == false)
            {
                return false;
            }
            else
            {
                return null;
            }
        }



    }

    public function processAdminRequest(RequestTypes $method, UserTypes $userType)
    {

        if ($userType !== UserTypes::Admin)
        {
            http_response_code(403);
            exit;
        }

        $sql = "SELECT * FROM private_products";

        


    }



}