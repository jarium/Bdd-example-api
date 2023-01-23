<?php

namespace app;

use app\models\ApiToken;
use app\models\Posts;
use PDO;

class Database
{
    public static Database $db;

    private $host = DBHOST;
    private $port = DBPORT;
    private $dbname = DBNAME;
    private $username = DBUSER;
    private $password = DBPASS;

    public function __construct()
    {
        $this->pdo = new PDO("mysql:host=$this->host;port=$this->port;dbname=$this->dbname", $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        self::$db = $this;
    }

    public function rawQuery(string $query): void
    {
        $this->pdo->query($query);
    }

    public function createPosts(Posts $posts): void
    {
        $statement = $this->pdo->prepare("INSERT INTO posts (admin_id, title, content, category, status, created_at)
        VALUES (:admin_id, :title, :content, :category, :status, :created_at)");

        $statement->bindValue(':admin_id', $posts->adminId);
        $statement->bindValue(':title', $posts->title);
        $statement->bindValue(':content', $posts->content);
        $statement->bindValue(':category', $posts->category);
        $statement->bindValue(':status', $posts->status);
        $statement->bindValue(':created_at', $posts->createdAt);
        $statement->execute();
    }

    public function updatePosts(Posts $posts)
    {
        $statement = $this->pdo->prepare("UPDATE posts SET title = :title, content = :content, category = :category, status = :status, updated_at = :updated_at WHERE id = :id");

        $statement->bindValue(':id', $posts->id);
        $statement->bindValue(':title', $posts->title);
        $statement->bindValue(':content', $posts->content);
        $statement->bindValue(':category', $posts->category);
        $statement->bindValue(':status', $posts->status);
        $statement->bindValue(':updated_at', $posts->updatedAt);
        $statement->execute();
    }

    public function getPostsById(int $id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM posts WHERE id = :id");
        $statement->bindValue(':id', $id);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function updateApiToken(ApiToken $apiToken)
    {
        $statement = $this->pdo->prepare("UPDATE api_token SET status = :status, updated_at = :updated_at WHERE id = :id");

        $statement->bindValue(':id', $apiToken->id);
        $statement->bindValue(':status', $apiToken->status);
        $statement->bindValue(':updated_at', $apiToken->updatedAt);
        $statement->execute();
    }

    public function getApiTokenById(int $id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM api_token WHERE id = :id");
        $statement->bindValue(':id', $id);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getApiTokenByToken(string $token)
    {
        $statement = $this->pdo->prepare("SELECT * FROM api_token WHERE token = :token");
        $statement->bindValue(':token', $token);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }
}