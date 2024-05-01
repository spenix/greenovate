<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title inertia>{{ config('app.name', 'Laravel') }}</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  @include('layouts.css')
  @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
  @routes
  @inertiaHead
</head>

<body>
  @inertia
  @include('layouts.script')
</body>

</html>