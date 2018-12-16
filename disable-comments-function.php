<?php

$disable_comments_option = get_option('disable_comments');

if($disable_comments_option == 'disable_everywhere' ){
	add_action( 'admin_menu', 'remove_menu_comments' );
	add_action( 'admin_init' ,  'restriction_access_comments' ) ;
	add_action( 'add_admin_bar_menus', 'remove_bar_menu_comments' );
	add_action( 'admin_enqueue_scripts', 'disable_dashboard_widgets' );
	add_action( 'widgets_init', 'remove_comments_widget' );
	add_action( 'admin_menu' , 'remove_comments_fields' );
	add_action( 'rss_tag_pre', 'action_function_name_4280' );
	add_action( 'do_feed_rss2', 'disable_comments_feed', 1, 2 );
	add_action( 'do_feed_atom', 'disable_comments_feed', 1, 2 );	
	add_filter( 'comments_template','remove_comments_template' );
	add_filter( 'manage_edit-post_columns', 'remove_comments_columns' );
	add_filter( 'manage_edit-page_columns', 'remove_comments_columns' );
	add_filter( 'manage_media_columns', 'remove_comments_columns' );
	add_filter( 'wp_headers', 'remove_x_pingback' );
	add_filter( 'site_url', 'pingback_href_replacement', 10, 4 );
	add_filter( 'pings_open', 'disable_pingback_tag', 10, 2 );
}

elseif ($disable_comments_option == 'disable_selected') {
	$disabled_types = get_option('disabled_types');
	if (!empty($disabled_types)){
		add_action( 'rss_tag_pre', 'action_function_name_4280' );
		add_filter( 'comments_template','remove_comments_template' );
		add_action( 'admin_menu' , 'remove_comments_fields' );
		add_filter( 'wp_headers', 'remove_x_pingback' );
		add_filter( 'pings_open', 'disable_pingback_tag', 10, 2 );
		foreach ($disabled_types as $val) {
			if ($val == 'attachment'){
				add_filter( 'manage_media_columns', 'remove_comments_columns' );
			}
			else{
				add_filter( 'manage_edit-'.$val.'_columns', 'remove_comments_columns' );
			}
		}
	}
}

function disable_comments_feed( $is_comment_feed, $feed ){
	if ( $is_comment_feed == 1){
			return wp_die('Comments are closed');
		}
	else
		switch ($feed) {
			case 'atom':
				load_template( ABSPATH . WPINC . '/feed-atom.php' );
				break;
			
			case 'rss2':
				load_template( ABSPATH . WPINC . '/feed-rss2.php' );
				break;
		}
		
}

function disable_pingback_tag( $open, $post_id ){
	$disabled_types = get_option('disabled_types');
	if(empty($disabled_types)){
		$open = false;
		return $open;
	}
	else{
		$post_type = get_post_type($post_id);
		foreach ($disabled_types as $val) {
			if ($val == $post_type){
				$open = false;
				return $open;
			}
		}
	}
	return $open;
}

function pingback_href_replacement( $url, $path, $scheme, $blog_id ){
		if($path == 'xmlrpc.php'){
			$url = " ";
		}
		return $url;
}

function restriction_access_comments ( ) {
	$path = $_SERVER['REQUEST_URI'];
	switch ($path) {
		case '/wordpress/wp-admin/edit-comments.php':
		case '/wordpress/wp-admin/options-discussion.php':
			wp_die('Comments are closed');
			break;
		default:
			break;
	}
	return; 
}

function remove_menu_comments(){
	remove_menu_page( 'edit-comments.php' ); 
}

function remove_bar_menu_comments(){
	if ( ! is_network_admin() && ! is_user_admin() ) {
		remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 ); ; 
	}
}

function remove_comments_template( $comment_template ){
	$disabled_types = get_option('disabled_types');
	if(empty($disabled_types)){
		return __DIR__ . '/no-comments.php';
	}
	else{
		$post_type = get_post_type();
		foreach ($disabled_types as $val) {
			if ($val == $post_type){
				return __DIR__ . '/no-comments.php';
			}
		}
	}
}

function remove_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}

function remove_comments_widget() {
	unregister_widget('WP_Widget_Recent_Comments');
}

function remove_comments_fields() {
	$disabled_types = get_option('disabled_types');
	if(empty($disabled_types)){
		$disabled_types = array('post', 'page', 'attachment' );
	}

	remove_meta_box( 'commentstatusdiv' , $disabled_types , 'advanced' );
	remove_meta_box( 'commentsdiv' , $disabled_types , 'advanced' );
	remove_meta_box( 'trackbacksdiv' , $disabled_types , 'advanced' );
}

function remove_comments_columns( $columns ){
	unset($columns['comments']);
	return $columns;
}

function disable_dashboard_widgets(){
	wp_enqueue_style( 'dashboard-disable-comments', plugins_url('assets/css/dashboard-disable-comments.css', __FILE__) );
};

?>