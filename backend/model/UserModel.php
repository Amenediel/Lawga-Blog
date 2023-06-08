<?php
class UserModel
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM user";
        $stmt = $this->conn->query($sql);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO user(firstname,lastname,email,password,username)
                VALUES(:firstname,:lastname,:email,:password,:username)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":firstname", $data["firstname"], PDO::PARAM_STR);
        $stmt->bindValue(":lastname", $data["lastname"], PDO::PARAM_STR);
        $stmt->bindValue(":username", $data["username"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $data["password"], PDO::PARAM_STR);
        $stmt->bindValue(":email", $data["email"], PDO::PARAM_STR);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function get(string $id): array | false
    {
        $sql = "SELECT * 
                FROM user
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data;
    }

    public function update(array $current, array $new): int
    {
        $sql = "UPDATE user 
                SET firstname = :firstname, 
                lastname= :lastname,
                email = :email,
                password = :password,
                username = :username
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":firstname", $new["firstname"] ?? $current["firstname"], PDO::PARAM_STR);
        $stmt->bindValue(":lastname", $new["lastname"] ?? $current["lastname"], PDO::PARAM_STR);
        $stmt->bindValue(":email", $new["email"] ?? $current["email"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $new["password"] ?? $current["password"], PDO::PARAM_STR);
        $stmt->bindValue(":username", $new["username"] ?? $current["username"], PDO::PARAM_STR);

        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id): int
    {
        $sql = "DELETE FROM user
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}
