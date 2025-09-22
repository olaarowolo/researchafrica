<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleKeyword extends Model
{
    use HasFactory;

    public $table = 'article_keywords';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'title',
        'status',
        'created_at',
        'updated_at',
    ];

    public const STATUS_SELECT = [
        'Active'        => 'Active',
        'Inactive'      => 'Inactive',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
