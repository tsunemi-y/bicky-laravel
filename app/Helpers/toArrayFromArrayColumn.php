<?php

declare(strict_types=1);

if (!function_exists('toArrayFromArrayColumn')) {
    
    function toArrayFromArrayColumn($arrayColumn)
    {
        return explode(',', str_replace(['{', '}'], '', $arrayColumn));
    }
}
