<h4 class="text-center">Select Product type and set dimenssions</h4>

<?php
	global $post;
	
	woocommerce_wp_select( array(
		'id' => 'motif_measurement_unit',
		'label' => __('Mesurements', 'motif-woocommerce-price-calculator'),
		'desc_tip'		=> 'true',
		'description'	=> __( 'Select measurement for your product', 'motif-woocommerce-price-calculator' ),
		'options' => array( 
		'none'        => __('None', 'motif-woocommerce-price-calculator'),
		'weight' => __('Weight', 'motif-woocommerce-price-calculator'),
		'area' => __('Area', 'motif-woocommerce-price-calculator'),
		'volume' => __('Volume', 'motif-woocommerce-price-calculator'),
		),
		'value'	=> get_post_meta( $post->ID, 'motif_measurement_unit', true )
	) ); 

	woocommerce_wp_hidden_input(array(
		'id' => 'motif_measurement_product',
		'value' => "yes",
	));
?>

<script type="text/javascript">
	jQuery(document).ready(function(){
	    jQuery("#motif_measurement_unit").change(function(){
	        jQuery(this).find("option:selected").each(function(){
	            if(jQuery(this).attr("value")=="weight"){
	                jQuery(".square").not(".weight").hide();
	                jQuery(".weight").show();
	            }
	            else if(jQuery(this).attr("value")=="area"){
	                jQuery(".square").not(".area").hide();
	                jQuery(".area").show();
	            }
	            else if(jQuery(this).attr("value")=="volume"){
	                jQuery(".square").not(".volume").hide();
	                jQuery(".volume").show();
	            }
	            else{
	                jQuery(".square").hide();
	            }
	        });
	    }).change();
	});
</script>

<div id="motif_weight_pc" class="weight square" >
	<?php
		woocommerce_wp_text_input( array(
			'id'			=> 'motif_pricing_label_weight',
			'label'			=> __( 'Pricing Label', 'motif-woocommerce-price-calculator' ),
			'desc_tip'		=> 'true',
			'description'	=> __( 'Weight unit for next to pricing as label', 'motif-woocommerce-price-calculator' ),
			'type' 			=> 'text',
			'value'			=> get_post_meta( $post->ID, 'motif_pricing_label_weight', true )
			) );

		echo '<hr>';

		woocommerce_wp_text_input( array(
			'id'			=> 'motif_weight_label',
			'label'			=> __( 'Weight Label', 'motif-woocommerce-price-calculator' ),
			'desc_tip'		=> 'true',
			'description'	=> __( 'Label in front of custom measurement for customers', 'motif-woocommerce-price-calculator' ),
			'type' 			=> 'text',
			'value'			=> get_post_meta( $post->ID, 'motif_weight_label', true )
			) );

		woocommerce_wp_select( array( 
			'id'      		=> 'motif_weight_unit', 
			'label'   		=> __( 'Weight Unit', 'motif-woocommerce-price-calculator' ), 
			'desc_tip'		=> 'true',
			'description'	=> __( 'Units in which you sale your weight product', 'motif-woocommerce-price-calculator' ),
			'options' 		=> array(
				'' 			=> __('None','motif-woocommerce-price-calculator'),
				't'   		=> __( 't', 'motif-woocommerce-price-calculator' ),
				'kg'   		=> __( 'kg', 'motif-woocommerce-price-calculator' ),
				'g' 		=> __('g','motif-woocommerce-price-calculator'),
				'lb'   		=> __( 'lb', 'motif-woocommerce-price-calculator' ),
				'mg'   		=> __( 'mg', 'motif-woocommerce-price-calculator' ),
				'oz'   		=> __( 'oz', 'motif-woocommerce-price-calculator' )
			),
			'value'			=> get_post_meta( $post->ID, 'motif_weight_unit', true )
			));

		?>
</div>

<div id="motif_volume_pc" class="volume square" >
	<?php 
		
		woocommerce_wp_text_input( array(
			'id'			=> 'motif_pricing_label_volume',
			'label'			=> __( 'Pricing Label', 'motif-woocommerce-price-calculator' ),
			'desc_tip'		=> 'true',
			'description'	=> __( 'Volume unit for next to pricing as label', 'motif-woocommerce-price-calculator' ),
			'type' 			=> 'text',
			'value'			=> get_post_meta( $post->ID, 'motif_pricing_label_volume', true )
		) );

		echo '<hr>';

		woocommerce_wp_text_input( array(
			'id'			=> 'motif_volume_label',
			'label'			=> __( 'Volume Label', 'motif-woocommerce-price-calculator' ),
			'desc_tip'		=> 'true',
			'description'	=> __( 'Label in front of custom measurement for customers', 'motif-woocommerce-price-calculator' ),
			'type' 			=> 'text',
			'value'			=> get_post_meta( $post->ID, 'motif_volume_label', true )
		) );

		woocommerce_wp_select( array( 
			'id'      		=> 'motif_volume_unit', 
			'label'   		=> __( 'Volume Unit', 'motif-woocommerce-price-calculator' ), 
			'desc_tip'		=> 'true',
			'description'	=> __( 'Units in which you sale your volume product', 'motif-woocommerce-price-calculator' ),
			'options' 		=> array(
				'' 			=> __('None','motif-woocommerce-price-calculator'),
				'cu m'   	=> __( 'cu m', 'motif-woocommerce-price-calculator' ),
				'l'   		=> __( 'l', 'motif-woocommerce-price-calculator' ),
				'ml' 		=> __('ml','motif-woocommerce-price-calculator'),
				'gal'   	=> __( 'gal', 'motif-woocommerce-price-calculator' ),
				'qt'   		=> __( 'qt', 'motif-woocommerce-price-calculator' ),
				'pt'   		=> __( 'pt', 'motif-woocommerce-price-calculator' ),
				'cup'   	=> __( 'cup', 'motif-woocommerce-price-calculator' ),
				'fl. oz.'   => __( 'fl. oz.', 'motif-woocommerce-price-calculator' ),
				'cu. yd.'   => __( 'cu. yd.', 'motif-woocommerce-price-calculator' ),
				'cu. ft.'   => __( 'cu. ft.', 'motif-woocommerce-price-calculator' ),
				'cu. in.'   => __( 'cu. in.', 'motif-woocommerce-price-calculator' )
			),
			'value'			=> get_post_meta( $post->ID, 'motif_volume_unit', true )
		));			

		?>
</div>

<div id="motif_area_pc" class="area square" >
	<?php 
		
		woocommerce_wp_text_input( array(
			'id'			=> 'motif_pricing_label_area',
			'label'			=> __( 'Pricing Label', 'motif-woocommerce-price-calculator' ),
			'desc_tip'		=> 'true',
			'description'	=> __( 'Area unit for next to pricing as label', 'motif-woocommerce-price-calculator' ),
			'type' 			=> 'text',
			'value'			=> get_post_meta( $post->ID, 'motif_pricing_label_area', true )
		) );

		echo '<hr>';

		woocommerce_wp_text_input( array(
			'id'			=> 'motif_area_label',
			'label'			=> __( 'Area Label', 'motif-woocommerce-price-calculator' ),
			'desc_tip'		=> 'true',
			'description'	=> __( 'Label in front of custom measurement for customers', 'motif-woocommerce-price-calculator' ),
			'type' 			=> 'text',
			'value'			=> get_post_meta( $post->ID, 'motif_area_label', true )
		) );

		woocommerce_wp_select( array( 
			'id'      		=> 'motif_area_unit', 
			'label'   		=> __( 'Area Unit', 'motif-woocommerce-price-calculator' ), 
			'desc_tip'		=> 'true',
			'description'	=> __( 'Units in which you sale your area product', 'motif-woocommerce-price-calculator' ),
			'options' 		=> array(
				'' 			=> __('None','motif-woocommerce-price-calculator'),
				'ha'   		=> __( 'ha', 'motif-woocommerce-price-calculator' ),
				'sq km'   	=> __( 'sq km', 'motif-woocommerce-price-calculator' ),
				'sq m' 		=> __('sq m','motif-woocommerce-price-calculator'),
				'sq cm'   	=> __( 'sq cm', 'motif-woocommerce-price-calculator' ),
				'sq mm'   	=> __( 'sq mm', 'motif-woocommerce-price-calculator' ),
				'acs'   	=> __( 'acs', 'motif-woocommerce-price-calculator' ),
				'sq mi.'   	=> __( 'sq mi.', 'motif-woocommerce-price-calculator' ),
				'sq yd.'   	=> __( 'sq yd.', 'motif-woocommerce-price-calculator' ),
				'sq. ft.'   => __( 'sq. ft.', 'motif-woocommerce-price-calculator' ),
				'sq. in.'   => __( 'sq. in.', 'motif-woocommerce-price-calculator' )
			),
			'value'			=> get_post_meta( $post->ID, 'motif_area_unit', true )
		));			

		?>
</div>
