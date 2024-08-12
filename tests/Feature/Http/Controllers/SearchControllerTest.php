<?php

use App\Models\Employer;
use App\Models\Job;
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

it('returns jobs based on search query', function () {
    // Create jobs
    Job::factory()->create(['title' => 'Software Developer', 'employer_id' => $this->employer->id]);
    Job::factory()->create(['title' => 'Frontend Engineer', 'employer_id' => $this->employer->id]);
    Job::factory()->create(['title' => 'Backend Developer', 'employer_id' => $this->employer->id]);

    // Perform search
    $response = $this->get(route('search', ['q' => 'Developer']));

    // Assert that the correct jobs are returned
    $response->assertOk();
    $response->assertViewIs('results');
    $response->assertViewHas('jobs', function ($jobs) {
        return $jobs->count() === 2 &&
               $jobs->first()->title === 'Software Developer' &&
               $jobs->last()->title === 'Backend Developer';
    });
    $response->assertViewHas('query', 'Developer');
});

it('validates the search query', function () {
    // Attempt search with empty query
    $response = $this->get(route('search', ['q' => '']));

    // Assert validation failure
    $response->assertSessionHasErrors(['q']);
});

it('paginates search results', function () {
    // Create multiple jobs to trigger pagination
    Job::factory()->count(5)->create(['title' => 'Developer', 'employer_id' => $this->employer->id]);

    // Perform search
    $response = $this->get(route('search', ['q' => 'Developer']));

    // Assert pagination is working (3 jobs per page)
    $response->assertOk();
    $response->assertViewIs('results');
    $response->assertViewHas('jobs', function ($jobs) {
        return $jobs->count() === 3; // Asserts that only 3 jobs are returned (due to pagination)
    });
    $this->assertTrue($response->viewData('jobs')->hasPages());
});
