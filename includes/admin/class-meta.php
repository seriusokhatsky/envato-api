<?php

namespace SSAdmin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Meta {

	public function cmb2_metaboxes() {
		if( ! class_exists('CMB2') ) return;
	    // Start with an underscore to hide fields from custom fields list
	    $prefix = '_ss_';
	    /**
	     * Initiate the metabox
	     */
	    $cmb = new_cmb2_box( array(
	        'id'            => 'forum_metabox',
	        'title'         => __( 'Forum Metabox', 'cmb2' ),
	        'object_types'  => array( 'forum', ), // Post type
	        'context'       => 'normal',
	        'priority'      => 'high',
	        'show_names'    => true, // Show field names on the left
	        // 'cmb_styles' => false, // false to disable the CMB stylesheet
	        // 'closed'     => true, // Keep the metabox closed by default
	    ) );

	    // Regular text field
	    $cmb->add_field( array(
	        'name'       => __( 'Envato items ids', 'cmb2' ),
	        'desc'       => __( 'Enter a comma sperated list of Envato Item IDs you want to be associated with this forum / entry.', 'cmb2' ),
	        'id'         => $prefix . 'envato_items',
	        'type'       => 'text'
	    ) );
	}

}