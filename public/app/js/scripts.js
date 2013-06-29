var packages      = document.querySelectorAll('.packages-list__package'),
		empty         = document.querySelector('.packages-list__empty'),
		search        = document.querySelector('.layout-search'),
		tags          = document.querySelectorAll('tags'),
		form          = document.getElementById('search'),
		nbPackages    = packages.length,
		packagesInfos = [];

//////////////////////////////////////////////////////////////////////
//////////////////////////////// HELPERS /////////////////////////////
//////////////////////////////////////////////////////////////////////

var addEventListener = function(handlers, selector, fn) {
	handlers = handlers.split(' ').forEach(function(handler) {

		// Build selector
		if (typeof(selector) == 'string') {
			selector = document.querySelectorAll(selector);
		}

		// Bind event handler
		if (selector instanceof NodeList) {
			[].forEach.call(selector, function(element) {
				element.addEventListener(handler, fn, false);
			});
		} else {
			selector.addEventListener(handler, fn, false);
		}


	});
};

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

//////////////////////////////////////////////////////////////////////
///////////////////////////////// EVENTS /////////////////////////////
//////////////////////////////////////////////////////////////////////



/**
 * Listens as the User types into the search field
 */
addEventListener('keyup', search, function(event) {
	refreshResults(this.value);
});

addEventListener('reset', form, function(event) {
	refreshResults('');
});

/**
 * Redirect to the first result on submit
 */
form.addEventListener('submit', function(event) {
	event.preventDefault();

	// Get first results
	for (key = 0; key < nbPackages; key++) {
		var package = packages[key];

		if (package.classList.contains('hidden')) continue;
		else {
			break;
		}
	}

	window.location = 'package/' + package.dataset['id'];
});

/**
 * Make Tag act like categories
 */
addEventListener('click', '.tag', function(tag) {
	search.value = this.innerHTML;
	refreshResults(this.innerHTML);
});
