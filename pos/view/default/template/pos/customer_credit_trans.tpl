<?php echo $header; ?><?php echo $column_left; ?>
 
                    
                    <div class="card">
                        
                        <div class="card-header">
                            <h2 class="card-title">Personal Information</h2>
                            <a href="<?php echo $return_url; ?>">
                                <i class="fa zmdi zmdi-long-arrow-left fw" style="float: right"></i>
                            </a>
                            <small class="card-subtitle">Name : <?php echo $name; ?></small>
                            <small class="card-subtitle">Mobile no : <?php echo $mobile_number; ?></small>
							<small class="card-subtitle">Membership ID : <?php echo $_GET['customer_id']; ?></small>
                            <!--<small class="card-subtitle">Aadhar No: <?php echo $aadhar; ?></small>-->
                            <small class="card-subtitle">Credit Balance : <?php echo RUPPE_SIGN.$credit; ?></small>
                        </div>

                        
                    </div>

 <div class="card">
                    

                    <div class="card-block">
                        <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                            <tr>
                                <th>S NO.</th>
                                <th>Date</th>
                                <th>Inv No</th>
									<th>Mode</th>
                                <th>Amt Paid</th>
                                <th>Amt Credit</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $a=1;
                            foreach($results as $product)
                            {
                            ?>
                            <tr>
                                <td><?php echo $a; ?></td>
									<td><?php echo $product['dat']; ?></td>
                                <td><?php echo $product['invoice_prefix'].$product['invoice_no']; ?></td>
									<td><?php echo $product['transaction_type']; ?></td>
								<td><?php echo RUPPE_SIGN.number_format((float)$product['credit_amount'], 0, '.', ''); ?></td>
									<td><?php echo RUPPE_SIGN.number_format((float)$product['total_credit'], 0, '.', ''); ?></td>
                               
                                
                            </tr>
                            <?php $a++; } ?>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
<div class="modal" id="modal"  tabindex="-1">
<div class="modal-dialog" >
<div class="modal-content" style="">
    <button type="button" class="close pull-right" style="text-align: right;margin-bottom: -4px;margin-right: 8px;"
        onclick="return close_model();" data-dismiss="modal">
    &times;
</button>
	<div class="modal-header" style="height:60px;">
	
	<label>Order Id  :&nbsp;&nbsp;&nbsp;</label><label id="orderid"></label>&nbsp;&nbsp;&nbsp;&nbsp;<label>ORDER DATE  :</label><label id="orderdate"></label>
	</div>
	<div class="modal-body" id="printarea">
	<div class="table-responsive">
	<img id="cr_img" src="view/image/processing_image.gif" style="float: right; margin-right:40%; height: 60px;display : none;"/>
			<table class="table table-bordered" id="prd_table">
			<thead>
				<tr>
					<td class="text-left">S no</td>
				    <td class="text-left">Product Name</td>
				    <td class="text-left">Quantity</td>
				    <td class="text-left">Price</td>
					<td class="text-left">Tax</td>		
					<td class="text-left">Total</td>
				
					
               </tr>
           </thead>
			<tbody id="productdtl_body"> 
				 	 
			</tbody>
			</table>			
	</div>
	</div>
    
  </div>
	</div>
	</div>
 <!-- Ignore backdrop click 
                            <div class="modal" id="modal-backdrop-ignore" data-backdrop="static" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content" style="background-color: #D5D5D5 !important;">
                                        
                                        <div class="modal-header">
                                            <h5 class="modal-title pull-left">Order Information(<span id='order_id'></span>)</h5>
                                        
                                        </div>
                                        <div class="modal-body">
                                            here desc
                                            
                                        </div>
                                        <div class="modal-footer">
                                            
                                            <button type="button" onclick="return close_model();" class="btn btn-link" style="width: 10px;" data-dismiss="modal">X</button>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
<?php echo $footer; ?>
<script type="text/javascript">
function open_model(order_id)
{ 
    //$("#order_id").html(order_id);
    productdtl(order_id);
    $('.modal').show();
       
}
function productdtl(oid)
 {
	$('#cr_img').show(); 
	$('#prd_table').hide();
	var url= 'index.php?route=pos/report/orderInfo&token=<?php echo $token; ?>&order_id=' +  encodeURIComponent(oid);
	$('#productdtl_body').html('');
	$.ajax({
		url:url,
		dataType: 'json',			
		success: function(json) {
               
			$('#cr_img').hide(); 	
             var json_obj = json;
			 var r=1;
			
			$('#productdtl_body').html('');
			$("#orderid").html(oid);
			$("#orderdate").html(json['order_date']);
			
					var all_product_total=0;
					var addamount=0;
					var tot=0;
					for (i=0; i < json_obj['orders'].length; i++)
					{	
						
						addamount=(parseFloat(json_obj['orders'][i]['price']))+(parseFloat(json_obj['orders'][i]['tax']));
						tot=parseFloat((json_obj['orders'][i]['quantity'])*(addamount));
						tot=tot.toFixed(2);
						
						$('#productdtl_body').append('<tr><td>'+r+'</td><td>'+json_obj['orders'][i]['name']+'</td><td>'+json_obj['orders'][i]['quantity']+'</td><td><?php echo RUPPE_SIGN; ?>'+json_obj['orders'][i]['price']+'</td><td><?php echo RUPPE_SIGN; ?>'+json_obj['orders'][i]['tax']+'</td><td><?php echo RUPPE_SIGN; ?>'+tot+'</td></tr>');
					    
						all_product_total=parseFloat(all_product_total)+parseFloat(tot);
						
					r=r+1;
					}
						$('#productdtl_body').append('<tr><td colspan="5" style="text-align: right;">Total Amount</td><td><?php echo RUPPE_SIGN; ?> '+all_product_total+'</td></tr>');
						
						$('#productdtl_body').append('<tr><td colspan="6" style="text-align: right;"></td></tr>');
						$('#productdtl_body').append('<tr><td colspan="2" style="text-align: right;"><b>Order Total </b></td><td colspan="2" style="text-align: right;"><b>Cash Amount </b></td><td colspan="2"><b>Credit Amount</b></td></tr>');
						$('#productdtl_body').append('<tr><td colspan="2" style="text-align: right;"><?php echo RUPPE_SIGN; ?>'+json['order_total']+'</td><td colspan="2" style="text-align: right;"><?php echo RUPPE_SIGN; ?>'+json['order_cash']+'</td><td colspan="2"><?php echo RUPPE_SIGN; ?>'+json['order_credit']+'</td></tr>');
						
				 $('#prd_table').show();	
		
	        },
            error:function (json)
			{
					$('#cr_img').hide(); 	
                   
					alertify.error("Opps some error occurred !");
            }
               
	});
	 
 }  
function close_model()
{
    
    $('.modal').hide();
}
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
</script>


