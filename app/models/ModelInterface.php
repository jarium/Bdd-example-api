<?php

namespace app\models;

interface ModelInterface
{
    public function loadByDbData(array $data): static;
}