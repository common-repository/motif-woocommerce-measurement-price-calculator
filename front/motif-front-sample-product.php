<table cellspacing="0">

	<tbody>

		<tr>
			<td>
				<label><?php echo $m_quantity_field_label.' in '.$unit; ?></label>
			</td>
			<td>
				<input type="text" required step="any" class="qtye" name="reqweight" id="reqweight" />	
			</td>
		</tr>
		<tr>
			<td>
				<label><?php _e('Calulated Price', 'motif-woocommerce-price-calculator'); ?></label>
			</td>
			<td>
				<span id="calculatd_amount"></span>
				<input type="hidden" name="motif_cal_price" id="motif_cal_price" value=""/>
				<input type="hidden" name="product_type" value="motif_<?php echo $motif_product_type; ?>_product" />
				<input type="hidden" name="motif_product_id" id="motif_product_id" value="<?php echo $post->ID; ?>" />
			</td>
		</tr>

	</tbody>

</table> 

<script type="text/javascript">

	jQuery('#reqweight').on('input',function(event) { 
	    var quantity = jQuery('#reqweight').val();
	    get_item_quantity(quantity);
	});
	function get_item_quantity(quantity) {
		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
		var product_id = jQuery("#motif_product_id").val();
	    var condition = 'simple_measurements_motif';
	    jQuery.ajax({
	        url : ajaxurl,
	        type : 'post',
	        data : {
	            action : 'simple_price_calculation',
	            condition :condition,
	            quantity : quantity,
	            product_id : product_id,
	        },
	        success : function( response ) {
	        	var price_form = "<?php echo get_option( 'woocommerce_currency_pos' ); ?>";
				var op_price = "";
				if(price_form == 'left') {
					op_price = accounting.formatMoney(response, { symbol: "<?php echo $string; ?>",  format: "%s%v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
				} else if(price_form == 'left_space') {
					op_price = accounting.formatMoney(response, { symbol: "<?php echo $string; ?>",  format: "%s %v" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
				} else if(price_form == 'right') {
					op_price = accounting.formatMoney(response, { symbol: "<?php echo $string; ?>",  format: "%v%s" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
				} else if(price_form == 'right_space') {
					op_price = accounting.formatMoney(response, { symbol: "<?php echo $string; ?>",  format: "%v %s" }, "<?php echo wc_get_price_decimals(); ?>", "<?php echo wc_get_price_thousand_separator(); ?>", "<?php echo wc_get_price_decimal_separator(); ?>"); // €4.999,99
				}

	            jQuery('#calculatd_amount').html(op_price);
	           	jQuery("input[id=motif_cal_price]").val(op_price);
	        }
	    });  
	} 

	// restrict user to not enter dot and abc:P
	jQuery('.qtye').on('input propertychange paste', function (e) {
	    var reg = /^0+/gi;
	    if (this.value.match(reg)) {
	        this.value = this.value.replace(reg, '');
	    }
	});
	jQuery(function(){
		  jQuery('.qtye').keypress(function(e) {
			if(isNaN(this.value+""+String.fromCharCode(e.charCode))) return false;
		  })

		  .on("cut copy paste",function(e){
			e.preventDefault();
		  });
	});
</script>