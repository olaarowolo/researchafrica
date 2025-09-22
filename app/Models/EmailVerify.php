<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerify extends Model
{
    use HasFactory;

    public $table = 'email_verifies';

    protected $fillable = [
        'token',
        'member_id',
    ];


    public function member()
    {
        return $this->belongsTo(Member::class);
    }

}
