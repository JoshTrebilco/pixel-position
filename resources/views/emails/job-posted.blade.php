<!DOCTYPE html>
<html>
<head>
    <title>New Job Listed</title>
</head>
<body>
    <h1>A New Job Has Been Listed!</h1>
    <p>A new job "{{ $job->title }}" has been listed.</p>
    <p>Description: {{ $job->description }}</p>
    <p>To view this job, click <a href="{{ route('jobs.show', $job) }}">here</a>.</p>
</body>
</html>
