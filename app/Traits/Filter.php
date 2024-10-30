<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait Filter
{
    public function applyFilters(Request $request, $query)
    {
        $sort = $request->input('sort', 'terbaru');
        $status = $request->input('status');

        if ($status) {
            switch ($status) {
                case 'rendah':
                    $query->where('status', 'cold');
                    break;
                case 'sedang':
                    $query->where('status', 'warm');
                    break;
                case 'tinggi':
                    $query->where('status', 'hot');
                    break;
            }
        }

        if ($sort === 'terlama') {
            $query->orderBy('updated_at', 'asc');
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $perPage = $request->input('per_page', 10);

        return $query->paginate($perPage);
    }

}
