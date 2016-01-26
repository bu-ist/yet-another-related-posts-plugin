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

	/**
	* @subcommand analyze-duplicates
	*/
	public function analyze_duplicates() {
		//high-scoring relatedness can indicate duplicate content
		//this command gets all of the highest scoring relationships, and isolates the higher scoring entry from it's reciprocal entry

		//get all of the high score associations
		$score='40';

		global $wpdb;
		$table = $wpdb->prefix . YARPP_TABLES_RELATED_TABLE;
		$sql = $wpdb->prepare("SELECT * FROM $table WHERE score > '%f' ORDER BY score DESC", $score);
		$results = $wpdb->get_results($sql, ARRAY_A);

		//each potential dulicate combo will have 2 entries, one for each post.  we need to collapse the two records into one

		//create a target array and only copy unique combinations into it
		$target = array();

		//initalize by moving first record to the target array 
		$target[] = array_shift($results);

		foreach ($results as $rec) {
			//take the reference_ID from the $rec and filter through the $target array
			//looking for items in the target array where the inverse of the ID and reference_id match
			//since the results are sorted by score, this should only retain the higher scoring entry

			$match_exists = false;

			foreach ($target as $check) {
				if ($rec['reference_ID'] == $check['ID'] && $rec['ID'] == $check['reference_ID']) {
					$match_exists = true;
				}
			}
			//if such a record exists in target then skip the current rec

			//otherwise copy it to target
			if (!$match_exists) {$target[] = $rec;}
		}

		//echo json_encode($results);
		echo json_encode($target);

	}

}