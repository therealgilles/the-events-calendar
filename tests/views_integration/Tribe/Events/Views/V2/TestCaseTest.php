<?php

namespace Tribe\Events\Views\V2;

require_once codecept_data_dir( 'Views/V2/classes/Test_View.php' );
require_once codecept_data_dir( 'Views/V2/classes/Test_Context_View.php' );
use Tribe\Test\Products\WPBrowser\Views\V2\TestCase;

class TestCaseTest extends TestCase {

	/**
	 * It should throw if building context for non registered view class
	 *
	 * @test
	 */
	public function should_throw_if_building_context_for_non_registered_view_class() {
		add_filter( 'tribe_events_views', function () {
			return [];
		} );

		$this->expectException( \RuntimeException::class );

		$context = $this->given_a_main_query_request()
		                ->for_view( Test_View::class );
	}

	/**
	 * It should be able to setup a main query context
	 *
	 * @test
	 */
	public function should_be_able_to_setup_a_main_query_context() {
		// Set test as an enabled view.
		$options = \Tribe__Settings_Manager::get_options();
		$options['tribeEnableViews'] = ['test'];
		\Tribe__Settings_Manager::set_options( $options );

		add_filter( 'tribe_events_views', function () {
			return [ 'test' => Test_View::class ];
		} );

		$this->given_a_main_query_request()
		     ->for_view( Test_View::class )
		     ->with_args( [
			     'view_data' => [
				     'day' => '2019-03-12',
			     ],
		     ] )
		     ->alter_global_context();

		$actual = tribe_context()->to_array();
		$this->assertEquals( 'test', $actual['view'] );
		$this->assertEquals( true, $actual['is_main_query'] );
		$this->assertEquals( [
			'day' => '2019-03-12',
		], $actual['view_data'] );
	}
}