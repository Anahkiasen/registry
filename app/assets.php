<?php

//////////////////////////////////////////////////////////////////////
///////////////////////////////// ASSETS /////////////////////////////
//////////////////////////////////////////////////////////////////////

Basset::collection('application', function($collection) {
	$collection->stylesheet('components/normalize-css/normalize.css');
	$collection->stylesheet('app/css/styles.css');

	$collection->javascript('components/jquery/jquery.min.js');
	$collection->javascript('components/jquery.tablesorter/js/jquery.tablesorter.min.js');
})->rawOnEnvironment('local');