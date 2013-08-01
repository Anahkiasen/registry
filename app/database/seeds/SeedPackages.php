<?php
use Carbon\Carbon;

class SeedPackages extends DatabaseSeeder
{

	/**
	 * An array of timers
	 *
	 * @var array
	 */
	protected $timers;

	/**
	 * The current timer
	 *
	 * @var integer
	 */
	protected $timer;

	/**
	 * A list of packages to ignore (forks or CI)
	 *
	 * @var array
	 */
	protected $ignore = array(
		'composer/installers',
		'typo3/flow-composer-installers',
		'iyoworks/former',
		'ppi/skeleton-app',
	);

	/**
	 * Seed the packages
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('packages')->truncate();

		$packages = $this->getPackages();
		foreach ($packages as $key => $package) {
			$this->timer = microtime(true);
			if (in_array($package->getName(), $this->ignore)) continue;

			// Create model
			$package = $this->createPackageModel($package);

			// Skip non-library
			if ($package->getPackagist()->type != 'library') {
				continue;
			}

			// Add repository and packagist statistics
			$this->fetchInformations($key, $package, $packages);
			$this->hydrateStatistics($package);
		}

		// Invert freshness scale
		$fresh = Package::min('freshness');
		foreach (Package::all() as $package) {
			$package->freshness = abs($package->freshness - $fresh);
			$package->save();
		}

		// Compute indexes
		$this->computeAllIndexes();
	}

	/**
	 * Compute indexes
	 *
	 * @return void
	 */
	public function computeAllIndexes()
	{
		$this->computePopularity();
		$this->computeTrust();
	}

	/**
	 * Get all Laravel packages
	 *
	 * @return array
	 */
	protected function getPackages()
	{
		$this->comment('Fetching list of packages');

		return Cache::rememberForever('packages', function() {
			return App::make('packagist')->search('laravel');
		});
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////// PACKAGE CREATION ///////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Create the Package model from raw informations
	 *
	 * @param  array $package
	 *
	 * @return Package
	 */
	protected function createPackageModel($package)
	{
		// Get type of package
		$vendor  = explode('/', $package->getName())[0];
		$type    = in_array($vendor, array('illuminate', 'laravel')) ? 'component' : 'package';
		$slug    = str_replace('/', '-', $package->getName());

		// Create model
		$package = Package::create(array(
			'name'        => $package->getName(),
			'slug'        => Str::slug($slug),
			'description' => $package->getDescription(),
			'packagist'   => $package->getUrl(),
			'type'        => $type,
		));

		// Unify Git repository URL
		$basePattern         = '([a-zA-Z0-9\-]+)';
		$repository          = $package->getPackagist()->repository;
		$package->repository = preg_replace('#((https|http|git)://|git@)(github.com|bitbucket.org)(:|/)' .$basePattern. '/' .$basePattern. '(.git)?#', 'http://$3/$5/$6', $repository);

		// Add Travis URL
		$travis          = explode('/', $package->repository);
		$package->travis = $package->repository ? $travis[3].'/'.$travis[4] : '';
		$package->save();

		return $package;
	}

	/**
	 * Hydrate the statistics of a Package
	 *
	 * @param  Package $package
	 *
	 * @return void
	 */
	public function hydrateStatistics(Package $package)
	{
		// Get and compute statistics ---------------------------------- /

		// Favorites
		$repository  = $package->getRepository();
		$watchers    = array_get($repository, 'watchers', array_get($repository, 'followers_count'));
		$forks       = array_get($repository, 'forks', array_get($repository, 'forks_count'));

		// Seniority and freshness
		$created_at  = new Carbon(array_get($repository, 'created_at', array_get($repository, 'utc_created_on')));
		$pushed_at   = new Carbon(array_get($repository, 'pushed_at',  array_get($repository, 'utc_last_updated')));

		// Tests consistency
		$builds       = $package->getTravisBuilds();
		$consistency  = array_filter($builds, function($build) {
			return $build['result'] !== 1;
		});
		$consistency  = $builds ? round(sizeof($consistency) * 100 / sizeof($builds)) : 0;
		$buildStatus = array_get($package->getTravis(), 'last_build_status', 2);
		$buildStatus = (int) abs($buildStatus - 2);

		// Tests coverage
		$coverage = $package->getScrutinizer();
		$methods  = array_get($coverage, 'php_code_coverage.methods', 1);
		$covered  = array_get($coverage, 'php_code_coverage.covered_methods', 1);
		$coverage = ceil($covered * 100 / $methods);

		// Ratio of open issues
		$openIssues   = array_get($repository, 'open_issues_count', 0);
		$totalIssues  = $package->getRepositoryIssues();
		$totalIssues = array_get($totalIssues, '0.number', array_get($totalIssues, 'count'));
		if ($totalIssues == 0) {
			$issues = 100;
		} else {
			$issues = ($totalIssues- $openIssues) * 100 / $totalIssues;
		}

		// Save additional informations -------------------------------- /

		$lastVersion = $package->getPackagist()->versions;
		$lastVersion = current($lastVersion);
		$require     = array_merge(array_get($lastVersion, 'require', array()), array_get($lastVersion, 'require-dev', array()));
		$illuminate  = array('illuminate/support', 'laravel/framework');
		foreach ($illuminate as $i) {
			if (array_key_exists($i, $require)) {
				$illuminate = true;
				break;
			} else {
				$illuminate = false;
			}
		}

		$package->fill(array(
			'illuminate'        => $illuminate,

			// Downloads
			'downloads_total'   => $package->getPackagist()->downloads['total'],
			'downloads_monthly' => $package->getPackagist()->downloads['monthly'],
			'downloads_daily'   => $package->getPackagist()->downloads['daily'],

			// Repository statistics
			'watchers'          => $watchers,
			'forks'             => $forks,
			'favorites'         => $package->getPackagist()->favers,
			'issues'            => round($issues),

			// Unit testing
			'build_status'      => $buildStatus,
			'consistency'       => $consistency,
			'coverage'          => $coverage,

			// Date-related statistics
			'created_at'        => $created_at->toDateTimeString(),
			'pushed_at'         => $pushed_at->toDateTimeString(),
			'seniority'         => $created_at->diffInDays(),
			'freshness'         => $pushed_at->diffInDays(),
		))->save();
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// INDEXES ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Compute every package's popularity
	 *
	 * @return void
	 */
	protected function computePopularity()
	{
		$this->computeIndexes('popularity', array(
			'downloads_total' => 1.25,
			'watchers'        => 2,
			'forks'           => 0.75,
			'favorites'       => 0.25,
			'freshness'       => 0.75,
		), array(
			'downloads_total' => 'downloads_total',
			'watchers'        => 'watchers',
			'forks'           => 'forks',
			'favorites'       => 'favorites',
			'freshness'       => 'freshness',
		));
	}

	/**
	 * Compute every package's trust index
	 *
	 * @return void
	 */
	protected function computeTrust()
	{
		$this->computeIndexes('trust', array(
			'travisStatus' => 1.25,
			'seniority'    => 0.5,
			'freshness'    => 1,
			'consistency'  => 1,
			'issues'       => 0.75,
			'coverage'     => 0.5,
		), array(
			'travisStatus' => 2,
			'seniority'    => 'seniority',
			'freshness'    => 'freshness',
			'consistency'  => 'consistency',
			'issues'       => 'issues',
			'coverage'     => 100,
		), 0);
	}

	/**
	 * Compute an index
	 *
	 * @param  string $attribute
	 * @param  array  $weights
	 * @param  array  $ceilings
	 * @param integer $rounding
	 *
	 * @return void
	 */
	protected function computeIndexes($attribute, $weights, $ceilings, $rounding = 2)
	{
		$this->info('Computing ' .$attribute. ' indexes');

		// Get all packages
		$packages = Package::all();
		$inverted = array('freshness');

		// Fetch maximum value
		foreach ($ceilings as $name => $value) {
			if (is_string($value)) {
				$ceilings[$name] = Package::whereType('package')->max($value);
			}
		}

		// Compute indexes
		foreach ($packages as $package) {
			foreach ($ceilings as $index => $value) {
				if (in_array($index, $inverted)) {
					$indexes[$index] = (($package->$index * 100 / -$value) + 100) * $weights[$index];
				} else {
					$indexes[$index] = ($package->$index * 100 / $value) * $weights[$index];
				}
			}

			// Round and save
			$package->$attribute = round(array_sum($indexes) / array_sum($weights), $rounding);
			$package->save();
		}
	}

	////////////////////////////////////////////////////////////////////
	/////////////////////////////// HELPERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	/**
	 * Print progress
	 *
	 * @param  integer    $key
	 * @param  Package    $package
	 * @param  Collection $total
	 * @param  string     $message
	 *
	 * @return string
	 */
	protected function fetchInformations($key, $package, $total)
	{
		$key   = $key + 1;
		$total = sizeof($total);
		$name  = ($package instanceof Package) ? $package->name : $package->getName();

		// Global message
		$this->info(sprintf("Fetching informations for [%s/%s] %s", $key, $total, $name));
		$this->comment("-- Repository");
		$package->getRepository();
		$this->comment("-- Repository issues");
		$package->getRepositoryIssues();
		$this->comment('-- Packagist');
		$package->getPackagist();
		$this->comment('-- Travis');
		$package->getTravis();
		$this->comment('-- Travis builds');
		$package->getTravisBuilds();
		$this->comment('-- Scrutinizer');
		$package->getScrutinizer();

		$timer = round(microtime(true) - $this->timer, 4);
		$final = '--- Total time : '.$timer.'s';

		// Remaining time
		$this->timers[] = $timer;
		$final .= ', remaining : '.$this->computeRemainingTime($key, $total)->format('%H:%I:%S');

		$this->line($final);
	}

	/**
	 * Compute the time remaining
	 *
	 * @param integer $current
	 * @param integer $totla
	 *
	 * @return Carbon
	 */
	protected function computeRemainingTime($current, $total)
	{
		$remaining = array_sum($this->timers) / sizeof($this->timers);
		$remaining = $remaining * ($total - $current);
		$remaining = Carbon::now()->addSeconds($remaining);
		$remaining = $remaining->diff(Carbon::now());

		return $remaining;
	}

}
