<?php
/*
Plugin Name: Test LMB^Box WordPress Plugin API
Plugin URI: http://lmbbox.com/projects/wordpress-plugin-api/
Description: A Test plugin for LMB^Box WordPress Plugin API.
Author: Thomas Montague
Version: 0.2
Author URI: http://lmbbox.com/
*/

require_once('wordpress-plugin-api.php');

class lmbbox_test_class extends WordPress_Plugin_API {

/* No need to have a class constructor as the parent class will handle everything
	// PHP5 Constructor
	function __construct($name, $version, $folder, $file) {
		parent::__construct($name, $version, $folder, $file);
	}
	// PHP4 Constructor - Just calls PHP5 Constructor
	function lmbbox_test_class($name, $version, $folder, $file) {
		$this->__construct($name, $version, $folder, $file);
	}
*/

	// Setup Options
	function _setup_options() {
		$which_values = array(
								'one'		=> 'Option One',
								'two'		=> 'Option Two',
								'three'		=> 'Option Three',
								'four'		=> 'Option Four',
								'five'		=> 'Option Five',
							);
		$choose_values = array(
								'1'		=> 'Show 1',
								'2'		=> 'Show 2',
								'3'		=> 'Show 3'
							);

		// Plugin Options
		$this->_add_option('author', 'Thomas Montague', 'This is the author.', 'text');
		$this->_add_option('pass', '', 'Set your password.', 'password');
		$this->_add_option('content', '', 'Content:', 'textarea');
		$this->_add_option('show', FALSE, 'Show your info.', 'checkbox');
		$this->_add_option('hide', 'no-show', 'You will not see this!', 'hidden');
		$this->_add_option('which', 'two', 'Select which one you want.', 'select', $which_values);
		$this->_add_option('choose', '3', 'Radio which one you want.', 'radio', $choose_values);

		// Widget Options
		$this->_add_option('test_widget_title', 'LMB^Box Test Widget', 'Title', 'text');
		$this->_add_option('test_widget_message', 'Thanks for stopping by.', 'Message', 'text');
	}

	// Setup Display Items
	function _setup_display() {
		// lmbbox_test
		$this->_add_menu_page('lmbbox_test', 'LMB^Box Test Page', 'LMB^Box Test Menu', 8);
		$this->_add_meta_box('lmbbox_test', 'normal', 'author', 'Author Options Title', 'author,pass');
		$this->_add_meta_box('lmbbox_test', 'normal', 'content', 'Content Option Title', 'content');
		$this->_add_meta_box('lmbbox_test', 'normal', 'show', 'Show Option Title', 'show');
		$this->_add_meta_box('lmbbox_test', 'hidden', 0, 'Hide Option Title', 'hide');
		$this->_add_meta_box('lmbbox_test', 'normal', 'choose', 'Multiple Choice Options', 'which,choose');	

		// lmbbox_test_widget
		$this->_add_widget('lmbbox_test_widget', 'LMB^Box Test Widget', 'test_widget', 'This is the description', 'classname', array('widget_param' => 'Widget: Where does this show up?'));
		$this->_add_widget_control('lmbbox_test_widget', 250, 200, 'lmbbox_', array('widget_control_param' => 'Control: Where does this one show up?'));
		$this->_add_meta_box('lmbbox_test_widget', 'widget', 'main', 'Test Widget Meta Box', 'test_widget_title,test_widget_message');	
	}

	// Setup Hooks
	function _setup_hooks() {

	}

	// Widget Callback
	function test_widget($args){
//		print_r($args); // Uncomment this line to see what is in the $args variable
		extract($args);

		echo $before_widget . $before_title . $this->_get_option('test_widget_title') . $after_title;
		echo '<div id="' . $widget_id . '-message">' . $this->_get_option('test_widget_message') . '</div>';
		echo $after_widget;
	}
}


$lmbbox_test = new lmbbox_test_class('Test LMB^Box WordPress Plugin API', '0.2', 'lmbbox-wordpress-plugin-api', 'lmbbox-test.php');

?>