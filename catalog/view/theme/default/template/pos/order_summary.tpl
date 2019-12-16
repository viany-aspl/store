<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>invoice</title>

    <!-- Bootstrap -->
    <link href="catalog/view/theme/default/template/pos/css/bootstrap.min.css" rel="stylesheet">
	<link href="catalog/view/theme/default/template/pos/css/style.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet"> 

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<meta id="description" name="description" "Thanks for shopping at <?php echo $store_name; ?>, digitally powered by Unnati AgriPOS." >
		
		<meta property="og:title" content="AgriPOS Invoice" />
		<meta property="og:url" content="https://unnatiagro.in/stores/index.php?route=mpos/openretailer/invoice&order_id=<?php echo $_GET['order_id']; ?>" />
		<meta property="og:description" content="Thanks for shopping at <?php echo $store_name; ?>, digitally powered by Unnati AgriPOS.">
		
		<meta property="og:image" content="https://unnatiagro.in/images/agri-logo_300x200.jpg">
		<meta property="og:type" content="website" />
		<meta property="og:locale" content="en_GB" />
		<meta property="og:locale:alternate" content="fr_FR" />
		<meta property="og:locale:alternate" content="es_ES" />
  </head>
  <body>
    
	<div class="container">
		<div class="inner_space">
		
			<div class="row">
				<div class="col-xs-6 col-sm-6 col-md-6">
					<div class="logo">
						<img src="catalog/view/theme/default/template/pos/images/logo.png">
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
						<?php if(!empty($store_gstn)){ ?>
						<p><strong>GSTN :</strong> <?php echo $store_gstn; ?></p>
						<?php } ?>
					</div>
				</div>
				
			</div>
			
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
							<p><strong>Total :</strong> &nbsp;<?php echo RUPPE_SIGN;  echo number_format($order_info['total'],2); ?></p>
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
								  <td class="text-center"><?php echo str_replace('Rs.',RUPPE_SIGN,$product['price']); ?></td>
								  <td class="text-center"><?php echo str_replace('Rs.',RUPPE_SIGN,$product['total']); ?></td>
								</tr>
							<?php } ?>		
							<?php foreach ($totals as $totals) { ?>
								<tr>
								  <td colspan="3" class="text-right"><?php echo $totals['title']; ?>:</td>
								  <td class="text-center"><?php echo str_replace('Rs.',RUPPE_SIGN,$totals['text']); ?></td>
								</tr>
							<?php } ?>
							<?php if(!empty($order_info['discount'])){ ?>
								<!--<tr>
								  <td colspan="3" class="text-right">Discount:</td>
								  <td class="text-center"><?php echo RUPPE_SIGN;  echo number_format($order_info['discount'],2); ?></td>
								</tr>-->
							<?php } ?>
							<?php if(!empty($order_info['cash'])){ ?>
								<tr>
								  <td colspan="3" class="text-right">Cash:</td>
								  <td class="text-center"><?php echo RUPPE_SIGN;  echo number_format($order_info['cash'],2); ?></td>
								</tr>
							<?php } ?>
							<?php if(!empty($order_info['credit'])){ ?>
								<tr>
								  <td colspan="3" class="text-right">Credit:</td>
								  <td class="text-center"><?php echo RUPPE_SIGN;  echo number_format($order_info['credit'],2); ?></td>
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
            
                  <td class="text-right"><?php echo str_replace('Rs.',RUPPE_SIGN,$taxd['value']); ?></td>
                 
                </tr>
              <?php } }else
				{
					?>
					<tr>
                  <td class="text-left">NO-TAX</td>
            
                  <td class="text-right"><?php echo RUPPE_SIGN;echo '0.00'; ?></td>
                 
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

						<span><img src="catalog/view/theme/default/template/pos/images/smartphone.png" class="icon" ></span>

						<a class="text_block" href="tel:0120 4040180">0120 4040180</a>

					</div>

				</div>

				<div class="col-xs-6 col-sm-6 col-md-6">

					<div class="footer_left">

						<span><img src="catalog/view/theme/default/template/pos/images/web.png" class="icon" ></span>

						<a class="text_block" href="https://unnatiagro.in/">unnatiagro.in</a>

					</div>

				</div>

			</div>

			</div>

		</footer>
		
		
		
	</div>
	
	<footer class="text-center m_20">
        <p>© Agri POS. All rights reserved.</p>
	</footer>
          
	
</div>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="catalog/view/theme/default/template/pos/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="catalog/view/theme/default/template/pos/js/bootstrap.min.js"></script>
  </body>
</html>