<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewerAccept extends Model
{
    use HasFactory;

    protected $table = 'reviewer_accepts';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id',
        'member_id',
        'assigned_id',
        // 'comment_id',
        'created_at',
        'updated_at',
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
    public function scopeLastArticle($query, $article)
    {
        return $query->where('article_id', $article)->where('member_id', '!=', null);
    }

    // public function comment()
    // {
    //     return $this->belongsTo(Comment::class);
    // }
}
