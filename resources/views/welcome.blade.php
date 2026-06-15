<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Task Manager') }}</title>
    <meta http-equiv="refresh" content="0; url=/admin" />
    <style>
        body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f8fafc; color: #334155; }
        a { color: #4f46e5; text-decoration: none; }
    </style>
</head>
<body>
    <p>Redirecting to <a href="/admin">Admin Panel</a>...</p>
</body>
</html>
