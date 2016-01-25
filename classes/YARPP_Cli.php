<?php

if (!defined('ABSPATH')) {
    die();
}
 
// Bail if WP-CLI is not present
if ( !defined( 'WP_CLI' ) ) return;


class YARPP_CLI extends WP_CLI_Command {
	/**
	 * Tell YARPP to do stuff
	 *
	 * ## EXAMPLES
	 *
	 * cache-all-posts: calculate relatedness for all published posts
	 *
	 * cache-clear: flushes the entire yarpp cache 
	 *
	 */

	/**
	* @subcommand cache-all-posts
	*/
	public function cache_all_posts() {
		//get an array of all the published post ids
		$post_ids = get_posts(array(
			'numberposts'   => -1, // get all posts.
			'post_type'		=> 'post',
			'post_status'   => 'publish',
    		'fields'        => 'ids' // Only get post IDs
		));

		//find related posts and cache the result
		foreach ($post_ids as $id) {
			WP_CLI::log("calculating related for:".$id);
			related_posts(array(),$id,false);
		}

		WP_CLI::success("processed all published posts!");
	}

	/**
	* @subcommand cache-clear
	*/
	public function cache_clear() {
		global $yarpp;
		if(!$yarpp->cache->is_enabled()){
			WP_CLI::warning("cache not enabled");
			exit();
		}

		//delete the entire cache using the flush function of the cache object
		$yarpp->cache->flush();
		WP_CLI::success("all cached items cleared");
	}
}