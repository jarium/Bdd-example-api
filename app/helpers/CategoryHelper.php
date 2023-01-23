<?php

namespace app\helpers;

class CategoryHelper
{
    private const AVAILABLE_CATEGORIES = ['Science', 'Health', 'Politicial', 'Technology', 'World', 'Economy', 'Sports', 'Art', 'Education', 'Social'];

    public static function getAvailableCategories(): array
    {
        return self::AVAILABLE_CATEGORIES;
    }
}