<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Stuff extends Model
{
    use softDeletes;
    protected $fillable = ["name", "category"];

    public function stuffStock()
    {
        return $this->hasOne(stuffStock::class);
    }

    public function inboundStuffs()
    {
        return $this->hasMany(inboundStuffs::class);
    }

    public function lendings()
    {
        return $this ->hasMany(lending::class);
    }
}
