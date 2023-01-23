<?php

namespace app\models;

use app\helpers\TimeHelper;

class Posts extends AbstractModel implements ModelInterface
{
    public const STATUS_PASSIVE = 0;
    public const STATUS_ACTIVE = 1;

    public ?int $id;
    public int $adminId;
    public string $title;
    public string $content;
    public string $category;
    public int $status;
    public string $createdAt;
    public ?string $updatedAt;

    public function loadByPostData(array $data): static
    {
        $this->id = null;
        $this->title = $data['title'];
        $this->content = $data['content'];
        $this->category = $data['category'];
        $this->status = self::STATUS_ACTIVE;
        $this->createdAt = TimeHelper::now();
        $this->updatedAt = null;

        return $this;
    }

    public function loadByDbData(array $data): static
    {
        $this->id = $data['id'];
        $this->adminId = $data['admin_id'];
        $this->title = $data['title'];
        $this->content = $data['content'];
        $this->category = $data['category'];
        $this->status = $data['status'];
        $this->createdAt = $data['created_at'];
        $this->updatedAt = $data['updated_at'];

        return $this;
    }
}