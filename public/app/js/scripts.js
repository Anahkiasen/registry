var packages      = $('.packages-list__package'),
		packagesInfos = [],
		empty         = $('.packages-list__empty');


$('.layout-search').on('keyup', function(event) {
	var query      = $(this).val()
			nbPackages = packages.length
			visible    = 0;

	for (key = 0; key <= nbPackages; key++) {
		var package = packages[key];

		// Get packages informations
		if (packagesInfos[key] === undefined) {
			var infos = $('td', package);
			packagesInfos[key] = {
				'name'        : infos[1].innerHTML,
				'description' : infos[2].innerHTML,
				'tags'        : infos[3].innerHTML,
			}
		}

		// Show matching results
		if (
			packagesInfos[key].name.match(query) ||
			packagesInfos[key].description.match(query) ||
			packagesInfos[key].tags.match(query)) {
			$(package).show();
			visible += 1;
		} else {
			$(package).hide();
		}
	}

	if (visible == 0) {
		empty.show();
	} else {
		empty.hide();
	}

});