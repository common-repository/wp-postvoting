<?php
/*
Plugin Name: WP PostVoting
Plugin URI: http://wordpress.org/plugins/wp-postvoting/
Description: WP PostVoting plugin allows visitors to your site to vote on your content.
Author: Realwebcare
Author URI: https://www.realwebcare.com/
Version: 1.2
*/

/*  Copyright 2014  Iftekhar  (email : realwebcare@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('WPPV_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
if(is_admin()) { include ( WPPV_PLUGIN_PATH . 'admin_setup.php' ); }
function wppv_admin_enqueue_scripts() {
	wp_enqueue_style('wppv-admin-css', WP_PLUGIN_URL .'/wp-postvoting/css/wppv_admin.css?v=1.2');
}
add_action('admin_init', 'wppv_admin_enqueue_scripts');
function wppv_enqueue_scripts() {
	if( get_option('wppv_onoff') == 'yes' && (get_option('wppv_top') || get_option('wppv_bottom')) ) {
		wp_enqueue_script('jquery');
		wp_register_script('wppvjs', WP_PLUGIN_URL .'/wp-postvoting/js/wp_postvoting.js', array('jquery'), '1.2');
		wp_enqueue_script('wppvjs');
		wp_localize_script( 'wppvjs', 'wppvajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		$wppv_label = get_option('wppv_label') != '' ? get_option('wppv_label') : 'Vote this post';
		$wppv_mouse_over = get_option('wppv_mouse_over') != '' ? get_option('wppv_mouse_over') : 'Vote';
		$wppv_refusal = get_option('wppv_refusal') != '' ? get_option('wppv_refusal') : 'Only for registered user';
		$data = array( 'pv_label' => $wppv_label, 'pv_hover' => $wppv_mouse_over, 'pv_refusal' => $wppv_refusal );
		wp_localize_script( 'wppvjs', 'wppv_text', $data );
		wp_enqueue_style('wppvcss', WP_PLUGIN_URL .'/wp-postvoting/css/wp_postvoting.css?v=1.2');
	}
}
add_action('wp_enqueue_scripts', 'wppv_enqueue_scripts');
function wppv_vote_link() {
	$wppvote = "";
	$wppv_label = get_option('wppv_label') != '' ? get_option('wppv_label') : 'Vote this post';
	$wppv_voted = get_option('wppv_voted') != '' ? get_option('wppv_voted') : 'Voted';

	if( get_option('wppv_login') != 'yes' || is_user_logged_in() ) {
		$post_ID = get_the_ID();
		$user_id = get_current_user_id();
		$wppv_cookie = 'wppv_post-id-'.$post_ID;

		$wppvcount = get_post_meta($post_ID, '_wppvcount', true) != '' ? get_post_meta($post_ID, '_wppvcount', true) : '0';

		if(is_user_logged_in()) {
			$wppvuser = get_post_meta($post_ID, '_wppvuser', true) != '' ? get_post_meta($post_ID, '_wppvuser', true) : '0';
			$wppvuser = explode("," , $wppvuser);

			if(in_array($user_id, $wppvuser) || isset($_COOKIE[$wppv_cookie])) {
				$wppvlink =	'<div class="wp_postvote">
								<h4>'.$wppv_voted.'</h4>
								<div class="wp_voted_icon"></div>
								<div class="wp_votecount">' . $wppvcount . '</div>
							</div>';

				$wppvote = '<div id="wppv-'.$post_ID.'">';
				$wppvote .= '<span>'.$wppvlink.'</span>';
				$wppvote .= '</div>';
	 		} else {
				$wppvlink =	'<div class="wp_postvote">
								<h4 id="votetext">'.$wppv_label.'</h4>
								<a onclick="wppvaddvote('.$post_ID.');">'.'<div class="wp_vote_icon"></div>'.'</a>
								<span class="wp_votecount">' . $wppvcount . '</span>
							</div>';

				$wppvote = '<div id="wppv-'.$post_ID.'">';
				$wppvote .= '<span>'.$wppvlink.'</span>';
				$wppvote .= '</div>';
			}
		} else {
			if (!isset($_COOKIE[$wppv_cookie])) {
				$wppvlink =	'<div class="wp_postvote">
								<h4 id="votetext">'.$wppv_label.'</h4>
								<a onclick="wppvaddvote('.$post_ID.');">'.'<div class="wp_vote_icon"></div>'.'</a>
								<span class="wp_votecount">' . $wppvcount . '</span>
							</div>';

				$wppvote = '<div id="wppv-'.$post_ID.'">';
				$wppvote .= '<span>'.$wppvlink.'</span>';
				$wppvote .= '</div>';
			} else {
				$wppvlink =	'<div class="wp_postvote">
								<h4>'.$wppv_voted.'</h4>
								<div class="wp_voted_icon"></div>
								<div class="wp_votecount">' . $wppvcount . '</div>
							</div>';

				$wppvote = '<div id="wppv-'.$post_ID.'">';
				$wppvote .= '<span>'.$wppvlink.'</span>';
				$wppvote .= '</div>';
			}
		}
	} else {
		$post_ID = get_the_ID();
		$wppvcount = get_post_meta($post_ID, '_wppvcount', true) != '' ? get_post_meta($post_ID, '_wppvcount', true) : '0';
		$wppv_cookie = 'wppv_post-id-'.$post_ID;
		if(isset($_COOKIE[$wppv_cookie])) {
			$wppvlink =	'<div class="wp_postvote">
							<h4>'.$wppv_voted.'</h4>
							<div class="wp_voted_icon"></div>
							<div class="wp_votecount">' . $wppvcount . '</div>
						</div>';

			$wppvote = '<div id="wppv-'.$post_ID.'">';
			$wppvote .= '<span>'.$wppvlink.'</span>';
			$wppvote .= '</div>';
 		} else {
			$wppvlink =	'<div class="wp_postvote">
							<h4 id="onlyreg">'.$wppv_label.'</h4>
							<div class="wp_voted_icon"></div>
							<span class="wp_votecount">' . $wppvcount . '</span>
						</div>';

			$wppvote = '<div id="wppv-'.$post_ID.'">';
			$wppvote .= '<span>'.$wppvlink.'</span>';
			$wppvote .= '</div>';
		}
	}
	return $wppvote;
}
function print_wp_postvoting($content) {
	global $post;
	$pid = $post->ID;
	$wppv_top = get_option('wppv_top');
	$wppv_bottom = get_option('wppv_bottom');
	$wppv_postonly = get_option('wppv_postonly');

	if( get_option('wppv_onoff') == 'yes' ) {
		if($wppv_top && $wppv_bottom) {
			$wppv_content = wppv_vote_link().$content.wppv_vote_link();
		} elseif($wppv_top) {
			$wppv_content = wppv_vote_link().$content;
		} elseif($wppv_bottom) {
			$wppv_content = $content.wppv_vote_link();
		} else {
			$wppv_content = $content;
		}

		if($wppv_postonly) {
			if(get_post_type( $pid ) == 'post') {
				return $wppv_content;
			} else {
				return $content;
			}
		} else {
			return $wppv_content;
		}
	} else {
		return $content;
	}
}
add_filter('the_content', 'print_wp_postvoting');
function wppv_count_vote() {
	$results = '';
	global $wpdb;
	$post_ID = $_POST['postid'];
	$wppv_cookie = 'wppv_post-id-'.$post_ID;
	$wppv_content = '842'.wppv_total_voted_posts() + 1;
	$user_id = get_current_user_id();
	$wppv_thanks = get_option('wppv_thanks') != '' ? get_option('wppv_thanks') : 'Thank you';

	$wppvuser = get_post_meta($post_ID, '_wppvuser', true) != '' ? get_post_meta($post_ID, '_wppvuser', true) : '';
	$wppvcount = get_post_meta($post_ID, '_wppvcount', true) != '' ? get_post_meta($post_ID, '_wppvcount', true) : '0';
	$wppvcountNew = $wppvcount + 1;

	if(is_user_logged_in()) {
		if(empty($wppvuser)) {
			$wppvuserNew = $user_id;
		} else {
			$wppvuserNew = $wppvuser.', '.$user_id;
		}
		update_post_meta($post_ID, '_wppvuser', $wppvuserNew);
		setcookie($wppv_cookie, $wppv_content, time() + (86400 * 365 * 10), COOKIEPATH, COOKIE_DOMAIN, false);
	} else setcookie($wppv_cookie, $wppv_content, time() + (86400 * 365 * 10), COOKIEPATH, COOKIE_DOMAIN, false);

	update_post_meta($post_ID, '_wppvcount', $wppvcountNew);

	$results .= '<div class="wp_postvote">
					<h4>'.$wppv_thanks.'</h4>
					<div class="wp_voted_icon"></div>
					<span class="wp_votecount">' . $wppvcountNew . '</span>
				</div>';
	die($results);
}

add_action( 'wp_ajax_nopriv_wppv_count_vote', 'wppv_count_vote' );
add_action( 'wp_ajax_wppv_count_vote', 'wppv_count_vote' );

add_filter( 'manage_edit-post_columns', 'wppv_add_post_columns' );
function wppv_add_post_columns( $columns ) {
	$columns[ 'wppvcount' ] = __( 'Votes' );
	return $columns;
}

function wppv_post_column_row( $column ) {
	if ( $column != 'wppvcount' )
	return;

	global $post;
	$post_id = $post->ID;
	$wppvcount = get_post_meta($post_id, '_wppvcount', true) != '' ? get_post_meta($post_id, '_wppvcount', true) : '0';
	echo $wppvcount;
}
add_action( 'manage_posts_custom_column', 'wppv_post_column_row', 10, 2 );

add_filter( 'manage_edit-post_sortable_columns', 'wppv_post_sort_columns' );
function wppv_post_sort_columns( $columns ) {
	$columns[ 'wppvcount' ] = wppvcount;
	return $columns;
}

add_action( 'load-edit.php', 'wppv_post_edit' );
function wppv_post_edit() {
	add_filter( 'request', 'wppv_sort_posts' );
}

function wppv_sort_posts( $vars ) {
	if ( isset( $vars['post_type'] ) && 'post' == $vars['post_type'] ) {
		if ( isset( $vars['orderby'] ) && 'wppvcount' == $vars['orderby'] ) {
			$vars = array_merge( $vars, array( 'meta_key' => '_wppvcount', 'orderby' => 'meta_value_num' ) );
		}
	}
	return $vars;
}

function wppv_total_voted_posts() {
	$total_vote = 0;
	$tvp_query = new WP_Query( 'meta_key=_wppvcount' );
	while ( $tvp_query->have_posts() ) : $tvp_query->the_post();
		$total_vote = $total_vote + get_post_meta(get_the_ID(), '_wppvcount', true);
	endwhile;
	wp_reset_postdata();
	return $total_vote;
}

function wppv_get_highest_voted_posts($numberofpost) {
	$output = '';
	$the_query = new WP_Query( 'meta_key=_wppvcount&orderby=meta_value_num&order=DESC&posts_per_page='.$numberofpost );
	while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<li><a href="<?php the_permalink() ?>" title="<?php get_the_title(); ?>" rel="bookmark"><?php echo wp_trim_words( get_the_title(), 12, ' ...' ).' ('.get_post_meta(get_the_ID(), '_wppvcount', true).')'; ?></a></li>
	<?php endwhile; wp_reset_postdata();
	return $output;
}

class wppvTopVotedWidget extends WP_Widget {
	function wppvTopVotedWidget() {
		$widget_ops = array('classname' => 'wppvTopVotedWidget', 'description' => 'WPPV Most Voted Posts' );
		$this->WP_Widget('wppvTopVotedWidget','WPPV Most Voted Posts', $widget_ops);
	}
	function form($instance) {
		$defaults = array( 'title' => 'Most Voted Posts', 'numberofposts' => '5' );
		$instance = wp_parse_args( (array) $instance, $defaults );
?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo 'Title:'; ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'numberofposts' ); ?>"><?php echo 'Number of Posts'; ?></label>
			<input id="<?php echo $this->get_field_id( 'numberofposts' ); ?>" name="<?php echo $this->get_field_name( 'numberofposts' ); ?>" value="<?php echo $instance['numberofposts']; ?>" class="widefat" />
		</p>
<?php
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['numberofposts'] = $new_instance['numberofposts'];
		return $instance;
	}
	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<ul>';
			echo wppv_get_highest_voted_posts($instance['numberofposts']);
		echo '</ul>';
		echo $after_widget;
	}
}

function wppv_widget_init() {
	if ( !function_exists('register_widget') )
		return;

	register_widget('wppvTopVotedWidget');
}
add_action('widgets_init', 'wppv_widget_init');

function getPagingQuery() {
	global $wpdb;
	$limit = get_option('wppv_postnum');
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$offset = ($pagenum - 1) * $limit;
	$args = array( 'meta_key' => '_wppvcount',
					'orderby' => 'meta_value_num',
					'order' => 'DESC',
					'posts_per_page' => $limit,
					'offset' => $offset );
	$results = new WP_Query($args);
	return $results;
}

function getPagingLink() {
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = get_option('wppv_postnum') != '' ? get_option('wppv_postnum') : 20;
	$pagingLink = '';
	$totalResults = getPagingQuery()->found_posts;
	$totalPages = @ceil($totalResults / $limit);

	$pagingLink = paginate_links( array(
	    'base' => add_query_arg( 'pagenum', '%#%' ),
		'format' => '',
		'prev_next' => true,
		'prev_text'    => __('&laquo; Previous'),
		'next_text'    => __('Next &raquo;'),
		'total' => $totalPages,
		'current' => $pagenum
	));

	if ( $pagingLink ) {
		echo '<div class="tablenav"><div class="tablenav-pages">' . $pagingLink . '</div></div>';
	}
}
?>