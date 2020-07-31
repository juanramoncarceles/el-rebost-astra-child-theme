<?php
/**
 * Astra Child - El Rebost Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child - El Rebost
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_EL_REBOST_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-el-rebost-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_EL_REBOST_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );



/**
 * Custom CSS.
 */
add_action('admin_head', 'food_css_icon');

function food_css_icon(){
	echo '<style>
	#woocommerce-product-data ul.wc-tabs li.nutrition_options.nutrition_tab a:before{
		content: "\f511";
	}
	</style>';
}

/**
 * Custom meta box to add additional info for the product.
 * @see https://www.sitepoint.com/adding-meta-boxes-post-types-wordpress/
 */
function custom_additional_info_meta_box() {

	add_meta_box(
		'custom-additional-info-wrapper',
		__( 'Información adicional', 'woocommerce' ),
		'custom_additional_info_meta_box_callback'
	);

}

add_action( 'add_meta_boxes_product', 'custom_additional_info_meta_box' );

function custom_additional_info_meta_box_callback( $post ) {

	$value = get_post_meta( $post->ID, '_custom_additional_info', true );

	echo '<textarea style="width:100%" id="custom_additional_info" name="custom_additional_info">' . esc_attr( $value ) . '</textarea>';
}

/**
 * When the product post is saved, saves our custom data.
 *
 * @param int $post_id
 */
function save_custom_additional_info_meta_box_data( $post_id ) {

	// Make sure that it is set.
	if ( ! isset( $_POST['custom_additional_info'] ) ) {
			return;
	}

	// Sanitize user input.
	$my_data = sanitize_text_field( $_POST['custom_additional_info'] );

	// Update the meta field in the database.
	update_post_meta( $post_id, '_custom_additional_info', $my_data );
}

add_action( 'save_post', 'save_custom_additional_info_meta_box_data' );

// *********************************
// SAVE PRODUCT NUTRITION FACTS INFO
// *********************************

/**
 * This adds an option like 'virtual' 'downloadable'
 * The problem is how to hook to show/hide tabs on change.
 */
// add_filter("product_type_options", function ($product_type_options) {

// 	$product_type_options["food"] = [
// 		"id"            => "_food",
// 		//"wrapper_class" => "show_if_simple",
// 		"label"         => "Alimento",
// 		"description"   => "Cualquier producto alimenticio, comida o bebida.",
// 		"default"       => "no",
// 	];

// 	return $product_type_options;

// });

// add_action("save_post_product", function ($post_ID, $product, $update) {
// 	update_post_meta($product->ID, "_food", isset($_POST["_food"]) ? "yes" : "no");
// }, 10, 3);


/**
 * Add a 'Nutrition facts' tab.
 * @see https://rudrastyh.com/woocommerce/product-data-metabox.html
 */
add_filter('woocommerce_product_data_tabs', 'food_product_settings_tabs' );

function food_product_settings_tabs( $tabs ){

	// Example of how to hide an existing tab.
	// unset( $tabs['inventory'] );

	$tabs['nutrition'] = array(
		'label'    => 'Valores nutricionales',
		'target'   => 'food_product_data',
		//'class'    => array('show_if_food'), // How to show only in certain cases 'show_if_simple', etc...
		'priority' => 21,
	);
	return $tabs;

}

/*
 * The content of the 'Nutrition facts' tab.
 */
add_action( 'woocommerce_product_data_panels', 'food_product_panels' );

function food_product_panels(){

	echo '<div id="food_product_data" class="panel woocommerce_options_panel hidden">';

	echo '<p>Información nutricional por 100g / 100ml</p>';

	// Show in front-end
	woocommerce_wp_checkbox( array(
		'id'                => 'show_food_info',
		'value'             => get_post_meta( get_the_ID(), 'show_food_info', true ),
		'label'             => 'Mostrar en página de producto',
		'desc_tip'					=> true,
		'description'       => 'Si esta opción no se marca estos datos solo se guardarán de forma interna.'
	) );

	// Input calories
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_calories',
		'value'             => get_post_meta( get_the_ID(), 'food_product_calories', true ),
		'label'             => 'Valor energético',
		'desc_tip'					=> true,
		'description'       => 'El valor energético o calorías del producto alimenticio.'
	) );

	// Input fats
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_fats',
		'value'             => get_post_meta( get_the_ID(), 'food_product_fats', true ),
		'label'             => 'Grasas',
		'desc_tip'					=> true,
		'description'       => 'Las grasas del producto alimenticio.'
	) );

	// Input saturated fats
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_saturated_fats',
		'value'             => get_post_meta( get_the_ID(), 'food_product_saturated_fats', true ),
		'label'             => 'Grasas saturadas',
		'desc_tip'					=> true,
		'description'       => 'Las grasas saturadas del producto alimenticio.'
	) );

	// Input carbs
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_carbs',
		'value'             => get_post_meta( get_the_ID(), 'food_product_carbs', true ),
		'label'             => 'Hidratos de carbono',
		'desc_tip'					=> true,
		'description'       => 'Los hidratos de carbono del producto alimenticio.'
	) );

	// Input sugar
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_sugar',
		'value'             => get_post_meta( get_the_ID(), 'food_product_sugar', true ),
		'label'             => 'Azúcares',
		'desc_tip'					=> true,
		'description'       => 'Los azúcares del producto alimenticio.'
	) );

	// Input fiber
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_fiber',
		'value'             => get_post_meta( get_the_ID(), 'food_product_fiber', true ),
		'label'             => 'Fibra',
		'desc_tip'					=> true,
		'description'       => 'La fibra del producto alimenticio.'
	) );

	// Input proteins
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_proteins',
		'value'             => get_post_meta( get_the_ID(), 'food_product_proteins', true ),
		'label'             => 'Proteínas',
		'desc_tip'					=> true,
		'description'       => 'Las proteínas del producto alimenticio.'
	) );

	// Input salt
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_salt',
		'value'             => get_post_meta( get_the_ID(), 'food_product_salt', true ),
		'label'             => 'Sal',
		'desc_tip'					=> true,
		'description'       => 'La sal del producto alimenticio.'
	) );

	// Text area input example.
	// woocommerce_wp_textarea_input( array(
	// 	'id'          => 'misha_changelog',
	// 	'value'       => get_post_meta( get_the_ID(), 'misha_changelog', true ),
	// 	'label'       => 'Changelog',
	// 	'desc_tip'    => true,
	// 	'description' => 'Prove the plugin changelog here',
	// ) );

	// Select input example.
	// woocommerce_wp_select( array(
	// 	'id'          => 'misha_ext',
	// 	'value'       => get_post_meta( get_the_ID(), 'misha_ext', true ),
	// 	'wrapper_class' => 'show_if_downloadable',
	// 	'label'       => 'File extension',
	// 	'options'     => array( '' => 'Please select', 'zip' => 'Zip', 'gzip' => 'Gzip'),
	// ) );

	echo '</div>';

}


/*
 * Save the values of the 'Nutrition facts' tab
 */
add_action( 'woocommerce_process_product_meta', 'food_save_fields', 10, 2 );

function food_save_fields( $id, $post ) {

	update_post_meta( $id, 'show_food_info', $_POST['show_food_info'] );

	//if( !empty( $_POST['food_product_proteins'] ) ) {
	update_post_meta( $id, 'food_product_calories', $_POST['food_product_calories'] );
	//} else {
	//	delete_post_meta( $id, 'food_product_proteins' );
	//}

	update_post_meta( $id, 'food_product_fats', $_POST['food_product_fats'] );

	update_post_meta( $id, 'food_product_saturated_fats', $_POST['food_product_saturated_fats'] );

	update_post_meta( $id, 'food_product_carbs', $_POST['food_product_carbs'] );

	update_post_meta( $id, 'food_product_sugar', $_POST['food_product_sugar'] );

	update_post_meta( $id, 'food_product_fiber', $_POST['food_product_fiber'] );

	update_post_meta( $id, 'food_product_proteins', $_POST['food_product_proteins'] );
	
	update_post_meta( $id, 'food_product_salt', $_POST['food_product_salt'] );
 
}


// SHOW NUTRITION FACTS INFO IN PRODUCT PAGE

/**
 * Add a custom product data tab.
 * @see https://docs.woocommerce.com/document/editing-product-data-tabs/
 * @see https://awhitepixel.com/blog/woocommerce-product-data-custom-fields-tabs/
 * @see https://rudrastyh.com/woocommerce/rename-product-tabs-and-heading.html
 */
add_filter( 'woocommerce_product_tabs', 'woo_custom_product_tab' );

function woo_custom_product_tab( $tabs ) {

	global $product;

	$show_food_info = $product->get_meta('show_food_info');

	// Add the custom nutrition facts tab only if it has been indicated.
	if ($show_food_info == "yes") {
		$tabs['nutrition_facts'] = array(
			'title' 	=> __( 'Valors nutricionals', 'woocommerce' ),
			'priority' 	=> 50,
			'callback' 	=> 'woo_nutrition_tab_content'
		);
	}

	// Manage the additional info tab.
	$additional_info = $product->get_meta('_custom_additional_info');

	if (!empty($additional_info)) {
		$tabs['additional_information']['title'] = __( 'Informació adicional' );
		$tabs['additional_information']['callback'] = function () use ($additional_info) {
			echo '<p>' . $additional_info . '</p>';
		};
	} else {
		unset( $tabs['additional_information'] );
	}

	// Rename the reviews tab.
	$tabs['reviews']['title'] = __( 'Valoracions (' . $product->get_review_count() . ')' );

 	// Remove the default description tab from the product page.
	unset( $tabs['description'] );

	return $tabs;

}

/**
 * The custom tab for nutrition facts content.
 */
function woo_nutrition_tab_content() {

	global $product;

	// The nutrition values.
	$calories = $product->get_meta('food_product_calories');
	$fats = $product->get_meta('food_product_fats');
	$saturated_fats = $product->get_meta('food_product_saturated_fats');
	$carbs = $product->get_meta('food_product_carbs');
	$sugar = $product->get_meta('food_product_sugar');
	$fiber = $product->get_meta('food_product_fiber');
	$proteins = $product->get_meta('food_product_proteins');
	$salt = $product->get_meta('food_product_salt');
	
	// If after checking all the data remains false means that there is no data to show.
	$show_values = false;

	if (!empty($calories)) {
		$nutrition_table_rows .= create_table_row( __( 'Valor energètic', 'woocommerce' ), $calories );
		$show_values = true;
	}

	if (!empty($fats)) {
		$nutrition_table_rows .= create_table_row( __( 'Greixos', 'woocommerce' ), $fats );
		$show_values = true;
	}

	if (!empty($saturated_fats)) {
		$nutrition_table_rows .= create_table_row( __( 'Greixos saturats', 'woocommerce' ), $saturated_fats );
		$show_values = true;
	}

	if (!empty($carbs)) {
		$nutrition_table_rows .= create_table_row( __( 'Hidrats de carboni', 'woocommerce' ), $carbs );
		$show_values = true;
	}

	if (!empty($sugar)) {
		$nutrition_table_rows .= create_table_row( __( 'Sucres', 'woocommerce' ), $sugar );
		$show_values = true;
	}

	if (!empty($fiber)) {
		$nutrition_table_rows .= create_table_row( __( 'Fibra', 'woocommerce' ), $fiber );
		$show_values = true;
	}

	if (!empty($proteins)) {
		$nutrition_table_rows .= create_table_row( __( 'Proteines', 'woocommerce' ), $proteins );
		$show_values = true;
	}

	if (!empty($salt)) {
		$nutrition_table_rows .= create_table_row( __( 'Sal', 'woocommerce' ), $salt );
		$show_values = true;
	}

	if ($show_values) {
		echo '<p>Informació nutricional per 100g / 100ml<p>';
		echo '<table class="woocommerce-product-attributes shop_attributes"><tbody>';
		echo $nutrition_table_rows;
		echo '</tbody></table>';
	}

}

function create_table_row( $label, $value) {
	$table_rows = '<tr class="woocommerce-product-attributes-item">';
	$table_rows .= '<th class="woocommerce-product-attributes-item__label">' . $label . '</th>';
	$table_rows .= '<td class="woocommerce-product-attributes-item__value"><p>' . $value . '</p></td>';
	$table_rows .= '</tr>';
	return $table_rows;
}