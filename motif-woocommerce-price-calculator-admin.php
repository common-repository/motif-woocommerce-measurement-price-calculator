<?php 

// Motif woocommerce price calculator admin class
class MOTIF_PRICE_CALCULATOR_ADMINCLASS {
	
	//constructor
	public function __construct() {

		add_action( 'plugins_loaded', array($this,'motif_woomeasurement_type_registration'));

		add_filter( 'product_type_selector', array($this,'motif_porduct_register_selectbox' ));

		add_action( 'admin_footer', array($this,'motif_woomeasurement_product_js' ));

		add_filter( 'woocommerce_product_data_tabs', array($this,'motif_woomeasurement_product_tab_register' ));

		add_action( 'woocommerce_product_data_panels', array($this,'motif_woomeasurement_product_content' ));
		
		add_action( 'woocommerce_process_product_meta_motifcal', array($this,'save_measurement_option_field'  ));

		add_action( 'wp_ajax_deleting_saved_rulesact', array($this,'saved_rule_resetcallback' ));
		add_action( 'wp_ajax_nopriv_deleting_saved_rulesact', array($this,'saved_rule_resetcallback' ));

		add_action( 'woocommerce_product_options_general_product_data', array($this,'motif_minimum_price_measurement' ));
		add_action( 'woocommerce_process_product_meta', array($this,'motif_minimum_price_measurement_save' ));

		add_action('manage_posts_custom_column',  array($this,'motif_order_listing_columns_content'));
	}

	//setting price product listing for measurement products
	function motif_order_listing_columns_content($column) {
		
		global $post, $wpdb;
		
		$motif_m_product = get_post_meta($post->ID, 'motif_measurement_product', true);
		
		if($motif_m_product == 'yes') {  	
	   			
	   		$motif_product_type = get_post_meta( $post->ID, 'motif_measurement_unit', true ); 	
	   		$pricinglabel = get_post_meta($post->ID, 'motif_pricing_label_'.$motif_product_type, true);
	   		$ranges = get_post_meta($post->ID, '_ranges_motif', true);

	   		foreach($ranges as $range) {
			    
			    if ($range === reset($ranges)) {

			    	if($range['sale_rang'] != '') {

			    		switch ($column) {
						
							case 'price':
								
								echo '<del>'. wc_price($range['regular_price']).' '.$pricinglabel.'</del> <ins>'. wc_price($range['sale_rang']).' '.$pricinglabel.'</ins>';
							
							break;		
						}
			    	
			    	} else {

			    		switch ($column) {
						
							case 'price':
								
								echo '<ins>'. wc_price($range['regular_price']).' '.$pricinglabel.'</ins> <ins>';
							
							break;		
						}
			    	}
			    }
			}
		}
	}

	// saved ranges delete callback
	public function saved_rule_resetcallback() {
		
		if(isset($_POST['condition']) && $_POST['condition'] == "delete_saved_row") {

			// get ranges array key
			$row_delete_id = $_POST['row_id'];
			$array_key = $row_delete_id[0];
			
			// get post id for meta key
			$post_id = $_POST['post_id'];
			$saved_ranges = get_post_meta($post_id, '_ranges_motif', true);
			
			unset($saved_ranges[$array_key]);

			$actual_array = array_values($saved_ranges);
			
			update_post_meta( $post_id, '_ranges_motif', $actual_array);
		}
		
		die();
	}

	// register product type
	public function motif_woomeasurement_type_registration() {

		require_once( MOTIF_PIRCECALULATOR_INVOICES_DIR.'motif-woocommerce-price-calculator-productreg.php' );
	}

	// adding my product to menu
	public function motif_porduct_register_selectbox( $types ){

		// adding product in select box note: key should be same as in product register class
		$types[ 'motifcal' ] = __( 'Motif Woo-Measurements' );

		return $types;
	}

	//show if product tab have my product type
	public function motif_woomeasurement_product_js() {

		if ( 'product' != get_post_type() ) :
			
			return;
		
		endif; ?>
		
		<script type='text/javascript'>
			jQuery( document ).ready( function() {
				jQuery( '.options_group.pricing' ).addClass( 'show_if_motifcal' ).show();
			});

		</script>
	
	<?php }

	//adding product tab for my product type
	public function motif_woomeasurement_product_tab_register( $tabs) {

		$tabs['motifcal'] = array(
			'label'		=> __( 'Motif Measurement', 'motif-woocommerce-price-calculator' ),
			'target'	=> 'motifcal_option',
			'class'		=> array( 'show_if_motifcal' ),
		);
		return $tabs;
	}

	// product ranges and dimessions
	public function motif_woomeasurement_product_content() { ?>

		<div id='motifcal_option' class='panel woocommerce_options_panel'>

			<div class="bootstrap-iso">

				<ul class="nav nav-pills nav-justified indigo">
					<li class="active"><a data-toggle="tab" href="#m_dimen_section"><strong>Measurement Dimenssions</strong></a></li>
				    <li><a data-toggle="tab" href="#m_ranges_section"><strong>Measurement Ranges</strong></a></li>
				</ul>

				<div class="tab-content">
				    
				    <div id="m_dimen_section" class="tab-pane fade in active">
				  
						<?php require_once( MOTIF_PIRCECALULATOR_INVOICES_DIR.'/admin/motif-woo-pc-dimenssion.php' ); ?>

				    </div>
				   
				    <div id="m_ranges_section" class="tab-pane fade">

				    	<?php require_once( MOTIF_PIRCECALULATOR_INVOICES_DIR.'/admin/motif-woo-pc-range-view.php' ); ?>
				     	
				    </div>
				    <!-- End of measurement ranges section -->

				</div>
				<!-- End of tab content section -->

			</div>
			<!-- End of bootstrap iso section -->

		</div>
		<!-- End of main section motifcal_option -->
		
	<?php }

	// saving ranges meta callback
	public function save_measurement_option_field ($post_id) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

		// minimum value price for all ranges

		// select product measurement type
		if ( isset( $_POST['motif_measurement_unit'] ) ) :
			update_post_meta( $post_id, 'motif_measurement_unit', sanitize_text_field( $_POST['motif_measurement_unit'] ) );
		endif;

		if ( isset( $_POST['motif_measurement_product'] ) ) :
			update_post_meta( $post_id, 'motif_measurement_product', sanitize_text_field( $_POST['motif_measurement_product'] ) );
		endif;

		// weight product saving options

		if( isset( $_POST['motif_pricing_label_weight']) && !empty($_POST['motif_pricing_label_weight'])) :
			update_post_meta( $post_id, 'motif_pricing_label_weight',sanitize_text_field( $_POST['motif_pricing_label_weight'] ) );
		endif;

		if( isset( $_POST['motif_weight_label']) && !empty($_POST['motif_weight_label'])) :
			update_post_meta( $post_id, 'motif_weight_label',sanitize_text_field( $_POST['motif_weight_label'] ) );
		endif;

		if( isset( $_POST['motif_weight_unit']) && !empty($_POST['motif_weight_unit'])) :
			update_post_meta( $post_id, 'motif_weight_unit',sanitize_text_field( $_POST['motif_weight_unit'] ) );
		endif;


		// volume product saving options

		if( isset( $_POST['motif_pricing_label_volume']) && !empty($_POST['motif_pricing_label_volume'])) :
			update_post_meta( $post_id, 'motif_pricing_label_volume',sanitize_text_field( $_POST['motif_pricing_label_volume'] ) );
		endif;

		if( isset( $_POST['motif_volume_label']) && !empty($_POST['motif_volume_label'])) :
			update_post_meta( $post_id, 'motif_volume_label',sanitize_text_field( $_POST['motif_volume_label'] ) );
		endif;

		if( isset( $_POST['motif_volume_unit']) && !empty($_POST['motif_volume_unit'])) :
			update_post_meta( $post_id, 'motif_volume_unit',sanitize_text_field( $_POST['motif_volume_unit'] ) );
		endif;

		// area product saving options

		if( isset( $_POST['motif_pricing_label_area']) && !empty($_POST['motif_pricing_label_area'])) :
			update_post_meta( $post_id, 'motif_pricing_label_area',sanitize_text_field( $_POST['motif_pricing_label_area'] ) );
		endif;

		if( isset( $_POST['motif_area_label']) && !empty($_POST['motif_area_label'])) :
			update_post_meta( $post_id, 'motif_area_label',sanitize_text_field( $_POST['motif_area_label'] ) );
		endif;

		if( isset( $_POST['motif_area_unit']) && !empty($_POST['motif_area_unit'])) :
			update_post_meta( $post_id, 'motif_area_unit',sanitize_text_field( $_POST['motif_area_unit'] ) );
		endif;

		// measurement ranges saving
		if(isset($_POST['m_table'])) {

			$new_data = $_POST['m_table'];

		 	$previous_data = get_post_meta($post_id, '_ranges_motif', true);
			
		 	$result = array_merge($previous_data, $new_data);

		 	if(empty($previous_data)) {

		 		update_post_meta( $post_id, '_ranges_motif', $new_data);
		 	
		 	} else {

		 		update_post_meta( $post_id, '_ranges_motif', $result);

		 	}
		}
		
	}

	// view motif minimum price
	function motif_minimum_price_measurement() {

		$currency = get_woocommerce_currency();
		$string = get_woocommerce_currency_symbol( $currency );

		global $post;
		
		$motif_m_product = get_post_meta($post->ID, 'motif_measurement_product', true);
		
		if($motif_m_product == 'yes') { ?>

			<script>
				
			    	jQuery("._regular_price_field").css("display", "none");
			    	jQuery("._sale_price_field").css("display", "none");
			
			</script>

		<?php  }

		echo '<div class="options_group">';
			  
			woocommerce_wp_text_input( 
				array( 
					'id'                => 'motif_minimum_price_range', 
					'label'             => __( 'Minimum Price ('.$string.')', 'motif-woocommerce-price-calculator' ), 
					'description'       => __( 'Minimum price for those quantities who are not in you rules', 'motif-woocommerce-price-calculator' ),
					'desc_tip'		=> 'true',
					'type'              => 'number', 
					'custom_attributes' => array(
							'step' 	=> 'any',
							'min'	=> '0'
						),
					'value'	=> get_post_meta( $post->ID, 'motif_minimum_price_range', true )
				)
			);

		echo '</div>';
	}

	//minimum values motif save function
	function motif_minimum_price_measurement_save($post_id) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

		if ( isset( $_POST['motif_minimum_price_range'] ) ) :
			update_post_meta( $post_id, 'motif_minimum_price_range', sanitize_text_field( $_POST['motif_minimum_price_range'] ) );
		endif;
	}



}new MOTIF_PRICE_CALCULATOR_ADMINCLASS();
