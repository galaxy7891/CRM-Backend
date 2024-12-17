<?php

namespace App\Http\Controllers;

use App\Helpers\ActionMapperHelper;
use App\Http\Resources\ApiResponseResource;
use App\Models\Article;
use App\Traits\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{
    
    use Filter;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Article::query();

            $query = $this->applyFiltersArticles($request, $query);
            $articles = $this->applyFilters($request, $query);
            $articles->getCollection()->transform(function ($article) {
                $article->status = ActionMapperHelper::mapStatusArticle($article->status);
                return $article;
            });

            if (!$articles) {
                return new ApiResponseResource(
                    false,
                    'Data article tidak ditemukan',
                    null
                );
            }

            return new ApiResponseResource(
                true,
                'Daftar Artikel',
                $articles
            );
        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string',
            'status' => 'required|in:Draf,Terbit',
            'photo_article' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'title.required' => 'Judul artikel tidak boleh kosong',
            'title.string' => 'Judul artikel harus berupa teks',
            'title.max' => 'Judul artikel maksimal 100 karakter',
            'description.required' => 'Isi deskripsi artikel tidak boleh kosong', 
            'description.string' => 'Isi deskripsi artikel harus berupa teks',
            'status.required' => 'Status artikel tidak boleh kosong',
            'status.in' => 'Status artikel harus pilih salah satu: Draf atau Terbit',
            'photo_article.required' => 'Foto artikel tidak boleh kosong.',
            'photo_article.image' => 'Foto artikel harus berupa gambar.',
            'photo_article.mimes' => 'Foto artikel tidak sesuai format.',
            'photo_article.max' => 'Foto artikel maksimal 2mb.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }

        $articleData = $request->all();
        if (isset($articleData['status'])) {
            $articleData['status'] = ActionMapperHelper::mapStatusArticleToDatabase($articleData['status']);
        }

        try {
            $article = Article::createArticle($articleData);

            return new ApiResponseResource(
                true,
                'Artikel berhasil dibuat',
                $article
            );

        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $articleId)
    {
        try {
            $article = Article::find($articleId);
            if (!$article) {
                return new ApiResponseResource(
                    false, 
                    'Data article tidak ditemukan.',
                    null
                );
            }

            $article->status = ActionMapperHelper::mapStatusArticle($article->status);
            
            return new ApiResponseResource(
                true,
                "Data article",
                $article
            );

        } catch (\Exception $e) {
            return new ApiResponseResource(
                false, 
                $e->getMessage(),
                null
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $articleId)
    {
        $article = Article::find($articleId);
        if (!$article) {
            return new ApiResponseResource(
                false, 
                'Data article tidak ditemukan.',
                null
            );
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:100',
            'description' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:Draf,Terbit',
            'photo_article' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'title.required' => 'Judul artikel tidak boleh kosong',
            'title.string' => 'Judul artikel harus berupa teks',
            'title.max' => 'Judul artikel maksimal 100 karakter',
            'description.required' => 'Isi deskripsi artikel tidak boleh kosong', 
            'description.string' => 'Isi deskripsi artikel harus berupa teks',
            'status.required' => 'Status artikel tidak boleh kosong',
            'status.in' => 'Status artikel harus pilih salah satu: Draf atau Terbit',
            'photo_article.required' => 'Foto artikel tidak boleh kosong.',
            'photo_article.image' => 'Foto artikel harus berupa gambar.',
            'photo_article.mimes' => 'Foto artikel tidak sesuai format.',
            'photo_article.max' => 'Foto artikel maksimal 2mb.',
        ]);
        if ($validator->fails()) {
            return new ApiResponseResource(
                false,
                $validator->errors(),
                null
            );
        }
        
        $articleData = $request->all();
        if (isset($articleData['status'])) {
            $articleData['status'] = ActionMapperHelper::mapStatusArticleToDatabase($articleData['status']);
        }

        try {
            $article = Article::updateArticle($articleData, $articleId);
            return new ApiResponseResource(
                true,
                "Data artikel berhasil diubah",
                $article
            );

        } catch (\Exception $e) {
            return new ApiResponseResource(
                false, 
                $e->getMessage(),
                null
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('id', []);
        if (empty($ids)) {
            return new ApiResponseResource(
                true,
                "Pilih data yang ingin dihapus terlebih dahulu",
                null
            );
        }
        
        try {
            $deletedCount = Article::whereIn('id', $ids)->delete();

            if ($deletedCount > 0) {
                return new ApiResponseResource(
                    true,
                    $deletedCount . ' data article berhasil dihapus',
                    null
                );
            }
            
            return new ApiResponseResource(
                false,
                'Data article tidak ditemukan',
                null
            );
                    
        } catch (\Exception $e) {
            return new ApiResponseResource(
                false,
                $e->getMessage(),
                null
            );
        }
    }
}
