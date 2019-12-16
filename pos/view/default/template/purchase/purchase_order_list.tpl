<?php echo $header; ?><?php echo $column_left; ?>
 

                    <div class="card">
                        <div class="card-header">
                            <h1 style="float: left;"><?php echo "Purchase Order"; ?></h1>
                            <div class="pull-right" style="float: right;">
									<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo "Add"; ?>" class="btn btn-primary">
									<i class="zmdi zmdi-plus"></i></a>
								</div>
                        </div>

                        
                    </div>

                <div class="card">
                    

                    <div class="card-block">
                        <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                            <tr>
                                <th class="text-left">Supplier Name</th>
									<th class="text-left">PO Date</th>
									<th class="text-left">PO Number</th>
                    				<th class="text-left">INV Number</th>
									<th class="text-left">Delivery Address</th>
									<th class="text-left">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($order_list as $order){?>  
                            <tr>
									<td class="text-left"><?php echo utf8_decode(urldecode($order['supplier_name'])); ?></td>
									<td class="text-left"><?php echo date('d-m-Y',$order['create_date']->sec); ?></td>	
									<td class="text-left"><?php echo $order['id_prefix'].$order['sid']; ?></td>
									<td class="text-left"><?php echo $order['inv_no']; ?></td>
									<td class="text-left"><?php echo $order['store_name']; ?></td>
									<td class="text-left">
									<?php  
										if($order['status']=='0') 
										{
											echo "PO Raised";
										}
										else if($order['status']=='1') 
										{
											echo "PO Invoiced";
										}
										else if($order['status']=='2') 
										{
											echo "Invoice Paid";
										}
                            
									?>
								</td>
							</tr>
                            <?php } ?>
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
	function send_email() 
	{
		url = 'index.php?route=purchase/purchase_order/email&token=<?php echo $token; ?>';
	
		var filter_date_start = $('#input-date-start').val();
		if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
		}
        var filter_supplier = $('#input-supplier').val();
		if (filter_supplier) {
		url += '&filter_supplier=' + encodeURIComponent(filter_supplier);
		}
        var filter_date_end = $('#input-date-end').val();
        if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
		}
       var filter_status = $('#input-status').val();
        if (filter_status) {
		url += '&filter_status=' + encodeURIComponent(filter_status);
		}		
		$.ajax({
		url: url,
		//dataType: 'json',
		method: 'POST',
		beforeSend: function() {
			
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
  $('#button-filter').on('click', function() 
  {
	url = 'index.php?route=purchase/purchase_order&token=<?php echo $token; ?>';
	
    var filter_date_start = $('#input-date-start').val();
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}
        var filter_supplier = $('#input-supplier').val();
	if (filter_supplier) {
		url += '&filter_supplier=' + encodeURIComponent(filter_supplier);
	}
        var filter_date_end = $('#input-date-end').val();
        if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
       var filter_status = $('#input-status').val();
        if (filter_status) {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}
	location = url;
});
  <!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script>
 