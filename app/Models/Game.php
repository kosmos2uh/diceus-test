<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    public function teamHome()
    {
        return $this->belongsTo(Team::class, 'team_home_id');
    }

    public function teamVisitor()
    {
        return $this->belongsTo(Team::class, 'team_visitor_id');
    }
}
