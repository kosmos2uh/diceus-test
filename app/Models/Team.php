<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    public function gamesHome()
    {
        return $this->hasMany(Game::class, 'team_home_id');
    }

    public function gamesAway()
    {
        return $this->hasMany(Game::class, 'team_visitor_id');
    }

    public function games() {
        /**
         * VARIANT 3
         */

        return Game::where('team_home_id', $this->id)->orWhere('team_visitor_id', $this->id)->get();
        /*$users = DB::table('users')
            ->leftJoin('transactions', function ($join) {
                $join->on('users.id', '=', 'transactions.user_id')->where('transactions.type', '=', 'debit');
            })
            ->select('users.id', 'users.name', 'users.email', 'users.is_admin', DB::raw('SUM(transactions.amount) as debit'))
            ->groupBy('users.id')
            ->orderBy('users.id', 'desc')
            ->take(10)
            ->get();*/

        /**
         * VARIANT 1
         * this is not the best solution to do additional request inside foreach loop
         * but if we don't care about speed and resources we can use this variant.
         * It is pretty succinctly
         */
        /*$users = User::orderBy('id', 'desc')->take(10)->get();
        foreach ($users as &$user) {
            $user->debit = $user->transactions()->where('type', '=', 'debit')->sum('amount');
        }*/


        /**
         * VARIANT 2
         * much bigger and harder to read and understand
         * but faster (8 times faster) and more optimal if we care about speed and resources
         */
        /*$users = User::orderBy('id', 'desc')->take(10)->get();

        $arUserId = array();
        $arUserDebitTransaction = array();

        foreach ($users as $user) {
            $arUserId[] = $user->id;
        }

        if(!empty($arUserId)) {

            $transactions = Transaction::where('type', '=', 'debit')->whereIn('user_id', $arUserId)->get();

            foreach ($transactions as $transaction) {
                $arUserDebitTransaction[$transaction->user_id] = $arUserDebitTransaction[$transaction->user_id] ?? 0;
                $arUserDebitTransaction[$transaction->user_id] += $transaction->amount;
            }

            foreach ($users as &$user) {
                $user->debit = $arUserDebitTransaction[$user->id] ?? 0;
            }
        }*/
    }
}
