<?php

namespace App\Http\Filters;

use Carbon\Carbon;
use Closure;

class FilterByCreationDate
{
    public function handle($query, Closure $next)
    {
        $dateFilter = request()->query('date');

        if ($dateFilter) {
            switch ($dateFilter) {
                case 'day':
                    $query->where('created_at', '>=', Carbon::today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                    break;
            }
        }
        return $next($query);
    }
}
