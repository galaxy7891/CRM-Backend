<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait Filter
{
    public function applyFiltersDeals(Request $request, $query)
    {                                                                    
        $category = $request->input('kategori');
        if ($category) {
            switch ($category) {
                case 'pelanggan':
                    $query->where('category', 'customers');
                    break;
                case 'perusahaan':
                    $query->where('category', 'customers_companies');
                    break;
            }
        }
                              
        return $query;
    }

    public function applyFiltersArticles(Request $request, $query)
    {                                                                    
        $status = $request->input('status');
        if ($status) {
            switch ($status) {
                case 'terbit':
                    $query->where('status', 'post');
                    break;
                case 'draf':
                    $query->where('status', 'draft');
                    break;
            }
        }
                              
        return $query;
    }
    
    public function applyFiltersAccountsType(Request $request, $query)
    {                                                                    
        $type = $request->input('tipe');
        if ($type) {
            switch ($type) {
                case 'percobaan':
                    $query->where('account_type', 'trial');
                    break;
                case 'reguler':
                    $query->where('account_type', 'regular');
                    break;
                case 'profesional':
                    $query->where('account_type', 'professional');
                    break;
                case 'bisnis':
                    $query->where('account_type', 'business');
                    break;
                case 'tidak aktif':
                    $query->where('account_type', 'unactive');
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