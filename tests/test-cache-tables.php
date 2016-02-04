<?php

class YARPP_TestCache extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}


	function testCacheExists() {
		// replace this with some actual testing code
		global $yarpp;

		$this->assertTrue( $yarpp->cache->is_enabled() );
	}
}

