<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
   
    <!-- Bootstrap -->
    <link href="catalog/view/theme/default/css/bootstrap.min.css" rel="stylesheet">
	<link href="catalog/view/theme/default/css/style.css" rel="stylesheet">
	<!--<link href="https://unnatiagro.in/stores/pos/view/default/css/app.min.css" rel="stylesheet">-->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	
	<title>AgriPOS</title>
	
		<link rel="shortcut icon"  href="../favicon.ico"/>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<meta id="description" name="description" "Thanks for shopping at <?php echo $store_name; ?>, digitally powered by Unnati AgriPOS." >
		<!--<meta id="description" name="description" "Dear <?php echo $firstname." ".$lastname; ?>, Thanks for shopping at <?php echo $store_name; ?>, digitally powered by Unnati AgriPOS. Please click on the below link to download invoice for Rs.<?php echo number_format($order_info['total'],2); ?>" >-->
		<meta property="og:title" content="AgriPOS Invoice" />
		<meta property="og:url" content="https://unnatiagro.in/stores/index.php?route=mpos/openretailer/invoice&order_id=<?php echo $_GET['order_id']; ?>" />
		<meta property="og:description" content="Thanks for shopping at <?php echo $store_name; ?>, digitally powered by Unnati AgriPOS.">
		<!--<meta property="og:description" content="Dear <?php echo $firstname." ".$lastname; ?>, Thanks for shopping at <?php echo $store_name; ?>, digitally powered by Unnati AgriPOS. Please click on the below link to download invoice for Rs.<?php echo number_format($order_info['total'],2); ?>">-->
		<meta property="og:image" content="https://unnatiagro.in/images/agri-logo_300x200.jpg">
		<meta property="og:type" content="website" />
		<meta property="og:locale" content="en_GB" />
		<meta property="og:locale:alternate" content="fr_FR" />
		<meta property="og:locale:alternate" content="es_ES" />
		
		
		<meta name="author" content="AgriPOS">
  </head>
  <body>
    
	<div class="container">
		<div class="inner_space">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-md-6">
					<div class="invoice_from">
						<p>Invoice from</p>
						<h4><strong><?php echo $store_name; ?></strong></h4>
						<p><?php echo $store_address; ?></p>
						<p><?php echo $store_telephone; ?></p>
						<p><?php echo $store_email; ?></p>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6 col-md-6">
					<div class="invoice_to">
						<p>Invoice to</p>
						<h4><strong><?php echo $firstname." ".$lastname; ?></strong></h4>
						
						<p><?php echo $telephone; ?></p>
					</div>
				</div>
			</div>
			
			<div class="invoice_box">
				<div class="row">
					<div class="col-sm-3 col-md-3">
						<div class=" grey_box">
							<p>Invoice#</p>
							<h3><?php echo ($order_info['invoice_prefix'].$order_info['invoice_no']); ?></h3>
						</div>
					</div>
					<div class="col-sm-3 col-md-3">
						<div class=" grey_box">
							<p>Date</p>
							<h3><?php echo (date('d M, Y',$order_info['date_added']->sec)); ?></h3>
						</div>
					</div>
					<div class="col-sm-3 col-md-3">
						<div class=" grey_box">
							<p>Order ID</p>
							<h3><?php echo ($order_info['order_id']); ?></h3>
						</div>
					</div>
					<div class="col-sm-3 col-md-3">
						<div class="grey_box">
							<p>Total</p>
							<h3><?php echo RUPPE_SIGN;  echo number_format($order_info['total'],2); ?></h3>
						</div>
					</div>
				</div>	
			</div>
			
		<div class="product_table">	
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
								  <th class="text-left">Product</th>
							 
								  <th class="text-right">Quantity</th>
								  <th class="text-right">Unit Price</th>
								  <th class="text-right">Total</th>
								</tr>
							</thead>
							<tbody>
									<?php foreach ($products as $product) { ?>
                <tr>
                  <td class="text-left"><?php echo $product['name']; ?></td>
            
                  <td class="text-right"><?php echo $product['quantity']; ?></td>
                  <td class="text-right"><?php echo str_replace('Rs.',RUPPE_SIGN,$product['price']); ?></td>
                  <td class="text-right"><?php echo str_replace('Rs.',RUPPE_SIGN,$product['total']); ?></td>
                </tr>
                <?php } ?>
												
							<?php foreach ($totals as $totals) { ?>
                <tr>
                  <td colspan="3" class="text-right"><?php echo $totals['title']; ?>:</td>
                  <td class="text-right"><?php echo str_replace('Rs.',RUPPE_SIGN,$totals['text']); ?></td>
                </tr>
                <?php } ?>
												<tr>
								  <td colspan="3" class="text-right">Cash:</td>
								  <td class="text-right"><?php echo RUPPE_SIGN;  echo number_format($order_info['cash'],2); ?></td>
								</tr>
								<tr>
								  <td colspan="3" class="text-right">Credit:</td>
								  <td class="text-right"><?php echo RUPPE_SIGN;  echo number_format($order_info['credit'],2); ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		
		<div class="tax_des">
			<h2>Tax Description</h2>
			
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
					<div class="invoice_from">
						<span><img src="catalog/view/theme/default/images/smartphone.png" class="icon" ></span>
						<a href="tel:0120 4040160">0120 4040160</a>
					</div>
				</div>
				<div class="col-xs-6 col-sm-6 col-md-6">
					<div class="invoice_to">
						<span><img src="catalog/view/theme/default/images/web.png" class="icon" ></span>
						<a href="https://unnatiagro.in/">unnatiagro.in</a>
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
    <script src="catalog/view/theme/default/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="catalog/view/theme/default/js/bootstrap.min.js"></script>
  </body>
</html>