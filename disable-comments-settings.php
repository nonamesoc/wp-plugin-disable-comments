<?php 

add_action( 'admin_menu', 'disable_comments_options' );
add_action( 'admin_init', 'disable_comments_settings' );
add_action( 'admin_init', 'delete_comments_settings' ); 

function disable_comments_options() {

	add_menu_page(
	'Disable comments',
	'Disable comments',
	'manage_options',
	'disable_comments',
	'disable_comments_options_page'
	);

	add_submenu_page(
	'disable_comments',
	'Disable comments by Noname',
	'Disable comments',
	'manage_options',
	'disable_comments',
	'disable_comments_options_page'
	);

	add_submenu_page(
	'disable_comments',
	'Delete comments by Noname',
	'Delete comments',
	'manage_options',
	'discom_delete_comments',
	'delete_comments_page'
	); 
}

function disable_comments_settings() {
	register_setting( 'disable_comments', 'disable_comments' );
	register_setting( 'disable_comments', 'disabled_types' );

	add_settings_section(
	'discom_section', 
	'',
	'',
	'disable_comments'
	);

	add_settings_field(
	'disable_everywhere_output', 
	'',
	'disable_everywhere_output',
	'disable_comments',
	'discom_section',
	['id' => 'disable_everywhere']
	);
	add_settings_field(
	'disable_selected_output', 
	'',
	'disable_selected_output',
	'disable_comments',
	'discom_section',
	['id' => 'disable_selected']
	);
}

function delete_comments_settings(){
	add_settings_section(
	'delete_comments_section', 
	'',
	'',
	'discom_delete_comments'
	);
	add_settings_field(
	'delete_everywhere_output', 
	'',
	'delete_everywhere_output',
	'discom_delete_comments',
	'delete_comments_section'
	);
	add_settings_field(
	'delete_selected_output', 
	'',
	'delete_selected_output',
	'discom_delete_comments',
	'delete_comments_section'
	);

}

function disable_everywhere_output( $val ) {
	$id = $val['id'];
	?>
	<ul>
		<li>
			<label for="<? echo $id ?>">
				<input type="radio" id="<? echo $id ?>" name="disable_comments" 
           		value="disable_everywhere" <?php checked( 'disable_everywhere', esc_attr( get_option( 'disable_comments' ) ) ); ?> />
           		<strong>Everywhere</strong>: Disable all comment-related controls and settings in WordPress.
           	</label>
           	<p class="indent">
           		<strong style="color: #900">Warning</strong>: This option is global and will affect your entire site. Use it only if you want to disable comments everywhere.
           	</p>
        </li>
	<?php
}

function disable_selected_output( $val ) {
	$post_type = $page_type = $attachment_type = false;
	$id = $val['id'];
	$disabled_types = get_option('disabled_types');

	if(!empty($disabled_types)){
		foreach ($disabled_types as $value) {
			switch ($value) {
				case 'post':
					$post_type = true;
				break;
				case 'page':
					$page_type = true;
				break;
				case 'attachment':
					$attachment_type = true;
				break;
			}
		}
	}

	if(get_option('disable_comments') == 'disable_everywhere'){
		$post_type = $page_type = $attachment_type = true;
	}
	?>
        <li>
        	<label for="<? echo $id ?>">
	        	<input type="radio" id="<? echo $id ?>" name="disable_comments" 
	           		value="disable_selected" <?php checked( 'disable_selected', esc_attr( get_option( 'disable_comments' ) ) ); ?> />
	           	<strong>On certain post types</strong>:
	        </label>
           	<ul class=indent id="listoftypes">
           		<li>
           			<label for="post-type-post">
           				<input type="checkbox" id="post-type-post" name="disabled_types[]" value="post" <?php checked( true, $post_type ); ?> >Posts
           			</label>
           		</li>
           		<li>
           			<label for="post-type-page">
           				<input type="checkbox" id="post-type-page" name="disabled_types[]" value="page" <?php checked( true, $page_type ); ?> >Pages
           			</label>
           		</li>
           		<li>
           			<label for="post-type-attachment">
           				<input type="checkbox" id="post-type-attachment" name="disabled_types[]" value="attachment" <?php checked( true, $attachment_type ); ?> >Media
           			</label>
           		</li>
           	</ul>
           	<p class="indent">
           		Disabling comments will also disable trackbacks and pingbacks. All comment-related fields will also be hidden from the edit/quick-edit screens of the affected posts. These settings cannot be overridden for individual posts.
           	</p>	
        </li>
    </ul>
    
	<?php
}
 
function delete_everywhere_output( ) {
	?>
	<ul>
		<li>
			<label for="delete_everywhere">
				<input type="radio" id="delete_everywhere" name="delete_comments" 
           		value="delete_everywhere" />
           		<strong>Everywhere</strong>: Delete all comments in WordPress.
           	</label>
           	<p class="indent">
           		<strong style="color: #900">Warning</strong>: This function and will affect your entire site. Use it only if you want to delete comments <em>everywhere</em>.
           	</p>
        </li>
	<?php
}

function delete_selected_output( ) {
	?>
        <li>
        	<label for="delete_selected">
	        	<input type="radio" id="delete_selected" name="delete_comments" 
	           		value="delete_selected" checked/>
	           	<strong>For certain post types</strong>:
	        </label>
	        <p></p>
           	<ul class=indent id="listoftypes">
           		<li>
           			<label for="post-type-post">
           				<input type="checkbox" id="post-type-post" name="deleted_types[]" value="post" > Posts
           			</label>
           		</li>
           		<li>
           			<label for="post-type-page">
           				<input type="checkbox" id="post-type-page" name="deleted_types[]" value="page" > Pages
           			</label>
           		</li>
           		<li>
           			<label for="post-type-attachment">
           				<input type="checkbox" id="post-type-attachment" name="deleted_types[]" value="attachment" > Media
           			</label>
           		</li>
           	</ul>
           	<p class="indent">
           		<strong style="color: #900">Warning</strong>: Deleting comments will remove existing comment entries in the database and cannot be reverted without a database backup.
           	</p>	
        </li>
    </ul>
    
	<?php
}

function disable_comments_options_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_GET['settings-updated'] ) ) {

	add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'disable_comments' ), 'updated' );
	}

	settings_errors( 'wporg_messages' );
	?>
	<style>
		.indent{padding-left: 2em;}
	</style>
	<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<form action="options.php" method="post">
			<?php
			settings_fields( 'disable_comments' );

			do_settings_fields( 'disable_comments', 'discom_section' );

			submit_button( 'Save Settings' );
			?>
		</form>

	</div>
	
	<script>
	jQuery(document).ready(function($){
		function disable_checkboxes(){
			var list_checkboxes = $("#listoftypes");
			if( $("#disable_everywhere").is(":checked") )
				list_checkboxes.css("color", "#888").find(":input").attr("disabled", true );
			else
				list_checkboxes.css("color", "#000").find(":input").attr("disabled", false );
		}

		$("input").change(function(){
			$("#message").slideUp();
			disable_checkboxes();
		});

		disable_checkboxes();
	});
	</script>
	<?php
}

function delete_comments_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
	 	return;
	}

	if ( isset( $_GET['settings-updated'] ) ) {
		add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'disable_comments' ), 'updated' );
	}

	settings_errors( 'wporg_messages' );
	?>
	<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if(wp_count_comments()->total_comments > 0): ?> 

	<form action method="post">
		<?php
		
		settings_fields( 'discom_delete_comments' );
	
		do_settings_fields( 'discom_delete_comments', 'delete_comments_section' );
		
		?>
		<h4>Total Comments: <? echo wp_count_comments()->total_comments; ?></h4>
		<?php submit_button( 'Delete Comments', 'primary' , 'delete' ); ?>
	</form>

	<?php elseif($_REQUEST['delete_comments'] == 'delete_everywhere'): ?>
	
	<p><strong>All comments have been deleted.</strong></p>
	
	<?php else : ?>

	<p><strong>No comments available for deletion.</strong></p>

	<?php endif; ?>

	</div>

	<script>
	jQuery(document).ready(function($){
		function disable_checkboxes(){
			var list_checkboxes = $("#listoftypes");
			if( $("#delete_everywhere").is(":checked") )
				list_checkboxes.css("color", "#888").find(":input").attr("disabled", true );
			else
				list_checkboxes.css("color", "#000").find(":input").attr("disabled", false );
		}

		$("input").change(function(){
			$("#message").slideUp();
			disable_checkboxes();
		});

		disable_checkboxes();
	});
	</script>
	<?php
}

$plugin = DIS_COM_PLUGIN_BASENAME;
add_filter("plugin_action_links_$plugin", 'discom_plugin_settings_link' );

function discom_plugin_settings_link($links) { 
	$settings_link = '<a href="admin.php?page=disable_comments">Settings</a>'; 
	array_unshift($links, $settings_link); 
	return $links; 
}
 
 ?>