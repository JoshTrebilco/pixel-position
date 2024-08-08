<x-layout>
    <x-page-heading>Edit Job</x-page-heading>

    <x-forms.form method="POST" action="{{ route('jobs.update', $job) }}">
        @csrf
        @method('PUT')

        <x-forms.input label="Title" name="title" placeholder="CEO" :value="$job->title" />
        <x-forms.input label="Salary" name="salary" placeholder="$90,000 USD" :value="$job->salary" />
        <x-forms.input label="Location" name="location" placeholder="Winter Park, Florida" :value="$job->location" />
        <x-forms.textarea label="Description" name="description" placeholder="We're looking for a CEO to lead our company into the future." :value="$job->description" />

        <x-forms.select label="Schedule" name="schedule">
            <option value="Part Time" {{ $job->schedule == 'Part Time' ? 'selected' : '' }}>Part Time</option>
            <option value="Full Time" {{ $job->schedule == 'Full Time' ? 'selected' : '' }}>Full Time</option>
        </x-forms.select>

        <x-forms.input label="URL" name="url" placeholder="https://acme.com/jobs/ceo-wanted" :value="$job->url" />
        <x-forms.checkbox label="Feature (Costs Extra)" name="featured" :checked="$job->featured" />

        <x-forms.divider />

        <x-forms.input label="Tags (comma separated)" name="tags" placeholder="laracasts, video, education" :value="implode(', ', $job->tags->pluck('name')->toArray())" />

        <x-forms.button>Update</x-forms.button>
    </x-forms.form>
</x-layout>
