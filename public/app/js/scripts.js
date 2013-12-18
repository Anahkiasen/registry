var packages      = document.querySelectorAll('.packages-list__package'),
		empty         = document.querySelector('.packages-list__empty'),
		search        = document.querySelector('.layout-search__input'),
		form          = document.querySelector('.layout-search'),
		packagesInfos = [],
		lastQuery     = null;

//////////////////////////////////////////////////////////////////////
//////////////////////////////// HELPERS /////////////////////////////
//////////////////////////////////////////////////////////////////////

/**
 * Executes a forEeach on a QuerySelector
 *
 * @param  {NodeList}   selector
 * @param  {Function}   fn
 *
 * @return {void}
 */
var each = function(selector, fn) {
	if (typeof(selector) === 'string') {
		selector = document.querySelectorAll(selector);
	}

	[].forEach.call(selector, fn);
};

/**
 * Bind event(s) on a selector
 *
 * @param  {string}            handlers
 * @param  {string|NodeList}   selector
 * @param  {Function}          fn
 *
 * @return {void}
 */
var handleEvent = function(handlers, selector, fn) {
	handlers = handlers.split(' ').forEach(function(handler) {

		// Build selector
		if (typeof(selector) === 'string') {
			selector = document.querySelectorAll(selector);
		}

		// Bind event handler
		if (selector instanceof NodeList) {
			each(selector, function(element) {
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
	var visible = 0;

	// Cache last query
	if (query === lastQuery) {
		return false;
	} else {
		lastQuery = query;
	}

	each('.packages-list__package', function(package) {
		var key = package.children[0].innerHTML;

		// Show matching results
		if (
			packagesInfos[key][1].match(query) ||
			packagesInfos[key][2].match(query) ||
			packagesInfos[key][3].match(query)) {
			package.classList.remove('hidden');
			visible += 1;
		} else {
			package.classList.add('hidden');
		}
	});

	// Show "No results" row
	if (visible === 0) {
		empty.classList.remove('hidden');
	} else {
		empty.classList.add('hidden');
	}
};

//////////////////////////////////////////////////////////////////////
///////////////////////// BUILD PACKAGES INDEX ///////////////////////
//////////////////////////////////////////////////////////////////////

each(packages, function(package) {
	var key = package.children[0].innerHTML;

	packagesInfos[key] = {
		0 : parseInt(key),
		1 : package.children[1].innerHTML.trim(),
		2 : package.children[2].innerHTML.trim(),
		3 : package.children[3].innerHTML.trim(),
		4 : package.children[4].innerHTML,
		5 : parseInt(package.children[5].innerHTML),
		6 : parseInt(package.children[6].innerHTML)
	};
});

//////////////////////////////////////////////////////////////////////
///////////////////////////////// SEARCH /////////////////////////////
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
	var firstPackage = document.querySelector('.packages-list__package:not(.hidden)');

	window.location = 'package/' + firstPackage.dataset.id;
});

/**
 * Make Tag act like categories
 */
document.addEventListener('click', function() {
	if (event.target.classList.contains('tag')) {
		search.value = event.target.innerHTML;
		refreshResults(search.value);
	}
});

//////////////////////////////////////////////////////////////////////
//////////////////////////////// SORTING /////////////////////////////
//////////////////////////////////////////////////////////////////////

handleEvent('click', 'th', function() {
	var _this         = this,
			index         = 0,
			packages      = document.querySelectorAll('.packages-list__package'),
			packagesArray = _.toArray(packages);

	// Get index, clean sorting classes
	each(this.parentNode.getElementsByTagName('th'), function(element, key) {
		if (element === _this) {
			index = key;
		} else {
			element.classList.remove('sort-asc');
			element.classList.remove('sort-desc');
		}
	});

	// Switch class
	if (this.classList.contains('sort-asc')) {
		this.classList.toggle('sort-asc');
		this.classList.toggle('sort-desc');
	} else {
		this.classList.add('sort-asc');
		this.classList.remove('sort-desc');
	}

	// Sort packages and cast to HTML
	packagesArray = _
		.sortBy(packagesArray, function(package) {
			return packagesInfos[package.children[0].innerHTML][index];
		})
		.map(function(package) {
			return package.outerHTML;
		});

	// Inverse if requested
	if (this.classList.contains('sort-desc')) {
		packagesArray = packagesArray.reverse();
	}

	// Replace table HTML
	document.getElementsByTagName('tbody')[0].innerHTML = packagesArray.join('');
});
