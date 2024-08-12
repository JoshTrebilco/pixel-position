<?php

use App\Actions\JobCreatedAction;
use App\Mail\JobPosted;
use App\Models\Employer;
use App\Models\Job;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

it('displays a list of jobs', function () {
    // Create some jobs and tags
    $jobs = Job::factory()->count(3)->create();
    $featuredJobs = Job::factory()->count(2)->create(['featured' => true]);
    $tags = Tag::factory()->count(3)->create();

    $response = $this->get(route('jobs.index'));

    $response->assertOk();
    $response->assertViewIs('jobs.index');
    $response->assertViewHas('jobs', fn ($viewJobs) => $viewJobs->count() === 3);
    $response->assertViewHas('featuredJobs', fn ($viewFeaturedJobs) => $viewFeaturedJobs->count() === 2);
    $response->assertViewHas('tags', $tags);
});

it('stores a new job', function () {
    // Fake the email sending
    Mail::fake();
    $jobCreatedAction = Mockery::spy(JobCreatedAction::class);
    app()->instance(JobCreatedAction::class, $jobCreatedAction);

    // Define the input data
    $data = [
        'title' => 'Sample Job',
        'salary' => '50000',
        'location' => 'Remote',
        'schedule' => 'Full Time',
        'url' => 'http://example.com',
        'tags' => 'PHP,Laravel',
        'description' => 'Job description here',
        'featured' => true,
    ];

    // Call the store method
    $response = $this->post(route('jobs.store'), $data);

    // Assert the job was created
    $this->assertDatabaseHas('jobs', [
        'title' => 'Sample Job',
        'featured' => true,
        'slug' => Str::slug('Sample Job'),
    ]);

    // Assert that JobCreatedAction was called
    $jobCreatedAction->shouldHaveReceived('__invoke')->once();

    // Assert the redirection
    $response->assertRedirect('/');
});

it('shows a job', function () {
    // Create a job
    $job = Job::factory()->create();

    $response = $this->get(route('jobs.show', $job));

    $response->assertOk();
    $response->assertViewIs('jobs.show');
    $response->assertViewHas('job', fn ($viewJob) => $viewJob->id === $job->id);
});

it('updates a job', function () {
    // Create a job
    $job = Job::factory()->create([
        'title' => 'Old Job Title',
        'schedule' => 'Part Time',
    ]);

    // Define the updated data
    $data = [
        'title' => 'Updated Job Title',
        'salary' => '60000',
        'location' => 'Onsite',
        'schedule' => 'Full Time',
        'url' => 'http://example.com',
        'tags' => 'Vue,React',
        'description' => 'Updated description',
        'featured' => false,
    ];

    // Call the update method
    $response = $this->put(route('jobs.update', $job), $data);

    // Assert the job was updated
    $response->assertStatus(302); // Ensure redirection occurs

    // Re-fetch the job to check the updated values
    $job->refresh();

    $this->assertEquals('Updated Job Title', $job->title);
    $this->assertEquals('Full Time', $job->schedule);

    // Assert the database has the updated job
    $this->assertDatabaseHas('jobs', [
        'id' => $job->id,
        'title' => 'Updated Job Title',
        'schedule' => 'Full Time',
    ]);
});

it('deletes a job', function () {
    // Create a job
    $job = Job::factory()->create();

    // Call the destroy method
    $response = $this->delete(route('jobs.destroy', $job));

    // Assert the job was soft deleted (checks the deleted_at column is not null)
    $this->assertSoftDeleted('jobs', [
        'id' => $job->id,
    ]);

    // Assert the redirection
    $response->assertRedirect(route('home'));
});

it('previews job email', function () {
    // Create a job
    $job = Job::factory()->create();

    // Call the email preview method
    $response = $this->get(route('jobs.email', $job));

    // Assert the response is a JobPosted Mailable instance
    $response->assertStatus(200);
    $this->assertInstanceOf(JobPosted::class, $response->original);
    $this->assertEquals($job->id, $response->original->job->id);
});
