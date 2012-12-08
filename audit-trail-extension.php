<?php
/*
Plugin Name: Audit Trail Extension
Description: Adds custom events to the Audit Trail plugin
Version: 0.1
Author: Ian Dunn
Author URI: http://iandunn.name
*/
 
if( $_SERVER['SCRIPT_FILENAME'] == __FILE__ )
	die("Access denied.");

if( !class_exists( 'AuditTrailExtension' ) )
{
	class AuditTrailExtension
	{
		const PREFIX = 'ate_';
		
		/**
		 * Constructor
		 * @author Ian Dunn <ian@iandunn.name>
		 */
		public function __construct()
		{	
			// Register actions, filters and shortcodes
			add_action( 'audit_listen',			array( $this, 'registerEventCallbacks' ) );
			
			add_filter( 'audit_collect',		array( $this, 'addCollectionMethods' ), 11 );
			add_filter( 'audit_show_operation', array( $this, 'updateEntryAction' ) );
			add_filter( 'audit_show_item', 		array( $this, 'updateEntryTarget' ) );
			add_filter( 'audit_show_details', 	array( $this, 'updateEntryDetails' ) );
		}
	
		/**
         * Adds custom collection methods to the Options page and AT's internal data structures
         * @author Ian Dunn <ian@iandunn.name>
		 * @param array $methods
		 * @return array
         */
		public function addCollectionMethods( $methods )
		{
			if( !is_array( $methods ) )
				$methods = array();
			
			$methods[ self::PREFIX . 'plugin-activate-deactivate' ]	= 'Plugin Activation/Deactivation';
			$methods[ self::PREFIX . 'core-setting-changes' ]		= 'Option Changes';
			
			return $methods;
		}
		
		/**
         * Registers callback methods that will be called when the custom events fire
         * @author Ian Dunn <ian@iandunn.name>
		 * @param string $method
         */
		public function registerEventCallbacks( $method )
		{
			switch( $method )
			{
				case self::PREFIX . 'core-setting-changes':
					add_action( 'update_option', 		array( $this, 'optionChanges' ), 10, 3 );
				break;
				
				case self::PREFIX . 'plugin-activate-deactivate':
					add_action( 'activate_plugin',		array( $this, 'pluginStatusChanges' ), 10, 2 );
					add_action( 'deactivate_plugin',	array( $this, 'pluginStatusChanges' ), 10, 2 );
				break;
			}
		}
		
		/**
         * Updates the output of an event entry in the Action column of the audit trail
         * @author Ian Dunn <ian@iandunn.name>
		 * @param AT_Audit $item
		 * @return AT_Audit
         */
		public function updateEntryAction( $item )
		{	
			switch( $item->operation )
			{
				case 'update_option':
					$item->message = '<a href="#'. $item->id .'" class="audit-view">Option Updated</a>';
				break;
				
				case 'activate_plugin':
				case 'deactivate_plugin':
					$action = str_replace( '_plugin', '', $item->operation );
					$item->message = '<a href="#'. $item->id .'" class="audit-view">Plugin '. ucfirst( $action ) . 'd</a>';
				break;
			}
			
			return $item;
		}
		
		/**
         * Updates the output of an event entry in the Target column of the audit trail
         * @author Ian Dunn <ian@iandunn.name>
		 * @param AT_Audit $item
		 * @return AT_Audit
         */
		public function updateEntryTarget( $item )
		{
			switch( $item->operation )
			{
				case 'update_option':
					$option = unserialize( $item->data );
					$item->message = $option[ 'Option' ];
				break;
				
				case 'activate_plugin':
				case 'deactivate_plugin':
					$details = unserialize( $item->data );
					$item->message = $details[ 'Plugin' ];
				break;
			}
			
			return $item;
		}
		
		/**
         * Updates the output of an event entry in the details of the audit trail
         * @author Ian Dunn <ian@iandunn.name>
		 * @param AT_Audit $item
		 * @return AT_Audit
         */
		public function updateEntryDetails( $item )
		{
			$view = false;
			
			switch( $item->operation )
			{
				case 'update_option':
					$details = unserialize( $item->data );
					$view = 'details-array.php'; 
				break;
				
				case 'activate_plugin':
				case 'deactivate_plugin':
					$details = unserialize( $item->data );
					$view = 'details-array.php';
				break;
			}
			
			if( $view )
				$view = dirname( __FILE__ ) . '/views/' . $view;
			
			if( is_file( $view ) )
			{
				ob_start();
				require_once( $view );
				$item->message = ob_get_contents();
				ob_end_clean();
			}
			
			return $item;
		}
		
		/**
         * Adds log entries to the event trail when option values are changed
         * @author Ian Dunn <ian@iandunn.name>
		 * @param string $option
		 * @param string $oldValue
		 * @param string $newValue
         */
		public function optionChanges( $option, $oldValue, $newValue )
		{
			// Note: We have no way of knowing if the update failed or successeded, because WP doesn't supply adequate hooks for that
			
			$ignoredOptions = array( 'cron', 'active_plugins', 'recently_activated', 'gmt_offset' );
			
			if( $oldValue == $newValue )
				return;		// == may be worse than === to compare old vs new, but have to use because sometimes new is string from form, and old value is int from database
			
			if( substr( $option, 0, 1 ) == '_' || strpos( $option, 'transient' ) !== false || in_array( $option, $ignoredOptions ) )
				return;
			
			if( !class_exists( 'AT_Audit' ) || !method_exists( AT_Audit, 'create' ) )
				return;

			AT_Audit::create(
				'update_option',
				$option,
				array(
					'Option'	=> $option,
					'Old Value' => $oldValue == null ? 'null' : $oldValue,
					'New Value' => $newValue == null ? 'null' : $newValue
				)
			);
		}
		
		/**
         * Adds log entries to the event trail when plugins are activated/deactivated
         * @author Ian Dunn <ian@iandunn.name>
		 * @param string $plugin
		 * @param bool $networkWide
         */
		public function pluginStatusChanges( $plugin, $networkWide )
		{
			// Note: doesn't catch silent plugin activation/deactivation, or failed attempts to activate because WP doesn't supply adequate hooks for those
			
			if( !class_exists( 'AT_Audit' ) || !method_exists( AT_Audit, 'create' ) )
				return;
			
			$pluginData = get_plugin_data( dirname( __DIR__ ) .'/'. $plugin );
			
			AT_Audit::create(
				current_filter(),
				$plugin,
				array(
					'Plugin'		=> $pluginData[ 'Name' ],
					'Network wide'	=> $networkWide ? 'True' : 'False'
				)
			);
		}
	} // end AuditTrailExtension
	
	$ate = new AuditTrailExtension();
}

?>