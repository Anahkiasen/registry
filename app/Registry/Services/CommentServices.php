<?php
namespace Registry\Services;

use Auth;
use Registry\Comment;
use Registry\Repositories\PackagesRepository;
use Validator;

/**
 * Various comment services
 */
class CommentServices
{
	/**
	 * The Comments model
	 *
	 * @var Comment
	 */
	protected $comments;

	/**
	 * The Packages repository
	 *
	 * @var PackagesRepository
	 */
	protected $packages;

	/**
	 * Build a new PackagesController
	 *
	 * @param Comment            $comments
	 * @param PackagesRepository $packages The Packages Repository
	 */
	public function __construct(Comment $comments, PackagesRepository $packages)
	{
		$this->packages = $packages;
		$this->comments = $comments;
	}

	/**
	 * Create a comment from input data
	 *
	 * @param  array  $input
	 * @param  string $slug   The package slug
	 *
	 * @return Comment|Validator
	 */
	public function createFromInput($input, $slug)
	{
		$validation = Validator::make($input, ['content' => 'required']);
		if ($validation->fails()) {
			return $validation;
		}

		// Create comment
		$this->comments->create(array(
			'content'       => $input['content'],
			'maintainer_id' => Auth::user()->id,
			'package_id'    => $this->packages->findBySlug($slug)->id,
		));

		return true;
	}
}