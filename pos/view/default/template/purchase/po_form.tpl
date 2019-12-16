<?php echo $header; ?><?php echo $column_left; ?>
 
            
            <?php if (isset($_SESSION['unsuccess_message'])) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['unsuccess_message']; unset($_SESSION['unsuccess_message']); ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
	<?php if (isset($_SESSION['delete_unsuccess_message'])) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['delete_unsuccess_message']; unset($_SESSION['delete_unsuccess_message']); ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
                <div class="card">
                        <div class="card-header">
                            <h1 style="float: left;">Add Purchase Order </h1>
                            <div class="pull-right" style="float: right;">
									<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo "Cancel"; ?>" class="btn btn-default"><i class="zmdi zmdi-mail-reply"></i></a></div>
              
								</div>
                        </div>
                <div class="card">
                        <div class="card-block">
                            
                             <form method="post" action="" id="invoice_form">
	<div class="panel-body">
            
            <div class="row">
            <div class="col-sm-12">
               <div class="table-responsive">
                  <table class="table table-bordered">
                    <tr>
                        <td class="col-sm-6">
						<strong>From:</strong>
                            <br />
                             <?php echo OFFICE_ADDRESS; ?>
                        </td>
                        <td class="col-sm-6">
                            <strong>Supplier:</strong>
							<input type="hidden" value="" id="supplier_name" name="supplier_name" />
                          <select onchange="return get_supplier_to_data(this.value);" name="filter_supplier" id="input-supplier" required="required" style="width: 100%;" class="select2 form-control">
                              <option value="" >Select Supplier</option>
                  <?php foreach ($suppliers as $supplier) { ?>
                  <?php if ($supplier['pre_mongified_id'] == $filter_suplier) { ?>
                  <option value="<?php echo $supplier['pre_mongified_id']; ?>" selected="selected"><?php echo $supplier['first_name']." ".$supplier['last_name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $supplier['pre_mongified_id']; ?>"><?php echo $supplier['first_name']." ".$supplier['last_name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
                            <br /><br />
                            <?php  $store_to_data=explode('---',$store_to_data);  ?>
                            Name : <span id="to_supplier_name"><?php echo $store_to_data[0];    ?></span> <br />
                            Address : <span id="to_supplier_address"><?php  echo $store_to_data[1];    ?></span> <br />
                            Phone No : <span id="to_supplier_phone"><?php  echo $store_to_data[2];    ?></span><br />
                            Email Id : <span id="to_supplier_email"><?php  echo $store_to_data[3];    ?></span> <br />
                            Pan Card : <span id="to_supplier_pan"><?php echo $store_to_data[4];   ?></span> <br />
		            GSTN : <span id="to_supplier_gstn"><?php  echo $store_to_data[5];   ?></span> <br />
                        </td>
                    </tr>

                      <tr>
                          <td class="col-sm-6">
                              <strong>Delivery Address:</strong>
							  <br/><br/>
                              <?php $store_to_data=explode('---',$store_data);  ?>
                             Name : <span id="to_store_name"><?php echo $store_to_data[0];  ?></span> <br />
                            Address : <span id="to_store_address"><?php  echo $store_to_data[1];  ?></span> <br />
                            Phone No : <span id="to_store_phone"><?php  echo $store_to_data[2];  ?></span><br />
                            Email Id : <span id="to_store_email"><?php  echo $store_to_data[3];  ?></span> <br />
                            Pan Card : <span id="to_store_pan"><?php  echo $store_to_data[4];  ?></span> <br />
                            GSTN : <span id="to_store_gst"><?php echo $store_to_data[5];   ?></span> <br />
		MSMFID : <span id="to_store_gst"><?php echo $store_to_data[6];   ?></span> <br />
	
                              
                         </td>
                          <td>
							<!--
                              <strong>Contact Person Name :</strong><input onkeypress="checkinputcharacter();" autocomplete="off"  name="contactname" id="contactname" type="text" value="" placeholder="Name" class="form-control"/><br />
                              <strong>Contact Person Mobile :</strong><input onkeypress='return event.charCode >= 48 &amp;&amp; event.charCode <= 57 || event.keyCode==8 || event.keyCode==46' placeholder="Mobile"  autocomplete="off"  maxlength="10" minlength="10" name="contactmobile" id="contactmobile" type="text" value="" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57 || event.keyCode==8 || event.keyCode==46" class="form-control"/><br />
                            --> 
								<strong>Valid Till Date:</strong><br />
                              <div class="input-group date">
                                
								<div class="form-group">
                                            <input type="text" id="filter_date" value="<?php echo $filter_date; ?>"  class="form-control date-picker" placeholder="Valid Date">
                                            <i class="form-group__bar"></i>
                                        </div>
                              </div>
                    
                           </td>
                      </tr>
                      </table>
                   
                </div>
            </div>
        </div>
            
	    
            <br/>
            <br/>
            
		<table class="table table-bordered" id="print_table_2" border="1">
          <thead>
                <tr>
                          
                          <th class="text-left" style="width: 11.11%;">Product</th>
                          <th class="text-left" style="width: 11.11%;">Rate</th>
						  <th class="text-left" style="width: 11.11%;">Tax Type</th>
                          <th class="text-left" style="width: 11.11%;">Quantity</th>
			 
                           <th class="text-left" style="width: 11.11%;">Amount</th>
							<th class="text-left" style="width: 11.11%;"></th>
                          
                </tr>
          </thead>
		  <input type="hidden" id="pd_count" name="pd_count" value="1" />
          <tbody id="t_body">
          
              
             
		  <?php
			$grand_total = 0;$a=1;
            
			
		  ?>
           <tr id="tr_<?php echo  $a; ?>">
			<input type="hidden" name="product_id[]" id="product_id_<?php echo  $a; ?>" />
			<!--<input type="hidden" name="product_name[]" id="product_name_<?php echo  $a; ?>" />-->
			<input type="hidden" name="hstn[]" id="hstn_<?php echo  $a; ?>" />
			<input type="hidden" name="per_tax[]" id="per_tax_<?php echo  $a; ?>" />
			<input type="hidden" name="tax[]" id="tax_<?php echo  $a; ?>" />
			<input type="hidden" name="tax_rate[]" id="p_tax_rate_<?php echo  $a; ?>" />
			<input type="hidden" name="product_amount[]" id="p_amount_<?php echo  $a; ?>" />
                  <td class="text-left" id="td_p_name_<?php echo  $a; ?>">
                  <input  required="required" placeholder="Product" class="form-control" name="product_name[]" id="p_name_<?php echo  $a; ?>" type="text" value="" />
              </td>
                               
				<td class="text-left" id="td_p_price_<?php echo  $a; ?>">
					<input required="required" autocomplete="off" class="form-control"  onkeypress="return remove_zero(this.value,<?php echo $a; ?>,event);" onkeyup="return update_by_price(this.value,<?php echo $a; ?>);" name="p_price[]" id="p_price_<?php echo  $a; ?>" type="text" value="" />
				</td>
              <td class="text-left" id="td_p_tax_rate_<?php echo  $a; ?>"></td>           
				<td class="text-left" id="td_p_qnty_<?php echo  $a; ?>">
					<input autocomplete="off"  required="required" onkeypress="return remove_zero_q(this.value,<?php echo $a; ?>,event);" onkeyup="return update_by_q(this.value,<?php echo $a; ?>);" class="form-control" name="p_qnty[]" id="p_qnty_<?php echo  $a; ?>" type="text" value="" />
				</td>
			  
                         
				<td class="text-left" id="td_p_amount_<?php echo  $a; ?>"></td>
				<td class="text-left" id="td_action_<?php echo  $a; ?>">
					<a href="#" onclick="return add_row();" id="add_btn_<?php echo  $a; ?>" ><i style="font-size: 30px;" class="fa zmdi zmdi-plus"></i></a>
				</td>             
			</tr>
		<?php
                        //$a++;
			//}
		?>
			
          
</tbody>
        </table>
       				
		<table class="table table-bordered">
          <tbody id="tax_body">
		   
			
            </tbody> 

		</table>
                
            <table class="table table-bordered" id="print_table_3" border="1">
			<tr id="tr_scgst_by_18" >
                    <td class="text-right" style="width: 78%;"><b>
                            <span id="span_cgst_txt_by_18">
                            Grand Total
                            </span>
                            
                        </b></td>
                    <td style="width: 22%;" class="text-left">
                        
                        <span id="td_grand_total">
                        0.00                       
							</span>
							<input type="hidden" name="grand_total" id="grand_total" value="0" />
                    </td>
		</tr> 
			    	
            </table>
           
            <button style="margin-right: 10px;" type="submit" onclick="return check_form();"  class="btn btn-primary pull-right" id="cr_btn1" >Save</button> &nbsp; &nbsp;
            <img id="cr_img" src="view/image/processing_image.gif" style="float: right;height: 60px;display: none;"/>
            
        
	
	</div> 
	</form>
                        </div>
                    </div>

<?php echo $footer; ?>

<script type="text/javascript">
function check_form()
{
var input_supplier=$("#input-supplier").val();

var input_date=$("#filter_date").val();

if(!input_supplier) 
{
    alert('Please Select Supplier');
	$("#input-supplier").focus();
    return false; 

}
if(!input_date)
{
alert('Please select Valid Date');
$("#filter_date").focus();
return false;
}
var pd_count=$("#pd_count").val();
	
	var count = parseFloat(pd_count).toFixed(0);
	for(c=1;c<=count;c++)
    {
		if(!$("#p_name_"+c).val())
		{
			alert('please select a product');
			$("#p_name_"+c).focus();
			return false;
		}
		if(!$("#p_price_"+c).val())
		{
			alert('please enter product price');
			$("#p_price_"+c).focus();
			return false;
		}
		if(!$("#p_qnty_"+c).val())
		{
			alert('please enter product quantity');
			$("#p_qnty_"+c).focus();
			return false;
		}
		
	}

     $("#cr_btn1").hide();
     $("#cr_btn2").hide();
     $("#cr_img").show(); 
     return true;

}

function enable_product_name(ware_house)
{
    if(ware_house!="")
    {
     $("#p_name_1").prop('disabled', false);
    }
    else
    {
        $("#p_name_1").prop('disabled', true);
    }
    return false;
}



function remove_row()
{
	var pd_count=$("#pd_count").val();
	
	var a=(parseFloat(pd_count)-parseFloat(1)).toFixed(0);
	$("#tr_"+pd_count).remove();
	//alert(count);
	$("#remove_btn_"+a).show();
	$("#add_btn_"+a).show();
	
	$("#pd_count").val(a);
	update_cart(a,'remove');
	return false;
}
function add_row()
{
	var pd_count=$("#pd_count").val();
	var a=(parseFloat(pd_count)+parseFloat(1)).toFixed(0);
	var count = parseFloat(pd_count).toFixed(0);
	for(c=1;c<=count;c++)
    {
		if(!$("#p_name_"+c).val())
		{
			alert('please select a product');
			$("#p_name_"+c).focus();
			return false;
		}
		if(!$("#p_price_"+c).val())
		{
			alert('please enter product price');
			$("#p_price_"+c).focus();
			return false;
		}
		if(!$("#p_qnty_"+c).val())
		{
			alert('please enter product quantity');
			$("#p_qnty_"+c).focus();
			return false;
		}
		$("#remove_btn_"+c).hide();
		$("#add_btn_"+c).hide();
	}
	var nw_html='<tr id="tr_'+a+'">'+
	 '<td class="text-left" id="td_p_name_'+a+'">'+
			'<input type="hidden" name="product_id[]" id="product_id_'+a+'" />'+
			//'<input type="hidden" name="product_name[]" id="product_name_'+a+'" />'+
			'<input type="hidden" name="hstn[]" id="hstn_'+a+'" />'+
			'<input type="hidden" name="per_tax[]" id="per_tax_'+a+'" />'+
			'<input type="hidden" name="tax[]" id="tax_'+a+'" />'+
			'<input type="hidden" name="tax_rate[]" id="p_tax_rate_'+a+'" />'+
			'<input type="hidden" name="product_amount[]" id="p_amount_'+a+'" />'+
                 
                  '<input  required="required" placeholder="Product" class="form-control" name="product_name[]" id="p_name_'+a+'" type="text" value="" />'+
              '</td>'+
                               
				'<td class="text-left" id="td_p_price_'+a+'">'+
					'<input required="required" autocomplete="off" class="form-control"  onkeypress="return remove_zero(this.value,'+a+',event);" onkeyup="return update_by_price(this.value,'+a+');" name="p_price[]" id="p_price_'+a+'" type="text" value="" />'+
				'</td>'+
              '<td class="text-left" id="td_p_tax_rate_'+a+'"></td>'+          
				'<td class="text-left" id="td_p_qnty_'+a+'">'+
					'<input autocomplete="off"  required="required" onkeypress="return remove_zero_q(this.value,'+a+',event);" onkeyup="return update_by_q(this.value,'+a+');" class="form-control" name="p_qnty[]" id="p_qnty_'+a+'" type="text" value="" />'+
				'</td>'+
			  
                         
				'<td class="text-left" id="td_p_amount_'+a+'"></td>'+
				'<td class="text-left" id="td_action_'+a+'">'+
					'<a href="#" onclick="return add_row();" id="add_btn_'+a+'" ><i style="font-size: 30px;" class="fa zmdi zmdi-plus"></i></a>'+
					' &nbsp; &nbsp; <a href="#" onclick="return remove_row();" id="remove_btn_'+a+'" ><i style="font-size: 30px;color: red;" class="fa zmdi zmdi-minus-circle"></i></a>'+
				'</td>'+         
			'</tr>';	
		$("#t_body").append(nw_html);
		$("#pd_count").val(a);
		setautoserarch(a);
	return false;
}
function update_by_q(qnty,a)
{
	update_cart(a,'update');
	return false;
}
function update_by_price(product_base_price,a)
{
	update_cart(a,'update');
	return false;
}

function remove_zero(valuee,a,evt)
{
    
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46)
          {
             if(valuee==0)
    {
    $("#p_price_"+a).val('');
    
    }
    return true;
          }
          if (charCode > 31 && (charCode < 48 || charCode > 57))
          {
             return false;
          }
          
    if(valuee==0)
    {
    $("#p_price_"+a).val('');
    }
}
function remove_zero_q(valuee,a,evt)
{
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 46)
          {
             if(valuee==0)
    {
    $("#p_qnty_"+a).val('');
    
    }
    return true;
          }
          if (charCode > 31 && (charCode < 48 || charCode > 57))
          {
             return false;
          }
          
    if(valuee==0)
    {
    $("#p_qnty_"+a).val('');
    }
}

function setautoserarch(id)
{
	$('#p_name_'+id).autocomplete({
     //$('#p_name_<?php echo $b; ?>').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=purchase/purchase_order/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) { 
                
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['product_id'],
                        price: item['price'],
                        price_wo_t: item['price_wo_t'],
                        hstn: item['hstn'],
                        product_tax_type: item['product_tax_type'],
                        product_tax_rate: item['product_tax_rate'],
                        
                    }
                }));
            }
        });
    },
    'select': function(item) {
        
        var this_id=(this.id).split('_');
        var thisid=this_id.pop();
        $('#p_name_'+thisid).val(item['label']);
		
        $('#product_name_'+thisid).val(item['label']);
        $('#product_id_'+thisid).val(item['value']);
       
        $('#hstn_'+thisid).val(item['hstn']);
        $('#p_price_'+thisid).val(item['price_wo_t']);
		$('#p_tax_rate_'+thisid).val(item['product_tax_rate']);
		$('#td_p_tax_rate_'+thisid).html(item['product_tax_type']);
		$("#td_p_amount_"+thisid).html('0.00');
		$("#p_amount_"+thisid).val(0);
		
		$('#tax_'+thisid).val(0);
		
    var tax_type='';  
    var tax_type=item['product_tax_type'];
	
    if(tax_type.trim()=="GST@5%")
    {
		$('#per_tax_'+thisid).val(5);
    }
    else if(tax_type.trim()=="GST@12%")
    {
      $('#per_tax_'+thisid).val(12);  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       $('#per_tax_'+thisid).val(18);;
    }
	else if(tax_type.trim()=="GST@28%")
    {
       $('#per_tax_'+thisid).val(28);
    }
    else 
    {
       $('#per_tax_'+thisid).val(0);
    }
	
	/////////////////////////
	update_cart(thisid,'update');
	///////////////////////
    }
});
}
   $('input[name=\'product_name[]\']').autocomplete({
    
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=purchase/purchase_order/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) { 
                
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['product_id'],
                        price: item['price'],
                        price_wo_t: item['price_wo_t'],
                        hstn: item['hstn'],
                        product_tax_type: item['product_tax_type'],
                        product_tax_rate: item['product_tax_rate'],
                        
                    }
                }));
            }
        });
    },
    'select': function(item) {
        
        var this_id=(this.id).split('_');
        var thisid=this_id.pop();
        $('#p_name_'+thisid).val(item['label']);
		
        $('#product_name_'+thisid).val(item['label']);
        $('#product_id_'+thisid).val(item['value']);
       
        $('#hstn_'+thisid).val(item['hstn']);
        $('#p_price_'+thisid).val(item['price_wo_t']);
		$('#p_tax_rate_'+thisid).val(item['product_tax_rate']);
		$('#td_p_tax_rate_'+thisid).html(item['product_tax_type']);
		
		$("#td_p_amount_"+thisid).html('0.00');
		$("#p_amount_"+thisid).val(0);
	
		$('#tax_'+thisid).val(0);
		
    var tax_type='';  
    var tax_type=item['product_tax_type'];
	
    if(tax_type.trim()=="GST@5%")
    {
		$('#per_tax_'+thisid).val(5);
    }
    else if(tax_type.trim()=="GST@12%")
    {
      $('#per_tax_'+thisid).val(12);  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       $('#per_tax_'+thisid).val(18);;
    }
	else if(tax_type.trim()=="GST@28%")
    {
       $('#per_tax_'+thisid).val(28);
    }
    else 
    {
       $('#per_tax_'+thisid).val(0);
    }
	/////////////////////////
	update_cart(thisid,'update');
	///////////////////////
    }
});
function update_cart(a,action)
{
	if(action=='update')
	{
		/////////////////////////
	var qnty=$("#p_qnty_"+a).val();
	if(!qnty)
	{
		qnty=0;
	}
	var product_base_price=$("#p_price_"+a).val();
	if(!product_base_price)
	{
		product_base_price=0;
	}
	var product_tax_percentage=$("#per_tax_"+a).val();
	
	
	var oly_multi=(parseFloat(product_base_price))*(parseFloat(product_tax_percentage));
	var one_peice_tax=parseFloat(parseFloat(oly_multi)/parseFloat(100)).toFixed(2);
	
	var product_price=parseFloat(one_peice_tax)+parseFloat(product_base_price);
	product_price=product_price.toFixed(2);
	
	$("#tax_"+a).val((parseFloat(qnty)*parseFloat(one_peice_tax)).toFixed(2));
	$("#p_tax_rate_"+a).val(parseFloat(one_peice_tax).toFixed(2));
	
	var product_amount=parseFloat(product_price)*parseFloat(qnty);
	$("#td_p_amount_"+a).html(parseFloat(product_amount).toFixed(2));
	$("#p_amount_"+a).val(parseFloat(product_amount).toFixed(2));
	}
	
		
	var pd_count=$("#pd_count").val();
	$("#tax_body").html('');
	var tax_total_by_type_temp_5=0;
	var tax_total_by_type_5=0;
	var tax_total_by_type_temp_12=0;
	var tax_total_by_type_12=0;
	var tax_total_by_type_temp_18=0;
	var tax_total_by_type_18=0;
	var tax_total_by_type_temp_28=0;
	var tax_total_by_type_28=0;
	var tax_by_product=0;
	var overall_total_tax=0;
	var grand_total=0;
	////////loop start here///////////
	for (i = 1; i <= pd_count; i++) 
	{
		var tax_type=$("#per_tax_"+i).val();
		tax_by_product=$("#tax_"+i).val();
		//alert('in loop '+i+ ' for tax type '+tax_type+' tax for this product is '+tax_by_product);
		if(($("#product_id_"+i).val()!='') && ($("#p_price_"+i).val()!='') && ($("#p_qnty_"+i).val()!='') )
		{
			if(tax_type=='5')
			{
				tax_total_by_type_temp_5=parseFloat(tax_total_by_type_temp_5)+parseFloat(tax_by_product);
				var tax_total_by_type_half=(parseFloat(tax_total_by_type_temp_5)/2).toFixed(2);
				if(parseFloat(tax_total_by_type_5)>0)
				{
					$("#tax_tr_"+tax_type).remove();
					
					var tax_html='<tr id="tax_tr_'+tax_type+'" >'+
                    '<td class="text-right" style="width: 78%;"><b>'+
                            '<span id="span_cgst_txt">'+
                            'CGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                           ' <br>'+
                           '<span id="span_sgst_txt">'+
                            'SGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                        '</b></td>'+
                    '<td style="width: 22%;" class="text-left">'+
                        '<span id="span_cgst">'+tax_total_by_type_half+'</span>'+
                        '<br>'+
                        '<span id="span_sgst">'+tax_total_by_type_half+                   
						'</span>'+
                   '</td>'+
					'</tr>';
				}
				else
				{
					
				var tax_html='<tr id="tax_tr_'+tax_type+'" >'+
                    '<td class="text-right" style="width: 78%;"><b>'+
                            '<span id="span_cgst_txt">'+
                            'CGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                           ' <br>'+
                           '<span id="span_sgst_txt">'+
                            'SGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                        '</b></td>'+
                    '<td style="width: 22%;" class="text-left">'+
                        '<span id="span_cgst">'+tax_total_by_type_half+'</span>'+
                        '<br>'+
                        '<span id="span_sgst">'+tax_total_by_type_half+                   
						'</span>'+
                   '</td>'+
				'</tr>';
				}
				tax_total_by_type_5=tax_total_by_type_temp_5;
				
				$("#tax_body").append(tax_html);
			}
			if(tax_type=='12')
			{
				
				tax_total_by_type_temp_12=parseFloat(tax_total_by_type_temp_12)+parseFloat(tax_by_product);
				var tax_total_by_type_half=(parseFloat(tax_total_by_type_temp_12)/2).toFixed(2);
				if(parseFloat(tax_total_by_type_12)>0)
				{
					$("#tax_tr_"+tax_type).remove();
					
					var tax_html='<tr id="tax_tr_'+tax_type+'" >'+
                    '<td class="text-right" style="width: 78%;"><b>'+
                            '<span id="span_cgst_txt">'+
                            'CGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                           ' <br>'+
                           '<span id="span_sgst_txt">'+
                            'SGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                        '</b></td>'+
                    '<td style="width: 22%;" class="text-left">'+
                        '<span id="span_cgst">'+tax_total_by_type_half+'</span>'+
                        '<br>'+
                        '<span id="span_sgst">'+tax_total_by_type_half+                   
						'</span>'+
                   '</td>'+
					'</tr>';
				}
				else
				{
					
				var tax_html='<tr id="tax_tr_'+tax_type+'" >'+
                    '<td class="text-right" style="width: 78%;"><b>'+
                            '<span id="span_cgst_txt">'+
                            'CGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                           ' <br>'+
                           '<span id="span_sgst_txt">'+
                            'SGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                        '</b></td>'+
                    '<td style="width: 22%;" class="text-left">'+
                        '<span id="span_cgst">'+tax_total_by_type_half+'</span>'+
                        '<br>'+
                        '<span id="span_sgst">'+tax_total_by_type_half+                   
						'</span>'+
                   '</td>'+
				'</tr>';
				}
				tax_total_by_type_12=tax_total_by_type_temp_12;
				
				$("#tax_body").append(tax_html);
			}
			if(tax_type=='18')
			{
				
				tax_total_by_type_temp_18=parseFloat(tax_total_by_type_temp_18)+parseFloat(tax_by_product);
				var tax_total_by_type_half=(parseFloat(tax_total_by_type_temp_18)/2).toFixed(2);
				if(parseFloat(tax_total_by_type_18)>0)
				{
					$("#tax_tr_"+tax_type).remove();
					
					var tax_html='<tr id="tax_tr_'+tax_type+'" >'+
                    '<td class="text-right" style="width: 78%;"><b>'+
                            '<span id="span_cgst_txt">'+
                            'CGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                           ' <br>'+
                           '<span id="span_sgst_txt">'+
                            'SGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                        '</b></td>'+
                    '<td style="width: 22%;" class="text-left">'+
                        '<span id="span_cgst">'+tax_total_by_type_half+'</span>'+
                        '<br>'+
                        '<span id="span_sgst">'+tax_total_by_type_half+                   
						'</span>'+
                   '</td>'+
					'</tr>';
				}
				else
				{
					
				var tax_html='<tr id="tax_tr_'+tax_type+'" >'+
                    '<td class="text-right" style="width: 78%;"><b>'+
                            '<span id="span_cgst_txt">'+
                            'CGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                           ' <br>'+
                           '<span id="span_sgst_txt">'+
                            'SGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                        '</b></td>'+
                    '<td style="width: 22%;" class="text-left">'+
                        '<span id="span_cgst">'+tax_total_by_type_half+'</span>'+
                        '<br>'+
                        '<span id="span_sgst">'+tax_total_by_type_half+                   
						'</span>'+
                   '</td>'+
				'</tr>';
				}
				tax_total_by_type_18=tax_total_by_type_temp_18;
				
				$("#tax_body").append(tax_html);
			}
			if(tax_type=='28')
			{
				
				tax_total_by_type_temp_28=parseFloat(tax_total_by_type_temp_28)+parseFloat(tax_by_product);
				var tax_total_by_type_half=(parseFloat(tax_total_by_type_temp_28)/2).toFixed(2);
				if(parseFloat(tax_total_by_type_28)>0)
				{
					$("#tax_tr_"+tax_type).remove();
					
					var tax_html='<tr id="tax_tr_'+tax_type+'" >'+
                    '<td class="text-right" style="width: 78%;"><b>'+
                            '<span id="span_cgst_txt">'+
                            'CGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                           ' <br>'+
                           '<span id="span_sgst_txt">'+
                            'SGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                        '</b></td>'+
                    '<td style="width: 22%;" class="text-left">'+
                        '<span id="span_cgst">'+tax_total_by_type_half+'</span>'+
                        '<br>'+
                        '<span id="span_sgst">'+tax_total_by_type_half+                   
						'</span>'+
                   '</td>'+
					'</tr>';
				}
				else
				{
					
				var tax_html='<tr id="tax_tr_'+tax_type+'" >'+
                    '<td class="text-right" style="width: 78%;"><b>'+
                            '<span id="span_cgst_txt">'+
                            'CGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                           ' <br>'+
                           '<span id="span_sgst_txt">'+
                            'SGST @'+(parseFloat(tax_type)/2).toFixed(1)+'%'+
                           ' </span>'+
                        '</b></td>'+
                    '<td style="width: 22%;" class="text-left">'+
                        '<span id="span_cgst">'+tax_total_by_type_half+'</span>'+
                        '<br>'+
                        '<span id="span_sgst">'+tax_total_by_type_half+                   
						'</span>'+
                   '</td>'+
				'</tr>';
				}
				tax_total_by_type_28=tax_total_by_type_temp_28;
				
				$("#tax_body").append(tax_html);
			}
			grand_total=parseFloat(grand_total)+parseFloat($("#p_amount_"+i).val());
		}
		
	} 
	///////loop end here//////
	overall_total_tax=parseFloat(overall_total_tax)+parseFloat(tax_total_by_type_5)+parseFloat(tax_total_by_type_12)+parseFloat(tax_total_by_type_18)+parseFloat(tax_total_by_type_28);
	grand_total=grand_total.toFixed(2);
	$("#td_grand_total").html(grand_total);
	$("#grand_total").val(grand_total);
	
	///////////////////////
}
</script>

<script>

function get_supplier_to_data(supplier_id)
{
   if((supplier_id!="") && (supplier_id!="0"))
    {
        $.ajax({
            url: 'index.php?route=purchase/purchase_order/get_to_supplier_data&token=<?php echo $token; ?>&supplier_id=' +  encodeURIComponent(supplier_id),
            
            success: function(json) {
                var data=json.split('---');
				//alert(json);
                $("#to_supplier_name").html(data[1]);
                $("#to_supplier_address").html(data[2]);
                $("#to_supplier_phone").html(data[3]);
                $("#to_supplier_email").html(data[4]);
                $("#to_supplier_pan").html(data[5]);
                $("#to_supplier_gstn").html(data[6]);
				$("#supplier_name").val(data[1]);
            }
        });
        $("#supplier_id").val(supplier_id);
    }
    else
    {
                $("#to_supplier_name").html('');
                $("#to_supplier_address").html('');
                $("#to_supplier_phone").html('');
                $("#to_supplier_email").html('');
                $("#to_supplier_pan").html('');
                $("#to_supplier_gstn").html('');
                $("#to_supplier_phone").val('');
                $("#to_supplier_email").val('');
    }
    if(supplier_id!="")
    {
     $("#p_name_1").prop('disabled', false);
    }
    else
    {
        $("#p_name_1").prop('disabled', true);
    }
    //return false;
}

</script>
 

