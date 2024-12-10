<?php

namespace App\Helpers;

class StringHelper
{
    public static function splitName(string $fullName): array
    {
        $nameParts = explode(' ', $fullName);
        $firstName = implode(' ', array_slice($nameParts, 0, -1));
        $lastName = end($nameParts);
        
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
        ];
    }
}
