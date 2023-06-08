<?php

class UserController
{
    public function __construct(private UserModel $gateway)
    {
    }

    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $user = $this->gateway->get($id);

        if (!$user) {
            http_response_code(404);
            echo json_encode(["message" => "user not found"]);
        }

        switch ($method) {
            case "GET":
                echo json_encode($user);
                break;
            case "PATCH":
                $data = (array)json_decode(file_get_contents("php://input"), true);
                $errors = $this->getValidationErrors($data, false);

                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $rows = $this->gateway->update($user, $data);

                echo json_encode([
                    "message" => "user $id updated",
                    "rows affected" => $rows
                ]);
                break;
            case "DELETE":
                $rows = $this->gateway->delete($id);

                echo json_encode([
                    "message" => "user $id deleted",
                    "rows affected" => $rows
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
                break;
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->gateway->getAll());
                break;
            case "POST":
                $data = (array)json_decode(file_get_contents("php://input"), true);
                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $id = $this->gateway->create($data);

                echo json_encode([
                    "message" => "user created",
                    "id" => $id
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }

    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];

        if ($is_new && empty($data["firstname"])) {
            $errors[] = "firstname is required";
        }
        if ($is_new && empty($data["lastname"])) {
            $errors[] = "lastname is required";
        }
        if ($is_new && empty($data["email"])) {
            $errors[] = "email is required";
        }
        if ($is_new && empty($data["password"])) {
            $errors[] = "password is required";
        }
        if ($is_new && empty($data["username"])) {
            $errors[] = "username is required";
        }

        return $errors;
    }
}
