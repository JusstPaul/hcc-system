<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
  @routes
  @vite('resources/js/app.js')
  @inertiaHead

  <style>
    html,
    body,
    #app {
      height: 100%;
    }
  </style>
</head>

<body>
  <script src="https://d3js.org/d3.v4.js"></script>
  <script src="https://rawgit.com/susielu/d3-annotation/master/d3-annotation.min.js"></script>
  @inertia
</body>

</html>
