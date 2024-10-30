<h4 class="text-center">Set product rules with your desire prices</h4>

<div class="alert alert-success" id="save_range_del">
  <strong>Success!</strong> One of your saved Range was deleted.
</div>

<table class="table table-striped">

    <thead>
      <tr>
        <th>Rules & Ranges</th>
        <th>Regular Price</th>
        <th>Sale Price</th>
        <th>Delete Range</th>
      </tr>
    </thead>

	<tbody id="motif">

		<?php global $post;
			
		$data_ranges = get_post_meta($post->ID, '_ranges_motif', true); 

		if($data_ranges != '') { 

			$i= 0;

		foreach ($data_ranges as $value) { ?>
			
		<tr id="<?php echo $i.'m_save_ranges'; ?>">
			<td class="m_data1">
				<div class="form-group">
					<input disabled type="number" class="m_frang form-control" name="ms_table[<?php echo $i ?>][start_rang]" value="<?php echo $value['start_rang']; ?>">
				    <input disabled type="number" class="m_frang form-control" name="ms_table[<?php echo $i ?>][end_rang]" value="<?php echo $value['end_rang']; ?>">
				</div>
			</td>

			<td class="m_data2 align-middle">
				<div class="form-group">
					<input disabled type="number" class="form-control align-middle" name="ms_table[<?php echo $i ?>][regular_price]" value="<?php echo $value['regular_price']; ?>">
				</div>
			</td>
			<td class="m_data3 align-middle">
				<div class="form-group">
					<input disabled type="number" class="form-control align-middle" name="ms_table[<?php echo $i ?>][sale_rang]" value="<?php echo $value['sale_rang']; ?>">
				</div>
			</td>
			<td class="m_data4 align-middle">
				<button type="button" id="<?php echo $i.'m_save_ranges'; ?>" onClick="m_saved_rules(this.id)" id="<?php echo $i.'delete-row';?>" class="btn btn-danger btn-sm">Delete</button>
				<input type="hidden" id="saved_meta_postid" value="<?php echo $post->ID; ?>">
			</td>
		</tr>

		<?php $i++; } } ?>

	</tbody>

</table>

<div class="m_add_rules">
	<button type="button" class="add-row btn btn-primary">Add New Rule</button>
</div>	

<script type="text/javascript">
	
	jQuery(document).ready(function(){
	        
	    var m_tr = 0;
	        
	    jQuery(".add-row").click(function(){
            m_tr ++;
            var m_ranges = "<tr id='m_tr"+m_tr+"'><td class='m_data1'><div class='form-group'><input required name='m_table["+m_tr+"][start_rang]' placeholder='Start Range' class='m_frang form-control' type='number'><input required name='m_table["+m_tr+"][end_rang]' placeholder='End Range' class='m_frang form-control' type='number'></div></td><td class='m_data2 align-middle'><div class='form-group '><input required name='m_table["+m_tr+"][regular_price]' placeholder='Regular Price' type='number' class='form-control'></div></td><td class='m_data3 align-middle'><div class='form-group'><input name='m_table["+m_tr+"][sale_rang]' placeholder='Sale Price' type='number' class='form-control'></div></td><td class='m_data4 align-middle'><button type='button' class='btn btn-danger btn-sm' id='m_tr"+m_tr+"' onclick='m_new_rule(this.id);'>Delete</button></td></tr>";

            jQuery("tbody#motif").append(m_ranges);
	    });
	});   

	// deleting new rules with no save
    function m_new_rule(id) {
    	jQuery('#'+id).remove();
    }

    // deleting saved rules
    function m_saved_rules(id) { 

    	var condition = 'delete_saved_row';
		var post_id = jQuery('#saved_meta_postid').val();
		jQuery.ajax({
			url : ajaxurl,
			type : 'post',
			data : {
				action : 'deleting_saved_rulesact',
				condition : condition,
				row_id : id,
				post_id : post_id,
			},
			success : function(response) {
				jQuery('#save_range_del').show().delay(3000).fadeOut();
				jQuery('#'+id).remove();
			}
		});
	}

</script>