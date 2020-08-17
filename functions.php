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
		$nutrition_table_rows .= create_table_row( __( 'Greixos saturats', 'woocommerce' ), $nutrition_facts['saturated_fats'] );
	}

	if (array_key_exists('carbs', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'Hidrats de carboni', 'woocommerce' ), $nutrition_facts['carbs'] );
	}

	if (array_key_exists('sugar', $nutrition_facts)) {
		$nutrition_table_rows .= create_table_row( __( 'Sucres', 'woocommerce' ), $nutrition_facts['sugar'] );
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

function create_table_row( $label, $value) {
	$table_rows = '<tr class="woocommerce-product-attributes-item">';
	$table_rows .= '<th class="woocommerce-product-attributes-item__label">' . $label . '</th>';
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
		'options'     => array( '' => 'Selecciona una opción', '50' => '50', '100' => '100'),
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
		if ($weight_measure == '100') {
			$price_formatted = format_price_by_weight_from_kg_to_g($price_num, 100);
		} else if ($weight_measure == '50') {
			$price_formatted = format_price_by_weight_from_kg_to_g($price_num, 50);
		}
		return sprintf( '<span class="amount">%s %s / %s %s</span>', $price_formatted, get_woocommerce_currency_symbol(), $weight_measure, get_option('woocommerce_weight_unit') );
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
// 		if ($weight_measure == '100') {
// 			return '<span class="woocommerce-Price-amount amount">' . format_price_by_weight_from_kg_to_g($formatted_price, 100) . ' <span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol() . '</span></span>';
// 		} else if ($weight_measure == '50') {
// 			return '<span class="woocommerce-Price-amount amount">' . format_price_by_weight_from_kg_to_g($formatted_price, 50) . ' <span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol() . '</span></span>';
// 		}
// 	} else {
// 		return $price;
// 	}

// }


/**
 * Formats the price by weight for the specified grams measure.
 * It uses the price by weight for 1 Kg.
 * It also formats the price with the decimal and thousand symbols specified in the Woocommerce settings.
 * @param {number} The price for 1 Kg.
 * @param {number} The amount of grams to format the price to. For example by 100 g.
 * @return {string} The string representation of the price.
 */
function format_price_by_weight_from_kg_to_g($kg_price, $g_measure) {
	return number_format($kg_price / (1000 / $g_measure), 2, wc_get_price_decimal_separator(), wc_get_price_thousand_separator());
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
			$price = $cart_item['data']->get_price();
			if ($weight_measure == '100') {
				$cart_item['data']->set_price( $price / 10 );
			} else if ($weight_measure == '50') {
				$cart_item['data']->set_price( $price / 20 );
			}
		}
	}
}


// Changes how quantities (including price) are displayed in the mini cart widget.
add_filter('woocommerce_widget_cart_item_quantity', 'custom_wc_widget_cart_item_quantity', 10, 3 );
function custom_wc_widget_cart_item_quantity( $output, $cart_item, $cart_item_key ) {
	$product_id = $cart_item['product_id'];
	$sell_by_weight = get_post_meta( $product_id, 'sell_by_weight', true );
	$weight_measure = get_post_meta( $product_id, 'sell_weight_measure', true );
	if ($sell_by_weight == "yes" && $weight_measure != "") {
		$price_num = $cart_item['data']->get_price();
		if ($weight_measure == '100') {
			$price_formatted = format_price_by_weight_from_kg_to_g($price_num, 100);
		} else if ($weight_measure == '50') {
			$price_formatted = format_price_by_weight_from_kg_to_g($price_num, 50);
		}
		return sprintf( '<span class="quantity">%s &times; <span class="woocommerce-Price-amount amount">%s <span class="woocommerce-Price-currencySymbol">%s</span></span></span>', $cart_item['quantity'], $price_formatted, get_woocommerce_currency_symbol() );
	} else {
		return $output;
	}
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