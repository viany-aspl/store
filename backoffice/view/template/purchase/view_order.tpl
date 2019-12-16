<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><!--<button onclick ="print_order()" data-toggle="tooltip" title="<?php echo "Print Order"; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>--><!--<a href="<?php echo $shipping; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_shipping_print; ?>" class="btn btn-info"><i class="fa fa-truck"></i></a> <a href="<?php echo $edit; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>--> <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo "Cancel Button"; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo "Requisition"; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
      
      <?php if (isset($error_warning)) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

	<?php if (isset($success)) {		?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success;?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    </div>
  </div>
  <div class="panel panel-default" id = "print_div">
	<div class="panel-heading">
		<h3 class="panel-title">
			<i class="fa fa-info-circle"></i>
			<?php echo "Requisition # " . $order_information['id']; ?>
                        
		</h3>
                <h3 class="panel-title pull-right">
			<i class="fa fa-info-circle"></i>
			<?php //echo "Order Status : " . $order_information['order_info']['order_status']; ?>
                        
		</h3>
	</div>
	<div class="panel-body">
                <div class="row">
			<div class="col-lg-3">
				<label>Store Name:</label>
			</div>
			<div class="col-lg-9">
				<?php echo $store_name; ?>
			</div>
                    
		</div>
		<div class="row">
			<div class="col-lg-3">
				<label>Ordered By:</label>
			</div>
			<div class="col-lg-9">
				<?php echo $orderby; ?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-3">
			<label>
				Purchase order date:
			</label>
			</div>
			<div class="col-lg-9">
				<?php echo date('Y-m-d',($order_information['order_date']->sec)); ?>
			</div>
		</div>
		<?php if($order_information['receive_date'] != ''){?>
		<div class="row">
			<div class="col-lg-3">
				<label>Received On:</label>
			</div>
			<div class="col-lg-9">
				<?php echo date('Y-m-d',($order_information['receive_date']->sec)); ?>
			</div>
		</div>
		<?php } ?>
            <form style="min-height: 320px;" action="<?php echo $submit_action; ?>" method="post" enctype="multipart/form-data" id="form-order-receive" class="form-horizontal">
                <table class="table table-bordered" id="print_table" border="1" >
          <thead>
            <tr>
                <td class="text-left" style="width: 11.11%;">Product Name</td>
                <td class="text-left" style="width: 11.11%;">Demand</td>
		<td class="text-left" style="width: 11.11%;">Total Received Quantity</td>
		<td class="text-left remaining_quantity" style="width: 11.11%;">Remaining Quantity</td>
                <td class="text-left" style="width: 20.11%;">Received Date</td>
		<td class="text-left" style="width: 11.11%;">Supplier</td>
		<td class="text-left" style="width: 11.11%;">Quantity from Supplier</td>
                
            </tr>
          </thead>
          <tbody>
		  <?php
                    $grand_total = 0;
		    $start_loop = 0;
                    $product=($order_information['po_product']);
		
		  ?>
            <tr>
                <td class="text-left"><?php echo  $product['name'];?></td>
		<td class="text-left"><?php echo  $product['quantity'];?></td>
		<td class="text-left">
                    <?php if($order_information['po_receive_details']['quantity'] == 0) 
                    {
                        echo ''; 
                    }
                    else 
                    {
                        echo $order_information['po_receive_details']['quantity'];
                    }
                    ?>
                </td>
		<td class="text-left remaining_quantity">
                    <?php 
                        echo $product['quantity'] - $order_information['po_receive_details']['quantity']; 
                    
                    ?>
                </td>
                <td>
                    <?php 
                    if($order_information['po_receive_details']['supplier_quantity'] == 0) 
                    {
                      ?>
                    <div class="input-group date">
                        <input onkeypress="return false" readonly="readonly" type="text" name="from" value="" placeholder="Recive Date" data-date-format="YYYY-MM-DD" id="input-order_receive_date" class="form-control" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                            </span>
                    </div>
                    <?php  
                    } 
                    else 
                    { 
                        echo date('Y-m-d',($order_information['order_sup_send']->sec));
                    } 
                    ?>
                </td>
		<td class="text-left">
                    <?php if(!empty($ware_house_name))
                        { 
			  echo $ware_house_name; 
                        } 
                        else 
                        {
                        ?>
                        <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id; ?>" />
                        <input type="hidden" name="order_by_user_id" id="order_by_user_id" value="<?php echo $order_by_user_id; ?>" />
                        <input type="hidden" name="demand_quantity" id="demand_quantity" value="<?php echo  $product['quantity'];?>" />
                        <select class="form-control" id="supplier" name="supplier">
                                    <option value="">SELECT SUPPLIER</option>
                                    <?php foreach($suppliers as $supplier)
                                        { ?>
                                            <option value="<?php echo $supplier['pre_mongified_id']; ?>" ><?php echo $supplier['first_name']." ".$supplier['last_name']; ?></option>
				<?php } ?>
			</select>
                        <?php
                        }
                        ?>
                        </td>
                        <td class="text-left">
                            <?php 
                                if($order_information['po_receive_details']['supplier_quantity'] == 0) 
                                {
                                ?>
                                    <input type="text" onkeypress="return isNumber(event)" class="form-control" name="supplier_quantity" id="supplier_quantity" placeholder="Supplier Quantity" />
                                <?php
                                }
                                else 
                                {
                                    echo $order_information['po_receive_details']['supplier_quantity'];
                                }
                                         
                                    
                                 
                            ?>
                        </td>
                    </tr>
		</tbody>
        </table>
           <?php //print_r($order_information['canceled_message']);
                if($order_information['po_receive_details']['supplier_quantity'] == 0) 
                {
                    if($order_information['canceled_message']=='')
                    { 
            ?>
            <a onclick="return confirm_order(<?php echo $order_id; ?>);"  data-toggle="tooltip" title="<?php echo "Approve Order"; ?>" style="margin-left: 5px;" class="btn btn-primary"><i class="fa fa-truck"></i></a>
            <a onclick="return cancel_order(<?php echo $order_id; ?>);"  data-toggle="tooltip" title="<?php echo "Cancel Order"; ?>" style="margin-left: 5px;" class="btn btn-danger"><i class="fa fa-close"></i></a>
                <?php } } ?>
            </form>                  
                    
                          
	</div>
	
  </div>
  
</div>
<!-- Modal -->
  <div class="modal fade" id="myModal_partner" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" id="partner_cncl_btn2" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Tell us the reason, why you canceled this order ? </h4>
        </div>
        <div class="modal-body">
        <form action="index.php?route=tag/bills/reject&token=<?php echo $token; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" id="user_id" value="<?php echo $user_id; ?>" />
            <div class="form-group">
            <label for="input-username">Reason </label>
            <div class="input-group"><span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <textarea name="reject_Message" required id="reject_Message" class="form-control" placeholder="Reason of reject"></textarea>
            </div>
            </div>
           <div class="text-right">
               <img id="processing_image" src="https://unnati.world/shop/admin/view/image/processing_image.gif" style="height: 50px;width: 50px;display: none;">
               <input type="button" id="submit_btn"  class="btn btn-primary" 
                   
                      onclick="return cancel_order_by_account(<?php echo $order_id; ?>);" 
                   
                      value="Submit" />
                <button type="button" id="submt_cncl_btn" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </form>
        </div>
        <!--<div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>-->
      </div>
      
    </div>
  </div>


<script>
    
function isNumber(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
       function isNumberp(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
    $('.date').datetimepicker({
		pickTime: false
	});
    $("#supplier").select2();
////////////////////////////////////////////////
function confirm_order(order_id)
{
    var supplier = $("#supplier").val();
    var demand_quantity =$("#demand_quantity").val();
    var order_by_user_id =$("#order_by_user_id").val();
    var order_receive_date =$("#input-order_receive_date").val();
    if(!order_receive_date)
    {
        alertify.error('Please Select Recive Date');
        $("#input-order_receive_date").focus();
        return false;
    }
    if(!supplier)
    {
        alertify.error('Please Select Supplier');
        $("#supplier").focus();
        return false;
    }
    var supplier_quantity = $("#supplier_quantity").val();
    if(!supplier_quantity)
    {
        alertify.error('Please Enter Quantity');
        $("#supplier_quantity").focus();
        return false;
    }
    if(parseInt(demand_quantity)<parseInt(supplier_quantity))
    {
        alertify.error('Entered Quantity is greater then demand Quantity');
        $("#supplier_quantity").focus();
        return false;
    }
    if(parseInt(demand_quantity)>parseInt(supplier_quantity))
    {
        alertify.error('Entered Quantity is less then demand Quantity');
        $("#supplier_quantity").focus();
        return false;
    }
    alertify.confirm('Are you sure ! you want to approve this order ?',
                function(e)
                { 
                    if(e)
                    {
        url = 'index.php?route=purchase/purchase_order/receive_order&token=<?php echo $token; ?>';
		
        url += '&order_id=' + encodeURIComponent(order_id);
        
        url += '&supplier_id=' + encodeURIComponent(supplier); 
        
        url += '&supplier_quantity=' + encodeURIComponent(supplier_quantity);
        url += '&order_by_user_id=' + encodeURIComponent(order_by_user_id);
        url += '&order_receive_date=' + encodeURIComponent(order_receive_date);
	location = url;
        }
        else
        {
            alertify.error('You canceled this action !'); 
            return false;
        }
    });
    $("#alertify-ok").html('Continue'); 
}
function cancel_order(order_id)
{
    
    alertify.confirm('Are you sure ! you want to cancel this order ?',
                function(e){ 
                    if(e){
                        $('#myModal_partner').modal('show');
                        return false;
                }else{
                    alertify.error('You canceled this action !'); 
                    return false;
                }
            }
                    
                        );
                 $("#alertify-ok").html('Continue'); 
                 //return false;
}

function cancel_order_by_account(order_id)
{
    var order_by_user_id =$("#order_by_user_id").val();
    var reject_Message =$("#reject_Message").val();
    if(!reject_Message)
    {
        alertify.error('Please enter the reason for cancel this order');
        $("#reject_Message").focus();
        return false;
    }
    alertify.confirm('Are you sure ! you want to Cancel this order ?',
                function(e){ 
                    if(e){
        url = 'index.php?route=purchase/purchase_order/cancel_order&token=<?php echo $token; ?>';
		
        url += '&order_id=' + encodeURIComponent(order_id);
        
        url += '&order_by_user_id=' + encodeURIComponent(order_by_user_id);
        url += '&reject_Message=' + encodeURIComponent(reject_Message);
        
	location = url;
                }else{
                    alertify.error('You canceled this action !'); 
                    return false;
                }
            }
                    
                        );
                 $("#alertify-ok").html('Continue'); 
    
                 //return false;
}


</script>


<?php echo $footer; ?>