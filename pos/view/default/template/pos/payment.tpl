<?php echo $header; ?><?php echo $column_left; ?>

   <!-- <div class="row">
        <aside class="col-sm-3"></aside>
        <aside class="col-sm-6">
            <article class="card">
            <table class='table table-bordered mb-0'>
                <thead>
                    <tr>
                        <th style="text-align:center;">Sub total</th>
                        <th style="text-align:center;">Tax</th>
                        <th style="text-align:center;">Order Totals</th>
                    </tr> 
                </thead>  
                <tbody id="total_wrapper">
                    <tr>
                        <td style="text-align:center;"><span id="sub-total"><?php echo ($cart_detail['subtotal']); ?></span></td>
                        <td style="text-align:center;"><span id="tax-total"><?php echo ($cart_detail['ttax']); ?></span></td>
                        <td style="text-align:center;"><span id="cart-total"><?php echo ($cart_detail['total']); ?></span></td>
                    </tr> 
                </tbody>
           </table>
            </article>
        </aside>
        <aside class="col-sm-3"></aside>
    </div>-->
    <div class="row">
        <aside class="col-sm-3"></aside>
        <aside class="col-sm-6">
				
            
            <article class="card">
			<table class='table table-bordered mb-0'>
                <thead>
                    <tr>
                        <th style="text-align:center;">Sub total</th>
                        <th style="text-align:center;">Tax</th>
                        <th style="text-align:center;">Order Totals</th>
                    </tr> 
                </thead>  
                <tbody id="total_wrapper">
                    <tr>
                        <td style="text-align:center;"><span id="sub-total"><?php $cart_detail['subtotal']=str_replace('Rs.','&#x20b9;',($cart_detail['subtotal'])); echo $cart_detail['subtotal']; $cart_detail['subtotal']=str_replace('&#x20b9;','Rs.',($cart_detail['subtotal'])); ?></span></td>
                        <td style="text-align:center;"><span id="tax-total"><?php $cart_detail['ttax']=str_replace('Rs.','&#x20b9;',($cart_detail['ttax'])); echo ($cart_detail['ttax']); $cart_detail['ttax']=str_replace('&#x20b9;','Rs.',($cart_detail['ttax'])); ?></span></td>
                        <td style="text-align:center;"><span id="cart-total"><?php $cart_detail['total']=str_replace('Rs.','&#x20b9;',($cart_detail['total'])); echo ($cart_detail['total']); $cart_detail['total']=str_replace('&#x20b9;','Rs.',($cart_detail['total'])); ?></span></td>
                    </tr> 
                </tbody>
           </table>
            
                <div class="card-body p-5">

                    <ul class="nav bg-light nav-pills rounded nav-fill mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#nav-tab-card">
                                <i class="fa fa-credit-card"></i> Cash
                            </a>
                        </li>
                    
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="nav-tab-card">
                            <!--<p class="alert alert-success">Some text success or error</p>-->
                            <form role="form" action="" id="pos_form" method="post" >
                                <input type="hidden" name="order_detail[]" id="order_detail" value='<?php echo ($cart_detail_json); ?>' />
                                <input type="hidden" id="order_total" name="order_total" value='<?php $cart_detail['total']=str_replace(',','',$cart_detail['total']); echo trim(substr(($cart_detail['total']), 3, 15)); ?>' />
                             <div class="row">   
								<div class="col-sm-6">
								<div class="form-group">
                                    <label for="cardNumber">Mobile</label>
                                    <div class="input-group">
                                        <input autocomplete="off" onkeypress="return isNumber(event)" maxlength="10" type="text" id="mobile" required="required" class="form-control" name="mobile" placeholder="">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="zmdi zmdi-smartphone-android"></i>   
                                            </span>
                                        </div>
                                    </div>
                                </div> <!-- form-group.// -->
								</div>
								<div class="col-sm-6">
                                <div class="form-group">
                                    <label for="cardNumber">Name</label>
                                    <div class="input-group">
                                        <input autocomplete="off" type="text" id="name" required="required" class="form-control" name="name" placeholder="">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fa zmdi zmdi-face fw"></i>   
                                            </span>
                                        </div>
                                    </div>
                                </div> <!-- form-group.// -->
								</div>
								</div>
								
                                <div class="form-group" style="display: none;">
                                    <label for="cardNumber">Aadhar</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="aadhar" value="N/A" id="aadhar" placeholder="">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="zmdi zmdi-account-box"></i>   
                                            </span>
                                        </div>
                                    </div>
                                </div> 
								<!-- form-group.// -->
								<div class="row">   
								<div class="col-sm-6">
                                <div class="form-group">
                                    <label for="cardNumber">Cash</label>
                                    <div class="input-group">
                                        <input type="text" required="required" onkeypress="return isNumber(event)" autocomplete="off" id="cash" onkeyup="return update_credit(this.value);" class="form-control" name="cash" placeholder="">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="zmdi zmdi-money"></i>   
                                            </span>
                                        </div>
                                    </div>
									</div>
                                </div> <!-- form-group.// -->
								<div class="col-sm-6">
                                <div class="form-group">
                                    <label for="cardNumber">Credit</label>
                                    <div class="input-group">
                                        <input type="text" required="required" readonly="readonly" class="form-control" name="credit" id="credit" value="0" placeholder="">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="zmdi zmdi-card"></i>   
                                            </span>
                                        </div>
                                    </div>
                                </div> <!-- form-group.// -->
								</div> 
								</div> 
                                <input type="hidden" value="<?php echo $cart_detail['billtype']; ?>" name="billtype" id="billtype" />
                                <img id="submit_img" src="view/image/processing_image.gif" style="height: 48px;width: 48px;display: none;float: right;" />
                                <button id="submit_btn" class="subscribe btn btn-primary btn-block" type="button"  onclick="return submit_order();" > Pay Now  </button>
                                
                  
                             </form>
                        </div> <!-- tab-pane.// -->
                    </div> <!-- tab-content .// -->
                </div> <!-- card-body.// -->
            </article> <!-- card.// -->
        </aside> <!-- col.// -->
        <aside class="col-sm-3"></aside>
    </div>
	
	<?php echo $footer; ?>
	
	<script type="text/javascript">
	$(document).ready(function() 
{ 
    $('input[name=\'mobile\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=pos/total_credit/autocomplete&token=<?php echo $token; ?>&filter_telephone=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['telephone'],
						value: item['customer_id'],
                        name: item['name'],
                        aadhar: item['aadhar'],
                        credit: item['credit']
					}
				}));
			}
		});
	},
	'select': function(item) 
    {
        $("#mobile").val(item['label']);
		$("#name").val(item['name']);
		if(item['aadhar'])
		{
			$("#aadhar").val(item['aadhar']);
		}
	}
});
});
	function isNumber(evt) 
	{
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode > 31 && (charCode < 48 || charCode > 57)) 
		{
			return false;
		}
		return true;
	}
  
	function submit_order()
	{
		
		//$("#submit_btn").hide();
		//$("#submit_img").show();
		
		var mobile=$("#mobile").val();
		var name=$("#name").val();
		var aadhar=$("#aadhar").val();
		
		var cash=$("#cash").val();
		var credit=$("#credit").val();
		var order_detail=$("#order_detail").val();
                var order_total=$("#order_total").val();
                var billtype=$("#billtype").val();
                if(!mobile)
                {
                    //alert('Please enter Customer mobile number');
                    alertify.error('Please enter Customer mobile number');
                    return false;
                }
                if(mobile.length<10)
                {
                    //alert('Mobile number should be min 10 Digit');
                    alertify.error('Mobile number should be min 10 Digit');
                    return false;
                }
                if(mobile.length>10)
                {
                    //alert('Mobile number should be max 10 Digit');
                    alertify.error('Mobile number should be max 10 Digit');
                    return false;
                }
                if(!name)
                {
                    //alert('Please enter the Customer name');
                    alertify.error('Please enter the Customer name');
                    return false;
                }
                
                if(!cash)
                {
                    //alert('Please enter the Cash amount');
                    alertify.error('Please enter the Cash amount');
                    return false;
                }
		if(Math.round(parseFloat(cash))>Math.round(parseFloat(order_total)))
		{
			//alert('Cash should be less then Order Total');
			alertify.error('Cash should be less then Order Total');
			return false;
		}
                
		$.ajax({
		url: 'index.php?route=pos/pos/submit_order&token=<?php echo $token; ?>',
		dataType: 'json',
		method: 'POST',
		data: {
                        order_detail:encodeURIComponent(order_detail),
                        order_total:encodeURIComponent(order_total),
                        farmer_name:encodeURIComponent(name),
                        customer_mob:encodeURIComponent(mobile),
                        aadhar:encodeURIComponent(aadhar),
                        cash:encodeURIComponent(cash),
                        credit:encodeURIComponent(credit),
                        billtype:encodeURIComponent(billtype)
                        }, 
		beforeSend: function() {
			$("#submit_btn").hide();
		    $("#submit_img").show();
			$(".page-loader").addClass("important");
			$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
			$(".page-loader").show();
		},
		complete: function() {
			
		},
		success: function(html) 
        {
			//alert(JSON.stringify(html));//return false;
                        
			if(html['success']=='')
			{
				//alert(html['error']);
				
				$("#submit_btn").show();
		    	$("#submit_img").hide();
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
				alertify.error(html['error']);
				return false;
			}
			else
			{
				$("#submit_btn").hide();
				$("#submit_img").hide();
				
				$(".page-loader").hide();
				alertify.success(html['success']);
				url = 'index.php?route=pos/pos/order_summary&token=<?php echo $token; ?>&pagetittle=Order Summary';
                                var order_id=html['order_id_encrypted'];
                                url += '&order_id=' + encodeURIComponent(order_id);
                                location.href =url;
				return false;
			}
			
			
		},
		error: function(xhr, ajaxOptions, thrownError) {

			//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			$(".page-loader").removeClass("important");
			$("#please_wait_span").remove();
			$(".page-loader").hide();
			alertify.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			
		}
	});
			
		
		return false;
	}

function update_credit(cash)
{
	
    if(cash)
    {
        var order_total=$("#order_total").val();
        var val =  parseFloat(order_total) - parseFloat(cash);
		var str = cash;

		var newStr = str.substring(0, (str.length - 1));
		
        if(parseFloat(val)<0)
		{
			$("#cash").val(newStr);
			
			alertify.error('Cash can not be greater the order total');
			
			return false;
		}
		val=val.toFixed(2);
        $("#credit").val(val);
		
        //alert(order_total);
       // alert(val);
    }
    else
    {
       $("#credit").val('0'); 
    }
}
</script>
