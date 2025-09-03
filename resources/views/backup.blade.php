<!-- resources/views/backup.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Database</title>
</head>

<body>
    <h1>Backup Database</h1>

    <!-- Display status message -->
    @if(session('status'))
    <p style="color: green;">{{ session('status') }}</p>
    @endif

    <!-- Backup Button -->
    <form action="{{ route('backup.database') }}" method="POST">
        @csrf
        <button type="submit">Backup Database Now</button>
    </form>

</body>

</html>