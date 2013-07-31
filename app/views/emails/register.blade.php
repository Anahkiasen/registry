<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Register</title>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<p class="panel">
		Your account was successfuly created. To activate it, click the following link :<br>
		{{ HTML::link(URL::action('UsersController@getActivate', $activation_code)) }}
	</p>
</body>
</html>