<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    public function __invoke(Tag $tag)
    {
        $jobs = $tag->jobs()
            ->with(['employer', 'tags'])
            ->latest()
            ->paginate(3)
            ->withQueryString();

        return view('results', ['jobs' => $jobs, 'query' => $tag->name]);
    }
}
