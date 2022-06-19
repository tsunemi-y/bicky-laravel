<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parentName',
        'email',
        'tel',
        'password',
        'childName',
        'age',
        'gender',
        'diagnosis',
        'childName2',
        'age2',
        'gender2',
        'diagnosis2',
        'address',
        'introduction',
        'coursePlan',
        'consaltation',
        'introduction',
        'fee',
        'userAgent',
        'use_time',
        'line_consultation_flag',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 予約テーブルとのリレーション
     *
     * @return void
     */
    public function reservations()
    {
        return $this->hasOne(Reservation::class);
    }

    /**
     * 利用児の曖昧検索
     *
     * @param [type] $query
     * @param [type] $name
     * @return void
     */
    public function scopeFuzzyName($query, $name)
    {
        if ($name != '') {
            return $query->where('parentName', 'like', "%$name%");
        }
    }

    /**
     * 引数の指定値に対応したレコード取得
     *
     * @param [type] $query
     * @param [type] $id
     * @return void
     */
    public function scopeEqualId($query, $id)
    {
        if ($id != '') {
            return $query->where('id', '=', $id);
        }
    }
}
