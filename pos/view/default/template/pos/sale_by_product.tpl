<?php echo $header; ?><?php echo $column_left; ?>
 

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Sale By Product</h2>
                            <small class="card-subtitle">This report is to see the Sale by Product </small>
                        </div>

                        <div class="card-block">

                            <div class="row">
                                <div class="col-sm-4">
                                    <label>Start Date</label>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                        <div class="form-group">
                                            <input type="text" id="start_date" value="<?php echo $filter_date_start; ?>"class="form-control date-picker" placeholder="Start Date">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                        
                                <div class="col-sm-4">
                                    <label>End Date</label>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                        <div class="form-group">
                                            <input type="text" id="end_date" value="<?php echo $filter_date_end; ?>" class="form-control date-picker" placeholder="End Date">
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
                                
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($sale as $sale2)
                            {?>  
                            <tr>
                               <td><?php echo $sale2['product_name']; ?></td>
                                <td><?php echo $sale2['quantity']; ?></td>
                                <td><?php echo RUPPE_SIGN.number_format((float)$sale2['price'], 2, '.', ''); ?></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>

  <button class="btn btn-danger btn--action btn--fixed " onclick="return send_email();"><i class="zmdi zmdi-email"></i></button>
 
<?php echo $footer; ?>
<script type="text/javascript">
$('#button-filter').on('click', function() 
    {
		var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
		if(new Date(start_date) > new Date(end_date))
		{
			alertify.error('Start date should be less then or equal to end date');
			return false;
		}
		
	var url = 'index.php?route=pos/report&pagetittle=Sale by Product&token=<?php echo $token; ?>';

	
        if(start_date)
        {
            url += '&filter_date_start=' + encodeURIComponent(start_date);
        }
        
        if (end_date) 
        {
            url += '&filter_date_end=' + encodeURIComponent(end_date);
        }
	$(".page-loader").addClass("important");
			$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
			$(".page-loader").show();
	location = url;
    });
	function send_email()
	{
		var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
		if(new Date(start_date) > new Date(end_date))
		{
			alertify.error('Start date should be less then or equal to end date');
			return false;
		}
		var url = 'index.php?route=pos/report/sale_by_product_email&token=<?php echo $token; ?>';

        if(start_date)
        {
            url += '&filter_date_start=' + encodeURIComponent(start_date);
        }
        
        if (end_date) 
        {
            url += '&filter_date_end=' + encodeURIComponent(end_date);
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
 