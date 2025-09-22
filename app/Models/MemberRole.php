<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRole extends Model
{
    use HasFactory;

    protected $table = 'member_roles';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public const STATUS_SELECT = [
        '1' => 'Active',
        '2' => 'Inactive',
    ];

    protected $fillable = [
        'title',
        'status',
        'created_at',
        'updated_at',
    ];


    public function members()
    {
        return $this->hasMany(Member::class);
    }
    public function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('y-m-d H:i:s');
    }
}
