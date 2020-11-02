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

    public function play() {
        if($this->finished != 1) {
            $this->goals_home = $this->calcScoreHome();
            $this->goals_visitor = $this->calcScoreVisitor();
            $this->finished = 1;
            $this->save();
        }
    }

    private function calcScoreHome() {
        return $this->calcScore($this->teamHome->rate, $this->teamVisitor->rate);
    }

    private function calcScoreVisitor() {
        return $this->calcScore($this->teamVisitor->rate, $this->teamHome->rate);
    }

    private function calcScore(int $rate_home, int $rate_visitor) {
        $score = round(mt_rand(-$rate_visitor * $rate_visitor / $rate_home / 2, $rate_home) / 10);
        if($score <= 0) {
            $score = 0;
        }
        return $score;
    }
}
