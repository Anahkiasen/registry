<?php

//////////////////////////////////////////////////////////////////////
///////////////////////////////// ASSETS /////////////////////////////
//////////////////////////////////////////////////////////////////////

Basset::collection('application', function($collection) {
	$collection->stylesheet('components/normalize-css/normalize.css');
	$collection->stylesheet('components/icomoon/style.css');
	$collection->stylesheet('app/css/styles.css');
})
->rawOnEnvironment('local')
->apply('CssMin')
->apply('UriRewriteFilter');

//////////////////////////////////////////////////////////////////////
///////////////////////// PAGE-SPECIFIC ASSETS ///////////////////////
//////////////////////////////////////////////////////////////////////

Basset::collection('home', function($collection) {
	$collection->javascript('components/lodash/dist/lodash.backbone.min.js');
	$collection->javascript('app/js/scripts.js');
})
->rawOnEnvironment('local')
->apply('JsMin');

Basset::collection('chart', function($collection) {
	$collection->javascript('components/nnnick-chartjs/Chart.min.js');
	$collection->javascript('app/js/chart.js');
})->rawOnEnvironment('local');