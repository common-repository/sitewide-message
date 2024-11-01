<?php
/*
Plugin Name: Sitewide Message
Plugin URI: http://thatblogger.co/scheduled-content-wordpress-plugin/
Description: Display a message at the top of every page on your website.
Author: ThatBlogger
Author URI: http://thatblogger.co/
Version: 0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// If this file is called directly, abort.
if(!defined('WPINC')){ die; }

date_default_timezone_set(get_option('timezone_string'));

#####################################################
##	Add Custom CSS to Head
#####################################################
add_action('wp_head', 'tb_add_message_css');
function tb_add_message_css(){
	$options = get_option('tb_msg_settings');
	?>
	<style type="text/css">
    .tb_notification_bar {
        display:block;
        width:100%;
        vertical-align:middle;
        text-align:center;
        z-index:99999;
        font-weight:normal;
        top:0;
        padding:2px 0;
        min-height:30px;
        line-height:30px;
        background-color:<?php esc_attr_e($options['bgcol']); ?>;
        color:#FFF;
        position:absolute;
        -webkit-animation-delay: 1000ms !important;
        animation-duration: 1000ms !important;
        -webkit-animation-duration: 2s;
        animation-duration: 2s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode:both;
        box-shadow:0 0 6px #111;
    }
    
    .tb_fadeInDown {
        -webkit-animation-name: fadeInDown;
        animation-name: fadeInDown;
    }
    
    .tb_inner_bar {
        vertical-align:middle;
    }
    
    .tb_specific_text {
        margin:0 10px;
    }
	
	.tb_specific_text a, .tb_specific_text a:hover {
		color:<?php esc_attr_e($options['txtcol']); ?>;
		text-decoration:none;
	}
	
	.custom-msg {
		margin-top:32px;
	}
	
	.admin-bar .tb_notification_bar {
		margin-top:32px;
	}
    </style>
<?php
}

#####################################################
##	Add Colour Picker
#####################################################
add_action('admin_enqueue_scripts', 'tb_enqueue_color_picker');
function tb_enqueue_color_picker($hook_suffix){
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style('wp-color-picker');
	wp_enqueue_style('jquery-ui-datepicker');
    wp_enqueue_script('my-script-handle', plugins_url('script.js', __FILE__ ), array('wp-color-picker'), false, true);
}

#####################################################
##	create custom plugin settings menu
#####################################################
add_action('admin_menu', 'tb_create_menu');
function tb_create_menu() {	
	add_submenu_page('index.php', 'Site Message', 'Site Message', 'edit_others_pages', 'site-message', 'tb_settings_page');
	add_action('admin_init', 'register_tbticker');
}

function register_tbticker(){
	// Register our settings
	register_setting('tb_msg_settings', 'tb_msg_settings');
}

function tb_settings_page() {
?>
    <div class="wrap">
    <div id="icon-options-general"></div>
    <h2>Website Message</h2>
    
	<?php
    //show saved options message
    if($_REQUEST['settings-updated']) : ?>
    	<div id="message" class="updated below-h2"><p><?php _e('Website message data saved!'); ?></p></div>
    <?php endif; ?>
    
    <form method="post" action="options.php">
		<?php settings_fields('tb_msg_settings'); ?>
		<?php $options = get_option('tb_msg_settings'); ?>
		<?php $ischecked = ($options['status'] == "1" ? "checked" : ""); ?>
        <?php $schedule_statusischecked = ($options['schedule_status'] == "1" ? "checked" : ""); ?>
        <?php $hide_adminischecked = ($options['hide_admin'] == "1" ? "checked" : ""); ?>
        
        <?php $checkbgcol = ($options['bgcol'] == NULL ? "#ff3233" : $options['bgcol']); ?>
        <?php $checktxtcol = ($options['txtcol'] == NULL ? "#ffffff" : $options['txtcol']); ?>
        
        <?php $checkschedule_show = ($options['schedule_show'] == NULL ? date("d/m/Y") : $options['schedule_show']); ?>
        <?php $checkschedule_hide = ($options['schedule_hide'] == NULL ? date("d/m/Y") : $options['schedule_hide']); ?>
        
        <p><?php _e('By turning on or off the site message, you will either show or hide the message on your website.') ?></p>
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Message On / Off</th>
                <td><input name="tb_msg_settings[status]" type="checkbox" id="tb_msg_settings[status]" value="1" <?php esc_attr_e($ischecked); ?> /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Message Text</th>
                <td><textarea name="tb_msg_settings[text]" cols="60" rows="5" id="tb_msg_settings[text]" class="regular-text"><?php esc_attr_e($options['text']); ?></textarea></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Message URL</th>
                <td><input type="text" name="tb_msg_settings[url]" value="<?php esc_attr_e($options['url']); ?>" class="regular-text" /> <p class="description"><?php _e('Leave blank to link to your website.') ?></p></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Link Location</th>
                <td>
                    <select name="tb_msg_settings[linkloc]" id="tb_msg_settings[linkloc]">
                    	<option <?php echo ($options['linkloc'] == "_self" ? "selected ":""); ?>value="_self">Same Window</option>
                    	<option <?php echo ($options['linkloc'] == "_blank" ? "selected ":""); ?>value="_blank">New Window</option>
                    </select>
                    <p class="description"><?php _e('Should the link open in a new window or stay in the same window?') ?></p></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Message Background Colour</th>
                <td><input type="text" name="tb_msg_settings[bgcol]" value="<?php esc_attr_e($checkbgcol); ?>" class="regular-text colorfield" data-default-color="#ff3233" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Message Text Colour</th>
                <td><input type="text" name="tb_msg_settings[txtcol]" value="<?php esc_attr_e($checktxtcol); ?>" class="regular-text colorfield" data-default-color="#ffffff" /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Hide When Logged In?</th>
                <td><input name="tb_msg_settings[hide_admin]" type="checkbox" id="tb_msg_settings[hide_admin]" value="1" <?php esc_attr_e($hide_adminischecked); ?> /></td>
            </tr>
        </table>
        
        <hr />
        
        <h2>Schedule Message</h2>
        
        <p><?php _e('NOTE: The below dates will not work unless schedule is turned on.') ?></p>
        
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Schedule Message On / Off</th>
                <td><input name="tb_msg_settings[schedule_status]" type="checkbox" id="tb_msg_settings[schedule_status]" value="1" <?php esc_attr_e($schedule_statusischecked); ?> /></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Display Date</th>
                <td><input type="date" id="tb_msg_settings[schedule_show]" name="tb_msg_settings[schedule_show]" value="<?php esc_attr_e($checkschedule_show); ?>" class="datepicker" /> <p class="description"><?php _e('Leave blank to display from now. Or set the date to display the message.') ?></td>
            </tr>
            
            <tr valign="top">
                <th scope="row">Expire Date</th>
                <td><input type="date" id="tb_msg_settings[schedule_hide]" name="tb_msg_settings[schedule_hide]" value="<?php esc_attr_e($checkschedule_hide); ?>" class="datepicker" /> <p class="description"><?php _e('Leave blank to never expire. Or set the date for the message to hide from your website.') ?></p></td>
            </tr>
        </table>
        
        <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
        <!--        
        <br /><br />
        
        Current: <?php echo current_time('Y-m-d'); ?><br>
        Show: <?php echo $options['schedule_show']; ?><br>
        Hide: <?php echo $options['schedule_hide']; ?><br>
        <br>
        <br>
        Current Timestamp: <?php echo current_time('timestamp'); ?><br>
        Show Timestamp: <?php echo strtotime($options['schedule_show']); ?><br>
        Hide Timestamp: <?php echo strtotime($options['schedule_hide']); ?><br>
        -->
    </form>
    </div>
<?php }

#####################################################
##	Display the message on the website
#####################################################
function tb_put_into_action(){
	$options = get_option('tb_msg_settings');
	
	if($options['url'] == NULL){
		$nurl = get_bloginfo("url");
	}else{
		$nurl = $options['url'];
	}
	
	// Backwards Compat
	if($options['linkloc'] == NULL){
		$options['linkloc'] = "_self";
	}
	
	if($options['status']){
		// Check against date stuff
		if($options['schedule_status']){
			
			$currentDate = current_time('timestamp');
			$schedule_showDate = strtotime($options['schedule_show']);
			$schedule_hideDate = strtotime($options['schedule_hide']);
			
			// Check if there is no expiery what so ever
			if($options['schedule_hide'] == NULL){
				// No expiery check to see if the content should be shown yet
				if($currentDate >= $schedule_showDate){
					// Return the content
					echo showMessage($nurl, $options['linkloc'], $options['text']);
				}
			}else{
				// Expiery set so lets check if the content has expired.
				if($currentDate < $schedule_hideDate){
					if($currentDate >= $schedule_showDate){
						// Return the content
						echo showMessage($nurl, $options['linkloc'], $options['text']);
					}
				}
			}
			
		}else{
			// If not selected show
			echo showMessage($nurl, $options['linkloc'], $options['text']);
		}
	}
}

function showMessage($nurl, $linkloc, $text){
	$options = get_option('tb_msg_settings');
	if($options['hide_admin'] == 1){
		if(is_user_logged_in()){
			return;
		}
	}
	
	return "<div class=\"tb_notification_bar tb_fadeInDown\"><div class=\"tb_inner_bar\"><span class=\"tb_specific_text\"><a href=\"".$nurl."\" target=\"".$linkloc."\">".$text."</a></span></div></div>";
}
add_action('wp_footer', 'tb_put_into_action');

#####################################################
##	Fix when a user is logged in
#####################################################
function tb_body_class($classes){
	$options = get_option('tb_msg_settings');
	if($options['status']){
		
		if($options['hide_admin'] == 1){
			if(is_user_logged_in()){
				return $classes;
			}
		}
	
		// Check against date stuff
		if($options['schedule_status']){
			
			$currentDate = current_time('timestamp');
			$schedule_showDate = strtotime($options['schedule_show']);
			$schedule_hideDate = strtotime($options['schedule_hide']);
			
			// Check if there is no expiery what so ever
			if($options['schedule_hide'] == NULL){
				// No expiery check to see if the content should be shown yet
				if($currentDate >= $schedule_showDate){
					// Return the content
					$classes[] = 'custom-msg';
				}
			}else{
				// Expiery set so lets check if the content has expired.
				if($currentDate < $schedule_hideDate){
					if($currentDate >= $schedule_showDate){
						// Return the content
						$classes[] = 'custom-msg';
					}
				}
			}
			
		}else{
			// If not selected show
			$classes[] = 'custom-msg';
		}
	}
	
    return $classes;
}
add_filter('body_class', 'tb_body_class');