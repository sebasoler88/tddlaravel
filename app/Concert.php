<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{

    protected $guarded = [];

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }
}
