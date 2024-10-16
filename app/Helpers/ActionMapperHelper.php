<?php

namespace App\Helpers;

class ActionMapperHelper
{
    public static function mapActionTitle(string $action): string
    {
        $actionMapping = [
            'CREATE' => 'Tambah',
            'UPDATE' => 'Perbarui',
            'DELETE' => 'Hapus',
        ];
    
        return $actionMapping[$action] ?? $action;
    }
    
    public static function mapActionDesc(string $action): string
    {
        $actionMapping = [
            'CREATE' => 'menambahkan',
            'UPDATE' => 'memperbarui',
            'DELETE' => 'menghapus',
        ];

        return $actionMapping[$action] ?? $action;
    }

}
