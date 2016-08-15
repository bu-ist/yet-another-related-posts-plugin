<?php

class YARPP_TestCache extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
	}


	function testCacheExists() {
		// see if the plug was successfully activated and the cache was enabled
		global $yarpp;
		$this->assertTrue( $yarpp->cache->is_enabled() );
	}

}

