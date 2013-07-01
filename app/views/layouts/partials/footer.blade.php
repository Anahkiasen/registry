<footer class="layout-footer">
	<p>{{ HTML::link('about', 'About') }} - {{ HTML::linkAction('PackagesController@index', 'Packages') }} - {{ HTML::linkAction('MaintainersController@index', 'Maintainers') }}</p>
	<p>&copy; {{ date('Y') }} - {{ HTML::link('https://github.com/Anahkiasen', 'Maxime Fabre') }} - {{ HTML::link('http://autopergamene.eu', 'Autopergamene') }}</p>
</footer>