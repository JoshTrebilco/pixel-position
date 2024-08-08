<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string'],
        ]);

        $query = request('q');

        $jobs = Job::query()
            ->with(['employer', 'tags'])
            ->where('title', 'LIKE', '%'.$query.'%')
            ->paginate(3)
            ->withQueryString();

        return view('results', ['jobs' => $jobs, 'query' => $query]);
    }
}
