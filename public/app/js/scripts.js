var packages      = document.querySelectorAll('.packages-list__package'),
		empty         = document.querySelector('.packages-list__empty'),
		search        = document.querySelector('.layout-search'),
		tags          = document.querySelectorAll('tags'),
		nbPackages    = packages.length,
		packagesInfos = [];

/**
 * Refreshes the results of the table
 */
var refreshResults = function(query) {
	var visible = 0;

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
};

/**
 * Listens as the User types into the search field
 *
 * @param  {event} event
 *
 * @return {void}
 */
search.addEventListener('input', function(event) {
	refreshResults(this.value);
});

/**
 * Redirect to the first result on submit
 *
 * @param  {event} event
 *
 * @return {void}
 */
document.getElementById('search').addEventListener('submit', function(event) {
	event.preventDefault();

	// Get first results
	for (key = 0; key < nbPackages; key++) {
		var package = packages[key];

		if (package.classList.contains('hidden')) continue;
		else {
			break;
		}
	}

	window.location = 'packages/package/' + package.dataset['id'];
});

[].forEach.call(document.querySelectorAll('.tag'), function(tag) {
	tag.addEventListener('click', function(event) {
		search.value = this.innerHTML;
		refreshResults(this.innerHTML);
	});
});
