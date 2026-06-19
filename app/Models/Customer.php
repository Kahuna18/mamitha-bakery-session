<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'is_member', 'user_id', 'points'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // =========================================================================
    // Member Rank & Point Accessors
    // =========================================================================

    public function getRankNameAttribute()
    {
        if ($this->points >= 1000) return 'Platinum';
        if ($this->points >= 300) return 'Gold';
        if ($this->points >= 100) return 'Silver';
        return 'Bronze';
    }

    public function getRankBadgeAttribute()
    {
        if ($this->points >= 1000) return '👑';
        if ($this->points >= 300) return '🥇';
        if ($this->points >= 100) return '🥈';
        return '🥉';
    }

    public function getNextRankNameAttribute()
    {
        if ($this->points >= 1000) return 'Max Rank';
        if ($this->points >= 300) return 'Platinum';
        if ($this->points >= 100) return 'Gold';
        return 'Silver';
    }

    public function getNextRankPointsAttribute()
    {
        if ($this->points >= 1000) return 1000;
        if ($this->points >= 300) return 1000;
        if ($this->points >= 100) return 300;
        return 100;
    }

    public function getPointsForNextRankAttribute()
    {
        if ($this->points >= 1000) return 0;
        return max(0, $this->next_rank_points - $this->points);
    }

    public function getRankProgressPercentageAttribute()
    {
        if ($this->points >= 1000) return 100;
        if ($this->points >= 300) {
            return min(100, round((($this->points - 300) / 700) * 100));
        }
        if ($this->points >= 100) {
            return min(100, round((($this->points - 100) / 200) * 100));
        }
        return min(100, round(($this->points / 100) * 100));
    }

    public function getRankThemeAttribute()
    {
        $rank = $this->rank_name;
        if ($rank === 'Platinum') {
            return [
                'text' => 'text-cyan-600 dark:text-cyan-400',
                'bg' => 'from-cyan-400 via-teal-300 to-blue-500',
                'border' => 'border-cyan-300 dark:border-cyan-700/60',
                'glow' => 'rgba(6, 182, 212, 0.4)',
                'animation' => 'animate-pulse-cyan'
            ];
        }
        if ($rank === 'Gold') {
            return [
                'text' => 'text-amber-600 dark:text-amber-400',
                'bg' => 'from-amber-400 via-amber-250 to-yellow-500',
                'border' => 'border-amber-300 dark:border-amber-700/60',
                'glow' => 'rgba(245, 158, 11, 0.4)',
                'animation' => 'animate-pulse-gold'
            ];
        }
        if ($rank === 'Silver') {
            return [
                'text' => 'text-slate-500 dark:text-slate-400',
                'bg' => 'from-slate-400 via-gray-250 to-slate-500',
                'border' => 'border-slate-300 dark:border-slate-700/60',
                'glow' => 'rgba(148, 163, 184, 0.3)',
                'animation' => 'animate-pulse-silver'
            ];
        }
        // Bronze
        return [
            'text' => 'text-orange-700 dark:text-orange-500',
            'bg' => 'from-orange-500 via-orange-350 to-amber-600',
            'border' => 'border-orange-300 dark:border-orange-700/60',
            'glow' => 'rgba(234, 88, 12, 0.3)',
            'animation' => 'animate-pulse-bronze'
        ];
    }
}
