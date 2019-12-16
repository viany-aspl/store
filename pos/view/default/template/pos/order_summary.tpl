<?php echo $header; ?><?php echo $column_left;
$RUPPE_SIGN='&#x20b9;';
 ?>
 <style type="text/css" media="print">
  @page { size: portrait; }
 
</style>
 <link href="../catalog/view/theme/default/template/pos/css/bootstrap.min.css" rel="stylesheet">
	<link href="../catalog/view/theme/default/template/pos/css/style.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet"> 
	<!--<script src="view/default/template/pos/pos/js/printer.js"></script>-->
     <div class="container">
		<div class="inner_space">
		
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-6">
					<div class="logo">
						<img src="../catalog/view/theme/default/template/pos/images/logo.png">
					</div>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6">
					<div class="invoice_from">
						<p><strong>Invoice From</strong></p>
						<p><?php echo $store_name; ?></p>
						<p><strong><?php echo $store_address; ?></strong></p>
						<p><strong><?php 
						//$store_telephone[0]='#';
						//$store_telephone[1]='#';
						//$store_telephone[2]='#';
						//$store_telephone[3]='#';
						//$store_telephone[4]='#';
						//$store_telephone[5]='#';
						echo $store_telephone; ?></strong></p>	
					</div>
				</div>
				
			</div>
			<?php //print_r($order_data); ?>
			<style type="text/css" >
 
  .content
  {
	  padding: 51px 30px 0 !important;
  }
</style>
			<div class="invoice_box">
				<h2 class="dark_blue">INVOICE</h2>
			</div>
				<div class="row">
					
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="invoice_to">
							<p><strong>Invoice to</strong></p>
							<p><?php echo $firstname." ".$lastname; ?></p>
							
							<p><strong><?php 
							//$telephone[0]='#';
							//$telephone[1]='#';
							//$telephone[2]='#';
							//$telephone[3]='#';
							//$telephone[4]='#';
							//$telephone[5]='#'; 
							echo $telephone; ?></strong></p>
						</div>
					</div>
					
					<div class="col-xs-6 col-sm-6 col-md-6">
						<div class="invoice_from">
							<p><strong>Invoice# :</strong> &nbsp; <?php echo ($order_info['invoice_prefix'].$order_info['invoice_no']); ?></p>
							<p><strong>Date :</strong> &nbsp;<?php echo (date('d M, Y',$order_info['date_added']->sec)); ?></p>
							<p><strong>Order ID :</strong> &nbsp;<?php echo ($order_info['order_id']); ?></p>
							<p><strong>Total :</strong> &nbsp;<?php echo $RUPPE_SIGN;  echo number_format($order_info['total'],2); ?></p>
						</div>
					</div>
					
				</div>	
			
		<h2>Item Details</h2>
		<div class="product_table">	
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
								  <th class="text-center">Product</th>
							 
								  <th class="text-center">Quantity</th>
								  <th class="text-center">Unit Price</th>
								  <th class="text-center">Total</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($products as $product) { ?>
								<tr>
								  <td class="text-center"><?php echo $product['name']; ?></td>
							
								  <td class="text-center"><?php echo $product['quantity']; ?></td>
								  <td class="text-center"><?php echo str_replace('Rs.',$RUPPE_SIGN,$product['price']); ?></td>
								  <td class="text-center"><?php echo str_replace('Rs.',$RUPPE_SIGN,$product['total']); ?></td>
								</tr>
							<?php } ?>		
							<?php foreach ($totals as $totals) { ?>
								<tr>
								  <td colspan="3" class="text-right"><?php echo $totals['title']; ?>:</td>
								  <td class="text-center"><?php echo str_replace('Rs.',$RUPPE_SIGN,$totals['text']); ?></td>
								</tr>
							<?php } ?>
							<?php if(!empty($order_info['discount'])){ ?>
								<tr>
								  <td colspan="3" class="text-right">Discount:</td>
								  <td class="text-center"><?php echo $RUPPE_SIGN;  echo number_format($order_info['discount'],2); ?></td>
								</tr>
							<?php } ?>
							<?php if(!empty($order_info['cash'])){ ?>
								<tr>
								  <td colspan="3" class="text-right">Cash:</td>
								  <td class="text-center"><?php echo $RUPPE_SIGN;  echo number_format($order_info['cash'],2); ?></td>
								</tr>
							<?php } ?>
							<?php if(!empty($order_info['credit'])){ ?>
								<tr>
								  <td colspan="3" class="text-right">Credit:</td>
								  <td class="text-center"><?php echo $RUPPE_SIGN;  echo number_format($order_info['credit'],2); ?></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		
		<h2>Tax Description</h2>
		<div class="product_table">
			
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
                <tr>
                  <th class="text-left" style="font-weight: bold;">Tax Title</th>
             
                  <th class="text-right" style="font-weight: bold;">Value</th>
                  
                </tr>
              </thead>
              <tbody>
				<?php if(!empty($tax_array)) { foreach ($tax_array as $taxd) { ?>
              <tr>
                  <td class="text-left"><?php echo $taxd['title']; ?></td>
            
                  <td class="text-right"><?php echo str_replace('Rs.',$RUPPE_SIGN,$taxd['value']); ?></td>
                 
                </tr>
              <?php } }else
				{
					?>
					<tr>
                  <td class="text-left">NO-TAX</td>
            
                  <td class="text-right"><?php echo $RUPPE_SIGN;echo '0.00'; ?></td>
                 
                </tr>
				<?php } ?>
                              </tbody>
         
						</table>
						
					</div>
					<p>THANK YOU FOR YOUR BUSINESS</p>
				</div>
			</div>
			
		</div>
		
		<footer>

			<div class="invoice_footer">

				<div class="row">

				<div class="col-xs-6 col-sm-6 col-md-6">

					<div class="footer_right">

						<span><img src="../catalog/view/theme/default/template/pos/images/smartphone.png" class="icon" ></span>

						<a class="text_block" href="tel:0120 4040160">0120 4040180</a>

					</div>

				</div>

				<div class="col-xs-6 col-sm-6 col-md-6">

					<div class="footer_left">

						<span><img src="../catalog/view/theme/default/template/pos/images/web.png" class="icon" ></span>

						<a class="text_block" href="https://unnatiagro.in/">unnatiagro.in</a>

					</div>

				</div>

			</div>

			</div>

		</footer>
		<button class="btn btn-danger btn--action btn--fixed " data-ma-action="" onclick="return select_print();"><i class="zmdi zmdi-print"></i></button>
		
		
	</div>
	 <div class="modal" id="modal-backdrop-ignore" data-backdrop="static" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title pull-left">Select Printer</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
													<select style="height: 34px;" onchange="change_printer(this.value);" class="form-control" name='printer_type' id='printer_type'>
														<option value=''>Select Printer</option>
														<option value='web'>Web</option>
														<option value='pos'>POS</option>
													</select>
                                            </div>
                                            
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" id='final_print' class="btn btn-link" data-ma-action="" >Print</button>
                                            <button type="button" onclick="return close_model();" class="btn btn-link" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
								
                            </div>
	<footer class="text-center m_20">
        <p>© Agri POS. All rights reserved.</p>
	</footer>
        <script src="catalog/view/theme/default/template/pos/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="catalog/view/theme/default/template/pos/js/bootstrap.min.js"></script>   
	
</div>
<?php echo $footer; ?>
<script>
var socket = null;
function change_printer(printer_type)
{
	if(printer_type=='web')
	{
		$("#final_print").attr("data-ma-action","print");
	}
	else if(printer_type=='pos')
	{
		$("#final_print").removeAttr("data-ma-action");
		pos_print(<?php echo $order_data; ?>);
	}
	else
	{
		$("#final_print").removeAttr("data-ma-action");
	}
}

function close_model()
{
    $('.modal').hide();
}
function select_print()
{
	$('.modal').show();
	return false;
}

$(document).ready( function(){

var socket_host = 'wss://127.0.0.1:6441'


    try {
		
        if(socket == null){ 
            socket = new WebSocket(socket_host);
			//alert('here');
            socket.onopen = function () {
                
            };
            socket.onmessage = function (msg) {
                
            };
            socket.onclose = function () {
                socket = null;
            };
			
        }
    } catch (e) {
        console.log(e);
    }



});

function pos_print(receipt)
{
	

//If printer type then connect with websocket
var content = receipt;
content.type = 'print-receipt';
alert('here2');
//Check if ready or not, then print.
if(socket.readyState != 1){

setTimeout(function() {
socket.send(JSON.stringify(content));
}, 700);
} else {
socket.send(JSON.stringify(content));
}


}
</script>
