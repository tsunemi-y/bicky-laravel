<?php

declare(strict_types=1);

if (!function_exists('convertCourseFeeToName')) {
    /**
     * コース料金をコース名に変換
     * @return string
     */
    function convertCourseFeeToName($fee)
    {
        $coursePlanList = [
            4400  => 'LINEのみ',
            7700  => '月1回平日',
            8800  => '月1回休日',
            13200 => '月1回兄弟',
            15400 => '月2回平日',
            17600 => '月2回休日',
            26400 => '月2回兄弟',
        ];

        return $coursePlanList[$fee];
    }
}
