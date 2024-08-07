<x-layout>
    <x-page-heading>Results for "{{ $query }}"</x-page-heading>

    <div class="space-y-6">
        @forelse($jobs as $job)
            <x-job-card-wide :$job />
        @empty
            <p class="text-center">No jobs found.</p>
        @endforelse
    </div>
</x-layout>
