<?php
add_action('admin_menu', 'wppv_setup_menu');
function wppv_setup_menu() {
	add_submenu_page('options-general.php','WP PostVoting','WP PostVoting','manage_options', __FILE__,'wppv_settings_page');
}
function wppv_settings_page() {
	$sn = 1;
	$vlists = '';
	$the_query = getPagingQuery();
	$wppv_onoff	= get_option('wppv_onoff');
	$wppv_label	= get_option('wppv_label');
	$wppv_mouse_over = get_option('wppv_mouse_over');
	$wppv_voted	= get_option('wppv_voted');
	$wppv_thanks = get_option('wppv_thanks');
	$wppv_refusal = get_option('wppv_refusal');
	$wppv_top = get_option('wppv_top');
	$wppv_bottom = get_option('wppv_bottom');
	$wppv_postonly = get_option('wppv_postonly');
	$wppv_login = get_option('wppv_login');
	$wppv_postnum = get_option('wppv_postnum');
?>
	<div class="wrap">
		<div id="wppv_container" class="postbox-container" style="width:75%;">
			<h2>WP PostVoting</h2>
			<div class="wppv_maincontent">
				<hr>
				<?php if(isset($_POST['wppv_process']) && $_POST['wppv_process'] == "process") {
					echo '<div id="message" class="updated below-h2"><p>'. __('Settings saved.') .'</p></div>'.PHP_EOL;
				} ?>
				<?php if($the_query->have_posts()) : ?>
					<h3>List of all voted posts</h3>
					<div class="table_list">
						<table id="wppv_lists" class="form-table">
							<thead><tr>
								<th>SN</th>
								<th>Post ID</th>
								<th>Post Title</th>
								<th>Vote Count</th>
							</tr></thead>
						<?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
							<tbody><tr <?php if($sn % 2 == 0) { echo 'class="wppv_alt"'; } ?>>
								<td width="5%"><?php echo $sn; ?></td>
								<td width="10%"><?php echo get_the_ID(); ?></td>
								<td width="65%"><?php echo '<a href="'.get_permalink().'" rel="bookmark" target="_blank">'.wp_trim_words( get_the_title(), 12, ' ...' ).'</a>'; ?></td>
								<td width="15%"><?php echo get_post_meta(get_the_ID(), '_wppvcount', true); ?></td>
							</tr></tbody>
							<?php $sn++; endwhile; wp_reset_postdata(); ?>
						</table>
					</div>
				<?php getPagingLink(); else : ?>
					<p class="get_started">You don't have any voted posts yet. From the below settings you can <strong>Active</strong> WP PostVoting. All the posts that have been voted by users will be displayed here. In the right sidebar <strong>Total voted posts</strong> and <strong>Total votes count</strong> will also be dislayed.</p>
				<?php endif; ?>
				<div id="poststuff">
					<div class="postbox">
						<h3>WP PostVoting Configuration</h3>
						<div class="inside">
							<form id='wppv_form' method="post" action="">
								<input type="hidden" name="wppv_process" value="process" />
								<table class="form-table">
									<tr valign="top">
			   							<th scope="row">PostVoting Active / Inactive</th>
										<td width="2%">:</td>
										<td colspan="2" width="48%">
											<Input type = 'Radio' Name ='wppv_onoff' value= 'yes' <?php if( get_option('wppv_onoff') == 'yes' ) echo 'checked';?> />Active
											<Input type = 'Radio' Name ='wppv_onoff' value= 'no' <?php if( get_option('wppv_onoff') != 'yes' ) echo 'checked';?> />Inactive
										</td>
									</tr>
									<tr valign="top">
			   							<th scope="row">PostVoting default text</th>
										<td width="2%">:</td>
										<td><input type="text" value="<?php if($wppv_label) { echo $wppv_label; } else { echo 'Vote this post'; } ?>" name="wppv_label" /></td>
			   						</tr>
									<tr valign="top">
			   							<th scope="row">PostVoting mouse over text</th>
										<td width="2%">:</td>
										<td><input type="text" value="<?php if($wppv_mouse_over) { echo $wppv_mouse_over; } else { echo 'Vote'; } ?>" name="wppv_mouse_over" /></td>
			   						</tr>
									<tr valign="top">
			   							<th scope="row">Post votted text</th>
										<td width="2%">:</td>
										<td><input type="text" value="<?php if($wppv_voted) { echo $wppv_voted; } else { echo 'Voted'; } ?>" name="wppv_voted" /></td>
			   						</tr>
									<tr valign="top">
			   							<th scope="row">PostVoting thanks text</th>
										<td width="2%">:</td>
										<td><input type="text" value="<?php if($wppv_thanks) { echo $wppv_thanks; } else { echo 'Thank you'; } ?>" name="wppv_thanks" /></td>
			   						</tr>
									<tr valign="top">
			   							<th scope="row">PostVoting refusal text</th>
										<td width="2%">:</td>
										<td><input type="text" value="<?php if($wppv_refusal) { echo $wppv_refusal; } else { echo 'Only for registered user'; } ?>" name="wppv_refusal" /></td>
			   						</tr>
									<tr valign="top">
			   							<th scope="row">Add Button in Posts/Pages Top</th>
										<td width="2%">:</td>
										<td><input type="checkbox" name="wppv_top" value="1" <?php if($wppv_top) { ?> checked="checked"  <?php } ?> /></td>
			   						</tr>
									<tr valign="top">
			   							<th scope="row">Add Button in Posts/Pages Bottom</th>
										<td width="2%">:</td>
										<td><input type="checkbox" name="wppv_bottom" value="1" <?php if($wppv_bottom) { ?> checked="checked"  <?php } ?>  /></td>
			   						</tr>
									<tr valign="top">
			   							<th scope="row">Add Button only in Posts:</th>
										<td width="2%">:</td>
										<td><input type="checkbox" name="wppv_postonly" value="1" <?php if($wppv_postonly) { ?> checked="checked"  <?php } ?>  /></td>
			   						</tr>
									<tr valign="top">
			   							<th scope="row">User Must be logged in for voting:</th>
										<td width="2%">:</td>
										<td colspan="2">
											<Input type='radio' name='wppv_login' value='yes' <?php if( get_option('wppv_login') == 'yes' ) echo 'checked';?> />Yes
											<Input type='radio' Name='wppv_login' value='no' <?php if( get_option('wppv_login') != 'yes' ) echo 'checked';?> />No
										</td>
			   						</tr>
								</table>
								<input type="submit" id="wppv_submit" name="wppv_submit" class="button-primary" value="<?php echo 'Save'; ?>" />
							</form>
						</div>
					</div>
				</div><!-- End poststuff -->
			</div><!-- End wppv_maincontent -->
		</div><!-- End postbox-container -->
		<div class="postbox-container" style="width:25%">
			<div id="wppv_sidebar">
				<?php if($the_query->have_posts()) : ?>
				<div id="wppv-statistics" class="wppvinfo_sidebar">
					<h3>WP PostVoting Statistics</h3>
					<div class="inside">
						<table class="form-table">
							<tr valign="top">
								<td>Total voted posts: <?php echo getPagingQuery()->found_posts; ?></td>
							</tr>
	       					<tr valign="top">
								<td>Total votes count: <?php echo wppv_total_voted_posts(); ?></td>
							</tr>
						</table>
					</div>
				</div>
				<div id="wppv-display" class="wppvinfo_sidebar">
				<form method="post" id="wppv_sidebar" enctype="multipart/form-data">
					<input type="hidden" name="wppv_display" value="show" />
					<h3>Number of Posts</h3>
					<div class="inside">
						<p>Enter the number of posts to show in WP PostVoting list's 1st page:</p>
						<table class="form-table">
							<tr valign="top">
								<td>Posts:</td>
								<td><input type="text" value="<?php if($wppv_postnum) { echo $wppv_postnum; } else { echo 20; } ?>" name="wppv_postnum" size="5" /></td>
								<td><input type="submit" name="get_post" class="button-primary" value="Submit"></td>
							</tr>
						</table>
					</div>
				</form>
				</div>
				<?php endif; ?>
				<div id="wppvdev-info" class="wppvinfo_sidebar">
					<h3>Plugin Info</h3>
					<ul class="wppvinfo_lists">
						<li>Price: Free!</li>
						<li>Version: 1.2.1</li>
						<li>Scripts: PHP + CSS + JS</li>
						<li>Requires: Wordpress 3.0+</li>
						<li>First release: 21-Feb-2014</li>
						<li>Last Update: 17 June, 2020</li>
                        <li>By: <a href="https://www.realwebcare.com/" target="_blank">Realwebcare</a><br/>
                        <li>Need Help? <a href="https://wordpress.org/support/plugin/wp-postvoting/" target="_blank">Support</a><br/>
                        <li>Like it? Please leave us a <a target="_blank" href="https://wordpress.org/support/plugin/wp-postvoting/reviews/?filter=5/#new-post">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. We highly appreciate your support!<br/>
                        <li>Published under: <a href="http://www.gnu.org/licenses/gpl.txt">GNU General Public License</a>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?php
}
if(isset($_POST['wppv_process']) && $_POST['wppv_process'] == "process") {
	global $blog_id;
	if( isset( $_POST['wppv_onoff'] ) ) {
		update_option( 'wppv_onoff' , $_POST[ 'wppv_onoff' ] );
	}
	if( isset( $_POST['wppv_label'] ) ) {
		update_option( 'wppv_label' , $_POST[ 'wppv_label' ] );
	}
	if( isset( $_POST['wppv_mouse_over'] ) ) {
		update_option( 'wppv_mouse_over' , $_POST[ 'wppv_mouse_over' ] );
	}
	if( isset( $_POST['wppv_voted'] ) ) {
		update_option( 'wppv_voted' , $_POST[ 'wppv_voted' ] );
	}
	if( isset( $_POST['wppv_thanks'] ) ) {
		update_option( 'wppv_thanks' , $_POST[ 'wppv_thanks' ] );
	}
	if( isset( $_POST['wppv_refusal'] ) ) {
		update_option( 'wppv_refusal' , $_POST[ 'wppv_refusal' ] );
	}
	if( isset( $_POST['wppv_top'] ) ) {
		update_option( 'wppv_top' , $_POST[ 'wppv_top' ] );
	} else {
		update_option( 'wppv_top' , 0 );
	}
	if( isset( $_POST['wppv_bottom'] ) ) {
		update_option( 'wppv_bottom' , $_POST[ 'wppv_bottom' ] );
	} else {
		update_option( 'wppv_bottom' , 0 );
	}
	if( isset( $_POST['wppv_postonly'] ) ) {
		update_option( 'wppv_postonly' , $_POST[ 'wppv_postonly' ] );
		$ok=true;
	} else {
		update_option( 'wppv_postonly' , 0 );
		$ok=true;
	}
	if( isset( $_POST['wppv_submit'] ) ) {
		update_option( 'wppv_login' , $_POST[ 'wppv_login' ] );
	}
}
if(isset($_POST['wppv_display']) && $_POST['wppv_display'] == "show") {
	if(isset($_POST['get_post']) && $_POST['get_post'] == 'Submit') {
		if( isset( $_POST['wppv_postnum'] ) ) {
			update_option( 'wppv_postnum' , $_POST[ 'wppv_postnum' ] );
		}
	}
}
?>