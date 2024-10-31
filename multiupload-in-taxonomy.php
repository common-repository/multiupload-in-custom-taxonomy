<?php
/**
 * The WordPress Plugin Multiupload In Taxonomy
 *
 *
 * @package   Multiupload_In_Taxonomy
 * @author    augustinfotech <http://www.augustinfotech.com/>
 * @license   GPL-2.0+
 * @link      http://www.augustinfotech.com
 * @copyright 2014 August Infotech
 *
 * @wordpress-plugin
 * Plugin Name:       Multiupload In Taxonomy
 * Description:       Add multiupload custom field in custom taxonomy.
 * Version:           1.2
 * Author:            August Infotech
 * Author URI:        http://www.augustinfotech.com/
 * Text Domain:       aimultiupload
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('MT_PDIR_PATH',plugin_dir_path(__FILE__ ));

/* ================================================ Taxonomy meta table - START ============================================================= */
/**
* Create taxonomy meta table
*
* Function Name: ai_add_metataxonomy_table
*
* @access public
* @param 
*
* @created by Payal Patel and 07/04/2014
**/
register_activation_hook(__FILE__,'ai_add_metataxonomy_table');

function ai_add_metataxonomy_table()
{
	global $wpdb;
	
	$charset_collate = '';
	if ( ! empty( $wpdb->charset ) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
	if ( ! empty( $wpdb->collate ) )
		$charset_collate .= " COLLATE $wpdb->collate";

	$tables = $wpdb->get_results( "show tables like '{$wpdb->prefix}taxonomymeta'" );
	if ( !count( $tables ) )
		$wpdb->query( "CREATE TABLE {$wpdb->prefix}taxonomymeta (
		meta_id bigint(20) unsigned NOT NULL auto_increment,
		taxonomy_id bigint(20) unsigned NOT NULL default '0',
		meta_key varchar(255) default NULL,
		meta_value longtext,
		PRIMARY KEY	(meta_id),
		KEY taxonomy_id (taxonomy_id),
		KEY meta_key (meta_key)
	) $charset_collate;" );
	

}
/* ================================================ Taxonomy meta table - END ============================================================= */

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
add_action( 'plugins_loaded','multiupload_taxonomy_manage' );

function multiupload_taxonomy_manage(){
	
	require_once( MT_PDIR_PATH . 'include/class-multiupload-in-taxonomy-admin.php' );
	
	if (class_exists( 'Multiupload_In_Taxonomy_Admin' ) ) {		
		$multiupload_taxonomy = new Multiupload_In_Taxonomy_Admin();
	}
}

/*====================================== Plugin deactivate - Start ================================================ */
/**
*  Deactivate Plugin : When deactivate plugin then meta value deleted in database.
*
* Function Name: multiupload_deactivate
*
* @created by Payal Patel and 07/04/2014
*
**/
register_deactivation_hook( __FILE__,'multiupload_deactivate' );
function multiupload_deactivate()
{
	delete_option('ai_taxonomy_name');
}
/*====================================== Plugin deactivate - End ================================================ */

/*====================================== Plugin uninstall - Start ================================================ */
/**
*  Uninstall Plugin : When uninstall plugin then tabel droped in databse.
*
* Function Name: multiupload_uninstall
*
* @created by Payal Patel and 07/04/2014
*
**/
register_uninstall_hook( __FILE__, 'multiupload_uninstall'  );
function multiupload_uninstall()
{
	global $wpdb;
	$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}taxonomymeta");
		
	delete_option('ai_taxonomy_name');
}
/*====================================== Plugin uninstall - End ================================================ */
/*====================================== Shortcode - Start ================================================ */
function multiimg_func($atts)
{
	extract( shortcode_atts( array(
        'id'  => '',
        'taxname'=> '',
   ), $atts ) );
   
   if($id && $taxname)
   {
		$ids = array();
   		$ids = get_metadata('taxonomy',$id,$taxname.'_image',true);
   		$ids = implode(",",$ids);
   		return $ids;
   }
   return "No image found.";
}
add_shortcode( 'multiimg', 'multiimg_func' );
/*====================================== Shortcode - Start ================================================ */
?>