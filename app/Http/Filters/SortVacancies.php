<?php

namespace App\Http\Filters;


use Closure;
use Illuminate\Http\Request;

class SortVacancies
{
    public function __construct(protected Request $request)
    {
        //
    }

    public function handle($query, Closure $next)
    {
        if ($sort = $this->request->get('sort')) {
            switch ($sort) {
                case 'date_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'date_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'applications_asc':
                    $query->withCount('applications')->orderBy('applications_count', 'asc');
                    break;
                case 'applications_desc':
                    $query->withCount('applications')->orderBy('applications_count', 'desc');
                    break;
            }
        }
        return $next($query);
    }
}
