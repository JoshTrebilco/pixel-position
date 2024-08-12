<?php

use App\Models\Employer;
use App\Models\Job;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
    // Create a user and authenticate before each test
    $this->user = User::factory()->create();

    // Create an employer associated with the user
    $this->employer = Employer::factory()->create([
        'user_id' => $this->user->id,
    ]);

    // Authenticate the user
    $this->actingAs($this->user);
});

it('returns jobs associated with a tag', function () {
    // Create a tag
    $tag = Tag::factory()->create(['name' => 'PHP']);

    // Create jobs associated with the tag
    $job1 = Job::factory()->create(['title' => 'PHP Developer', 'employer_id' => $this->employer->id]);
    $job2 = Job::factory()->create(['title' => 'Senior PHP Developer', 'employer_id' => $this->employer->id]);
    $job1->tags()->attach($tag->id);
    $job2->tags()->attach($tag->id);

    // Perform the request
    $response = $this->get(route('tags.show', $tag));

    // Assert that the correct jobs are returned
    $response->assertOk();
    $response->assertViewIs('results');
    $response->assertViewHas('jobs', function ($jobs) {
        return $jobs->count() === 2 &&
               $jobs->first()->title === 'PHP Developer' &&
               $jobs->last()->title === 'Senior PHP Developer';
    });
    $response->assertViewHas('query', $tag->name);
});

it('paginates jobs associated with a tag', function () {
    // Create a tag
    $tag = Tag::factory()->create(['name' => 'JavaScript']);

    // Create multiple jobs associated with the tag to trigger pagination
    $jobs = Job::factory()->count(5)->create(['title' => 'JavaScript Developer', 'employer_id' => $this->employer->id]);
    $jobs->each(fn ($job) => $job->tags()->attach($tag->id));

    // Perform the request
    $response = $this->get(route('tags.show', $tag));

    // Assert pagination is working (3 jobs per page)
    $response->assertOk();
    $response->assertViewIs('results');
    $response->assertViewHas('jobs', function ($jobs) {
        return $jobs->count() === 3; // Asserts that only 3 jobs are returned (due to pagination)
    });
    $this->assertTrue($response->viewData('jobs')->hasPages());
});
