<?php return array(

	// SCM repository
	//////////////////////////////////////////////////////////////////////

	// The SCM used (supported: "git", "svn")
	'scm' => 'git',

	// The SSH/HTTPS address to your repository
	// Example: https://github.com/vendor/website.git
		'repository' => 'https://github.com/Anahkiasen/registry.git',

	// The repository credentials : you can leave those empty
	// if you're using SSH or if your repository is public
	// In other cases you can leave this empty too, and you will
	// be prompted for the credentials on deploy
	'username'   => '',
	'password'   => '',

	// The branch to deploy
	'branch'     => 'master',

);
