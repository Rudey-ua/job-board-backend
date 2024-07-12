<?php

namespace App\Http\Filters;

use Closure;

class FilterByTags
{
    public function handle($query, Closure $next)
    {
        $tags = request()->query('tags');

        if ($tags) {
            $tagIds = explode(',', $tags);
            $query->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tags.id', $tagIds);
            });
        }
        return $next($query);
    }
}
