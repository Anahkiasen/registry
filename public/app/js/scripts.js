var packages      = document.querySelectorAll('.packages-list__package'),
		empty         = document.querySelector('.packages-list__empty'),
		search        = document.querySelector('.layout-search'),
		form          = document.getElementById('search'),
		nbPackages    = packages.length,
		packagesInfos = [],
		lastQuery     = null;

//////////////////////////////////////////////////////////////////////
//////////////////////////////// HELPERS /////////////////////////////
//////////////////////////////////////////////////////////////////////

var handleEvent = function(handlers, selector, fn) {
	handlers = handlers.split(' ').forEach(function(handler) {

		// Build selector
		if (typeof(selector) === 'string') {
			selector = document.querySelectorAll(selector);
		}

		// Bind event handler
		if (selector instanceof NodeList) {
			[].forEach.call(selector, function(element) {
				element.addEventListener(handler, fn);
			});
		} else if (typeof(selector) === 'object') {
			selector.addEventListener(handler, fn);
		} else {
			console.log(selector);
		}

	});
};

/**
 * Refreshes the results of the table
 */
var refreshResults = function(query) {
	var visible = 0, key;

	// Cache last query
	if (query === lastQuery) {
		return false;
	} else {
		lastQuery = query;
	}

	for (key = 0; key < nbPackages; key++) {
		var package = packages[key];

		// Get packages informations
		if (packagesInfos[key] === undefined) {
			packagesInfos[key] = {
				'name'        : package.children[1].innerHTML,
				'description' : package.children[2].innerHTML,
				'tags'        : package.children[3].innerHTML
			};
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
	if (visible === 0) {
		empty.classList.remove('hidden');
	} else {
		empty.classList.add('hidden');
	}
};

//////////////////////////////////////////////////////////////////////
///////////////////////////////// EVENTS /////////////////////////////
//////////////////////////////////////////////////////////////////////

/**
 * URL queries
 */
if (search.value.length !== 0) {
	refreshResults(search.value);
}

/**
 * Listens as the User types into the search field
 */
handleEvent('keyup', search, function() {
	refreshResults(this.value);
});

handleEvent('reset', form, function() {
	refreshResults('');
});

/**
 * Redirect to the first result on submit
 */
form.addEventListener('submit', function(event) {
	event.preventDefault();
	var firstPackage, key;

	// Get first results
	for (key = 0; key < nbPackages; key++) {
		var package = packages[key];

		if (package.classList.contains('hidden')) {
			continue;
		} else {
			firstPackage = package;
			break;
		}
	}

	window.location = 'package/' + firstPackage.dataset.id;
});

/**
 * Make Tag act like categories
 */
handleEvent('click', '.tag', function() {
	search.value = this.innerHTML;
	refreshResults(this.innerHTML);
});
