<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait Filter
{
    public function applyFiltersDeals(Request $request, $query)
    {
        $stage = $request->input('tahapan');
        if ($stage) {
            switch ($stage) {
                case 'kualifikasi':
                    $query->where('stage', 'qualificated');
                    break;
                case 'proposal':
                    $query->where('stage', 'proposal');
                    break;
                case 'negosiasi':
                    $query->where('stage', 'negotiate');
                    break;
                case 'gagal':
                    $query->where('stage', 'lose');
                    break;
                case 'tercapai':
                    $query->where('stage', 'won');
                    break;
            }
        }
        
        return $query;
    }
    
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

        if ($perPage === 'semua') {
            $data = $query->get();

            return new LengthAwarePaginator(
                $data, 
                $data->count(),
                $data->count(),
                1,
                [
                    'path' => $request->url(), 
                    'query' => $request->query()
                ] 
            );
        }

        return $query->paginate($perPage);
    }


}