<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    />
    @routes
    @viteReactRefresh
    @vite('resources/js/app.jsx')
    @vite('resources/scss/app.scss')
    @inertiaHead
</head>

<body class="antialiased text-gray-900 bg-gray-50">
    <script
        defer
        src="https://d3js.org/d3.v4.js"
    ></script>
    <script
        defer
        src="https://rawgit.com/susielu/d3-annotation/master/d3-annotation.min.js"
    ></script>
    @inertia
    <div id="portal"></div>
</body>

</html>
