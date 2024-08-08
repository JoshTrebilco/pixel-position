<x-layout>
    <div class="p-4 bg-white/5 rounded-xl border border-transparent text-white">
        <x-page-heading class="text-2xl font-bold">{{ $job->title }}</x-page-heading>

        <div class="mt-4 flex items-center justify-between text-sm text-gray-400">
            <div>
                <span>{{ $job->location }}</span>
                <span class="mx-2">•</span>
                <span>{{ $job->schedule }}</span>
            </div>
            <div>
                <a href="{{ $job->url }}" class="text-blue-400 hover:underline font-medium">Apply Now</a>
            </div>
        </div>

        <div class="mt-4 flex items-center text-sm text-gray-400">
            <span>Salary: {{ $job->salary }}</span>
            @if($job->featured)
            <span class="ml-4 bg-yellow-500 text-gray-900 px-2 py-1 rounded">Featured</span>
            @endif
        </div>

        <div class="mt-6">
            <h2 class="text-lg font-semibold">Job Description</h2>
            <p class="text-gray-300 leading-relaxed mt-2">{{ $job->description }}</p>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            @foreach($job->tags as $tag)
                <x-tag :$tag size="small" class="bg-blue-500 text-white" />
            @endforeach
        </div>
                @can('update', $job)
                <a href="{{ route('jobs.edit', $job) }}" class="text-blue-400 hover:underline">Edit</a>
                @endcan
    </div>
</x-layout>
