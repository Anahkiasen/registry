var packages      = $('.packages-list tbody tr'),
		packagesInfos = [];

$('.layout-search').on('keyup', function(event) {
	var query      = $(this).val()
			nbPackages = packages.length;

	for (key = 0; key <= nbPackages; key++) {
		var package = packages[key];

		if (packagesInfos[key] === undefined) {
			var infos = $('td', package);
			packagesInfos[key] = {
				'name'        : infos[1].innerHTML,
				'description' : infos[2].innerHTML,
				'tags'        : infos[3].dataset['tags'],
			}
		}

		if (
			packagesInfos[key].name.match(query) ||
			packagesInfos[key].description.match(query) ||
			packagesInfos[key].tags.match(query)) {
			$(package).show();
		} else {
			$(package).hide();
		}
	}

});