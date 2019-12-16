<?php echo $header; ?><?php echo $column_left; ?>
 

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Sale Book</h2>
                            <small class="card-subtitle">This report is to see the sale </small>
                        </div>

                        <div class="card-block">

                            <div class="row">
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
                                <div class="col-sm-3">
                                    <br/>
                                <button type="button" id="button-filter"  class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
<hr/>
            <div class="row stats">
                    <div class="col-sm-3 col-md-3">
                        <div class="stats__item">
                            <div class="stats__chart bg-lime">
                                <div class="flot-chart flot-chart--xs stats-chart-1 " >
                                    <h2 style="text-align: center;color:white;"><?php echo RUPPE_SIGN.number_format((float)$total, 2, '.', ''); ?></h2>
                                </div>
                            </div>

                            <div class="stats__info">
                                <div>
                                    
                                    <h2>Total</h2>
                                </div>
                            </div>
                        </div>
                    </div>
						<div class="col-sm-3 col-md-3">
                        <div class="stats__item">
                            <div class="stats__chart bg-teal" style="background-color: #7490b6 !important">
                                <div class="flot-chart flot-chart--xs stats-chart-2 " >
                                    <h2 style="text-align: center;color:white;"><?php echo RUPPE_SIGN.number_format((float)$amount['discount'], 2, '.', ''); ?></h2>
                                </div>
                            </div>

                            <div class="stats__info">
                                <div>
                                    
                                    <h2>Dsicount</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-3">
                        <div class="stats__item">
                            <div class="stats__chart bg-teal">
                                <div class="flot-chart flot-chart--xs stats-chart-2 " >
                                    <h2 style="text-align: center;color:white;"><?php echo RUPPE_SIGN.number_format((float)$amount['cash'], 2, '.', ''); ?></h2>
                                </div>
                            </div>

                            <div class="stats__info">
                                <div>
                                    
                                    <h2>Cash</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-md-3">
                        <div class="stats__item">
                            <div class="stats__chart bg-blue-grey">
                                <div class="flot-chart flot-chart--xs stats-chart-3 " >
                                    <h2 style="text-align: center;color:white;"><?php echo RUPPE_SIGN.number_format((float)$amount['credit'], 2, '.', ''); ?></h2>
                                </div>
                            </div>

                            <div class="stats__info">
                                <div>
                                    
                                    <h2>Credit</h2>
                                </div>
                            </div>
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
	var url = 'index.php?route=pos/report/sale_book&pagetittle=Sale Book&token=<?php echo $token; ?>';

	
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
		var url = 'index.php?route=pos/report/sale_book_email&token=<?php echo $token; ?>';

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
 


