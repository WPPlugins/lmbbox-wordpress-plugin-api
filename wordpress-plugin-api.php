<?php
/*
Plugin Name: LMB^Box WordPress Plugin API
Plugin URI: http://lmbbox.com/projects/wordpress-plugin-api/
Description: A WordPress Plugin API class that allows plugin developers to have a standard management class for their plugin.
Author: Thomas Montague
Version: 0.2
Author URI: http://lmbbox.com/
*/


/**
 * LMB^Box WordPress Plugin API class
 *
 * Use this class to be the base to plugins for WordPress.
 *
 * @category   PluginAPI
 * @package    LMB^Box WordPress Plugin API
 * @author     Thomas Montague <lmbbox@gmail.com>
 * @copyright  2008 LMB^Box
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    Release: 0.2
 * @link       http://lmbbox.com/projects/wordpress-plugin-api/
 */
class WordPress_Plugin_API {

	// protected Variables
	var $_options						= array();

	// private const Variables
	var $__CLASS						= 'LMB^Box WordPress Plugin API';
	var $__SITE							= 'http://lmbbox.com/projects/wordpress-plugin-api/';
	var $__VERSION						= '0.2';
	var $__BUILD						= '20080505';
	var $__PARENT_MENUS					= array(
												'topmenu'		=> 'admin.php',
												'dashboard'		=> 'index.php',
												'write'			=> 'post-new.php',
												'manage'		=> 'edit.php',
												'comments'		=> 'edit-comments.php',
												'blogroll'		=> 'link-manager.php',
												'presentation'	=> 'themes.php',
												'plugins'		=> 'plugins.php',
												'users'			=> 'users.php',
												'options'		=> 'options-general.php'
											);
	var $__ACCESS_LEVELS				= array(
												'administrator'		=> 8,
												'editor'			=> 3,
												'author'			=> 2,
												'contributor'		=> 1,
												'subscriber'		=> 0
											);

	// private Variables
	var $__plugin_name					= NULL;
	var $__plugin_version				= NULL;
	var $__plugin_folder				= NULL;
	var $__plugin_file					= NULL;
	var $__menu_pages					= array();
	var $__widgets						= array();
	var $__meta_boxes					= array();


	/**
	 * Sets inital settings for class
	 *
	 * PHP 5 constructor.
	 *
	 * @param string $name  the display name of plugin
	 * @param string $version  the version of the plugin
	 * @param string $folder  the folder name where the plugin file is located
	 * @param string $file  the file name of the plugin
	 *
	 * @return void
	 *
	 * @access public
	 */
	function __construct($name, $version, $folder, $file) {
		$this->__plugin_name = $name;
		$this->__plugin_version = $version;
		$this->__plugin_folder = $folder;
		$this->__plugin_file = $file;

		// Needs to be set in the class extending this API class
		$this->_setup_options();
		$this->_setup_display();

		// Handle Options ...
		$this->_get_options();
		$this->__save_options();

		// Add WordPress Actions and Filters
//		register_activation_hook($this->__plugin_folder . '/' . $this->__plugin_file, array(&$this, 'activate'));
//		register_deactivation_hook($this->__plugin_folder . '/' . $this->__plugin_file, array(&$this, 'deactivate'));
//		$this->_add_hook('action', 'shutdown', array(&$this, '__shutdown'));
		$this->_add_hook('action', 'admin_menu', array(&$this, '__register_menu_pages'));
		$this->_add_hook('action', 'plugins_loaded', array(&$this, '__register_widgets'));
		$this->_setup_hooks();
	}

	/**
	 * Sets inital settings for class
	 *
	 * PHP 4 constructor. Just calls PHP 5 constructor.
	 *
	 * @param string $name  the display name of plugin
	 * @param string $version  the version of the plugin
	 * @param string $folder  the folder name where the plugin file is located
	 * @param string $file  the file name of the plugin
	 *
	 * @return void
	 *
	 * @access public
	 */
	function WordPress_Plugin_API($name, $version, $folder, $file) {
		$this->__construct($name, $version, $folder, $file);
	}

	/**
	 * Uninstalls plugin from WordPress
	 *
	 * Called to delete stored options, deactivate plugin, and redirect to 
	 * WordPress plugin deactivated page.
	 *
	 * @return void
	 *
	 * @access private
	 */
	function __uninstall_plugin() {
		delete_option($this->__plugin_folder);
		deactivate_plugins($this->__plugin_folder . '/' . $this->__plugin_file);
		wp_redirect('plugins.php?deactivate=true');
	}

	/**
	 * Shutdown the plugin
	 *
	 * Runs shutdown tasks for plugin
	 *
	 * @return void
	 *
	 * @access private
	 */
	function __shutdown() {

	}

	/**
	 * Sets up plugin options
	 *
	 * Plugin extending this class should override this function. All calls 
	 * to _add_option() function should be made here.
	 *
	 * @return void
	 *
	 * @access protected
	 */
	function _setup_options() {}

	/**
	 * Sets up all display items
	 *
	 * Plugin extending this class should override this function. All calls 
	 * to _add_menu_page(), _add_widget(), _add_widget_control(), and 
	 * _do_meta_boxes() functions should be made here.
	 *
	 * @return void
	 *
	 * @access protected
	 */
	function _setup_display() {}

	/**
	 * Sets up plugin hooks
	 *
	 * Plugin extending this class should override this function. All calls 
	 * to _add_hook() or _remove_hook() function should be made here.
	 *
	 * @return void
	 *
	 * @access protected
	 */
	function _setup_hooks() {}

	/**
	 * Registers a function into WordPress
	 *
	 * Registers a Wordpress action, filter, widget or widget control via 
	 * WordPress's normal functions.
	 *
	 * @param string $type  the type of hook registering
	 * @param string $hook  the hook to register to or the name of the widget
	 * @param string|array $function  the callback function to register
	 * @param int $priority  the hook priority level
	 * @param int $accepted_args  the number of accepted args for the function
	 *
	 * @return bool
	 *
	 * @access protected
	 */
	function _add_hook($type, $hook, $function, $priority = 10, $accepted_args = 1) {
		switch ($type) {
			case 'action':
				return add_action($hook, $function, $priority, $accepted_args);
				break;
			case 'filter':
				return add_filter($hook, $function, $priority, $accepted_args);
				break;
/*
			case 'widget':
			    register_sidebar_widget($hook, $function);
				return TRUE;
				break;
			case 'widget-control':
			    register_widget_control($hook, $function);
				return TRUE;
				break;
*/
			default:
				return FALSE;
				break;
		}
	}

	/**
	 * Unregisters a function from WordPress
	 *
	 * Unregisters a Wordpress action, filter, widget or widget control via 
	 * WordPress's normal functions.
	 *
	 * @param string $type  the type of hook registering
	 * @param string $hook  the hook to register to or the name of the widget
	 * @param string|array $function  the callback function to register
	 *
	 * @return bool
	 *
	 * @access protected
	 */
	function _remove_hook($type, $hook, $function) {
		switch ($type) {
			case 'action':
				return remove_action($hook, $function);
				break;
			case 'filter':
				return remove_filter($hook, $function);
				break;
/*
			case 'widget':
			    unregister_sidebar_widget($hook);
				return TRUE;
				break;
			case 'widget-control':
			    unregister_widget_control($hook);
				return TRUE;
				break;
*/
			default:
				return FALSE;
				break;
		}
	}

	/**
	 * Adds an option to the options array
	 *
	 * Creates a new key in the options array with the params.
	 *
	 * @param string $id  the id of option
	 * @param string $value  the value of the option
	 * @param string $description  the description of the option
	 * @param string $type  the input type of the option
	 * @param array $values  the possiable values of the option
	 * @param string $callback  the callback display function for the option
	 *
	 * @return void
	 *
	 * @access protected
	 */
	function _add_option($id, $value, $description, $type, $values = NULL, $callback = '__display_option') {
		$this->_options[$id] = array(
										'value' 		=> $value,
										'values'		=> $values,
										'description' 	=> $description,
										'type' 			=> $type,
										'callback'		=> $callback
									);
	}

	/**
	 * Gets options stored in the database
	 *
	 * Merges current options array with options stored in database.
	 *
	 * @return bool
	 *
	 * @access protected
	 */
	function _get_options() {
		$options = get_option($this->__plugin_folder);
		if (is_array($options) && !empty($options)) {
			$this->_options = (is_array($this->_options) && !empty($this->_options)) ? array_merge($this->_options, $options) : $options;
			return TRUE;
		} else { return FALSE; }
	}

	/**
	 * Gets options stored in the database
	 *
	 * Merges current options array with options stored in database.
	 *
	 * @param string $id  the id of option
	 *
	 * @return mixed|bool
	 *
	 * @access protected
	 */
	function _get_option($id) {
		if (!is_array($this->_options) && empty($this->_options)) {
			if ($this->_get_options() === FALSE) { return FALSE; }
		}
		if (isset($this->_options[$id]['value'])) {
			return $this->_options[$id]['value'];
		} else { return FALSE; }
	}

	/**
	 * Updates options to the database
	 *
	 * Updates current options array with options param and updates to 
	 * in database.
	 *
	 * @param array $options  the options to update with values
	 *
	 * @return bool
	 *
	 * @access protected
	 */
	function _update_options($options) {
		if (!is_array($this->_options) && empty($this->_options)) {
			if ($this->_get_options() === FALSE) { return FALSE; }
		}

		if (is_array($options) && !empty($options)) {
			foreach ($this->_options as $option_id => $option_values) {
				if (isset($options[$option_id])) {
					$this->_options[$option_id]['value'] = $options[$option_id];
				} elseif (!isset($options[$option_id]) && $option_values['type'] == 'checkbox') {
					if (is_array($this->__meta_boxes[$options['__page_id']]) && !empty($this->__meta_boxes[$options['__page_id']])) {
						foreach ($this->__meta_boxes[$options['__page_id']] as $context) {
							foreach ($context as $box) {
								$box_options = explode(',', $box['options']);
								if (array_search($option_id, $box_options) !== FALSE) {
									$this->_options[$option_id]['value'] = FALSE;
								}
							}
						}
					}
					if (is_array($options['widget-id']) && !empty($options['widget-id'])) {
						foreach ($options['widget-id'] as $widget_id) {
							if (is_array($this->__meta_boxes[$widget_id]) && !empty($this->__meta_boxes[$widget_id])) {
								foreach ($this->__meta_boxes[$widget_id] as $context) {
									foreach ($context as $box) {
										$box_options = explode(',', $box['options']);
										if (array_search($option_id, $box_options) !== FALSE) {
											$this->_options[$option_id]['value'] = FALSE;
										}
									}
								}
							}
						}
					}
				}
			}
			return update_option($this->__plugin_folder, $this->_options);
		} else { return FALSE; }
	}

	/**
	 * Saves options to the database
	 *
	 * Saves current options array to in database. Should only be called 
	 * when first adding option to database.
	 *
	 * @return bool
	 *
	 * @access private
	 */
	function __save_options() {
		if (is_array($this->_options) && !empty($this->_options)) {
			add_option($this->__plugin_folder, $this->_options, $this->__plugin_name . $this->__plugin_version);
			return TRUE;
		} else { return FALSE; }
	}

	/**
	 * Resets options to defaults
	 *
	 * Resets current options array with options param and updates to 
	 * in database.
	 *
	 * @param array $options  the options to reset with values
	 *
	 * @return bool
	 *
	 * @access private
	 */
	function __reset_options($options) {
		if (!is_array($this->_options) && empty($this->_options)) {
			if ($this->_get_options() === FALSE) { return FALSE; }
		}

		if (is_array($options) && !empty($options)) {
			$current_options = $this->_options;
			unset($this->_options);
			$this->_setup_options();
			$default_options = $this->_options;
			$this->_options = $current_options;

			foreach ($this->_options as $option_id => $option_values) {
				if (isset($options[$option_id])) {
					$this->_options[$option_id]['value'] = $default_options[$option_id]['value'];
				}
			}
			return update_option($this->__plugin_folder, $this->_options);
		} else { return FALSE; }
	}

	/**
	 * Adds a menu page to the __menu_pages array
	 *
	 * Creates a new key in the __menu_pages array with the params.
	 *
	 * @param string $id  the id of the menu page
	 * @param string $page_title  the page display title of the menu page
	 * @param string $menu_title  the menu display title of the menu page
	 * @param int|string $access_level  the access level of the menu page
	 * @param string $parent  the parent page of the menu page
	 * @param bool $show_uninstall  whether to display the uninstall button or not
	 * @param string $file  the file name of the menu page
	 *
	 * @return void
	 *
	 * @access protected
	 */
	function _add_menu_page($id, $page_title, $menu_title, $access_level, $parent = 'plugins', $show_uninstall = TRUE, $file = NULL) {
		if (is_null($file)) { $file = $this->__plugin_folder . '/' . $this->__plugin_file; }
		$this->__menu_pages[$id] = array('id' => $id, 'page_title' => $page_title, 'menu_title' => $menu_title, 'access_level' => $access_level, 'parent' => $this->__PARENT_MENUS[$parent], 'show_uninstall' => $show_uninstall, 'file' => $file);
	}

	/**
	 * Sets up menu pages from __menu_pages array
	 *
	 * Handles the 'admin_menu' action callback function.
	 *
	 * @return void
	 *
	 * @access private
	 */
	function __register_menu_pages() {
		if (is_array($this->__menu_pages) && !empty($this->__menu_pages)) {
			foreach ($this->__menu_pages as $menu) {
				if ($menu['parent'] == 'admin.php') {
					add_menu_page($menu['page_title'], $menu['menu_title'], $menu['access_level'], $menu['file'], array(&$this, '__display_menu_page'));
				} else {
					add_submenu_page($menu['parent'], $menu['page_title'], $menu['menu_title'], $menu['access_level'], $menu['file'], array(&$this, '__display_menu_page'));			
				}
			}
		}
	}




	/**
	 * Adds a widget to the __widgets array
	 *
	 * Creates a new key in the __widgets array with the params.
	 *
	 * @param string $widget_id  the id of the widget
	 * @param string $widget_name  the name / title of the widget
	 * @param string $callback  the callback function for the widget
	 * @param string $description  the description of the widget
	 * @param string $classname  the classname of the widget
	 * @param array $params  the args to pass to the widget control
	 *
	 * @return void
	 *
	 * @access protected
	 */
	function _add_widget($widget_id, $widget_name, $callback, $description = NULL, $classname = NULL, $params = NULL) {
		$this->__widgets[$widget_id] = array('widget_id' => $widget_id, 'widget_name' => $widget_name, 'description' => $description, 'callback' => $callback, 'classname' => $classname, 'params' => $params);
	}

	/**
	 * Adds a widget control to the __widgets array
	 *
	 * Creates a control key in the __widgets[$widget_id] array with the params.
	 *
	 * @param string $widget_id  the id of the widget
	 * @param int $width  the width of the widget control
	 * @param int $height  the height of the widget control
	 * @param array $params  the params to pass to the widget control
	 *
	 * @return bool
	 *
	 * @access protected
	 */
	function _add_widget_control($widget_id, $width = NULL, $height = NULL, $id_base = NULL, $params = array()) {
		if (is_array($this->__widgets[$widget_id]) && !empty($this->__widgets[$widget_id])) {
			$params['widget_id'] = $widget_id;
			$this->__widgets[$widget_id]['control'] = array('width' => $width, 'height' => $height, 'id_base' => $id_base, 'params' => $params);
			return TRUE;
		} else { return FALSE; }
	}

	/**
	 * Sets up widgets from __widgets array
	 *
	 * Handles the 'plugins_loaded' action callback function.
	 *
	 * @return void
	 *
	 * @access private
	 */
	function __register_widgets() {
		if (is_array($this->__widgets) && !empty($this->__widgets)) {
			foreach ($this->__widgets as $widget) {
				$widget_ops = array('classname' => $widget['classname'], 'description' => $widget['description']);
				wp_register_sidebar_widget($widget['widget_id'], $widget['widget_name'], array(&$this, $widget['callback']), $widget_ops, $widget['params']);
				if (is_array($widget['control']) && !empty($widget['control'])) {
					$widget_ops = array('width' => $widget['control']['width'], 'height' => $widget['control']['height'], 'id_base' => $widget['control']['id_base']);
					wp_register_widget_control($widget['widget_id'], $widget['widget_name'], array(&$this, '__display_widget_control'), $widget_ops, $widget['control']['params']);
				}
			}
		}
	}

	/**
	 * Displays widget in __widgets array
	 *
	 * Handles displaying widget in __widgets array.
	 *
	 * @param array $args  the args of the widget
	 *
	 * @return void
	 *
	 * @access private
	 */
	function __display_widget($args){
		extract($args);

		if (isset($_POST['lmbbox_newsletters_register'])) {
			$message = $this->__register_user();
		} else {
			$message = $this->_get_option('widget_message');
		}

		echo $before_widget . $before_title . $this->_get_option('widget_name') . $after_title;
		echo '<div id="' . $widget_id . '">' . $message . '</div>';
		echo $widget_content;
		if ($display_form === TRUE) { echo '<form name="' . $widget_id . '-form" id="' . $widget_id . '-form" method="POST">' . $form_content . '</form>'; }
		echo $after_widget;
	}

	/**
	 * Displays widget control in __widgets array
	 *
	 * Handles displaying widget control in __widgets array.
	 *
	 * @param array $args  the args of the widget control
	 *
	 * @return void
	 *
	 * @access private
	 */
	function __display_widget_control($args) {
		extract($args);

//		if (@array_search($widget_id, $_POST['widget-id']) !== FALSE) { $this->_update_options($_POST); }
		if ($_POST[$widget_id . '-submit']) { $this->_update_options($_POST); }

		echo '<div>';
		echo '<input type="hidden" name="' . $widget_id . '-submit" id="' . $widget_id . '-submit" value="1" />';
		$this->__do_meta_boxes($widget_id, 'widget');
		echo '</div>';
	}






	/**
	 * Adds a meta box to the __meta_boxes array
	 *
	 * Creates a new key in the __meta_boxes array with the params.
	 *
	 * @param string $id  the id of where the meta box is added
	 * @param string $context  the context name of where the meta box is added
	 * @param string $box_id  the id of the meta box
	 * @param string $title  the display title of the meta box
	 * @param string $option_names  a comma separated list of option's names
	 *
	 * @return void
	 *
	 * @access protected
	 */
	function _add_meta_box($id, $context, $box_id, $title, $option_names) {
		$this->__meta_boxes[$id][$context][$box_id] = array('title' => $title, 'options' => $option_names);
	}

	/**
	 * Displays meta boxes in __meta_boxes array
	 *
	 * Handles displaying meta boxes setup in __meta_boxes array.
	 *
	 * @param string $id  the id of the meta box to display
	 * @param string $context  the context name of the meta box to display
	 *
	 * @return void
	 *
	 * @access private
	 */
	function __do_meta_boxes($id, $context) {
		if (is_array($this->__meta_boxes[$id][$context]) && !empty($this->__meta_boxes[$id][$context])) {
			foreach ($this->__meta_boxes[$id][$context] as $box ) {
				if (!empty($box['options'])) {
					if ($context != 'hidden' && $context != 'widget') { echo '<tr valign="top"><th scope="row">' . $box['title'] . '</th><td>'; }
					$options = explode(',', $box['options']);
					foreach ($options as $option_id) {
						call_user_func(array(&$this, $this->_options[$option_id]['callback']), $option_id);
					}
					if ($context != 'hidden' && $context != 'widget') { echo '</td></tr>'; }
				}
			}
		}
	}




	/**
	 * Displays menu page in __menu_pages array
	 *
	 * Displays menu page in __menu_pages array by matching page_hook. 
	 * Only one menu page per parent per file is allowed.
	 *
	 * @return void
	 *
	 * @access private
	 */
	function __display_menu_page() {
		// get the current page settings
		foreach ($this->__menu_pages as $menu_page) {
			if ($GLOBALS['page_hook'] == get_plugin_page_hook($menu_page['file'], $menu_page['parent'])) { break; }
		}

		if (isset($_POST['__plugin_options_update'])) {
			print_r($_POST);
			echo ($this->_update_options($_POST) === TRUE) ? '<div id="message" class="updated fade"><p><strong>' .  __('Options saved.') . '</strong></p></div>' : '<div id="message" class="error"><p><strong>' .  __('Options failed to save.') . '</strong></p></div>';
		} elseif (isset($_POST['__plugin_options_reset'])) {
			echo ($this->__reset_options($_POST) === TRUE) ? '<div id="message" class="updated fade"><p><strong>' .  __('Options reset.') . '</strong></p></div>' : '<div id="message" class="error"><p><strong>' .  __('Options failed to reset.') . '</strong></p></div>';
		} elseif (isset($_POST['__plugin_options_uninstall']) && $menu_page['show_uninstall'] === TRUE) {
			$this->__uninstall_plugin();
		}

?>
	<div class="wrap" id="<?php echo $menu_page['id']; ?>-wrap">
		<h2><?php echo $menu_page['page_title']; ?></h2>
		<form method="post">
        	<input type="hidden" name="__page_id" id="__page_id" value="<?php echo $menu_page['id']; ?>" />
			<?php $this->__do_meta_boxes($menu_page['id'], 'hidden'); ?>
			<table class="form-table">
				<?php $this->__do_meta_boxes($menu_page['id'], 'normal'); ?>
			</table>

<?php //$this->__do_meta_boxes($menu_page['id'], 'sidebar'); ?>

			<p class="submit">
				<input type="submit" name="__plugin_options_update" value="<?php _e('Save Changes') ?>" />
				<input type="submit" name="__plugin_options_reset" value="<?php _e('Reset Options') ?>" onclick='return( confirm( "Do you really want to reset these options to their default values?" ) );' />
			<?php if ($menu_page['show_uninstall'] === TRUE) { ?>
				<br /><br />
				<input type="submit" name="__plugin_options_uninstall" value="<?php _e('Uninstall Plugin') ?>" onclick='return( confirm( "Do you really want to uninstall this plugin and delete all stored information from the database?" ) );' />
			<?php } ?>
			</p>
		</form>
	</div>
<?php

   }

	/**
	 * Displays option form code
	 *
	 * Displays form code for option based on option type.
	 *
	 * @param string $option_id  the option id to display
	 *
	 * @return void
	 *
	 * @access private
	 */
	function __display_option($option_id) {
		$option = $this->_options[$option_id];
		if (is_array($option) && !empty($option)) {
			switch ($option['type']) {
				case 'hidden':
					$option_code = '<input type="hidden" name="' . $option_id . '" id="' . $option_id . '" value="' . $option['value'] . '" />';
					break;
				case 'text':
					$option_code = '<input type="text" name="' . $option_id . '" id="' . $option_id . '" value="' . $option['value'] . '" /> ' . $option['description'] . '<br />';
					break;
				case 'password':
					$option_code = '<input type="password" name="' . $option_id . '" id="' . $option_id . '" value="' . $option['value'] . '" /> ' . $option['description'] . '<br />';
					break;
				case 'textarea':
					$option_code = '<p>' . $option['description'] . '</p><p><textarea name="' . $option_id . '" id="' . $option_id . '" style="width: 98%; font-size: 12px;" class="code" cols="60" rows="10"> ' . $option['value'] . '</textarea></p>';
					break;
				case 'checkbox':
					$option_code = '<label for="' . $option_id . '"><input type="checkbox" name="' . $option_id . '" id="' . $option_id . '" value="1" ' . ($option['value'] ? 'checked="checked" ' : '') . '/> ' . $option['description'] . '</label><br />';
					break;
				case 'file':
					$option_code = '<input type="file" name="' . $option_id . '" id="' . $option_id . '" />' . $option['description'] . '<br />';
					break;
				case 'select':
					if (is_array($option['values']) && !empty($option['values'])) {
						$option_code = '<select name="' . $option_id . '" id="' . $option_id . '">';
						foreach ($option['values'] as $option_value => $option_value_label) {
							$option_code .= '<option value="' . $option_value . '" ' . ($option['value'] == $option_value ? 'selected="selected" ' : '') . '>' . $option_value_label . '</option>';
						}
						$option_code .= '</select>';
						if (!empty($option['description'])) { $option_code .= '<p>' . $option['description'] . '</p>'; } else { echo '<br />'; }
					} else { $option_code = ''; }
					break;
				case 'radio':
					if (is_array($option['values']) && !empty($option['values'])) {
						foreach ($option['values'] as $option_value => $option_value_label) {
							$option_code .= '<p><label for="' . $option_id . '-' . $option_value . '"><input type="radio" name="' . $option_id . '" id="' . $option_id . '-' . $option_value . '" value="' . $option_value . '" ' . ($option['value'] == $option_value ? 'checked="checked" ' : '') . '/> ' . $option_value_label . '</label></p>';
						}
						if (!empty($option['description'])) { $option_code .= '<p>' . $option['description'] . '</p>'; } else { echo '<br />'; }
					} else { $option_code = ''; }
					break;
				default:
					$option_code = '';
					break;
			}
			echo $option_code;
		}
	}

}

?>