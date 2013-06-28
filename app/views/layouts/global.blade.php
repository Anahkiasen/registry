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
	@yield('js')
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-728496-10']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
</body>
</html>