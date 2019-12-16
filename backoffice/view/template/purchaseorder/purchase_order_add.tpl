<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header"> 
    <div class="container-fluid">
      <div class="pull-right"><!--<button onclick ="print_order()" data-toggle="tooltip" title="<?php echo "Print Order"; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>--><!--<a href="<?php echo $shipping; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_shipping_print; ?>" class="btn btn-info"><i class="fa fa-truck"></i></a> <a href="<?php echo $edit; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>--> <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo "Cancel Button"; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo "Purchase Order"; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>">Purchase Order</a></li>
        <?php } ?>
      </ul>
      
      <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
      <?php if ($success) {  ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    </div>
  </div>
  <div class="panel panel-default" id = "print_div">
	<div class="panel-heading">
		<h3 class="panel-title">
			<i class="fa fa-info-circle"></i>
			<?php echo "Purchase Order"; ?>
		</h3>
	</div>
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
                            <input type="hidden" value="" id="store_name" name="store_name" />
                           <select onchange="return get_ship_to_data(this.value);" name="filter_store" style="width: 100%;" id="input-store" required="required" class="select2 form-control">
						   <option value="" >SELECT</option>
                  <?php foreach ($stores as $store) { ?>
                  <?php if ($store['store_id'] == $filter_store) { ?>
                  <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
	
                              <br /><br />
                            Name : <span id="ship_to_name"><?php echo $store_to_data[0];    ?></span> <br />
                            Address : <span id="ship_to_address"><?php  echo $store_to_data[1];    ?></span> <br />
                            Phone No : <span id="ship_to_phone"><?php  echo $store_to_data[2];    ?></span><br />
                            Email Id : <span id="ship_to_email"><?php  echo $store_to_data[3];    ?></span> <br />
                            Pan Card : <span id="ship_to_pan"><?php echo $store_to_data[4];    ?></span> <br />
		GSTN : <span id="ship_to_gstn"><?php  echo $store_to_data[5];    ?></span> <br />
                            MSMFID : <span id="ship_to_msmfid"><?php  echo $store_to_data[5];    ?></span> <br />
                         </td>
                          <td>
                              <strong>Contact Person Name :</strong><input onkeypress="checkinputcharacter();" autocomplete="off"  name="contactname" id="contactname" type="text" value="" placeholder="Name" class="form-control"/><br />
                              <strong>Contact Person Mobile :</strong><input onkeypress='return event.charCode >= 48 &amp;&amp; event.charCode <= 57 || event.keyCode==8 || event.keyCode==46' placeholder="Mobile"  autocomplete="off"  maxlength="10" minlength="10" name="contactmobile" id="contactmobile" type="text" value="" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57 || event.keyCode==8 || event.keyCode==46" class="form-control"/><br />
                              <strong>Valid Till Date:</strong><br />
                              <div class="input-group date">
                                <input type="text" name="filter_date" autocomplete="off"  value="" placeholder="<?php echo "Valid Date"; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" required="required"/>
                                <span class="input-group-btn">
                                   <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                </span>
                              </div>
                    
                           </td>
                      </tr>
                      </table>
                   
                </div>
            </div>
        </div>
            
	    
            <br/>
            <br/>
            
		<table class="table table-bordered" id="print_table" border="1">
          <thead>
                <tr>
                          
                          <td class="text-left" style="width: 11.11%;">Product</td>
                           <td class="text-left" style="width: 11.11%;">Rate</td>
                          <td class="text-left" style="width: 11.11%;">Quantity</td>
			 
                           <td class="text-left" style="width: 11.11%;">Amount</td>
                       
                          
                </tr>
          </thead>
          <tbody id="t_body">
          
              
             
		  <?php
			$grand_total = 0;$a=1;
                        $p_count=count($order_information['products']);
			//foreach($order_information['products'] as $product)
			//{ print_r($product);
		  ?>
            <tr id="tr_<?php echo  $a; ?>">
                <input name="product_id" id="p_id_<?php echo  $a; ?>" type="hidden" " value="<?php echo  $product['product_id'];?>" />
                <input name="product_hsn[]" id="p_hsn_<?php echo  $a; ?>" type="hidden" value="<?php echo  $product['product_hsn'];?>" />
                
                 <input name="buttonvalue" id="buttonvalue" type="hidden" value="save" />
               
                  
                <input class="form-control" name="p_tax_type" id="p_tax_type_<?php echo  $a; ?>" type="hidden" value="<?php echo  $product['product_tax_type'];?>" />
                <input class="form-control" name="p_tax_rate" id="p_tax_rate_<?php echo  $a; ?>" type="hidden" value="<?php echo  round($product['product_tax_rate'],PHP_ROUND_HALF_UP);?>" />
                <input class="form-control" name="p_amount" id="p_amount_<?php echo  $a; ?>" type="hidden" value="<?php echo (round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity']; ?>" />
                     
                      <?php  print_r($product['product_name']); ?>
                         <td class="text-left" id="td_p_name_<?php echo  $a; ?>">
                               <?php if(empty($created_po)){ ?> 
                              <input disabled="disabled" required="required" placeholder="Product" class="form-control" name="product_name[]" id="p_name_<?php echo  $a; ?>" type="text" value="<?php echo  $product['product_name'];?>" />
                               <?php }else{ ?>
			<?php echo  $product['product_name'];?>
		<?php } ?>
 
                          </td>
                           <!--<td class="text-left" id="td_p_tax_rate_<?php echo  $a; ?>">
			  <?php echo round($product['product_tax_rate'],PHP_ROUND_HALF_UP);?>
                              
			  </td>-->
                          
			 
			  
			  <td class="text-left" id="td_p_price_<?php echo  $a; ?>">
		 <?php if(empty($created_po)){ ?> 
                              <input required="required" autocomplete="off" class="form-control"  onkeypress="return remove_zero(this.value,<?php echo $a; ?>,event);" onkeyup="return update_by_price(this.value,<?php echo $a; ?>);" name="p_price" id="p_price_<?php echo  $a; ?>" type="text" value="<?php echo round($product['product_price'],PHP_ROUND_HALF_UP);?>" />
		<?php }else{ ?>
			<?php echo round($product['product_price'],PHP_ROUND_HALF_UP);?>
		<?php } ?>
                           </td>
                           
			   <td class="text-left" id="td_p_qnty_<?php echo  $a; ?>">
			   <?php if(empty($created_po)){ ?> 
                              <input autocomplete="off"  required="required" onkeypress="return remove_zero_q(this.value,<?php echo $a; ?>,event);" onkeyup="return update_by_q(this.value,<?php echo $a; ?>);" class="form-control" name="p_qnty" id="p_qnty_<?php echo  $a; ?>" type="text" value="<?php echo  $product['product_quantity'];?>" />
			<?php }else{ ?>
			<?php echo  $product['product_quantity'];?>
		<?php } ?>
			  </td>
			  
                         
			  <td class="text-left" id="td_p_amount_<?php echo  $a; ?>">
			  <?php //+$product['product_tax_rate']
                          echo number_format((float)((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity']), 2, '.', '');
                          $grand_total=$grand_total+((round($product['product_price'],PHP_ROUND_HALF_UP))*$product['product_quantity']);
                          ?>
                              
			  </td>
                          
			</tr>
		<?php
                        //$a++;
			//}
		?>
			
          
</tbody>
        </table>
           
                
            <table class="table table-bordered" id="print_table_3" border="1">
                <tr>
                    <td class="text-left" style="width: 78%;" id="set_colspan" >
                        <textarea name="rem" id="rem"  class="form-control" placeholder="Remarks"></textarea>
                                </td>
                                <input type="text" id="grand_total" name="grand_total" value="<?php echo $grand_total; ?>" />
	            </tr>    
		 <tr>
                    <td class="text-left" style="width: 78%;" id="set_colspan" >             
		<strong>Delivery Type</strong>
                            
                           <select  name="delivery_type" id="delivery_type" required="required" class=" form-control">
                <option value="" >Select Delivery Type</option>
                  <option value="FOR Destination" >FOR Destination</option>
                  <option value="Ex Works (EXW)" >Ex Works (EXW)</option>
                </select>
		 </td>
                                
	            </tr>    	
            </table>
           <button  type="submit"  onclick="return updatebuttonvalue('save_email');" class="btn btn-primary pull-right" id="cr_btn2">Save And Email</button>
            
            <button style="margin-right: 10px;" type="submit" onclick="return updatebuttonvalue('save');"  class="btn btn-primary pull-right" id="cr_btn1" >Save</button> &nbsp; &nbsp;
            <img id="cr_img" src="http://www.danubis-dcm.org/Content/Images/processing.gif" style="float: right;height: 60px;display: none;"/>
            
        
	
	</div>
	</form>
  </div>
  
</div>
<script type="text/javascript">

function checkinputcharacter(){ 
 $('#contactname').on('keypress', function (event) {
 var regex = new RegExp("^[a-zA-Z ]+$");
 var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
 if (!regex.test(key)) {
 event.preventDefault();
 return false;
 }
});
}
 
$('.date').datetimepicker({
	pickTime: false
});
$("#input-supplier").select2();

$("#input-store").select2();
</script>
<script type="text/javascript">
function updatebuttonvalue(data)
{

$("#buttonvalue").val(data);
var input_supplier=$("#input-supplier").val();
var input_store=$("#input-store").val();
var contactname=$("#contactname").val();
var contactmobile=$("#contactmobile").val();
var input_date=$("#input-date-start").val();
var p_name=$("#p_name_1").val();
var p_price=$("#p_price_1").val();
var p_qnty=$("#p_qnty_1").val();
var delivery_type=$("#delivery_type").val();
//alert(input_supplier+'--'+input_store+'--'+contactname+'--'+contactmobile+'--'+input_date+'--'+p_name+'--'+p_price+'--'+p_qnty);
//alert(input_store);
if(!input_supplier) 
{
        alertify.error('Please Select Supplier');
        return false; 

}

else if(input_store=='0')
{
alertify.error('Please select Store');
return false;
}

else if(!contactname)
{
alertify.error('Please Fill Contact Person Name');
return false;
}
else if(!contactmobile)
{
alertify.error('Please Fill Contact Person Mobile Number');
return false;
}
else if(!input_date)
{
alertify.error('Please select Valid Date');
return false;
}
else if(!p_name)
{
alertify.error('Please select Product');
return false;
}

else if(p_price=='0')
{
alertify.error('Please Fill Product Price');
return false;
}
else if(!p_qnty)
{
alertify.error('Please Fill Quantity');
return false;
}
else if(p_qnty=='0')
{
alertify.error('Please Fill Quantity');
return false;
}
else if(!delivery_type)
{
alertify.error('Please Select Delivery Type');
return false;
}

else
{
     $("#cr_btn1").hide();
     $("#cr_btn2").hide();
     $("#cr_img").show(); 
     return true;
}
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


function get_supplier_to_data(supplier_id)
{
    //alert(supplier_id);
   if((supplier_id!="") && (supplier_id!="0"))
    {
        $.ajax({
            url: 'index.php?route=purchaseorder/purchase_order/get_to_supplier_data&token=<?php echo $token; ?>&supplier_id=' +  encodeURIComponent(supplier_id),
            
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
                //$("#ship_to_creditlimit").html('');
                //$("#ship_to_currentcredit").html('');
                //$("#ship_to_currentoutstanding").html();
				
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





function get_ship_to_data(store_id)
{
    //alert(store_id);
   if((store_id!="") && (store_id!="0"))
    {
        $.ajax({
            url: 'index.php?route=purchaseorder/purchase_order/get_to_store_data&token=<?php echo $token; ?>&store_id=' +  encodeURIComponent(store_id),
            
            success: function(json) {
                var data=json.split('---');
              // alert(json);
                $("#ship_to_name").html(data[0]);
                $("#ship_to_address").html(data[1]);
                $("#ship_to_phone").html(data[2]);
                $("#ship_to_email").html(data[3]);
                $("#ship_to_pan").html(data[4]);
                $("#ship_to_gstn").html(data[5]);
                $("#ship_to_msmfid").html(data[6]);
                //$("#ship_to_currentcredit").html(data[7]);
                //$("#ship_to_currentoutstanding").html(data[8]);
				
                $("#store_owner_phone").val(data[2]);
                $("#store_owner_email").val(data[3]);
				$("#store_name").val(data[0]);
            }
        });
        $("#store_id").val(store_id);
    }
    else
    {
                $("#ship_to_name").html('');
                $("#ship_to_address").html('');
                $("#ship_to_phone").html('');
                $("#ship_to_email").html('');
                $("#ship_to_pan").html('');
                $("#ship_to_gstn").html('');
                $("#ship_to_msmfid").html('');
                //$("#ship_to_currentcredit").html('');
                //$("#ship_to_currentoutstanding").html();
				
                $("#store_owner_phone").val('');
                $("#store_owner_email").val('');
    }
    if(store_id!="")
    {
     $("#p_name_1").prop('disabled', false);
    }
    else
    {
        $("#p_name_1").prop('disabled', true);
    }
    //return false;
}
function get_to_store_data(store_id)
{
    if((store_id!="") && (store_id!="0")) 
    {
        $.ajax({
            url: 'index.php?route=purchaseorder/purchase_order/get_to_store_data&token=<?php echo $token; ?>&store_id=' +  encodeURIComponent(store_id),
            
            success: function(json) { 
                var data=json.split('---');
                //alert(json);
                $("#to_store_name").html(data[0]);
                $("#to_store_address").html(data[1]);
                $("#to_store_phone").html(data[2]);
                $("#to_store_email").html(data[3]);
                $("#to_store_pan").html(data[4]);
                $("#to_store_gstn").html(data[5]);
                if(store_id!="")
    {
     $("#p_name_1").prop('disabled', false);
    }
    else
    {
        $("#p_name_1").prop('disabled', true);
    }
            }
        }); 
    }
    else
    {
                $("#to_store_name").html('');
                $("#to_store_address").html('');
                $("#to_store_phone").html('');
                $("#to_store_email").html('');
                $("#to_store_pan").html('');
                $("#to_store_gstn").html('');
    }
}
    
function send_to_download(order_id)
{
    //alert(order_id);
    url = 'index.php?route=partner/purchase_order/download_invoice_b2b&token=<?php echo $token; ?>&order_id='+order_id;
    //location = url;
    window.open(url, '_blank');
}

///////////////
function remove_row(a)
{
    if(a==1)
    {
        alertify.error('You can not delete all products '); 
        return false;
    }
    else
    {
    var aa=parseFloat(a)-parseFloat(1) ;
    //alert(a);
    //alert(aa);
    alertify.confirm('Are you sure ! you want to delete this product ?',
                function(e){ 
                    if(e){
                     
                     var row_amount=$('#p_amount_'+a).val();
                     var stotal=$('#sub_total').val();
                     var gtotal=$('#grand_total').val();
        //alert(stotal+"-"+row_amount);
        if(row_amount=="")
        {
            row_amount=0;
        }
        var sub_total = parseFloat(stotal) - parseFloat(row_amount);
        sub_total=parseFloat(sub_total).toFixed(2);
        $("#tr_scgst_"+a).remove();
        $("#tr_"+a).remove();
        var total_tax=0;
        var total_tax_by_5=0;
        var total_tax_by_12=0;
        var total_tax_by_18=0;
        var grand_total=0;
        var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @2.5%")
         {
             total_tax_by_5=parseFloat(total_tax_by_5)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @6%")
         {
             total_tax_by_12=parseFloat(total_tax_by_12)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @9%")
         {
             total_tax_by_18=parseFloat(total_tax_by_18)+parseFloat(span_cgst);//
         }
         
        }
        total_tax_by_5=parseFloat(total_tax_by_5).toFixed(2);
        total_tax_by_12=parseFloat(total_tax_by_12).toFixed(2);
        total_tax_by_18=parseFloat(total_tax_by_18).toFixed(2);
        //alert(total_tax_by_5+','+total_tax_by_12+','+total_tax_by_18);
        
        $("#span_cgst_by_5").html(total_tax_by_5);
        $("#span_sgst_by_5").html(total_tax_by_5);
        $("#span_cgst_by_12").html(total_tax_by_12);
        $("#span_sgst_by_12").html(total_tax_by_12);
        $("#span_cgst_by_18").html(total_tax_by_18);
        $("#span_sgst_by_18").html(total_tax_by_18);
        
        if(total_tax_by_5>0)
        {
            $("#tr_scgst_by_5").show();
        }
        else
        {
            $("#tr_scgst_by_5").hide();  
        }
        if(total_tax_by_12>0)
        {
            $("#tr_scgst_by_12").show();
        }
        else
        {
            $("#tr_scgst_by_12").hide();  
        }
        if(total_tax_by_18>0)
        {
            $("#tr_scgst_by_18").show();
        }
        else
        {
            $("#tr_scgst_by_18").hide();  
        }
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        
        
        
        $('.add_btn').hide();
        $('.remove_btn').hide();
        $('#add_btn_'+aa).show();
        $('#remove_btn_'+aa).show();
                }else{
                    alertify.error('You canceled this action !'); 
                    return false;
                }
            }
                    
                        );
                 $("#alertify-ok").html('Continue');
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
function update_by_q(valuee,a)
{
	alert('up');
    if(valuee=="")
    { 
       $("#p_qnty_"+a).val('0');
    } 
    if(valuee=="")
    {
        valuee=0;
        //return false;
    }
	alert(11);
    //valuee=valuee;
    var tax_rate=parseFloat($("#p_tax_rate_"+a).val());
    var p_price=parseFloat($("#p_price_"+a).val());
    //alert(p_price+'+'+tax_rate);+tax_rate
    var total_amount=(p_price)*(valuee);
    total_amount=parseFloat(total_amount).toFixed(2);
    $('#p_amount_'+a).val(total_amount);
    $('#td_p_amount_'+a).html(total_amount);
    var tax_type=$('#td_p_tax_type_'+a).html();
	alert(12);
    var cgst="";
    var sgst="";
    if(tax_type.trim()=="GST@5%")
    {
       cgst="2.5";
       sgst="2.5";
       
    }
    else if(tax_type.trim()=="GST@12%")
    {
      cgst="6";
      sgst="6";  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       cgst="9";
       sgst="9";
    }
    else 
    {
       cgst="0";
       sgst="0";
    }
    var tax_cgst=(parseFloat(total_amount)*parseFloat(cgst))/parseFloat(100);
    var tax_sgst=(parseFloat(total_amount)*parseFloat(sgst))/parseFloat(100);
    tax_cgst=parseFloat(tax_cgst).toFixed(2);
    tax_sgst=parseFloat(tax_sgst).toFixed(2);
	alert(13);
    //alert(tax_type+","+cgst+"+"+sgst);
    $("#span_cgst_txt_"+a).html('CGST @'+cgst+'% ');
    $("#span_sgst_txt_"+a).html('SGST @'+sgst+'% ');
    $("#span_cgst_"+a).html(tax_cgst);
    $("#span_sgst_"+a).html(tax_sgst);
    alert(1);
    var sub_total=0;
    var count = parseFloat($('#print_table tr').length);//
    for(c=1;c<=count;c++)
    {
        
        var p_amount=$('#p_amount_<?php //echo $c; ?>'+c).val();
        if(p_amount!=undefined)
        {
        var sub_total = parseFloat(sub_total) + parseFloat(p_amount);
        }
        
    }
        sub_total=parseFloat(sub_total).toFixed(2);
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        alert(2);
        var total_tax=0;
        var total_tax_by_5=0;
        var total_tax_by_12=0;
        var total_tax_by_18=0;
        var grand_total=0;
        var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @2.5%")
         {
             total_tax_by_5=parseFloat(total_tax_by_5)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @6%")
         {
             total_tax_by_12=parseFloat(total_tax_by_12)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @9%")
         {
             total_tax_by_18=parseFloat(total_tax_by_18)+parseFloat(span_cgst);//
         }
         
        }
		alert(3);
        total_tax_by_5=parseFloat(total_tax_by_5).toFixed(2);
        total_tax_by_12=parseFloat(total_tax_by_12).toFixed(2);
        total_tax_by_18=parseFloat(total_tax_by_18).toFixed(2);
        //alert(total_tax_by_5+','+total_tax_by_12+','+total_tax_by_18);
        
        $("#span_cgst_by_5").html(total_tax_by_5);
        $("#span_sgst_by_5").html(total_tax_by_5);
        $("#span_cgst_by_12").html(total_tax_by_12);
        $("#span_sgst_by_12").html(total_tax_by_12);
        $("#span_cgst_by_18").html(total_tax_by_18);
        $("#span_sgst_by_18").html(total_tax_by_18);
        alert(4);
        if(total_tax_by_5>0)
        {
            $("#tr_scgst_by_5").show();
        }
        else
        {
            $("#tr_scgst_by_5").hide();  
        }
        if(total_tax_by_12>0)
        {
            $("#tr_scgst_by_12").show();
        }
        else
        {
            $("#tr_scgst_by_12").hide();  
        }
        if(total_tax_by_18>0)
        {
            $("#tr_scgst_by_18").show();
        }
        else
        {
            $("#tr_scgst_by_18").hide();  
        }
		alert(grand_total);
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        grand_total=parseFloat(grand_total).toFixed(2);
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        
        
        //$('#td_grand_total').html(grand_total);
        //$('#grand_total').val(grand_total);
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
function update_by_price(valuee,a)
{
    if(valuee=="")
    { 
       $("#p_price_"+a).val('0');
    } 
    if(valuee=="")
    {
        valuee=0;
    }
    valuee=parseFloat(valuee);
    var tax_rate=parseFloat($("#p_tax_rate_"+a).val());
    //+(tax_rate)
    var total_amount=((valuee))*($("#p_qnty_"+a).val());
    total_amount=parseFloat(total_amount).toFixed(2);
    $('#p_amount_'+a).val(total_amount);
    $('#td_p_amount_'+a).html(total_amount);
    
    var tax_type=$('#td_p_tax_type_'+a).html();
    var cgst="";
    var sgst="";
    if(tax_type.trim()=="GST@5%")
    {
       cgst="2.5";
       sgst="2.5";
       
    }
    else if(tax_type.trim()=="GST@12%")
    {
      cgst="6";
      sgst="6";  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       cgst="9";
       sgst="9";
    }
    else 
    {
       cgst="0";
       sgst="0";
    }
    var tax_cgst=(parseFloat(total_amount)*parseFloat(cgst))/parseFloat(100);
    var tax_sgst=(parseFloat(total_amount)*parseFloat(sgst))/parseFloat(100);
    tax_cgst=parseFloat(tax_cgst).toFixed(2);
    tax_sgst=parseFloat(tax_sgst).toFixed(2);
    //alert(cgst+"+"+sgst);
    $("#span_cgst_txt_"+a).html('CGST @'+cgst+'% ');
    $("#span_sgst_txt_"+a).html('SGST @'+sgst+'% ');
    $("#span_cgst_"+a).html(tax_cgst);
    $("#span_sgst_"+a).html(tax_sgst);
    
    var grand_total=0;
    var sub_total=0;
    var total_tax=0;
    var total_tax_by_5=0;
    var total_tax_by_12=0;
    var total_tax_by_18=0;
    var count = parseFloat($('#print_table tr').length);//
    for(c=1;c<=count;c++)
    {
        
        var p_amount= $('#p_amount_<?php //echo $c; ?>'+c).val()
        if(p_amount!=undefined)
        {
        var sub_total = parseFloat(sub_total) + parseFloat(p_amount);
        }
        
    }
    var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
        if($("#span_cgst_txt_"+c).html().trim()=="CGST @2.5%")
         {
             total_tax_by_5=parseFloat(total_tax_by_5)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @6%")
         {
             total_tax_by_12=parseFloat(total_tax_by_12)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @9%")
         {
             total_tax_by_18=parseFloat(total_tax_by_18)+parseFloat(span_cgst);//
         }
         
        }
        total_tax_by_5=parseFloat(total_tax_by_5).toFixed(2);
        total_tax_by_12=parseFloat(total_tax_by_12).toFixed(2);
        total_tax_by_18=parseFloat(total_tax_by_18).toFixed(2);
        //alert(total_tax_by_5+','+total_tax_by_12+','+total_tax_by_18);
        
        $("#span_cgst_by_5").html(total_tax_by_5);
        $("#span_sgst_by_5").html(total_tax_by_5);
        $("#span_cgst_by_12").html(total_tax_by_12);
        $("#span_sgst_by_12").html(total_tax_by_12);
        $("#span_cgst_by_18").html(total_tax_by_18);
        $("#span_sgst_by_18").html(total_tax_by_18);
        
        if(total_tax_by_5>0)
        {
            $("#tr_scgst_by_5").show();
        }
        else
        {
            $("#tr_scgst_by_5").hide();  
        }
        if(total_tax_by_12>0)
        {
            $("#tr_scgst_by_12").show();
        }
        else
        {
            $("#tr_scgst_by_12").hide();  
        }
        if(total_tax_by_18>0)
        {
            $("#tr_scgst_by_18").show();
        }
        else
        {
            $("#tr_scgst_by_18").hide();  
        }
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        
        grand_total=parseFloat(grand_total).toFixed(2);
        sub_total=parseFloat(sub_total).toFixed(2);
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        return false;
}
</script>

<script type="text/javascript">
   $('input[name=\'contactname\']').autocomplete({
     //$('#p_name_<?php echo $b; ?>').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=purchaseorder/purchase_order/user_autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) { 
                console.log(JSON.stringify(json));
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['mobile_number'],
                        
                        
                    }
                }));
            }
        });
    },
    'select': function(item) {
        
        
        $('#contactname').val(item['label']);
        $('#contactmobile').val(item['value']);
        
      
        
        
    }
});
</script>
 <script type="text/javascript">
   $('input[name=\'product_name[]\']').autocomplete({
     //$('#p_name_<?php echo $b; ?>').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=purchaseorder/purchase_order/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) { 
                console.log(JSON.stringify(json));
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
        $('#p_id_'+thisid).val(item['value']);
        $('#td_p_tax_type_'+thisid).html(item['product_tax_type']);
        $('#td_p_hsn_'+thisid).html(item['hstn']);
        $('#p_hsn_'+thisid).val(item['hstn']);
        $('#p_price_'+thisid).val(item['price_wo_t']);
      
        var total_amount=(parseFloat(item['price_wo_t']))*($("#p_qnty_"+thisid).val());
        total_amount=parseFloat(total_amount).toFixed(2);
        $('#p_amount_'+thisid).val(total_amount);
        $('#td_p_amount_'+thisid).html(total_amount);
        $('#p_tax_rate_'+thisid).val(item['product_tax_rate']);
        $('#p_tax_type_'+thisid).val(item['product_tax_type']);
    var tax_type='';  
    var tax_type=item['product_tax_type'];
	//alert(tax_type);
    var cgst="";
    var sgst="";
    if(tax_type.trim()=="GST@5%")
    {
       cgst="2.5";
       sgst="2.5";
       
    }
    else if(tax_type.trim()=="GST@12%")
    {
      cgst="6";
      sgst="6";  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       cgst="9";
       sgst="9";
    }
    else 
    {
       cgst="0";
       sgst="0";
    }
    var tax_cgst=(parseFloat(total_amount)*parseFloat(cgst))/parseFloat(100);
    var tax_sgst=(parseFloat(total_amount)*parseFloat(sgst))/parseFloat(100);
    tax_cgst=parseFloat(tax_cgst).toFixed(2);
    tax_sgst=parseFloat(tax_sgst).toFixed(2);
    //alert(cgst+"+"+sgst);
    $("#span_cgst_txt_"+thisid).html('CGST @'+cgst+'% ');
    $("#span_sgst_txt_"+thisid).html('SGST @'+sgst+'% ');
    $("#span_cgst_"+thisid).html(tax_cgst);
    $("#span_sgst_"+thisid).html(tax_sgst);
        
        
        var grand_total=0;
        var sub_total=0;
        var total_tax=0;
        var total_tax_by_5=0;
        var total_tax_by_12=0;
        var total_tax_by_18=0;
        var count = parseFloat($('#print_table tr').length);//
        for(c=1;c<=count;c++)
        {
        
        var p_amount=$('#p_amount_'+c).val();
        if(p_amount!=undefined)
        {
        
        var sub_total = parseFloat(sub_total) + parseFloat(p_amount);
        }
        
        }
        var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @2.5%")
         {
             total_tax_by_5=parseFloat(total_tax_by_5)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @6%")
         {
             total_tax_by_12=parseFloat(total_tax_by_12)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @9%")
         {
             total_tax_by_18=parseFloat(total_tax_by_18)+parseFloat(span_cgst);//
         }
         
        }
        total_tax_by_5=parseFloat(total_tax_by_5).toFixed(2);
        total_tax_by_12=parseFloat(total_tax_by_12).toFixed(2);
        total_tax_by_18=parseFloat(total_tax_by_18).toFixed(2);
        //alert(total_tax_by_5+','+total_tax_by_12+','+total_tax_by_18);
        
        $("#span_cgst_by_5").html(total_tax_by_5);
        $("#span_sgst_by_5").html(total_tax_by_5);
        $("#span_cgst_by_12").html(total_tax_by_12);
        $("#span_sgst_by_12").html(total_tax_by_12);
        $("#span_cgst_by_18").html(total_tax_by_18);
        $("#span_sgst_by_18").html(total_tax_by_18);
        
        if(total_tax_by_5>0)
        {
            $("#tr_scgst_by_5").show();
        }
        else
        {
            $("#tr_scgst_by_5").hide();  
        }
        if(total_tax_by_12>0)
        {
            $("#tr_scgst_by_12").show();
        }
        else
        {
            $("#tr_scgst_by_12").hide();  
        }
        if(total_tax_by_18>0)
        {
            $("#tr_scgst_by_18").show();
        }
        else
        {
            $("#tr_scgst_by_18").hide();  
        }
        
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        sub_total=parseFloat(sub_total).toFixed(2);
        grand_total=parseFloat(grand_total).toFixed(2);
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        $.ajax({
            url: 'index.php?route=partner/purchase_order/get_current_inventory&token=<?php echo $token; ?>&filter_id=' +  encodeURIComponent(item['value'])+'&filter_ware_house='+($("#ware_house").val()),
            dataType: 'json',
            success: function(json) { 
                //alert(json);
                if(json=="")
                {
                    json=0;
                }
                $("#td_p_inventory_"+thisid).html(json);
            }
        });
        
    }
});


function setautoserarch(id)
{
       $('#'+id).autocomplete({
     //$('#p_name_<?php echo $b; ?>').autocomplete({
    
     //$('#p_name_<?php echo $b; ?>').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=partner/purchase_order/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) { 
                console.log(JSON.stringify(json));
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
        $('#p_id_'+thisid).val(item['value']);
        $('#td_p_tax_type_'+thisid).html(item['product_tax_type']);
        $('#td_p_hsn_'+thisid).html(item['hstn']);
        $('#p_hsn_'+thisid).val(item['hstn']);
        $('#p_price_'+thisid).val(item['price_wo_t']);
      
        var total_amount=(parseFloat(item['price_wo_t']))*($("#p_qnty_"+thisid).val());
        total_amount=parseFloat(total_amount).toFixed(2);
        $('#p_amount_'+thisid).val(total_amount);
        $('#td_p_amount_'+thisid).html(total_amount);
        $('#p_tax_rate_'+thisid).val(item['product_tax_rate']);
        $('#p_tax_type_'+thisid).val(item['product_tax_type']);
        
    var tax_type=$('#td_p_tax_type_'+thisid).html();
    var cgst="";
    var sgst="";
    if(tax_type.trim()=="GST@5%")
    {
       cgst="2.5";
       sgst="2.5";
       
    }
    else if(tax_type.trim()=="GST@12%")
    {
      cgst="6";
      sgst="6";  
    }
    else if(tax_type.trim()=="GST@18%")
    {
       cgst="9";
       sgst="9";
    }
	else if(tax_type.trim()=="GST@28%")
    {
       cgst="14";
       sgst="14";
    }
    else 
    {
       cgst="0";
       sgst="0";
    }
    var tax_cgst=(parseFloat(total_amount)*parseFloat(cgst))/parseFloat(100);
    var tax_sgst=(parseFloat(total_amount)*parseFloat(sgst))/parseFloat(100);
    tax_cgst=parseFloat(tax_cgst).toFixed(2);
    tax_sgst=parseFloat(tax_sgst).toFixed(2);
    //alert(cgst+"+"+sgst);
    $("#span_cgst_txt_"+thisid).html('CGST @'+cgst+'% ');
    $("#span_sgst_txt_"+thisid).html('SGST @'+sgst+'% ');
    $("#span_cgst_"+thisid).html(tax_cgst);
    $("#span_sgst_"+thisid).html(tax_sgst);
        
        
        var grand_total=0;
        var sub_total=0;
        var total_tax=0;
        var total_tax_by_5=0;
        var total_tax_by_12=0;
        var total_tax_by_18=0;
		var total_tax_by_28=0;
        var count = parseFloat($('#print_table tr').length);//
        for(c=1;c<=count;c++)
        {
        
        var p_amount=$('#p_amount_'+c).val();
        if(p_amount!=undefined)
        {
        
        var sub_total = parseFloat(sub_total) + parseFloat(p_amount);
        }
        
        }
        var count = parseFloat($('#print_table_2 tr').length)-parseFloat(1);//
        //alert(count);
        for(c=1;c<=count;c++)
        {
         var span_cgst=$("#span_cgst_"+c).html();
         var span_sgst=$("#span_cgst_"+c).html();
         if((span_cgst!="") && (span_sgst!=""))
         {
         total_tax=parseFloat(total_tax)+parseFloat(span_cgst)+parseFloat(span_sgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @2.5%")
         {
             total_tax_by_5=parseFloat(total_tax_by_5)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @6%")
         {
             total_tax_by_12=parseFloat(total_tax_by_12)+parseFloat(span_cgst);//
         }
         if($("#span_cgst_txt_"+c).html().trim()=="CGST @9%")
         {
             total_tax_by_18=parseFloat(total_tax_by_18)+parseFloat(span_cgst);//
         }
		 if($("#span_cgst_txt_"+c).html().trim()=="CGST @14%")
         {
             total_tax_by_28=parseFloat(total_tax_by_28)+parseFloat(span_cgst);//
         }
         
        }
        total_tax_by_5=parseFloat(total_tax_by_5).toFixed(2);
        total_tax_by_12=parseFloat(total_tax_by_12).toFixed(2);
        total_tax_by_18=parseFloat(total_tax_by_18).toFixed(2);
		total_tax_by_28=parseFloat(total_tax_by_28).toFixed(2);
        //alert(total_tax_by_5+','+total_tax_by_12+','+total_tax_by_18);
        
        $("#span_cgst_by_5").html(total_tax_by_5);
        $("#span_sgst_by_5").html(total_tax_by_5);
        $("#span_cgst_by_12").html(total_tax_by_12);
        $("#span_sgst_by_12").html(total_tax_by_12);
        $("#span_cgst_by_18").html(total_tax_by_18);
        $("#span_sgst_by_18").html(total_tax_by_18);
        
		$("#span_cgst_by_28").html(total_tax_by_28);
        $("#span_sgst_by_28").html(total_tax_by_28);
        if(total_tax_by_5>0)
        {
            $("#tr_scgst_by_5").show();
        }
        else
        {
            $("#tr_scgst_by_5").hide();  
        }
        if(total_tax_by_12>0)
        {
            $("#tr_scgst_by_12").show();
        }
        else
        {
            $("#tr_scgst_by_12").hide();  
        }
        if(total_tax_by_18>0)
        {
            $("#tr_scgst_by_18").show();
        }
        else
        {
            $("#tr_scgst_by_18").hide();  
        }
		if(total_tax_by_28>0)
        {
            $("#tr_scgst_by_28").show();
        }
        else
        {
            $("#tr_scgst_by_28").hide();  
        }
        
        grand_total=parseFloat(total_tax)+parseFloat(sub_total);//
        sub_total=parseFloat(sub_total).toFixed(2);
        grand_total=parseFloat(grand_total).toFixed(2);
        $('#td_sub_total').html(sub_total);
        $('#sub_total').val(sub_total);
        
        $('#td_grand_total').html(grand_total);
        $('#grand_total').val(grand_total);
        $.ajax({
            url: 'index.php?route=partner/purchase_order/get_current_inventory&token=<?php echo $token; ?>&filter_id=' +  encodeURIComponent(item['value'])+'&filter_ware_house='+($("#ware_house").val()),
            dataType: 'json',
            success: function(json) { 
                //alert(json);
                if(json=="")
                {
                    json=0;
                }
                $("#td_p_inventory_"+thisid).html(json);
            }
        });
        
    }
});
    
}
</script>


<script type="text/javascript">
	function print_order()
	{
		var prtContent = document.getElementById("print_div");
		var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
		
		WinPrint.document.writeln('<!DOCTYPE html>');
                WinPrint.document.writeln('<html><head><title></title>');
                WinPrint.document.writeln('<style>table{border:1px; border-collapse:collapse;}');
                WinPrint.document.writeln('table, td, th {border: 1px solid black;}');
		WinPrint.document.writeln('label{font-weight:bold}');
		WinPrint.document.writeln('.text-right{text-align:right;}');
		WinPrint.document.writeln('.remaining_quantity{display:none;}');
		document.getElementById('set_colspan').setAttribute('colspan','7');
		WinPrint.document.writeln('</style></head><body>');
		WinPrint.document.write(prtContent.innerHTML);
		WinPrint.document.writeln('</body></html>');
		WinPrint.document.close();
		WinPrint.focus();
		WinPrint.print();
		WinPrint.close();
	}
	
	function download_pdf()
	{
		var doc = new jsPDF();
		doc.fromHTML($('#print_div').get(0),20,20,{
			'width':5000
		});
		doc.save('test.pdf');
	}
	
	function print_order()
	{
		document.getElementById("download_pdf").style.display = "none";
                var printContents = document.getElementById('print_div').innerHTML;
                var originalContents = document.body.innerHTML;
		document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
		document.getElementById("download_pdf").style.display = "block";
    }
	
	
	function demoFromHTML() 
        {
            var pdf = new jsPDF('l', 'pt', 'letter',true);
            //pdf.setFontSize(8);
            source = $('#print_div')[0];
            specialElementHandlers = {
            '#bypassme': function (element, renderer) {
            return true
        }
    };
    margins = {
        top: 100,
        bottom: 80,
        left: 80,
        width:1000
    };
    pdf.fromHTML(
    source,
    margins.left,
    margins.top, {
        'width': margins.width,
        'elementHandlers': specialElementHandlers
    },

    function (dispose) {
        pdf.save('Test.pdf');
    }
	);
}
</script>
<?php echo $footer; ?>