<?php echo $header; ?><?php echo $column_left; ?>
 

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">My Inventory</h2>
                            <small class="card-subtitle">This report is to see the My Inventory </small>
                        </div>

                        <div class="card-block">

                            <div class="row">
                                <div class="col-sm-4">
                                    <label>Search Product</label>

                                    <div class="input-group">
                                        <span class="input-group-addon">@</span>
                                        <div class="form-group">
                                            <input type="text" name="filter_name" class="form-control" value="<?php echo $filter_product; ?>" placeholder="Product">
                                            <input type="hidden" name="filter_name_id" value="<?php echo $filter_product_id; ?>" >
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                        
                                
                                <div class="col-sm-3">
                                    <br/>
                                <button type="button" id="button-filter" class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>

 <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Total Amount</h2>
                        <small class="card-subtitle"><?php echo $total; ?></small>
                    </div>

                    <div class="card-block">
                        <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                            <tr>
									<th>SID</th>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $aa=1; foreach($products as $product)
                            {
                            ?>
                            <tr>
									<td><?php echo $aa; ?></td>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo $product['quantity']; ?></td>
                                <td><?php echo $product['pirce']; ?></td>
                                <td><?php echo $product['quantity']*$product['pricewithtax']; ?></td>
                            </tr>
                            <?php $aa++; } ?>
                            </tbody>
                        </table>
                        </div>
							<div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
                    </div>
                </div>
 <button class="btn btn-danger btn--action btn--fixed " onclick="return send_email();"><i class="zmdi zmdi-email"></i></button>
 
<?php echo $footer; ?>
<script type="text/javascript">
    $('#button-filter').on('click', function() 
    {
	var url = 'index.php?route=pos/inventory_report&token=<?php echo $token; ?>';

	var filter_name_id = $('input[name=\'filter_name_id\']').val();
        var filter_name = $('input[name=\'filter_name\']').val();

        if (filter_name_id) 
        {
            if(filter_name!="")
            {
                url += '&filter_name_id=' + encodeURIComponent(filter_name_id);
            }
        }
        if (filter_name) 
        {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }

	location = url;
    });

$(document).ready(function() 
{ 
    $('input[name=\'filter_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=pos/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_name\']').val(item['label']);
                $('input[name=\'filter_name_id\']').val(item['value']);
	}
});
});

	function send_email() 
	{
		var url = 'index.php?route=pos/inventory_report/email&token=<?php echo $token; ?>';

		var filter_name_id = $('input[name=\'filter_name_id\']').val();
        var filter_name = $('input[name=\'filter_name\']').val();

        if (filter_name_id) 
        {
            if(filter_name!="")
            {
                url += '&filter_name_id=' + encodeURIComponent(filter_name_id);
            }
        }
        if (filter_name) 
        {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }		
		$.ajax({
		url: url,
		//dataType: 'json',
		method: 'POST',
		beforeSend: function() {
			$("#submit_btn").hide();
		    $("#submit_img").show();
			$(".page-loader").addClass("important");
			$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
			$(".page-loader").show();
		},
		complete: function() 
		{
			$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
		},
		success: function(html) 
        {
			//alert(html);
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
				alertify.success(html);
				return false;
		},
		error: function(xhr, ajaxOptions, thrownError) 
		{
			$(".page-loader").removeClass("important");
			$("#please_wait_span").remove();
			$(".page-loader").hide();
			alertify.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			
		}
	});
			
		
		return false;
	}

</script>


