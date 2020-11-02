<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Team;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index() {

        $teams = Team::all();

        $schedule = Schedule::orderBy('week_number')->get();

        if($schedule->count() == 0) {
            $this->fillSchedule($teams);
            $schedule = Schedule::orderBy('week_number')->get();
        }

        $arResults = $this->getResultList($schedule);

        $prev_week = 0;

        foreach ($schedule as $item) {
            if($item->game->finished != 1 && $prev_week != $item->week_number && $prev_week > 0) {
                break;
            }
            $prev_week = $item->week_number;

        }

        $week = $prev_week;

        $this->calcTeams($teams);

        return view('index')
            ->with('teams', $teams)
            ->with('week', $week)
            ->with('results', $arResults);
    }


    public function resetLeague() {
        $schedules = Schedule::all();
        foreach ($schedules as $schedule) {
            $schedule->delete();
        }

        $games = Game::all();
        foreach ($games as $game) {
            $game->delete();
        }

        $teams = Team::all();

        $this->fillSchedule($teams);
        $schedule = Schedule::orderBy('week_number')->get();

        $arResults = $this->getResultList($schedule);

        $this->calcTeams($teams);

        return view('ajaxcontent')
            ->with('teams', $teams)
            ->with('week', 0)
            ->with('results', $arResults);
    }


    public function nextWeek() {

        $teams = Team::all();

        $schedule = DB::table('schedules')
            ->leftJoin('games', function ($join) {
                $join->on('games.id', '=', 'schedules.game_id');
            })
            ->select('schedules.week_number')
            ->where('games.finished', '=', 0)
            ->orderBy('schedules.week_number', 'asc')
            ->take(1)
            ->get();

        if($schedule->count() == 1) {
            $week_to_play = $schedule[0]->week_number;
        } else {
            $week_to_play = 0;
        }

        $this->playWeek($week_to_play);

        $schedule = Schedule::orderBy('week_number')->get();

        $this->calcTeams($teams);

        $arResults = $this->getResultList($schedule);

        return view('ajaxcontent')
            ->with('teams', $teams)
            ->with('week', $week_to_play)
            ->with('results', $arResults);

    }


    public function playAll() {

        $teams = Team::all();

        $arWeekToPlay = [];

        $schedule = DB::table('schedules')
            ->leftJoin('games', function ($join) {
                $join->on('games.id', '=', 'schedules.game_id');
            })
            ->select('schedules.week_number')
            ->where('games.finished', '=', 0)
            ->orderBy('schedules.week_number', 'asc')
            ->get();

        $last_week = 0;

        foreach ($schedule as $item) {
            if(!in_array($item->week_number, $arWeekToPlay)) {
                $arWeekToPlay[] = $item->week_number;
                $last_week = $item->week_number;
            }
        }

        foreach ($arWeekToPlay as $week_to_play) {
            $this->playWeek($week_to_play);
        }

        $schedule = Schedule::orderBy('week_number')->get();

        $this->calcTeams($teams);

        $arResults = $this->getResultList($schedule, false);

        return view('ajaxcontent')
            ->with('teams', $teams)
            ->with('week', $last_week)
            ->with('results', $arResults);

    }


    private function getResultList($schedule, $only_last_week = true) {

        $arResults = [];

        $prev_week = 0;

        foreach ($schedule as $item) {

            if($item->game->finished != 1 && $prev_week != $item->week_number && $prev_week > 0) {
                break;
            }

            if(!isset($arResults[$item->week_number])) {
                $arResults[$item->week_number] = [];
            }

            $arResults[$item->week_number][] = [
                'id' => $item->game->id,
                'team_home' => $item->game->teamHome->name,
                'team_visitor' => $item->game->teamVisitor->name,
                'goals_home' => $item->game->goals_home,
                'goals_visitor' => $item->game->goals_visitor,
                'finished' => $item->game->finished == 1,
                'week_number' => $item->week_number,
            ];

            $prev_week = $item->week_number;

        }

        if($only_last_week) {
            $arWeeksToDelete = array_diff(array_keys($arResults), [$prev_week]);
            foreach ($arWeeksToDelete as $week_number) {
                unset($arResults[$week_number]);
            }
        }

        return $arResults;
    }


    private function playWeek($week) {

        if($week > 0) {
            $schedule = Schedule::where('week_number', $week)->get();
            foreach ($schedule as $item) {
                $this->playGame($item->game);
            }
        }
        return $week;

    }


    private function playGame(Game $game) {

        if($game->finished != 1) {
            $game->goals_home = $this->calcScore($game->teamHome->rate, $game->teamVisitor->rate);
            $game->goals_visitor = $this->calcScore($game->teamVisitor->rate, $game->teamHome->rate);
            $game->finished = 1;
            $game->save();
        }
    }


    private function calcScore(int $rate_home, int $rate_visitor) {
        $score = round(mt_rand(-$rate_visitor * $rate_visitor / $rate_home, $rate_home) / 10);
        if($score <= 0) {
            $score = 0;
        }
        return $score;
    }


    private function calcTeams(&$teams) {

        $games = Game::where('finished', 1)->get();

        foreach ($teams as &$team) {
            $team->points = 0;
            $team->scored = 0;
            $team->conceded = 0;
            $team->goal_diff = 0;
            $team->win = 0;
            $team->lose = 0;
            $team->draw = 0;
            $team->played = 0;
        }

        if($games->count() > 0) {

            foreach ($games as $game) {
                foreach ($teams as &$team) {

                    if($team->id == $game->team_home_id) {
                        $scored = $game->goals_home;
                        $conceded = $game->goals_visitor;
                    } elseif($team->id == $game->team_visitor_id) {
                        $scored = $game->goals_visitor;
                        $conceded = $game->goals_home;
                    } else {
                        continue;
                    }
                    if($scored > $conceded) {
                        $points = 3;
                        $team->win++;
                    } elseif($scored == $conceded) {
                        $points = 1;
                        $team->draw++;
                    } else {
                        $points = 0;
                        $team->lose++;
                    }

                    $team->points += $points;
                    $team->scored += $scored;
                    $team->conceded += $conceded;
                    $team->goal_diff += $scored - $conceded;
                    $team->played++;

                }
            }

            $teams = $teams->sortByDesc('goal_diff')->sortByDesc('points');

        }
    }


    private function fillSchedule($teams) {

        $arWeek = [];
        for($i = 1; $i <= ($teams->count() - 1) * 2; $i++) {
            $arWeek[$i] = [];
        }

        foreach ($teams as $team_key => $team) {

            $rivals = $teams;

            $role = 'home';
            $this->fillTeamShedule($team, $rivals, $arWeek, $role);
            $role = 'visitor';
            $this->fillTeamShedule($team, $rivals, $arWeek, $role);

        }

    }


    private function storeGame(Team $team, Team $rival, $role, $week_number, &$arTeamId) {

        $game = new Game();

        if($role == 'home') {
            $game->team_home_id = $team->id;
            $game->team_visitor_id = $rival->id;
        } else {
            $game->team_home_id = $rival->id;
            $game->team_visitor_id = $team->id;
        }

        $game->save();

        $arTeamId[] = $team->id;
        $arTeamId[] = $rival->id;

        $schedule = new Schedule();
        $schedule->game_id = $game->id;
        $schedule->week_number = $week_number;
        $schedule->save();

    }

    private function fillTeamShedule(Team $team, $rivals, &$arWeek, &$role) {

        foreach ($rivals as $rival) {

            if($team->id == $rival->id) {
                continue;
            }

            foreach ($arWeek as $week_number => &$arTeamId) {
                if(!array_intersect($arTeamId, [$team->id, $rival->id])) {
                    $this->storeGame($team, $rival, $role, $week_number, $arTeamId);
                    $role = $role == 'home' ? 'visitor' : 'home';
                    break;
                }
            }

        }

    }

}
