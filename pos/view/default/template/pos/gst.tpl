<?php echo $header; ?><?php echo $column_left; ?>


<div class="row">
    <div class="col-sm-6">
                <div class="card-demo">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">GST FORMAT 1</h2>
                            <small class="card-subtitle">It contains summary of all outward supplies i.e sales</small>
                        </div>
                        <div class="col-sm-4">
                                    <label>Start Date</label>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                        <div class="form-group">
                                            <input type="text" id="start_date" class="form-control date-picker" value="<?php echo $filter_date_start; ?>" placeholder="Start Date">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                        
                                <div class="col-sm-4">
                                    <label>End Date</label>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                        <div class="form-group">
                                            <input type="text" id="end_date" value="<?php echo $filter_date_end; ?>"  class="form-control date-picker" placeholder="End Date">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3 mb-3">
										
											<button type="button" id="button-filter"  class="btn btn-primary"><i class="zmdi zmdi-download"></i> Download</button>
										
											<button type="button" id="button-filter_email"  class="btn btn-primary"><i class="zmdi zmdi-email"></i> Email</button>
										</div>
                               
                                </div>
								
                    </div>
                
    </div>
    
    
    <div class="col-sm-6">
                <div class="card-demo">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">GST FORMAT 1 (Order Wise)</h2>
                            <small class="card-subtitle">It contains details of all outward supplies i.e sales</small>
                        </div>
                        <div class="col-sm-4">
                                    <label>Start Date</label>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                        <div class="form-group">
                                            <input type="text" id="start_date_order_wise" class="form-control date-picker" value="<?php echo $filter_date_start; ?>" placeholder="Start Date">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                        
                                <div class="col-sm-4">
                                    <label>End Date</label>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                        <div class="form-group">
                                            <input type="text" id="end_date_order_wise" value="<?php echo $filter_date_end; ?>"  class="form-control date-picker" placeholder="End Date">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                               
								<div class="col-sm-12 mt-3 mb-3">
										
											<button type="button" id="button-filter_order_wise"  class="btn btn-primary"><i class="zmdi zmdi-download"></i> Download</button>
								<button type="button" id="button-email_filter_order_wise"  class="btn btn-primary"><i class="zmdi zmdi-email"></i> Email</button>
										</div>

                    </div>
                </div>
    </div>
	
	<div class="col-sm-6">
                <div class="card-demo">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">GST FORMAT2</h2>
                            <small class="card-subtitle">The details of inward purchases of taxable goods and/or services.</small>
                        </div>
                    
                        <div class="col-sm-4">
                                    <label>Start Date</label>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                        <div class="form-group">
                                            <input type="text" id="start_date2" class="form-control date-picker" value="<?php echo $filter_date_start; ?>" placeholder="Start Date">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                        
                                <div class="col-sm-4">
                                    <label>End Date</label>

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                        <div class="form-group">
                                            <input type="text" id="end_date2" value="<?php echo $filter_date_end; ?>"  class="form-control date-picker" placeholder="End Date">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                
								<div class="col-sm-12 mt-3 mb-3">
									<button type="button" id="button-filter2"  class="btn btn-primary"><i class="zmdi zmdi-download"></i> Download</button>
								<button type="button" id="button-email2"  class="btn btn-primary"><i class="zmdi zmdi-email"></i> Email</button>
										</div>
                    </div>
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
$('#button-filter').on('click', function() 
    {
	var url = 'index.php?route=pos/report/download_gstr1&token=<?php echo $token; ?>';

	var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if(!start_date)
        {
            alertnotify('Please select start date');
            $('#start_date').focus();
            return false;
        }
        if(!end_date)
        {
            alertnotify('Please select end date');
            $('#end_date').focus();
            return false;
        }
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
		$(".page-loader").removeClass("important");
		$("#please_wait_span").remove();
		$(".page-loader").hide();
    });
	$('#button-filter_email').on('click', function() 
    {
	
	var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if(!start_date)
        {
            alertnotify('Please select start date');
            $('#start_date').focus();
            return false;
        }
        if(!end_date)
        {
            alertnotify('Please select end date');
            $('#end_date').focus();
            return false;
        }
        if(start_date)
        {
            url += '&filter_date_start=' + encodeURIComponent(start_date);
        }
        
        if (end_date) 
        {
            url += '&filter_date_end=' + encodeURIComponent(end_date);
        }

	
		var url = 'index.php?route=pos/report/email_gstr1&token=<?php echo $token; ?>';
		
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
			$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
			$(".page-loader").hide();
			alertify.success(html);
			return false;
		},
		error: function(xhr, ajaxOptions, thrownError) 
		{
			$(".page-loader").removeClass("important");
			$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
			$(".page-loader").hide();
			alertify.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			
		}
	});
			
		
		return false;
	
    });
    $('#button-filter_order_wise').on('click', function() 
    {
	var url = 'index.php?route=pos/report/download_gstrorder_wise&token=<?php echo $token; ?>';

	var start_date = $('#start_date_order_wise').val();
        var end_date = $('#end_date_order_wise').val();
        if(!start_date)
        {
            alertnotify('Please select start date');
            $('#start_date_order_wise').focus();
            return false;
        }
        if(!end_date)
        {
            alertnotify('Please select end date');
            $('#end_date_order_wise').focus();
            return false;
        }
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
		$(".page-loader").removeClass("important");
		$("#please_wait_span").remove();
		$(".page-loader").hide();
    });
	
	$('#button-email_filter_order_wise').on('click', function() 
    {
	var start_date = $('#start_date_order_wise').val();
        var end_date = $('#end_date_order_wise').val();
        if(!start_date)
        {
            alertnotify('Please select start date');
            $('#start_date_order_wise').focus();
            return false;
        }
        if(!end_date)
        {
            alertnotify('Please select end date');
            $('#end_date_order_wise').focus();
            return false;
        }
        if(start_date)
        {
            url += '&filter_date_start=' + encodeURIComponent(start_date);
        }
        
        if (end_date) 
        {
            url += '&filter_date_end=' + encodeURIComponent(end_date);
        }

	
		var url = 'index.php?route=pos/report/email_gstr_order_wise&token=<?php echo $token; ?>';
		
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
	
    });
	
	$('#button-filter2').on('click', function() 
    {
	var url = 'index.php?route=pos/report/download_gstr2&token=<?php echo $token; ?>';

	var start_date = $('#start_date2').val();
        var end_date = $('#end_date2').val();
        if(!start_date)
        {
            alertnotify('Please select start date');
            $('#start_date2').focus();
            return false;
        }
        if(!end_date)
        {
            alertnotify('Please select end date');
            $('#end_date2').focus();
            return false;
        }
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
		$(".page-loader").removeClass("important");
		$("#please_wait_span").remove();
		$(".page-loader").hide();
    });
	$('#button-email2').on('click', function() 
    {
	
	var start_date = $('#start_date2').val();
        var end_date = $('#end_date2').val();
        if(!start_date)
        {
            alertnotify('Please select start date');
            $('#start_date2').focus();
            return false;
        }
        if(!end_date)
        {
            alertnotify('Please select end date');
            $('#end_date2').focus();
            return false;
        }
        if(start_date)
        {
            url += '&filter_date_start=' + encodeURIComponent(start_date);
        }
        
        if (end_date) 
        {
            url += '&filter_date_end=' + encodeURIComponent(end_date);
        }

	
		var url = 'index.php?route=pos/report/email_gstr2&token=<?php echo $token; ?>';
		
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
	
    });
</script> 