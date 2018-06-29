<?php
/**
 * Class GitHubUpdater
 *
 * @package Traduttore\Tests
 */

namespace Required\Traduttore\Tests;

use \GP;
use \GP_UnitTestCase;
use \WP_REST_Request;
use \Required\Traduttore\GitHubUpdater as Updater;

/**
 *  Test cases for \Required\Traduttore\GitHubUpdater.
 */
class GitHubUpdater extends GP_UnitTestCase {
	/**
	 * @var \GP_Project
	 */
	protected $project;

	/**
	 * @var Updater
	 */
	protected $updater;

	public function setUp() {
		parent::setUp();

		$this->project = $this->factory->project->create(
			[
				'name'                => 'Sample Project',
				'slug'                => 'sample-project',
				'source_url_template' => 'https://github.com/wearerequired/traduttore/blob/master/%file%#L%line%',
			]
		);

		$this->updater = new Updater( $this->project );
		$this->updater->remove_local_repository();
	}

	public function test_get_repository_path() {
		$this->assertSame( get_temp_dir() . 'traduttore-github-sample-project', $this->updater->get_repository_path() );
	}

	public function test_remove_local_repository() {
		mkdir( $this->updater->get_repository_path() );
		touch( $this->updater->get_repository_path() . '/foo.txt' );

		$this->assertTrue( file_exists( $this->updater->get_repository_path() . '/foo.txt' ) );
		$this->assertTrue( $this->updater->remove_local_repository() );
		$this->assertFalse( file_exists( $this->updater->get_repository_path() . '/foo.txt' ) );
	}

	public function test_fetch_and_update() {
		$result = $this->updater->fetch_and_update();

		$originals = GP::$original->by_project_id( $this->project->id );

		$this->assertTrue( $result );
		$this->assertNotEmpty( $originals );
	}
}
