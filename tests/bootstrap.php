<?php

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../yarpp.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

//loading the plugin doesn't also run the activate function, so manually run it here in the bootstrap
//there must be a more elegant way to do this, because the first time the test is run, there are a lot of warnings from the wp test suite about the missing tables
//the second time, the errors go away, so the test suite must try a bunch of things in the test bootstrap, before the following lines execute
global $yarpp;
$yarpp->activate();