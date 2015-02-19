<?php

/*
Plugin Name: WPDemoWolf
Plugin URI: https://mainehost.com/wordpress-plugins/
Description: Allows you to display DemoWolf tutorials with a shortcode.
Author: Maine Hosting Solutions
Version: 1.0.1
Author URI: http://mainehost.com/
*/


if(!class_exists('wpdemowolf')) {
	/**
	 * @package default
	 */
	class wpdemowolf {
		/**
		 * Class constructor to setup WP stuff.
		 */
		function __construct() { # Setup the stuff we need to operate
			# Shortcodes
			add_shortcode('wpdemowolf', array($this,'show'));

			# Actions
			add_action('admin_menu', array($this,'menu'));	
		}
		/**
		 * Sets up the admin menu under Settings.
		 */
		function menu() {
			add_submenu_page('options-general.php','WPDemoWolf','WPDemoWolf','administrator', plugin_basename(dirname(__FILE__)), array($this,'admin'));
		}
		/**
		 * The settings page for this plugin.
		 */
		function admin() {
			$code = '<h1>WPDemoWolf</h1>';

			if($_POST) {
				update_option('wpdw_uri', $_POST['wpdw_uri']);
				update_option('wpdw_path', $_POST['wpdw_path']);
				$code .= '<div style="color: green; font-weight: bold;">Options Updated</div>';
			}

			$code .= $this->options();
			echo $code;
		}
		/**
		 * The options/settings for the plugin. Separated out in case more areas are added to the plugin.
		 * @return string The form itself and documentation.
		 */
		function options() {
			$wpdw_uri = get_option('wpdw_uri');
			$wpdw_path = get_option('wpdw_path');

			$code = '
				<h2>Requirements</h2>
				1. Have an active account at <a href="http://demowolf.com" target="_blank">DemoWolf</a><br />

				<h2>DemoWolf Setup</h2>
				1. In your DemoWolf Client Area Dashboard under My Purchases (or My Subscriptions) > Display Options, set Run Mode to PHP Include.<br />
				2. Again, in your DemoWolf Client Area Dashboard under My Purchases (or My Subscriptions) > Download Viewer<br />
				3. Unzip the Viewer then rename the folder/dir to something sensible like <i>viewer</i>, then upload it to the root of your WordPress Installation<br />
				<p>
				</p>
				<h2>Plugin Setup</h2>
				<p>
				Below you need to provide the URI to the DemoWolf Viewer. This path is relative to your WordPress installation. For example, if WordPress is installed at: http://mainehost.com and the folder I uploaded the Viewer to is in a folder called <i>viewer</i> then in the URI field below I would enter: /viewer/ (make sure it ends in a slash)
				</p>
				<p>
				The other field you need to provide is the Filesystem Path to DemoWolf. This is the filesystem path for your server to your DemoWolf Viewer folder, IE: /home/youraccount/public_html/viewer/ (make sure it ends in a slash)<br />
				</p>
				<p>
				On any page you would like your list of DemoWolf Tutorials to show on add the following shortcode: [wpdemowolf]
				<p>
				<form method="post" action="">
				<b>URI to DemoWolf:</b><br />
				<input type="text" name="wpdw_uri" value="' . $wpdw_uri . '" size="50">
				</p>
				<p>
				<b>Filesystem Path to DemoWolf:</b><br />
				<i>Detected WordPress Filesystem Path:</i> ' . get_home_path() . '<br />
				<input type="text" name="wpdw_path" value="' . $wpdw_path . '" size="50"> <input type="submit" value="Save" class="button button-primary">
				</form>
				</p>';

			return $code;
		}
		/**
		 * Displays the tutorials on the page - shortcode handler.
		 * @param array $atts Attributes from the shortcode.
		 * @return string The tutorial content from DemoWolf.
		 */
		function show($atts) {
			$code = '<div style="display: none;">';
			$uri_to_dw = get_option('wpdw_uri'); // URI (web address) to the folder -- be sure to put a trailing slash.
			$path_to_dw = get_option('wpdw_path'); // FILESYSTEM Path to the Tutorial Viewer folder -- be sure to put a trailing slash.
			$tutorials_php = esc_url($_SERVER['REQUEST_URI']); // Point the Tutorial Viewer to the WordPress page.

			define('DEMOWOLF_INTEGRATION', true);

			ob_start(); // DemoWolf uses echo so let's buffer.

			if(!defined('DEMO_MODE')) {
				include $path_to_dw . 'tutorials.php';

				$code .= ob_get_clean();
				$code .= $dw_head;
			}

			$code .= '</div>';

			if(defined('DEMO_MODE')) {
				$cwd = getcwd();
				chdir($path_to_dw);

				include 'demo.php';

				$code .= ob_get_clean();
				chdir($cwd);
			}
			else {
				$code .= $dw_content;
			}	

			return $code;		
		}
	}
}

$wpdw = new wpdemowolf();

?>
