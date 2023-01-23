<?php

namespace app\models;

use app\Database;

class ApiToken extends AbstractModel implements ModelInterface
{
    public const STATUS_PASSIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_EXPIRED = 2;

    public ?int $id;
    public int $adminId;
    public string $token;
    public int $status;
    public string $createdAt;
    public ?string $expiredAt;
    public ?string $updatedAt;

    public function loadByDbData(array $data): static
    {
        $this->id = $data['id'];
        $this->adminId = $data['admin_id'];
        $this->token = $data['token'];
        $this->status = $data['status'];
        $this->createdAt = $data['created_at'];
        $this->expiredAt = $data['expired_at'];
        $this->updatedAt = $data['updated_at'];

        return $this;
    }

    public static function findByToken(string $token): ?ApiToken
    {
        $data = (new Database())->getApiTokenByToken($token);

        if (!$data) {
            return null;
        }

        return (new static())->loadByDbData($data);
    }
}