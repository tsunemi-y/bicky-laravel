<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reservation_date',
        'reservation_time',
    ];

    /**
     * ユーザテーブルとのリレーション
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 予約時間の取得
     *
     * @param  string  $value
     * @return string
     */
    // public function getReservationTimeAttribute($value)
    // {
    //     return substr($value, 0, -3);
    // }

    public function scopeEqualDate($query, $date)
    {
        if ($date != '') {
            return $query->where('reservation_date', '=', $date);
        }
    }

    public function scopeFuzzyName($query, $name)
    {
        if ($name != '') {
            return $query->where('name', 'like', "%$name%");
        }
    }

    public function scopeEqualId($query, $id)
    {
        if ($id != '') {
            return $query->where('id', '=', $id);
        }
    }
}
