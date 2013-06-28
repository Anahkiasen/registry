<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Laravel packages registry</title>
	{{ Basset::show('application.css') }}
</head>
<body>
	@yield('layout')
	{{ Basset::show('application.js') }}
</body>
</html>