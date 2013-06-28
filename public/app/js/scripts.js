var packages      = document.querySelectorAll('.packages-list__package'),
		packagesInfos = [],
		empty         = document.querySelector('.packages-list__empty');

document.querySelector('.layout-search').addEventListener('keyup', function(event) {
	var query      = this.value,
			nbPackages = packages.length
			visible    = 0;

	for (key = 0; key < nbPackages; key++) {
		var package = packages[key];

		// Get packages informations
		if (packagesInfos[key] === undefined) {
			var infos = package.children;
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
			package.classList.remove('hidden');
			visible += 1;
		} else {
			package.classList.add('hidden');
		}
	}

	// Show "No results" row
	if (visible == 0) {
		empty.classList.remove('hidden');
	} else {
		empty.classList.add('hidden');
	}

});