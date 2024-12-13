<?php

namespace App\Models;

use App\Traits\HasUuid;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cviebrock\EloquentSluggable\Sluggable;

class Article extends Model
{
    use SoftDeletes, HasUuid, Sluggable;

    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'id',
        'title',
        'slug',
        'status',
        'description',
        'post_date',
        'image_url',
        'image_public_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Upload photo article of the articles.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return array
     */
    public function uploadPhoto($photo)
    {
        $cloudinary = new Cloudinary();
        if ($this->image_public_id) {
            $cloudinary->uploadApi()->destroy($this->image_public_id);
        }

        $result = $cloudinary->uploadApi()->upload($photo->getRealPath(), [
            'folder' => 'articles',
        ]);

        return [
            'image_url' => $result['secure_url'],
            'image_public_id' => $result['public_id'],
        ];
    }

    /**
     * The attributes that should be cast to date instances.
     * 
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public static function createArticle(array $dataArticle): self
    {
        $postDate = $dataArticle['status'] === 'draft' ? null : now();

        $articleData = new Article();
        $articleData->title = $dataArticle['title'];
        $articleData->status = $dataArticle['status'];
        $articleData->description = $dataArticle['description'];
        $articleData->post_date = $postDate;

        if (isset($dataArticle['photo'])) {
            $uploadResult = (new self())->uploadPhoto($dataArticle['photo']);
            $articleData->image_url = $uploadResult['image_url'];
            $articleData->image_public_id = $uploadResult['image_public_id'];
        }
        
        $articleData->save();
        return $articleData;
    }

    public static function updateArticle(array $dataArticle, string $articleId): self
    {
        $article = self::findOrFail($articleId);

        if ($dataArticle['status'] === 'draft') {
            $postDate = null;
        } elseif ($article->status === 'draft' && $dataArticle['status'] === 'post') {
            $postDate = now();
        } else {
            if ($article->post_date) {
                $postDate = $article->post_date;
            } else {
                $postDate = now();
            }
        }

        
        $articleData = [
            'title' => $dataArticle['title'] ?? $article->title,
            'status' => $dataArticle['status'] ?? $article->status,
            'description' => $dataArticle['description'] ?? $article->description,
            'post_date' => $postDate,
        ];

        if (isset($dataArticle['photo'])) {
            if ($article->image_public_id) {
                $article->deletePhoto(); 
            }

            $uploadResult = $article->uploadPhoto($dataArticle['photo']);
            $articleData['image_url'] = $uploadResult['image_url'];
            $articleData['image_public_id'] = $uploadResult['image_public_id'];
        }

        $article->update($articleData);

        return $article;
    }
}
