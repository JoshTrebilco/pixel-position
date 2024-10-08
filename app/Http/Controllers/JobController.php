<?php

namespace App\Http\Controllers;

use App\Actions\JobCreatedAction;
use App\Mail\JobPosted;
use App\Models\Job;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $featuredJobs = Job::latest()
            ->with(['employer', 'tags'])
            ->where('featured', true)
            ->get();

        $jobs = Job::latest()
            ->with(['employer', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate(3);

        return view('jobs.index', [
            'jobs' => $jobs,
            'featuredJobs' => $featuredJobs,
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, JobCreatedAction $jobCreatedAction)
    {
        $attributes = $request->validate([
            'title' => ['required'],
            'salary' => ['required'],
            'location' => ['required'],
            'schedule' => ['required', Rule::in(['Part Time', 'Full Time'])],
            'url' => ['required', 'active_url'],
            'tags' => ['nullable'],
            'description' => ['required'],
        ]);

        $attributes['featured'] = $request->has('featured');

        $attributes['slug'] = Str::slug($attributes['title']);

        $job = Auth::user()->employer->jobs()->create(Arr::except($attributes, 'tags'));

        if ($attributes['tags'] ?? false) {
            foreach (explode(',', $attributes['tags']) as $tag) {
                $job->tag(Str::trim($tag));
            }
        }

        // Execute the invokable action to send emails to all users
        $jobCreatedAction($job);

        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        return view('jobs.show', [
            'job' => $job->load(['employer', 'tags']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        Gate::authorize('update', $job);

        return view('jobs.edit', [
            'job' => $job->load('tags'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Job $job)
    {
        Gate::authorize('update', $job);

        $attributes = $request->validate([
            'title' => ['required'],
            'salary' => ['required'],
            'location' => ['required'],
            'schedule' => ['required', Rule::in(['Part Time', 'Full Time'])],
            'url' => ['required', 'active_url'],
            'tags' => ['nullable'],
            'description' => ['required'],
        ]);

        $attributes['featured'] = $request->has('featured');

        $attributes['slug'] = Str::slug($attributes['title']);

        $job->update(Arr::except($attributes, 'tags'));

        $job->removeTags();

        if ($attributes['tags'] ?? false) {
            foreach (explode(',', $attributes['tags']) as $tag) {
                $job->tag(Str::trim($tag));
            }
        }

        return redirect(route('jobs.show', $job));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        Gate::authorize('delete', $job);

        $job->delete();

        return redirect()->route('home');
    }

    /**
     * Preview the email that will be sent to all users.
     */
    public function email(Job $job)
    {
        return new JobPosted($job);
    }
}
