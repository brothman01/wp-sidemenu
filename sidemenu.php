<?php
/*
 * Plugin Name: Side Menu BCIT
 * Plugin URI:  https://wordpress.org/plugins/rename-wp-login/
 * Description: Creates a side menu widget that can be embedded on any page(s) of the site.
 * Version:     1.0.0
 * Author:      Ben Rothman
 * Author URI:  http://www.BenRothman.org
 * License:     GPL-2.0+
 */

 // Register and load the widget
 function bro_load_widget() {
     register_widget( 'bro_widget' );
 }
 add_action( 'widgets_init', 'bro_load_widget' );



 class bro_widget extends WP_Widget {

	 public $children;

 function __construct() {
 parent::__construct(

 // Base ID of your widget
	 'bro_widget',

	 // Widget name will appear in UI
	 __('WPBeginner Widget', 'bro_widget_domain'),

	 // Widget description
	 array( 'description' => __( 'Sample widget based on WPBeginner Tutorial', 'bro_widget_domain' ), )
	 );

	 		add_action( 'wp_enqueue_scripts', [ $this, 'sidemenu_enqueue_styles' ] );

	 		add_action( 'wp_enqueue_scripts', [ $this, 'sidemenu_enqueue_scripts' ] );

	 		add_action( 'widgets_init', [ $this, 'bro_load_widget' ] );


			if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
				require_once dirname( __FILE__ ) . '/cmb2/init.php';
			} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
				require_once dirname( __FILE__ ) . '/CMB2/init.php';
			}

		add_action( 'cmb2_admin_init', [ $this, 'yourprefix_register_demo_metabox'] );

 }

	public function yourprefix_register_demo_metabox() {
 	$prefix = 'BCIT_';

 	/**
 	 * Sample metabox to demonstrate each field type included
 	 */
 	$cmb_demo = new_cmb2_box( array(
 		'id'            => $prefix . 'metabox',
 		'title'         => esc_html__( 'Sidemenu Options', 'cmb2' ),
 		'object_types'  => array( 'page' ), // Post type
 	) );

 	// The Query
 $menus = wp_get_nav_menus();

 $options = [];

 foreach ($menus as $menu) {
 	$options[ $menu->slug ] = $menu->name;
 }

 // print_r( $menus );

 // $options = array( "standard" => esc_html__( "Option One", "cmb2" ),
 // "custom"   => esc_html__( "Option Two", "cmb2" ),
 // "none"     => esc_html__( "Option Three", "cmb2" ) );

 	$cmb_demo->add_field( array(
 		'name'             => esc_html__( 'Select Sidemenu', 'cmb2' ),
 		'desc'             => '',
 		'id'               => $prefix . 'selectmenu',
 		'type'             => 'select',
 		'show_option_none' => true,
 		'options'          => $options,
 	) );


 }



 // Creating widget front-end

 public function widget( $args, $instance ) {
 $title = apply_filters( 'widget_title', $instance['title'] );

 // before and after widget arguments are defined by themes
 echo $args['before_widget'];
 if ( ! empty( $title ) )
 echo $args['before_title'] . $title . $args['after_title'];

 // This is where you run the code and display the output

	echo $this->make_side_menus();

 echo $args['after_widget'];
 }

 	/**
 	 *  Build Top-level Menu and put IDs of children into children array
 	 *
 	 * @since 0.1
 	 */
 	public function make_side_menus() {

		$menu_slug = get_post_meta( get_the_ID(), 'BCIT_selectmenu', true );

		$menu = is_nav_menu( $menu_slug ) ? wp_get_nav_menu_items( $menu_slug ) : wp_get_nav_menu_items( 2 );

		$output = '<ul id="sidemenu">';

		for ( $i = 0; $i < count( $menu ); $i++ ) {

			if ( 0 == $menu[ $i ]->menu_item_parent ) {
				$output .=
				'<li data-open="false" class="sidemenu-item" data-id="' . $menu[ $i ]->ID . '">' .
					'<a href="#" data-id="' . $menu[ $i ]->ID . '">' . $menu[ $i ]->title . '</a>' .
					'<ul class="sidemenu_submenu" data-id="' . $menu[ $i ]->ID . '"></ul>' .
				'</li>';
			}

			// title::url::id::parentid
			$this->children[ $i ] = $menu[ $i ]->title . '::' . $menu[ $i ]->url . '::' . $menu[ $i ]->ID . '::' . $menu[ $i ]->menu_item_parent;

		}

		$output .= '</ul>';

		//print_r( $menu );
		return $output;
	}

// Widget Backend
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
} else {
 $title = __( 'New title', 'bro_widget_domain' );
}
 // Widget admin form
 ?>
 <p>
 <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
 <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
 </p>
 <?php
 }

 // Updating widget replacing old instances with new
 public function update( $new_instance, $old_instance ) {
 $instance = array();
 $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
 return $instance;
 }

 	/**
 	 *  Stylsheets enqueued
 	 *
 	 * @since 0.1
 	 */
 	public function sidemenu_enqueue_styles() {

 		wp_enqueue_style( 'sidemenu_stylesheet', plugin_dir_url( __FILE__ ) . '/library/css/sidemenu.css', [], false, 'all' );

 	}

 	/**
 	 *  Scripts enqueued
 	 *
 	 * @since 0.1
 	 */
 	public function sidemenu_enqueue_scripts() {

 		$this->make_side_menus();

 		wp_register_script( 'sidemenu_script', plugin_dir_url( __FILE__ ) . '/library/js/sidemenu.js', [ 'jquery' ], 'all', true );

 		wp_enqueue_script( 'sidemenu_script' );

 		wp_localize_script( 'sidemenu_script', 'vars', [
 			'children' => json_encode( $this->children ),
 		] );

 	}
 } // Class bro_widget ends here
