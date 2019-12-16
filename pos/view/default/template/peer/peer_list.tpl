<?php echo $header; ?><?php echo $column_left; ?>
 
<?php if ($error_warning) { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>
            <?php if ($success) { ?>
                <div class="alert alert-success" style="margin-bottom: 20px;"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>
                    <div class="card">
                        <div class="card-header">
                            <h1 style="float: left;"><?php echo "Sale to Retailer"; ?></h1>
                            <div class="pull-right" style="float: right;">
									<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo "Add"; ?>" class="btn btn-primary">
									<i class="zmdi zmdi-plus"></i></a>
								</div>
                        </div>

                        
                    </div>

                <div class="card">
                    
					<input type="hidden" name="lat" id="lat" value="<?php echo $lat; ?>" />
					<input type="hidden" name="lng" id="lng" value="<?php echo $lng; ?>" />
                    <div class="card-block">
                        <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                            <tr>
                                <th class="text-left">Category</th>
									<th class="text-left">Product Name</th>
									<th class="text-left">Offer Price</th>
									<th class="text-left">Min Qnty</th>
                    				
									<th class="text-left">Validity</th>
									<th class="text-left">Telephone</th>
									<th class="text-left">Email</th>
									<th class="text-left">Store Name</th>
									<th class="text-left">Created date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($products as $order){?>  
                            <tr>
									<td class="text-left"><?php echo ($order['category_name']); ?></td>
									<td class="text-left"><?php echo $order['product_name']; ?></td>	
									<td class="text-left"><?php echo $order['offer_price']; ?></td>
									<td class="text-left"><?php echo $order['quantity']; ?></td>
									<td class="text-left"><?php echo $order['validate']; ?></td>
									<td class="text-left"><?php echo $order['telephone']; ?></td>
									<td class="text-left"><?php echo $order['email']; ?></td>
									<td class="text-left"><?php echo $order['store_name']; ?></td>
									<td class="text-left"><?php echo $order['create_date']; ?></td>
									<td class="text-left">
									
									<span style="cursor: pointer;float: right;font-size: 22px;color: red;" id="fav_top_span_<?php echo ($order['id']); ?>">
									<?php if($order['favourite']==1) { ?>
										<span class="favourite"  onclick="return already_added_to_favourite(<?php echo ($order['id']); ?>);" id="favourite<?php echo ($order['id']); ?>" >
											<i class="zmdi zmdi-favorite"></i>
										</span>
									<?php }else{ ?>
										<span class="favourite"  onclick="return add_to_favourite(<?php echo ($order['id']); ?>);" id="favourite<?php echo ($order['id']); ?>" >
											<i class="zmdi zmdi-favorite-outline"></i>
										</span>
									<?php } ?>
									</span>
									
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

<style>
	label
	{
		font-weight: bold !important;
	}
	.important 
	{
    background-color: rgba(243, 243, 243, 0.52) !important;
	}
	</style>
<?php echo $footer; ?>
<script type="text/javascript">
	function add_to_favourite(pid)
	{
		var url='index.php?route=pos/peer/addtofavourite&token=<?php echo $token; ?>&peer_id='+pid;
		//alert(url);
		$.ajax({
            url: url,
            type: 'post',
            data: { peer_id: pid},
            dataType: 'json',
		beforeSend: function() 
		{
			
		    $("#please_wait_span").remove();
			$(".page-loader").addClass("important");
			$(".page-loader").append('<span id="please_wait_span" style="margin-top: 73px; text-align: center; margin-right: 0px;" class="loading_text">Please wait. Please do not close your browser or click back button ..</span>');
			$(".page-loader").show();
		},
        success: function(json) 
		{
			//alert(json);
			$(".page-loader").removeClass("important");
			$("#please_wait_span").remove();
			$(".page-loader").hide();
			if(json=='1')
			{
				var favourite='<span class="favourite"  onclick="return already_added_to_favourite('+pid+');" id="favourite'+pid+'" ><i class="zmdi zmdi-favorite"></i></span>';
				$("#fav_top_span_"+pid).html(favourite);
				alertify.success('Added to favourite.');
			}
			
			else 
			{
				alertify.error('Some error occur.please try again');
			}
        }, 
        error:function (json)
        {
             
                alert(JSON.stringify(json));
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
          }
        });
		return false;
	}
	function already_added_to_favourite(pid)
	{
		var url='index.php?route=pos/peer/remove_favourite&token=<?php echo $token; ?>&peer_id='+pid;
		//alert(url);
		$.ajax({
            url: url,
            type: 'post',
            data: { product_id: pid},
            dataType: 'json',
		beforeSend: function() 
		{
			
		    $("#please_wait_span").remove();
			$(".page-loader").addClass("important");
			$(".page-loader").append('<span id="please_wait_span" style="margin-top: 73px; text-align: center; margin-right: 0px;" class="loading_text">Please wait. Please do not close your browser or click back button ..</span>');
			$(".page-loader").show();
		},
        success: function(json) 
		{
			//alert(json);
			$(".page-loader").removeClass("important");
			$("#please_wait_span").remove();
			$(".page-loader").hide();
			if(json=='1')
			{
				var favourite='<span class="favourite"  onclick="return add_to_favourite('+pid+');" id="favourite'+pid+'" ><i class="zmdi zmdi-favorite-outline"></i></span>';
				$("#fav_top_span_"+pid).html(favourite);
				alertify.success('Removed from favourite.');
			}
			
			else 
			{
				alertify.error('Some error occur.please try again');
			}
        }, 
        error:function (json)
        {
             
                alert(JSON.stringify(json));
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
          }
        });
		return false;
}

//////////////////////
  $('#button-filter').on('click', function() 
  {
	url = 'index.php?route=pos/peer&token=<?php echo $token; ?>';
	
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

var lat=$("#lat").val();
var lng=$("#lng").val();
if((!lat) || (!lng))
{
	getLocation();
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) 
{
	$("#lat").val(position.coords.latitude);
	$("#lng").val(position.coords.longitude);
    url = 'index.php?route=pos/peer&token=<?php echo $token; ?>&pagetittle=Sale to Retailer&lat='+position.coords.latitude+'&lng='+position.coords.longitude;
	location = url;
}
</script>
 