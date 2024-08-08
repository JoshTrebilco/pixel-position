<x-layout>
    <x-page-heading>New Job</x-page-heading>

    <x-forms.form method="POST" action="{{ route('jobs.store') }}">
        <x-forms.input label="Title" name="title" placeholder="CEO" />
        <x-forms.input label="Salary" name="salary" placeholder="$90,000 USD" />
        <x-forms.input label="Location" name="location" placeholder="Winter Park, Florida" />
        <x-forms.textarea label="Description" name="description" placeholder="We're looking for a CEO to lead our company into the future." />

        <x-forms.select label="Schedule" name="schedule">
            <option>Part Time</option>
            <option>Full Time</option>
        </x-forms.select>

        <x-forms.input label="URL" name="url" placeholder="https://acme.com/jobs/ceo-wanted" />
        <x-forms.checkbox label="Feature (Costs Extra)" name="featured" />

        <x-forms.divider />

        <x-forms.input label="Tags (comma separated)" name="tags" placeholder="laracasts, video, education" />

        <x-forms.button>Publish</x-forms.button>
    </x-forms.form>
</x-layout>
