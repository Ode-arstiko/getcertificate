<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificates extends Model
{
    protected $guarded = [];

    public function zip() {
        return $this->belongsTo(Zips::class, 'zip_id', 'id');
    }
}
