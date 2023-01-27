<?php

namespace app\models;

use app\Database;
use app\helpers\TimeHelper;
use ReflectionClass;

abstract class AbstractModel
{
    public static Database $db;

    public function __construct()
    {
        self::$db = new Database();
    }

    public static function findById(int $id): ?AbstractModel
    {
        $reflect = new ReflectionClass(static::class);

        $method = 'get' . $reflect->getShortName() . 'ById';
        $data = (new Database())->$method($id);

        if (!$data) {
            return null;
        }

        return (new static())->loadByDbData($data);
    }

    public function save(): void
    {
        $model = $this;
        $reflect = new ReflectionClass($this);

        if (!$model->id) {
            $method = 'create' . $reflect->getShortName();
            self::$db->$method($model);
            $model->id = self::$db->pdo->lastInsertId();

            return;
        }

        $model->updatedAt = TimeHelper::now();
        $method = 'update' . $reflect->getShortName();
        self::$db->$method($model);
    }
}