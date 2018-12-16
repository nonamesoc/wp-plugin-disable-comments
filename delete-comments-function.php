<?php

if($_REQUEST['delete'] == 'Delete Comments'){
	global $wpdb;

	if ($_REQUEST['delete_comments'] == 'delete_everywhere'){
		$delete = $wpdb->query("TRUNCATE TABLE `wp_comments`");
		$update = $wpdb->query("UPDATE `wp_posts` SET `comment_count` = 0 WHERE `comment_count` > 0");
	}

	else {

		if (empty($_REQUEST['deleted_types'])){
			return;
		}

		$deleted_types = implode("','", $_REQUEST['deleted_types']);
		$deleted_types = "'".$deleted_types."'";
		
		$delete = $wpdb->query("DELETE FROM `wp_comments` WHERE `comment_post_ID` IN (SELECT `ID` FROM `wp_posts` WHERE `post_type` IN ($deleted_types))");
		$update = $wpdb->query("UPDATE `wp_posts` SET `comment_count` = 0 WHERE `comment_count` > 0 AND `post_type` IN ($deleted_types)");
	}
}

?>