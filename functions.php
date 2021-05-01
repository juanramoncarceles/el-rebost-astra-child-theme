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
define( 'CHILD_THEME_ASTRA_CHILD_EL_REBOST_VERSION', '1.0.2' );

/**
 * Enqueue styles
 */
function enqueue_custom_styles_and_scripts() {

	wp_enqueue_style( 'astra-child-el-rebost-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_EL_REBOST_VERSION, 'all' );

	wp_enqueue_script( 'main-custom-script-el-rebost', get_stylesheet_directory_uri() . '/script.js', array( 'jquery' ), '1.0.0', true );

	if ( is_cart() ) {
		wp_enqueue_script( 'cart-page-el-rebost', get_stylesheet_directory_uri() . '/js/cart_page.js', false, '1.0.0', true );
	}

	if (is_product()) {
		wp_enqueue_script( 'product-page-el-rebost', get_stylesheet_directory_uri() . '/product_page.js', false, '1.0.0', true );
	}

}

add_action( 'wp_enqueue_scripts', 'enqueue_custom_styles_and_scripts', 15 );



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


// ****************************************************************************
// ************* ADD A CUSTOM OPTIONS PAGE FOR EL REBOST THINGS ***************
// ****************************************************************************

add_action( 'admin_menu', 'elrebost_options_page' );

function elrebost_options_page() {
	add_options_page(
		'El Rebost custom settings', // Browser tab title.
		'El Rebost settings', // Title under WP options.
		'manage_options', // Capability to access the page.
		'elrebost-custom-settings', // The page URL slug.
		'elrebost_settings_page_content', // Callback function with content.
		2 // Priority.
	);
}
 
function elrebost_settings_page_content() {
	echo '<div class="wrap">
	<h1>El Rebost settings</h1>
	<form method="post" action="options.php">';

		settings_fields( 'elrebost_custom_settings' ); // Settings group name.
		do_settings_sections( 'elrebost-custom-settings' );
		submit_button();

	echo '</form></div>';
}


// ****************************************************************************
// ********** ADD A FIELD IN THE CUSTOM OPTIONS PAGE CREATED ABOVE ************
// ****************************************************************************

add_action( 'admin_init',  'elrebost_register_settings' );

function elrebost_register_settings() {

	// Registration of the settings.
	register_setting(
		'elrebost_custom_settings', // Settings group name.
		'product_only_pickup_and_home_delivery_message', // Option name.
		'sanitize_text_field' // Sanitization function.
	);
	register_setting(
		'elrebost_custom_settings',
		'show_payment_methods_in_product_page',
	);
	register_setting(
		'elrebost_custom_settings',
		'shipping_methods_cart_message',
		'sanitize_text_field'
	);
	register_setting(
		'elrebost_custom_settings',
		'only_pickup_and_home_cart_item_message',
		'sanitize_text_field'
	);
	register_setting(
		'elrebost_custom_settings',
		'url_to_page_with_shipping_methods_info',
		'sanitize_text_field'
	);

	// Creation of sections to group the settings.
	add_settings_section(
		'elrebost_product_page_settings_id', // Section ID.
		'Product pages settings', // Title (if needed), otherwise empty string.
		'', // Callback function (if needed).
		'elrebost-custom-settings' // The options page slug.
	);
	add_settings_section(
		'elrebost_cart_page_settings_id',
		'Cart page settings',
		'', // Callback function (if needed).
		'elrebost-custom-settings'
	);

	// Addition of the fields to the page.
	add_settings_field(
		'product_only_pickup_and_home_delivery_message', // Field ID.
		'Message for the product pages, visible for products that are only for local pickup and home delivery', // Field title.
		'elrebost_product_delivery_message_text_field_html', // Function which prints the field.
		'elrebost-custom-settings', // The options page slug.
		'elrebost_product_page_settings_id', // Section ID.
		array(
			'label_for' => 'product_only_pickup_and_home_delivery_message',
			'class' => 'elrebost-setting', // For <tr> element.
		)
	);
	add_settings_field(
		'show_payment_methods_in_product_page',
		'Show the available payment methods in the product pages',
		'elrebost_product_show_payments_checkbox_field_html',
		'elrebost-custom-settings',
		'elrebost_product_page_settings_id',
		array(
			'label_for' => 'show_payment_methods_in_product_page',
			'class' => 'elrebost-setting',
		)
	);
	add_settings_field(
		'shipping_methods_cart_message',
		'Message for the cart page, located after the shipping methods to add any extra information about them',
		'elrebost_shipping_methods_message_text_field_html',
		'elrebost-custom-settings',
		'elrebost_cart_page_settings_id',
		array(
			'label_for' => 'shipping_methods_cart_message',
			'class' => 'elrebost-setting',
		)
	);
	add_settings_field(
		'only_pickup_and_home_cart_item_message',
		'Short message for the cart page, located after each item that is only available for local pickup and home delivery',
		'elrebost_cart_item_message_text_field_html',
		'elrebost-custom-settings',
		'elrebost_cart_page_settings_id',
		array(
			'label_for' => 'only_pickup_and_home_cart_item_message',
			'class' => 'elrebost-setting',
		)
	);
	add_settings_field(
		'url_to_page_with_shipping_methods_info',
		'If you want the short message above to also be a link to a page with more info put the complete url (https://...) here',
		'elrebost_shipping_methods_info_page_url_text_field_html',
		'elrebost-custom-settings',
		'elrebost_cart_page_settings_id',
		array(
			'label_for' => 'url_to_page_with_shipping_methods_info',
			'class' => 'elrebost-setting',
		)
	);

}

function elrebost_product_delivery_message_text_field_html() {
	$delivery_methods_message = get_option( 'product_only_pickup_and_home_delivery_message' );
	printf(
		'<textarea id="product_only_pickup_and_home_delivery_message" name="product_only_pickup_and_home_delivery_message" rows="3" style="width:100&percnt;;">%s</textarea>',
		esc_attr( $delivery_methods_message )
	);
}

function elrebost_product_show_payments_checkbox_field_html() {
	$show_payment_methods = get_option( 'show_payment_methods_in_product_page' );
	printf(
		'<input type="checkbox" id="show_payment_methods_in_product_page" name="show_payment_methods_in_product_page" value="1" ' . checked(1, $show_payment_methods, false) . '/>'
	);
}

function elrebost_shipping_methods_message_text_field_html() {
	$shipping_methods_message_in_cart = get_option( 'shipping_methods_cart_message' );
	printf(
		'<input type="text" id="shipping_methods_cart_message" name="shipping_methods_cart_message" value="%s" style="width:100&percnt;;" />',
		esc_attr( $shipping_methods_message_in_cart )
	);
}

function elrebost_cart_item_message_text_field_html() {
	$cart_item_message_only_pickup_and_home = get_option( 'only_pickup_and_home_cart_item_message' );
	printf(
		'<input type="text" id="only_pickup_and_home_cart_item_message" name="only_pickup_and_home_cart_item_message" value="%s" style="width:100&percnt;;" />',
		esc_attr( $cart_item_message_only_pickup_and_home )
	);
}

function elrebost_shipping_methods_info_page_url_text_field_html() {
	$shipping_methods_info_page_url = get_option( 'url_to_page_with_shipping_methods_info' );
	printf(
		'<input type="text" id="url_to_page_with_shipping_methods_info" name="url_to_page_with_shipping_methods_info" placeholder="https://www.example.com/info/" value="%s" style="width:100&percnt;;" />',
		esc_attr( $shipping_methods_info_page_url )
	);
}


// ****************************************************************************
// *********** ADDED VERIFICATION FOR CONNECTION BETWEEN FB AND WC ************
// ****************************************************************************

function add_facebook_domain_verification_tag() {
  echo '<meta name="facebook-domain-verification" content="m2ndf0kgfmzkyliyw2n4phk3znzf6m" />';
}

add_action('wp_head', 'add_facebook_domain_verification_tag');


// ****************************************************************************
// ******************** CUSTOM ADDITIONAL INFO META BOX ***********************
// ****************************************************************************
/**
 * @see https://code.rohitink.com/2017/11/30/add-wysiwyg-editor-textarea-custom-meta-box-wordpress/
 */

/**
 * Custom meta box to add additional info for the product.
 * @see https://www.sitepoint.com/adding-meta-boxes-post-types-wordpress/
 */
function custom_additional_info_meta_box() {

	add_meta_box(
		'custom-additional-info-wrapper',
		__( 'Información adicional', 'woocommerce' ),
		'custom_additional_info_html_meta_box'
	);

}

add_action( 'add_meta_boxes_product', 'custom_additional_info_meta_box' );

/**
 * Creates a textarea input field with WYSIWYG editor.
 */
function custom_additional_info_html_meta_box( $post ) {
	wp_nonce_field( '_additional_info_meta_nonce', 'additional_info_meta_nonce' );

	// Get the current additional info meta content if any.
	$meta_content = get_post_meta( $post->ID, '_custom_additional_info', true );
	
	wp_editor($meta_content, 'meta_content_editor', array(
		//'wpautop'       => true, // Disabled since it does nothing.
		'media_buttons' => false,
		'textarea_name' => 'custom_additional_info',
		'textarea_rows' => 10,
		'teeny'         => true
	));
}

/**
 * Saves the value of the meta box as post meta data only in admin screen.
 * @param int $post_id
 */
function save_additional_info_metadata( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	if ( ! isset( $_POST['additional_info_meta_nonce'] ) || ! wp_verify_nonce( $_POST['additional_info_meta_nonce'], '_additional_info_meta_nonce' ) ) return;
	
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;
 
	if ( isset( $_POST['custom_additional_info'] ) ) {
		// Sanitize the input taking into account it is HTML. Another option would be with wp_kses_post().
		$input_data = wp_kses( $_POST['custom_additional_info'] , array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'ul' => array(),
			'ol' => array(),
			'li' => array(),
			'br' => array(),
			'em' => array(),
			'del' => array(),
			'strong' => array(),
			'span' => array(
				'style' => array()
			),
		));
		update_post_meta( $post_id, '_custom_additional_info', $input_data );
	}
}

add_action( 'save_post', 'save_additional_info_metadata' );


// ****************************************************************************
// ******************** SAVE PRODUCT NUTRITION FACTS INFO *********************
// ****************************************************************************

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

	$current_values = get_post_meta( get_the_ID(), 'food_product_nutrition_facts', true );

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
		'value'             => isset($current_values['calories']) ? $current_values['calories'] : '',
		'label'             => 'Valor energético',
		'desc_tip'					=> true,
		'description'       => 'El valor energético o calorías del producto alimenticio.'
	) );

	// Input fats
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_fats',
		'value'             => isset($current_values['fats']) ? $current_values['fats'] : '',
		'label'             => 'Grasas',
		'desc_tip'					=> true,
		'description'       => 'Las grasas del producto alimenticio.'
	) );

	// Input saturated fats
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_saturated_fats',
		'value'             => isset($current_values['saturated_fats']) ? $current_values['saturated_fats'] : '',
		'label'             => 'Grasas saturadas',
		'desc_tip'					=> true,
		'description'       => 'Las grasas saturadas del producto alimenticio.'
	) );

	// Input carbs
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_carbs',
		'value'             => isset($current_values['carbs']) ? $current_values['carbs'] : '',
		'label'             => 'Hidratos de carbono',
		'desc_tip'					=> true,
		'description'       => 'Los hidratos de carbono del producto alimenticio.'
	) );

	// Input sugar
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_sugar',
		'value'             => isset($current_values['sugar']) ? $current_values['sugar'] : '',
		'label'             => 'Azúcares',
		'desc_tip'					=> true,
		'description'       => 'Los azúcares del producto alimenticio.'
	) );

	// Input fiber
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_fiber',
		'value'             => isset($current_values['fiber']) ? $current_values['fiber'] : '',
		'label'             => 'Fibra',
		'desc_tip'					=> true,
		'description'       => 'La fibra del producto alimenticio.'
	) );

	// Input proteins
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_proteins',
		'value'             => isset($current_values['proteins']) ? $current_values['proteins'] : '',
		'label'             => 'Proteínas',
		'desc_tip'					=> true,
		'description'       => 'Las proteínas del producto alimenticio.'
	) );

	// Input salt
	woocommerce_wp_text_input( array(
		'id'                => 'food_product_salt',
		'value'             => isset($current_values['salt']) ? $current_values['salt'] : '',
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

	$nutrition_facts = [];

	if( !empty( $_POST['food_product_calories'] ) ) {
		$nutrition_facts['calories'] = $_POST['food_product_calories'];
	}

	if( !empty( $_POST['food_product_fats'] ) ) {
		$nutrition_facts['fats'] = $_POST['food_product_fats'];
	}

	if( !empty( $_POST['food_product_saturated_fats'] ) ) {
		$nutrition_facts['saturated_fats'] = $_POST['food_product_saturated_fats'];
	}

	if( !empty( $_POST['food_product_carbs'] ) ) {
		$nutrition_facts['carbs'] = $_POST['food_product_carbs'];
	}

	if( !empty( $_POST['food_product_sugar'] ) ) {
		$nutrition_facts['sugar'] = $_POST['food_product_sugar'];
	}

	if( !empty( $_POST['food_product_fiber'] ) ) {
		$nutrition_facts['fiber'] = $_POST['food_product_fiber'];
	}

	if( !empty( $_POST['food_product_proteins'] ) ) {
		$nutrition_facts['proteins'] = $_POST['food_product_proteins'];
	}

	if( !empty( $_POST['food_product_salt'] ) ) {
		$nutrition_facts['salt'] = $_POST['food_product_salt'];
	}

	update_post_meta( $id, 'food_product_nutrition_facts', $nutrition_facts );
 
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
	$nutrition_facts = $product->get_meta('food_product_nutrition_facts');

	// Manage the additional info tab.
	$additional_info = $product->get_meta('_custom_additional_info');

	if (!empty($additional_info)) {
		$tabs['additional_information']['title'] = __( 'Informació adicional' );
		$tabs['additional_information']['priority'] = 5;
		$tabs['additional_information']['callback'] = function () use ($additional_info) {
			// Important to wpautop() the output since it was saved from wp_editor() as metadata.
			echo wpautop( $additional_info );
		};
	} else {
		unset( $tabs['additional_information'] );
	}

	// Add the custom nutrition facts tab only if it has been indicated and there are values.
	if ($show_food_info == "yes" && !empty($nutrition_facts)) {
		$tabs['nutrition_facts'] = array(
			'title' 	=> __( 'Valors nutricionals', 'woocommerce' ),
			'priority' 	=> 10,
			'callback' 	=> 'woo_nutrition_tab_content'
		);
	}

	// Rename the reviews tab.
	$tabs['reviews']['title'] = __( 'Valoracions (' . $product->get_review_count() . ')' );
	$tabs['reviews']['priority'] = 15;

 	// Remove the default description tab from the product page.
	unset( $tabs['description'] );

	return $tabs;

}

/**
 * The custom tab for nutrition facts content.
 */
function woo_nutrition_tab_content() {

	global $product;
	//echo $product;
	// The nutrition values.
	$nutrition_facts = $product->get_meta('food_product_nutrition_facts');
	//echo $nutrition_facts; // Si es un empty string return o no existe return
	
	// If after checking all the data remains false means that there is no data to show.
	//$show_values = false;

	if (array_key_exists('calories', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'Valor energètic', 'woocommerce' ), $nutrition_facts['calories'] );
	}

	if (array_key_exists('fats', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'Greixos', 'woocommerce' ), $nutrition_facts['fats'] );
	}

	if (array_key_exists('saturated_fats', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'dels quals saturats', 'woocommerce' ), $nutrition_facts['saturated_fats'], 'sub-item' );
	}

	if (array_key_exists('carbs', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'Hidrats de carboni', 'woocommerce' ), $nutrition_facts['carbs'] );
	}

	if (array_key_exists('sugar', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'dels quals sucres', 'woocommerce' ), $nutrition_facts['sugar'], 'sub-item' );
	}

	if (array_key_exists('fiber', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'Fibra', 'woocommerce' ), $nutrition_facts['fiber'] );
	}

	if (array_key_exists('proteins', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'Proteines', 'woocommerce' ), $nutrition_facts['proteins'] );
	}

	if (array_key_exists('salt', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'Sal', 'woocommerce' ), $nutrition_facts['salt'] );
	}

	echo '<p>Informació nutricional per 100g / 100ml<p>';
	echo '<table class="woocommerce-product-attributes shop_attributes"><tbody>';
	echo $nutrition_table_rows;
	echo '</tbody></table>';

}

/**
 * Creates a string that corresponds to the HTML of a row in a table.
 * @param $custom_class (Optional) A class to be added to the 'th' element of the row.
 */
function create_table_row( $label, $value, $custom_class = '') {
	$table_rows = '<tr class="woocommerce-product-attributes-item">';
	$table_rows .= '<th class="woocommerce-product-attributes-item__label ' . ($custom_class ?: '') . '">' . $label . '</th>';
	$table_rows .= '<td class="woocommerce-product-attributes-item__value"><p>' . $value . '</p></td>';
	$table_rows .= '</tr>';
	return $table_rows;
}


// ****************************************************************************
// *************************** SELL BY WEIGHT LOGIC ***************************
// ****************************************************************************

// Show sell by weight options.
add_action( 'woocommerce_product_options_general_product_data', 'sell_by_weight_option_group' );
 
function sell_by_weight_option_group() {

	echo '<div class="option_group">';

	woocommerce_wp_checkbox( array(
		'id'      => 'sell_by_weight',
		'value'   => get_post_meta( get_the_ID(), 'sell_by_weight', true ),
		'label'   => 'Vender por peso',
		'desc_tip' => true,
		'description' => 'Marcar si es un producto que se vende por peso.',
	) );

	woocommerce_wp_select( array(
		'id'          => 'sell_weight_measure',
		'value'       => get_post_meta( get_the_ID(), 'sell_weight_measure', true ),
		//'wrapper_class' => 'show_if_downloadable',
		'label'       => sprintf( 'Medida de peso (%s)', get_option('woocommerce_weight_unit') ),
		'options'     => array( '' => 'Selecciona una opción', '20' => '20', '50' => '50', '100' => '100'), // Should be integer values.
		'desc_tip' => true,
		'description' => 'Elegir la medida de peso. Utiliza las unidades indicadas en los ajustes de Woocommerce. Solo tiene efecto si se habilita la opción de vender por peso.',
	) );
 
	echo '</div>';

}


// Save the sell by weight data.
add_action( 'woocommerce_process_product_meta', 'sell_by_weight_save_fields', 10, 2 );

function sell_by_weight_save_fields( $id, $post ){
 
	if( !empty( $_POST['sell_by_weight'] ) ) {
		update_post_meta( $id, 'sell_by_weight', $_POST['sell_by_weight'] );
	} else {
		delete_post_meta( $id, 'sell_by_weight' );
	}

	if( !empty( $_POST['sell_by_weight'] ) ) {
		update_post_meta( $id, 'sell_weight_measure', $_POST['sell_weight_measure'] );
	} else {
		delete_post_meta( $id, 'sell_weight_measure' );
	}
 
}


// Change how price is displayed in the product page.
add_filter( 'woocommerce_get_price_html', 'change_product_price_html', 10, 2 );

function change_product_price_html( $price, $product ) {
	
	$sell_by_weight = $product->get_meta('sell_by_weight');
	$weight_measure = $product->get_meta('sell_weight_measure');

	if ($sell_by_weight == "yes" && $weight_measure != "") {
		// Important: The product price in case of weight is by Kg.
		$price_num = $product->price;
		$price_grams = convert_price_from_kg_to_g($price_num, $weight_measure);
		$price_formatted = wc_price( $price_grams );
		return sprintf( '<span class="amount">%s / %s %s</span>', $price_formatted, $weight_measure, get_option('woocommerce_weight_unit') );
	} else {
		return sprintf( '<span class="amount">%s / ud</span>', $price );
	}

}


// Change how price is displayed in the cart page.
// add_filter( 'woocommerce_cart_item_price', 'change_product_price_cart', 10, 2 );

// function change_product_price_cart( $price, $cart_item ) {

// 	$sell_by_weight = get_post_meta( $cart_item['product_id'], 'sell_by_weight', true );
// 	$weight_measure = get_post_meta( $cart_item['product_id'], 'sell_weight_measure', true );

// 	if ($sell_by_weight == "yes" && $weight_measure != "") {
// 		$formatted_price = $cart_item["data"]->price;
// 		return '<span class="woocommerce-Price-amount amount">' . format_price_by_weight_from_kg_to_g($formatted_price, intval($weight_measure)) . ' <span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol() . '</span></span>';
// 	} else {
// 		return $price;
// 	}

// }


/**
 * Converts the price for 1 Kg to the price for the corresponding amount of grams.
 * @param $kg_price The price for 1 Kg.
 * @param $g_measure Should represent an amount of grams as an integer, either as a string or integer already.
 * @return {number}
 */
function convert_price_from_kg_to_g($kg_price, $g_measure) {
	return $kg_price / (1000 / intval($g_measure));
}


// Change how quantities are displayed in the cart page.
add_filter( 'woocommerce_cart_item_quantity', 'change_woocommerce_cart_item_quantity', 10, 3 );

function change_woocommerce_cart_item_quantity($product_quantity, $cart_item_key, $cart_item ) {

	$sell_by_weight = get_post_meta( $cart_item['product_id'], 'sell_by_weight', true );
	$weight_measure = get_post_meta( $cart_item['product_id'], 'sell_weight_measure', true );

	if ($sell_by_weight == "yes" && $weight_measure != "") {
		return $product_quantity . sprintf( '&times; %s %s', $weight_measure, get_option('woocommerce_weight_unit') );
	} else {
		return $product_quantity;
	}

}


// Just remove the default quantity added after the name in the checkout page.
add_filter( 'woocommerce_checkout_cart_item_quantity', 'change_checkout_quantities', 10, 2 );

function change_checkout_quantities ( $quantity, $cart_item ) {

	// $sell_by_weight = get_post_meta( $cart_item['product_id'], 'sell_by_weight', true );
	// $weight_measure = get_post_meta( $cart_item['product_id'], 'sell_weight_measure', true );

	// if ($sell_by_weight == "yes" && $weight_measure != "") {
	// 	return sprintf( '<div class="product-quantity">(%s %s)</div>', $cart_item['quantity'] * $weight_measure, get_option('woocommerce_weight_unit') );
	// } else {
	// 	return sprintf( '<div class="product-quantity">(%s ud)</div>', $cart_item['quantity'] );
	// }

	return;

}


// Sets the right value for items sold by weight internally once they reach the cart page.
// By changing the price internally it is ready for the successive steps like checkout.
add_action( 'woocommerce_before_calculate_totals', 'wc_recalc_prices_by_weight' );
 
function wc_recalc_prices_by_weight( $cart_object ) {
	foreach ( $cart_object->get_cart() as $cart_item_key => $cart_item ) {
		$product_id = $cart_item['product_id'];
		$sell_by_weight = get_post_meta( $product_id, 'sell_by_weight', true );
		$weight_measure = get_post_meta( $product_id, 'sell_weight_measure', true );
		if ($sell_by_weight == "yes" && $weight_measure != "") {
			$price_num = wc_get_product( $product_id )->get_price();
			$cart_item['data']->set_price( $price_num / (1000 / intval($weight_measure)) );
		}
	}
}


// Changes how quantities (including price) are displayed in the mini cart widget.
// Includes the total price for each item as well as if it's sold by weight or by unit.
add_filter('woocommerce_widget_cart_item_quantity', 'custom_wc_widget_cart_item_quantity', 10, 3 );
function custom_wc_widget_cart_item_quantity( $output, $cart_item, $cart_item_key ) {
	$product_id = $cart_item['product_id'];
	$product_quantity = $cart_item['quantity'];
	$sell_by_weight = get_post_meta( $product_id, 'sell_by_weight', true );
	$weight_measure = get_post_meta( $product_id, 'sell_weight_measure', true );
	$price_num = wc_get_product( $product_id )->get_price();
	if ($sell_by_weight == "yes" && $weight_measure != "") {
		// Don't use $cart_item['data']->get_price() because since sometimes the calculate_totals hook has already calculated the price it gets formatted twice.
		$weight_unit = get_option('woocommerce_weight_unit');
		$price_grams = convert_price_from_kg_to_g($price_num, $weight_measure);
		$price_formatted = wc_price( $price_grams );
		$total_price = wc_price($product_quantity * $price_grams);
		$sold_by = $weight_measure . " " . $weight_unit;
		$total_weight = $product_quantity * $weight_measure . " " . $weight_unit;
		return cart_widget_quantity_html($price_formatted, $sold_by, $product_quantity, $total_price, $total_weight);
	} else {
		$total_price = wc_price($product_quantity * $price_num);
		return cart_widget_quantity_html(wc_price($price_num), 'ud', $product_quantity, $total_price);
	}
}


/**
 * Creates a string that corresponds with the HTML for an item quantity in the widget cart.
 */
function cart_widget_quantity_html($single_price, $sold_by, $quantity, $total_price, $total_weight = "") {
	if ($total_weight != "") {
		$total_weight = sprintf('<br><span>(%s)</span>', $total_weight);
	}
	return sprintf('<div class="widget_cart_quantity_container"><div>%s<span> / %s</span></div><div><span>&times; %s</span></div><div class="subtotal">%s%s</div></div>', $single_price, $sold_by, $quantity, $total_price, $total_weight);
}


// Add to the subtotal the amount in weight or units in the cart and checkout pages. 
add_filter( 'woocommerce_cart_item_subtotal', 'filter_woocommerce_cart_item_subtotal', 10, 3 ); 

function filter_woocommerce_cart_item_subtotal( $wc, $cart_item, $cart_item_key ) { 

	$sell_by_weight = get_post_meta( $cart_item['product_id'], 'sell_by_weight', true );
	$weight_measure = get_post_meta( $cart_item['product_id'], 'sell_weight_measure', true );

	if ($sell_by_weight == "yes" && $weight_measure != "") {
		return sprintf( '%s (%s %s)', $wc, $cart_item['quantity'] * $weight_measure, get_option('woocommerce_weight_unit') );
	} else {
		return sprintf( '%s (%s ud)', $wc, $cart_item['quantity'] );
	}

};


// Change cart in menu total amount to display the total of different types of products.
add_filter( 'astra_woo_header_cart_total', 'custom_cart_total' );

function custom_cart_total() {
	echo count( WC()->cart->get_cart() );
}


// ****************************************************************************
// *************** LIVE PRICE / AMOUNT DISPLAY ON PRODUCT PAGE ****************
// ****************************************************************************

function action_wc_live_product_total() {
	global $product;
	
	$sell_by_weight = $product->get_meta('sell_by_weight');
	$weight_measure = $product->get_meta('sell_weight_measure');
	$sold_by_unit = '';
	// Important: The product price in case of weight is by Kg.
	$price_num = $product->price;

	if ($sell_by_weight == "yes" && $weight_measure != "") {
		$price_num = convert_price_from_kg_to_g($price_num, $weight_measure);
		$amount = $weight_measure;
		$sold_by_unit = get_option('woocommerce_weight_unit');
	} else {
		$amount = '1';
		$sold_by_unit = 'ud';
	}

	// If there is only one in stock and is not variable the number input is not displayed so neither this should be displayed.
	$visible = '';
	if ( !$product->is_type( 'variable' ) && $product->stock_quantity <= 1) { 
		$visible = 'display:none;';
	}

	echo '<div class="product_total" style="' . $visible . 'order:1;font-weight:bold;"><span id="total_product_price" data-initial-price="' . $price_num . '"></span>&nbsp;' . get_woocommerce_currency_symbol() . ' /&nbsp;<span id="total_product_amount" data-initial-amount="' . $amount . '"></span>&nbsp;' . $sold_by_unit . '</div>';
};

add_action( 'woocommerce_before_add_to_cart_button', 'action_wc_live_product_total', 10, 0 );


// ****************************************************************************
// **** MESSAGES ON SHIPPING AREA TO ADD EXTRA INFO DEPENDING ON CONDITIONS ***
// ****************************************************************************

// Show a message depending on the total amount and the shipping methods avaialble.
function action_woocommerce_after_shipping_calculator() {
	//$total_price = WC()->cart->total;
	// Get the customer post code.
	//echo WC()->customer->get_shipping_postcode();
	// Get the shipping methods.
	//print_r(WC()->shipping->get_shipping_methods());
	// Get the packages.
	//print_r(WC()->shipping()->get_packages());

	/* FRAGMENT OF OLD CODE THAT WAS SHOWING A DIFFERENT MESSAGE DEPENDING ON THE SHIPPING METHODS AVAILABLE. NOT RELIABLE.
	// Get the available shipping methods for the first package.
	// TODO first package hard coded is not a good idea, however they don't expect to be using the packages feature.
	$available_methods = WC()->shipping()->get_packages()[0]['rates'];
	
	$local_pickup = 0;
	$flat_rate = 0;
	$free_shipping = 0;
	
	// Loop the array and count how many of each type there are.
	array_walk($available_methods, function($item) use(&$local_pickup, &$flat_rate, &$free_shipping) {
		if (strpos($item->id, 'local_pickup') !== false) {
			$local_pickup++;
		} else if (strpos($item->id, 'flat_rate') !== false || strpos($item->id, 'jem_table_rate') !== false) {
			$flat_rate++;
		} else if (strpos($item->id, 'free_shipping') !== false) {
			$free_shipping++;
		}
	});

	$message = '';
	
	// Choose the message based on the available methods.
	if ($local_pickup > 0 && $flat_rate == 0 && $free_shipping == 0) {
		$message = "<span style=\"font-weight:bold;\">L'enviament a domicili está disponible només amb una compra mínima de 25€.</span>"; // TODO Esto ya no aparece nunca.
	} else if ($flat_rate > 0 && $free_shipping == 0) {
		$message = "<span>L'enviament és gratuït a partir de 80€ i també a Sant Feliu de Llobregat, Sant Joan Despí, Sant Just Desvern i Molins de Rei a partir de 25€.</span>";
	}

	if ($message !== '') {
		echo "<div style=\"padding: 5px;border: 1px solid #ddd;margin-bottom: 10px; background-color: #e1e1e1;\">" . $message . "</div>";
	}
	*/

	// Shows the message only if it has been set.
	$shipping_methods_message_in_cart = get_option( 'shipping_methods_cart_message' );
	if (!empty($shipping_methods_message_in_cart)) {
		echo "<div style=\"padding: 6px;border: 1px solid #ddd;margin-bottom: 10px; background-color: #fbfbfb;\">" . $shipping_methods_message_in_cart . "</div>";
	}
};

add_action( 'woocommerce_before_shipping_calculator', 'action_woocommerce_after_shipping_calculator', 10, 0 ); 


// ****************************************************************************
// ******** ADDITIONAL CUSTOM FIELDS ON THE SHIPPING METHODS SETTINGS *********
// ****************************************************************************

// Adds a custom field to each shipping method using a filter at woocommerce start.
add_action('woocommerce_init', 'shipping_instance_form_fields_filters');

function shipping_instance_form_fields_filters() {
  $shipping_methods = WC()->shipping->get_shipping_methods();
	foreach($shipping_methods as $shipping_method) {
		if ($shipping_method->id == "flat_rate") {
			add_filter('woocommerce_shipping_instance_form_fields_' . $shipping_method->id, 'flat_rate_extra_fields', 10, 1);
			add_filter('woocommerce_shipping_instance_form_fields_' . $shipping_method->id, 'free_shipping_and_flat_rate_extra_fields', 10, 1);
		} else if ($shipping_method->id == "free_shipping") {
			add_filter('woocommerce_shipping_instance_form_fields_' . $shipping_method->id, 'free_shipping_and_flat_rate_extra_fields', 10, 1);
		}
    add_filter('woocommerce_shipping_instance_form_fields_' . $shipping_method->id, 'shipping_instance_form_add_extra_fields', 10, 1);
  }
}

// Custom fields added to all shipping methods options.
function shipping_instance_form_add_extra_fields( $settings ) {
	// Field to save a custom text for the shipping method that can be displayed later on.
	$settings['shipping_custom_field_for_display'] = [
    'title'       => 'Información extra para mostrar',
    'type'        => 'text', 
		'placeholder' => 'Entrega solo los lunes...',
		'desc_tip'		=> true,
    'description' => 'Añade información extra que puede resultar útil en relación al método de envío. Se muestra bajo el título del método de envío en la página del carrito y de pago.'
	];

  return $settings;
}

// Custom fields that are added only to free_shipping and flat_rate shipping options.
function free_shipping_and_flat_rate_extra_fields( $settings ) {
	// Field to indicate if it is a method for home delivery / entrega a domicilio.
	$settings['shipping_custom_field_is_home_delivery'] = [
		'title'       => 'Es entrega a domicilio',
		'label'       => 'Entrega a domicilio a ciertos municipios.',
		'type'        => 'checkbox',
		'desc_tip'    => true,
		'description' => 'Marcar si se trata de un método de tipo "entrega a domicilio". Esto permite limitar ciertos productos mediante clases de envío a solo los métodos de envío que tienen esta opción marcada.'
	];

	// TODO add option to shipping methods flat_rate and free_shipping to indicate if their minimum
	// should compared against the real cart total or the one without special products?

	return $settings;
}

// Custom fields that are added only for the flat rate shipping options.
function flat_rate_extra_fields( $settings ) {
	$settings['flat_rate_shipping_custom_field_minimum_price'] = [
    'title'       => 'Coste mínimo',
    'type'        => 'number', 
		'placeholder' => '10',
		'desc_tip'		=> true,
    'description' => 'Los usuarios tendrán que gastar esta cantidad para ver este método. Si el campo "Precio antes del mínimo" se especifica, se aplicará ese precio hasta que no se alcance este mínimo, si no se especifica, este método simplemente no aparecera hasta no alcanzar el mínimo.'
	];

	$settings['flat_rate_shipping_custom_field_price_before_minimum'] = [
    'title'       => 'Precio antes del mínimo',
    'type'        => 'number', 
		'placeholder' => '5',
		'desc_tip'		=> true,
    'description' => 'Precio que se aplicará al método de envío si el coste del carrito no llega al mínimo especificado en "Coste mínimo". En el caso de que "Coste mínimo" no tenga valor este campo será ignorado.'
	];

	return $settings;
}


// ****************************************************************************
// ** DISPLAY THE TEXT ADDED TO THE SHIPPING METHOD ON CART & CHECKOUT PAGES **
// ****************************************************************************

// Shows custom data stored for shipping methods under its label in the cart and checkout pages.
function action_show_custom_shipping_method_data( $method, $package_index ) {
	$formatted_method_id = $method->method_id . "_" . $method->instance_id;
	$text = get_option('woocommerce_' . $formatted_method_id . '_settings')['shipping_custom_field_for_display'];
	if ( !empty( $text ) ) {
		// TODO add another text field to input a color as string to be used to style the text.
		echo "<br><small>" . __( $text ) . "</small>";
	}
}

add_action( 'woocommerce_after_shipping_rate', 'action_show_custom_shipping_method_data', 10, 2 );


// ***********************************************************************************************
// *********************************** MODIFY SHIPPING METHODS ***********************************
// ***********************************************************************************************

/**
 * The accumulated total for all the cart products excluding products "no-shipping-no-count" (products
 * that are only for home delivery and local pickup, and that their price doesn't add to the free minimum).
 */
function get_cart_total_without_special_products() {
	$cart_contents_total_without_no_shipping_special = 0;
	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( $cart_item['data']->get_shipping_class() != 'no-shipping-no-count' ) {
			$cart_contents_total_without_no_shipping_special += ($cart_item['data']->price * $cart_item['quantity']);
		}
	}
	return $cart_contents_total_without_no_shipping_special;
}

/**
 * @param array $rates Array of rates found for the package.
 */
function modify_shipping_methods($rates) {
	// Cart total without special products for home delivery methods.
	$cart_total_without_specials = get_cart_total_without_special_products();
	// Real cart total for non home delivery methods.
	$cart_total = WC()->cart->cart_contents_total + WC()->cart->tax_total;

	foreach ( $rates as $rate_key => $rate ) {
		// If it is flat_rate and home delivery use the cart_total_without_specials, if it is flat_rate but no home delivery use cart_total.
		if ( 'flat_rate' === $rate->method_id) {
			$is_home_delivery = get_option('woocommerce_' . str_replace(":", "_", $rate->id) . '_settings')['shipping_custom_field_is_home_delivery'];
			$minimum_price = get_option('woocommerce_' . str_replace(":", "_", $rate->id) . '_settings')['flat_rate_shipping_custom_field_minimum_price'];
			if (($is_home_delivery == "yes" && $cart_total_without_specials < $minimum_price) || ($is_home_delivery != "yes" && $cart_total < $minimum_price)) {
				// If the "price before" also exists apply it, othewise hide the method.
				$price_before = get_option('woocommerce_' . str_replace(":", "_", $rate->id) . '_settings')['flat_rate_shipping_custom_field_price_before_minimum'];
				if ($price_before) {
					$rate->cost = $price_before;
				} else {
					unset( $rates[ $rate_key ] );
				}
			}
		} else if ( "free_shipping" === $rate->method_id ) {
			// If it is free_shipping and home delivery remove it if the cart_total_without_specials is below its minimum.
			$is_home_delivery = get_option('woocommerce_' . str_replace(":", "_", $rate->id) . '_settings')['shipping_custom_field_is_home_delivery'];
			$minimum_price = get_option('woocommerce_' . str_replace(":", "_", $rate->id) . '_settings')['min_amount'];
			if ($is_home_delivery == "yes" && $cart_total_without_specials < $minimum_price) {
				unset( $rates[ $rate_key ] );
			}
		}
	}

	// OTHER LOGIC TO REMOVE PAID SHIPPING METHODS WHEN FREE ONES ARE AVAILABLE.
	// foreach ( $rates as $rate_key => $rate ) {
	// 	if ( 'free_shipping' === $rate->method_id || 'local_pickup' === $rate->method_id ) {
	//    Here I would get only the free shipping and local pickup ones, skipping the paid ones...
	// 	}
	// }

	return $rates;	
}

add_filter( 'woocommerce_package_rates', 'modify_shipping_methods', 100 );


// ****************************************************************************
// ********* ADDITIONAL MESSAGES FOR PRODUCTS ON PRODUCT PAGE & CART **********
// ****************************************************************************

// Adds an info text under the cart items that are 'no-shipping' and 'no-shipping-no-count' if the text value has been set from the custom options page.
function action_after_cart_item_title( $cart_item, $cart_item_key ) {
	$item_shipping_class = $cart_item['data']->get_shipping_class();
	if ($item_shipping_class == 'no-shipping' || $item_shipping_class == 'no-shipping-no-count') {
		$cart_item_message_only_pickup_and_home = get_option( 'only_pickup_and_home_cart_item_message' );
		$shipping_methods_info_page_url = get_option( 'url_to_page_with_shipping_methods_info' );
		$custom_product_message = "";
		if (!empty($cart_item_message_only_pickup_and_home)) {
			$custom_product_message = '<small>' . $cart_item_message_only_pickup_and_home . '</small>';
			if (!empty($shipping_methods_info_page_url)) {
				$custom_product_message = '<a href="' . $shipping_methods_info_page_url . '" target="_blank">' . $custom_product_message . '</a>';
			}
			echo '<div>' . $custom_product_message . '</div>';
		}
	}
};

add_action( 'woocommerce_after_cart_item_name', 'action_after_cart_item_title', 10, 2 );


// Add a info text on the product page indicating that the product is only for local pickup and home delivery.
function action_add_no_shipping_waring() {
	$delivery_methods_message = get_option( 'product_only_pickup_and_home_delivery_message' );
	if (!empty($delivery_methods_message)) {
		global $product;
		$product_shipping_class = $product->get_shipping_class();
		if ($product_shipping_class == 'no-shipping' || $product_shipping_class == 'no-shipping-no-count') {
			echo "<p style=\"background-color:#f7f6f7;padding:10px;font-weight:bold;\">" . $delivery_methods_message . "</p>";
		}
	}
}

add_action('woocommerce_single_product_summary', 'action_add_no_shipping_waring', 15);


// Show payment methods available in the product page.
function action_add_payment_methods_available() {
	$show_payment_methods = get_option( 'show_payment_methods_in_product_page' );
	if (!empty($show_payment_methods)) {
		$available_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
		if (!empty($available_payment_methods)) {
			echo '<div>
			<p style="margin-bottom:0;"><strong>Mètodes de pagament acceptats:</strong></p>
			<ul>';
			foreach( $available_payment_methods as $method ) {
				echo '<li>' . $method->title . '</li>';
			}
			echo '</ul>
			</div>';
		}
	}
}

add_action('woocommerce_single_product_summary', 'action_add_payment_methods_available', 50);


// ****************************************************************************
// ******* ACTION OF REMOVING NO SHIPPING PRODUCTS IF URL PARAM IS SET ********
// ****************************************************************************

// Register a custom url query parameter to use it later.
function add_get_val() {
	global $wp;
	$wp->add_query_var('remove_noshipping_items');
}
add_action('init', 'add_get_val');


// Runs at the beginning of the checkout page to remove items with no shipping if the query parameter is set in the url.
function remove_no_shipping_items_if_param_exists() {
	$remove_noshipping_items = get_query_var('remove_noshipping_items');
	//$remove_noshipping_items = $_GET['remove_noshipping_items']; /* This would also work but is not recommended. */
	if ($remove_noshipping_items === 'true') {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$item_shipping_class = $cart_item['data']->get_shipping_class();
			if ( $item_shipping_class == 'no-shipping' || $item_shipping_class == 'no-shipping-no-count' ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
 		}
	}
}

add_action('woocommerce_before_checkout_form', 'remove_no_shipping_items_if_param_exists');


// ****************************************************************************
// ******************** SAVE PRODUCT NUTRITION FACTS INFO *********************
// ****************************************************************************

// Change the "add to cart" button text on product page.
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text' ); 
function woocommerce_custom_single_add_to_cart_text() {
  return __( 'Afegeix', 'woocommerce' ); 
}

// Change the "add to cart" button text on product archive (collection) page.
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_custom_product_add_to_cart_text', 10, 2 );
function woocommerce_custom_product_add_to_cart_text( $output, $instance ) {
	global $product;
	// Possible values: 'simple', 'grouped', 'variable', 'external'
	if ( !$product->is_type( 'variable' ) && $instance->stock_status == "instock" ) {
		return __( 'Afegeix', 'woocommerce' );
	} else {
		return $output;
	}
}


// ****************************************************************************
// *********** PRIVACY POLICY ACCEPTANCE CHECKBOX ON CHECKOUT PAGE ************
/**
 * @see https://themeskills.com/add-privacy-checkbox-gdpr-woocoommerce-checkout/
 */ 

add_action( 'woocommerce_review_order_before_submit', 'add_privacy_checkbox', 9 );

function add_privacy_checkbox() {
	woocommerce_form_field( 'privacy_policy', array(
		'type' => 'checkbox',
		'class' => array('form-row privacy'),
		'label_class' => array('woocommerce-form__label woocommerce-form__label-for-checkbox checkbox'),
		'input_class' => array('woocommerce-form__input woocommerce-form__input-checkbox input-checkbox'),
		'required' => true,
		'label' => 'He llegit i accepto la <a href="/politica-de-privacitat">política de privacitat</a>',
	));
}

add_action( 'woocommerce_checkout_process', 'privacy_checkbox_error_message' );

function privacy_checkbox_error_message() {
	if ( ! (int) isset( $_POST['privacy_policy'] ) ) {
		wc_add_notice( __( 'Has d\'acceptar la nostra política de privacitat per poder continuar.' ), 'error' );
	}
}


// ****************************************************************************
// ************************ ADD WEIGHT TO ORDER ITEMS *************************
// ****************************************************************************
// This way it is displayed in the order page that the user sees as well as the admin one.

add_action( 'woocommerce_checkout_create_order_line_item', 'custom_checkout_create_order_line_item', 20, 4 );

function custom_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {

	$sell_by_weight = get_post_meta( $item['product_id'], 'sell_by_weight', true );
	$weight_measure = get_post_meta( $item['product_id'], 'sell_weight_measure', true );
	
	if ($sell_by_weight == "yes" && $weight_measure != "") {
		$product_quantity = $item['quantity'];
		$item->update_meta_data( 'Pes total', $product_quantity * $weight_measure . " " . get_option('woocommerce_weight_unit') );
	}

}