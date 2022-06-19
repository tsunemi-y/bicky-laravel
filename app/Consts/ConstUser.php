<?php

namespace App\Consts;

class ConstUser
{
    // コース、月の利用回数
    public const ONE_USE = 1;
    public const TWO_USE = 2;

    // コースタイプ
    public const COURSE_WEEKDAY = 1; // 平日
    public const COURSE_HOLIDAY = 2; // 休日

    // コース別料金
    public const FEE_LINE_ONLY = 4400;  // LINEのみ相談
    public const FEE_ONE_WEEKDAY = 7700;  // 月1回平日
    public const FEE_ONE_HOLIDAY = 8800;  // 月1回休日
    public const FEE_ONE_SIBLING = 13200; // 月1回兄弟
    public const FEE_TWO_WEEKDAY = 15400; // 月2回平日
    public const FEE_TWO_HOLIDAY = 17600; // 月2回休日
    public const FEE_TWO_SIBLING = 26400; // 月2回兄弟

    // 利用時間
    public const NORMAL_USE_TIME = 60; // 60分
    public const LONG_USE_TIME   = 90; // 90分
}
