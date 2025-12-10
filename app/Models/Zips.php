<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zips extends Model
{
    protected $guarded = [];

    public function certificates() {
        return $this->hasMany(Certificates::class, 'zip_id', 'id');
    }
}
