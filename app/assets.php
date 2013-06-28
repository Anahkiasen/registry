<?php

//////////////////////////////////////////////////////////////////////
///////////////////////////////// ASSETS /////////////////////////////
//////////////////////////////////////////////////////////////////////

Basset::collection('application', function($collection) {
	$collection->stylesheet('//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css');
	$collection->stylesheet('app/css/styles.css');

	$collection->javascript('components/jquery/jquery.min.js');
	$collection->javascript('components/jquery.tablesorter/js/jquery.tablesorter.min.js');
})->rawOnEnvironment('local');