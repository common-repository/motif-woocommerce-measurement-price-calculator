<?php 

/*
Plugin Name: Motif: Woocommerce Measurement Price calculator
Plugin URI: http://motif-solution.com
Description: For the purpose of calculating price based on customer measurements.
Author: motifsolution
Version: 1.0.1
Developed By: motifsolution
Author URI: http://motif-solution.com/
Support: http://support@motifsolution.com
textdomain: motif-woocommerce-price-calculator
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

//If not user for security purpose
if ( ! defined( 'ABSPATH' ) ) exit; 

	//Exit if woocommerce not installed
	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

		function my_admin_notice() {
		// Deactivate the plugin
		deactivate_plugins(__FILE__);
	
		$error_message = __('<div class="error notice"><p>This plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugin to be installed and active!</p></div>', 'motif-woocommerce-price-calculator');
	
		die($error_message);
		}
	
	add_action( 'admin_notices', 'my_admin_notice' );
}

//Motif woo price calculator main class
class MOTIF_PRICE_CALCULATOR_MAINCLASS {
	
	//constructor
	public function __construct() {
		
		$this->moduling_constant();

		add_action('wp_loaded', array( $this, 'motif_scripts_styles_enqueue'));

		if(is_admin()) {
			
			require_once( MOTIF_PIRCECALULATOR_INVOICES_DIR.'motif-woocommerce-price-calculator-admin.php');
		}

		add_filter( 'woocommerce_is_purchasable', array($this, 'motif_product_purchasable_measurement'), 1, 2 );

		add_action( 'woocommerce_before_add_to_cart_button', array($this,'motif_calculation_fields_single_product' ));

		add_filter( 'woocommerce_add_cart_item_data', array($this,'addProductToCart'), 10, 2 );

		add_filter( 'woocommerce_add_cart_item', array($this,'add_cart_item'), 20, 1);

		add_filter( 'woocommerce_get_cart_item_from_session', array($this, 'get_cart_item_from_session'), 10, 2);

		add_filter( 'woocommerce_get_item_data', array($this,'getting_car_item_data'), 10, 2 );	

		add_filter( 'woocommerce_cart_item_price', array($this,'motif_woocommerce_cart_item_price'), 10, 3 );

		add_filter( 'woocommerce_product_tabs', array($this,'motif_woo_ranges_product_tab' ));

		add_action( 'wp_ajax_simple_price_calculation', array($this,'motif_calculation_pricesfun' ));
		add_action( 'wp_ajax_nopriv_simple_price_calculation', array($this,'motif_calculation_pricesfun' ));

		if(!is_admin()) { 
		
			add_filter( 'woocommerce_get_price_html', array($this,'moti_diplay_product_price' ));
		}
	
		add_action('woocommerce_before_add_to_cart_form', array($this,'isa_before_add_to_cart_form'));

		add_filter( 'woocommerce_loop_add_to_cart_link', array($this,'motif_must_add_product_quty' ));

		add_action( 'woocommerce_add_order_item_meta',  array($this, 'add_order_item_meta') , 10, 2 );

		add_action('woocommerce_after_add_to_cart_form', array($this,'motif_single_page_view_table'));
	}

	//simple measurements calcualtion callback
	function motif_calculation_pricesfun() {
		
		global $wpdb;

			if(isset($_POST['condition']) && $_POST['condition'] == "simple_measurements_motif" ) {

				$quantity = $_POST['quantity'];
				
				$produt_id = $_POST['product_id'];

				$ranges = get_post_meta($produt_id, '_ranges_motif', true);
				$min_price = get_post_meta($produt_id, 'motif_minimum_price_range', true);

				$flag = 0;
 				
 				foreach ($ranges as $qrange) {
     
	    			if($quantity >= $qrange['start_rang'] && $quantity <= $qrange['end_rang'] ) { 
	      
				  		if($qrange['sale_rang'] != "") {
						   
							$total_net = $quantity * $qrange['sale_rang'];
						   
						    $flag = 1;

				  		} else {
						   
						    $total_net = $quantity * $qrange['regular_price'];
						   
						    $flag = 1;
						}
	  				}
	  			}
  
  				if ($flag == 1) {
				   
				   echo $total_net;
				
				} else {
				
				   echo $total_net = $quantity * $min_price;
				
				}
			}
		
		die();
	}

	// if measurement product set to purchasable
	public function motif_product_purchasable_measurement( $purchasable, $product ) {

		$motif_m_product = get_post_meta($product->get_id(), 'motif_measurement_product', true);

		if($motif_m_product == "yes" ) {

			if( $product->get_price() == 0 || $product->get_price() == '' ) {
	
	        	$purchasable = true;
	    	} 
		}
	    
		return $purchasable;

	}

	// front fields and values
	public function motif_calculation_fields_single_product() { 
		
		global $post;

	    $currency = get_woocommerce_currency();
		$string = get_woocommerce_currency_symbol( $currency ); 

		$motif_m_product = get_post_meta($post->ID, 'motif_measurement_product', true);
		$motif_product_type = get_post_meta( $post->ID, 'motif_measurement_unit', true );
		$unit	= get_post_meta($post->ID, 'motif_'.$motif_product_type.'_unit', true); 

		$m_quantity_field_label = get_post_meta( $post->ID, 'motif_'.$motif_product_type.'_label', true ); 

		// simple measurements
		if($motif_m_product == "yes" && $motif_product_type == "weight"  || $motif_product_type == "volume" || $motif_product_type == "area") { 

			require_once( MOTIF_PIRCECALULATOR_INVOICES_DIR.'front/motif-front-sample-product.php' );
		} 

	}
	
	 //function to get item/fields for cart
	public function addProductToCart( $cart_item_data, $product_id ) {

	    $motif_m_product = get_post_meta($product_id, 'motif_measurement_product', true);
	    $motif_product_type = get_post_meta( $product_id, 'motif_measurement_unit', true ); 

		if(isset($motif_m_product) && $motif_m_product  == 'yes') {

		    if( isset( $_REQUEST['motif_cal_price'] ) ) {

		        $cart_item_data[ 'motif_cal_price' ] = $_REQUEST['motif_cal_price'];

		        $cart_item_data['unique_key'] = md5( microtime().rand() );

		        $cart_item_data['reqweight'] = $_REQUEST['reqweight'];

		        $cart_item_data['product_type'] = $_REQUEST['product_type'];
		    }
	    }

	    return $cart_item_data;
	}

	//calculation for cart and sending total value
	public function add_cart_item($cart_items) {

		global $wpdb, $woocommerce;
		
		$motif_m_product = get_post_meta($cart_items['product_id'], 'motif_measurement_product', true);
		$motif_product_type = get_post_meta( $cart_items['product_id'], 'motif_measurement_unit', true ); 
		
		if(isset($motif_m_product) && $motif_m_product  == 'yes') {

			$total_net = $cart_items['motif_cal_price'];

			$cart_items['data']->set_price($total_net);	

			return $cart_items;
		
		} else {

			return $cart_items;
		}
	}

	//get cart item in all the pages via ajax session	
	public function get_cart_item_from_session($cart_items, $values) {
		
		$cart_items = $this->add_cart_item($cart_items);

		return $cart_items;
	}

	//dispaly option fields in cart
	public function getting_car_item_data( $cart_data, $carti = null ) {
	   

	    $custom_items = array();
	    
	    if( !empty( $cart_data ) ) {
	    
	        $custom_items = $cart_data;
	    }

	    if( isset( $carti['reqweight'] ) ) {
	        
	        if(isset($carti['product_type']) && $carti['product_type'] == "motif_weight_product") {

	        	$custom_items[] = array( 'name' => __('Required Weight', 'motif-woocommerce-price-calculator'), 'value' => $carti['reqweight'] );
	        }

	        if(isset($carti['product_type']) && $carti['product_type'] == "motif_volume_product") {

	        	$custom_items[] = array( 'name' => __('Required Volume', 'motif-woocommerce-price-calculator'), 'value' => $carti['reqweight'] );
	        }

	        if(isset($carti['product_type']) && $carti['product_type'] == "motif_area_product") {

	        	$custom_items[] = array( 'name' => __('Required Area', 'motif-woocommerce-price-calculator'), 'value' => $carti['reqweight'] );
	        }

	    }
	    
	    return $custom_items;
	}

	//setting prices single range
	function motif_woocommerce_cart_item_price( $sale_item_price, $cart_item, $cart_item_key ) {

	  	$motif_m_product = get_post_meta($cart_item['product_id'], 'motif_measurement_product', true); 	
		
		if(isset($motif_m_product) && $motif_m_product  == "yes") {

		  	$weight_needed = $cart_item['reqweight'];

		 	$cart_item['line_total'];

		 	$cart_item['quantity'];

		 	$item_sale_price_own = $cart_item['line_total'] / $weight_needed;

		 	$sale_item_price  = wc_price($item_sale_price_own / $cart_item['quantity']);
		 	
		 	return $sale_item_price;
		  	
	  	} else {

	  		return $sale_item_price;
	  	}
	}

	// adding tab for showing user product ranes
	public function motif_woo_ranges_product_tab( $tabs ) {
	
	   	global $post;

		$motif_m_product = get_post_meta($post->ID, 'motif_measurement_product', true);

		if($motif_m_product == "yes" ) {

		    $tabs['desc_tab'] = array(
		        'title'     => __( 'Measurement Price Ranges', 'motif-woocommerce-price-calculator' ),
		        'priority'  => 50,
		        'callback'  => array($this,'woo_new_product_tab_content')
		    );

		    return $tabs;
		}
	}

	// tab callback function for ranges
	public function woo_new_product_tab_content() {
	  
	  	global $post;

	  	$currency = get_woocommerce_currency();
		$curency_symbol = get_woocommerce_currency_symbol( $currency );

		$motif_product_type = get_post_meta( $post->ID, 'motif_measurement_unit', true ); 
		$unit	= get_post_meta($post->ID, 'motif_'.$motif_product_type.'_unit', true);
		$ranges	= get_post_meta($post->ID, '_ranges_motif', true); ?>

		<div class="bootstrap-iso">
			<table class="table table-striped">
				<thead>
					<tr>
						<th><?php _e( 'Ranges', 'motif-woocommerce-price-calculator' ); ?></th>
						<th><?php _e( 'Regular Price', 'motif-woocommerce-price-calculator' ); ?> (<?php echo $unit; ?>)</th>
						<th><?php _e( 'Sale Price', 'motif-woocommerce-price-calculator' ); ?> (<?php echo $unit; ?>)</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $ranges as $range ) { ?>
						<tr>
							<td><?php echo $range['start_rang'] ?> - <?php echo $range['end_rang'] ?> - <?php echo $unit; ?></td>
						<?php if($range['sale_rang'] == "" ) { ?>	
							<td><del><?php echo $curency_symbol; ?> <?php echo $range['regular_price'] ?> - <?php echo $unit; ?><del></td>
						<?php } else { ?>
							<td><?php echo $curency_symbol; ?> <?php echo $range['regular_price'] ?> - <?php echo $unit; ?></td>
						<?php } ?>
						<?php if($range['sale_rang'] != "" ) { ?>
							<td><?php echo $curency_symbol; ?> <?php echo $range['sale_rang'] ?> - <?php echo $unit; ?></td>
						<?php } ?>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
			
	<?php }

	//function to disply pricing label
	function moti_diplay_product_price($price) {
		
		global $post, $wpdb;

		$motif_m_product = get_post_meta($post->ID, 'motif_measurement_product', true);
	   
	   	if($motif_m_product == 'yes') {
	   			
	   		$motif_product_type = get_post_meta( $post->ID, 'motif_measurement_unit', true ); 	
	   		$pricinglabel = get_post_meta($post->ID, 'motif_pricing_label_'.$motif_product_type, true);
	   		$ranges = get_post_meta($post->ID, '_ranges_motif', true);

	   		foreach($ranges as $range) {
			    
			    if ($range === reset($ranges)) {

			    	if($range['sale_rang'] != "") {

				    	$price = '<del>'. wc_price($range['regular_price']).' '.$pricinglabel. '</del> <ins>'. wc_price($range['sale_rang']).' '.$pricinglabel. '</ins>';

				    } else {

				    	$price = '<ins>'. wc_price($range['regular_price']).' '.$pricinglabel. '</ins>';
				    }

			    }
			    
			}

			return $price;

	   	} else {

	   		return $price;
	   	}
	}

	function isa_before_add_to_cart_form() {
 
	    global $post;
	 
	    $motif_m_product = get_post_meta($post->ID, 'motif_measurement_product', true);
	   
	   	if($motif_m_product == 'yes') {
	 
	   		$mini_price = get_post_meta($post->ID, 'motif_minimum_price_range', true);
	   		
	   		echo _e('Minimum Price ', 'motif-woocommerce-price-calculator').wc_price($mini_price);
	   	}
	 
	}

	//adding through measurements change link in shop page
	function motif_must_add_product_quty( $link ) {
	 	
	 	global $product;
	 
	 	$product_id = $product->get_id();
	 	
	 	$product_sku = $product->get_sku();
	 	
	 	$motif_m_product = get_post_meta($product_id, 'motif_measurement_product', true);

	 	if(isset($motif_m_product) && $motif_m_product  == 'yes') {

		 	$link = '<a href="'.get_permalink().'" rel="nofollow" data-product_id="'.$product_id.'" data-product_sku="'.$product_sku.'" data-quantity="1" class="button add_to_cart_button product_type_variable">Add to Cart</a>';
		 
	 	return $link;

	 	} else {

	 		return $link;
	 	}
	}

		//adding order meta in fields for retriving in admin order datail
	function add_order_item_meta( $item_id, $cart_item ) {

		if(isset($cart_item['reqweight'])) {

			$weight_need = $cart_item['reqweight'];

			if(isset($cart_item['product_type']) && $cart_item['product_type'] == "motif_weight_product") {

				$unites = get_post_meta($cart_item['product_id'], 'motif_weight_unit', true);
	        	wc_add_order_item_meta($item_id, 'Required Weight'.' '.$unites, $weight_need);
	        }

	        if(isset($cart_item['product_type']) && $cart_item['product_type'] == "motif_volume_product") {

				$unites = get_post_meta($cart_item['product_id'], 'motif_volume_unit', true);
	        	wc_add_order_item_meta($item_id, 'Required Volume'.' '.$unites, $weight_need);
	        }

	        if(isset($cart_item['product_type']) && $cart_item['product_type'] == "motif_area_product") {

				$unites = get_post_meta($cart_item['product_id'], 'motif_area_unit', true);
	        	wc_add_order_item_meta($item_id, 'Required Area'.' '.$unites, $weight_need);
	        }

		}
	}

	// single page view price table lightbox
	public function motif_single_page_view_table() { 
			
		global $post;

	  	$currency = get_woocommerce_currency();
		$curency_symbol = get_woocommerce_currency_symbol( $currency );

		$motif_product_type = get_post_meta( $post->ID, 'motif_measurement_unit', true ); 
		$unit	= get_post_meta($post->ID, 'motif_'.$motif_product_type.'_unit', true);
		$ranges	= get_post_meta($post->ID, '_ranges_motif', true); 

		$motif_m_product = get_post_meta($post->ID, 'motif_measurement_product', true);
	   
	   	if($motif_m_product == 'yes') { ?>


		<div class="bootstrap-iso">

			<button type="button" class="btn btn-info btn-xm" data-toggle="modal" data-target="#myModal">View Price Ranges</button>

  			<!-- Modal -->
			<div class="modal fade" id="myModal" role="dialog">
			    <div class="modal-dialog">
			    
			      <!-- Modal content-->
			      	<div class="modal-content">

			        <div class="modal-body">
			        		<table class="table table-striped">
								<thead>
									<tr>
										<th><?php _e( 'Ranges', 'motif-woocommerce-price-calculator' ); ?></th>
										<th><?php _e( 'Regular Price', 'motif-woocommerce-price-calculator' ); ?> (<?php echo $unit; ?>)</th>
										<th><?php _e( 'Sale Price', 'motif-woocommerce-price-calculator' ); ?> (<?php echo $unit; ?>)</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $ranges as $range ) { ?>
										<tr>
											<td><?php echo $range['start_rang'] ?> - <?php echo $range['end_rang'] ?> - <?php echo $unit; ?></td>
										<?php if($range['sale_rang'] == "" ) { ?>	
											<td><del><?php echo $curency_symbol; ?> <?php echo $range['regular_price'] ?> - <?php echo $unit; ?><del></td>
										<?php } else { ?>
											<td><?php echo $curency_symbol; ?> <?php echo $range['regular_price'] ?> - <?php echo $unit; ?></td>
										<?php } ?>
										<?php if($range['sale_rang'] != "" ) { ?>
											<td><?php echo $curency_symbol; ?> <?php echo $range['sale_rang'] ?> - <?php echo $unit; ?></td>
										<?php } ?>
										</tr>
									<?php } ?>
								</tbody>
							</table>
			        </div>
			        
			        <div class="modal-footer">
			          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			        </div>
			      
			      </div>
			      
			    </div>
			</div>

		</div>
			
	<?php } }

	//Motif module constant 
	public function moduling_constant() {

		if ( !defined( 'MOTIF_PIRCECALULATOR_INVOICES_URL' ) )
	    define( 'MOTIF_PIRCECALULATOR_INVOICES_URL', plugin_dir_url( __FILE__ ) );

	    if ( !defined( 'MOTIF_PIRCECALULATOR_INVOICES_BASENAME' ) )
	    define( 'MOTIF_PIRCECALULATOR_INVOICES_BASENAME', plugin_basename( __FILE__ ) );

	    if ( ! defined( 'MOTIF_PIRCECALULATOR_INVOICES_DIR' ) )
	    define( 'MOTIF_PIRCECALULATOR_INVOICES_DIR', plugin_dir_path( __FILE__ ) );
	}

	//Motif scripts and style
	public function motif_scripts_styles_enqueue() { 

		wp_enqueue_script('jquery');

		wp_enqueue_style( 'motif-bootstrap-css', plugins_url( '/css/bootstrap-iso.css', __FILE__ ), false );
		wp_enqueue_style( 'motif-backend-css', plugins_url( '/css/backend-style.css', __FILE__ ), false );

		wp_enqueue_script( 'motif-accounting-js-woo', plugins_url( 'scripts/accounting.min.js', __FILE__ ), false );
		wp_enqueue_script( 'motif-bootstrap-js', plugins_url( '/scripts/bootstrap.min.js', __FILE__ ), false);
		
		if ( function_exists( 'load_plugin_textdomain' ) )
				load_plugin_textdomain( 'motif-woocommerce-price-calculator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );	
	} 	

} new MOTIF_PRICE_CALCULATOR_MAINCLASS();

