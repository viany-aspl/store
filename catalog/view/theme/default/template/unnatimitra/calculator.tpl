<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Unnati Mitra</title>
	<link rel="stylesheet" href="pos/view/default/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
    <!-- Bootstrap -->
    <link href="catalog/view/theme/default/template/unnatimitra/css/bootstrap.min.css" rel="stylesheet">
	<link href=":https://unnati.world/stores/backoffice/view/javascript/bootstrap/css/select2.css" rel="stylesheet" type="text/css"/>
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="catalog/view/theme/default/template/unnatimitra/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="catalog/view/theme/default/template/unnatimitra/js/bootstrap.min.js"></script>
	<script src=":https://unnati.world/stores/backoffice/view/javascript/bootstrap/js/select2.js" type="text/javascript"></script>

	<link href="catalog/view/theme/default/template/unnatimitra/css/style.css" rel="stylesheet">
<!-- App styles -->
        <link rel="stylesheet" href="pos/view/default/css/app.min.css">
		<link rel="stylesheet" href="pos/view/default/css/style.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body onbeforeunload="return destable()">
	<div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>
	<div class="for_green">
	</div>
	<?php if((empty($store_id)) && ($store_id=='')){  ?>
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<div class="logo">
						<a href="https://unnatiagro.in/unnatimitra?store_id=<?php echo $store_id; ?>"><img class="img-responsive" src="catalog/view/theme/default/template/unnatimitra/images/logo.png" alt="logo"></a>
				</div>
			</div>
			<div class="col-md-9">
			</div>
		</div>
	</div>
    <?php  } ?>
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 ">
				<div class="inner_space1">
					<div class="inner_container">
						<div class="row">
							<div class="col-md-12">
								<div class="content_area">
									<!--
									<div class="row">

											<div class="col-md-2">

											</div>

											<div class="col-md-8">


												<div class="text-center">
				
													<select onchange="return get_cal(this.value);" name="filter_store" style="width: 100%" id="input-store" class="select2 form-control">
                   <option selected="selected" value="">SELECT STORE</option>
                  <?php foreach ($stores as $store) { //echo $store['store_id'];  ?>
                  <?php if ($store['store_id'] == $filter_store) {
                      if($filter_store!=""){
                      ?>
                  <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                      <?php }} else { ?>
                  <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
												</div>

												

											</div>

									

									

											<div class="col-md-2">

											</div>

										</div>
									<div class="row">

											<div class="col-md-2">

											</div>

											<div class="col-md-8">

												<div class="text-center">

													<input type="text" id="search-sto" name="filter_store" placeholder="Search store">

													<a href="#"><img class="search-icon" src="catalog/view/theme/default/template/unnatimitra/images/search-icon.png"></a>

												</div>

												

											</div>

									

									

											<div class="col-md-2">

											</div>

										</div>
									-->
									<div class="row">
										<div class="col-md-12">
											<div class="table-responsive" style="overflow-x: inherit !important;">
								
												<table id="data-table" class="table table-bordered" style="width:100%;" >
													<thead>
														<tr>
														<?php if((empty($store_id)) && ($store_id=='')){  ?>
															<th style="width:25%;background:#1d618e;color:#fff;">Store Name</th>
															<?php  } ?>
															<th style="width:25%;background:#1d618e;color:#fff;">Product Name</th>
															<th style="width:25%;background:#1d618e;color:#fff;">SKU</th>
															<th style="width:25%;background:#1d618e;color:#fff;">Unit Price</th>
															<th style="width:25%;background:#1d618e;color:#fff;">Rewards Per unit </th>
														</tr>
													</thead>
													<tbody style="min-height:300px;">
														<?php foreach($orders as $order){ //print_r($order);  ?>
                                    <tr>
									<?php if((empty($store_id)) && ($store_id=='')){  ?>
											<td><?php echo strtoupper($order['st'][0]['name']); ?></td>
											<?php  } ?>
                                        <td><?php echo strtoupper($order['product_name']); ?></td>
										<td><?php echo $order['product_sku']; ?></td>
										
                                        <td>
										
										<?php 
										
										$store_price=0.00;
										foreach($order['pd'] as $pd)
										{
											
											if($pd['store_id']==$order['store_id'])
											{
												$store_price=number_format((float)($pd['store_price']+$pd['store_tax_amt']),2,'.','');
												if($store_price!=0.00)
												{ //echo 'here';
													echo number_format((float)($pd['store_price']+$pd['store_tax_amt']),2,'.','');
												}
											}
										}
										
										if($store_price==0.00)
										{
											//print_r($order['pd2'][0]['price_tax']);
											echo number_format((float)($order['pd2'][0]['price_tax']),2,'.','');
										}	
										
										
										?>
										
										</td>
                                        <td><?php echo $order['points']; ?></td>
                                        
                                    </tr>
									
                                <?php } ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									
								</div>
							</div>
							
							<!--<div class="col-xs-12 col-sm-5 col-md-5">
								<div class="Qr_code">
									<img src="images/qr_code.jpg" alt="">
								</div>
							</div>--->
				
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
	
	<footer>
        <div class="container mt_40">
			<div class="row">
			<?php if((empty($store_id)) && ($store_id=='')){  ?>
				<div class="col-xs-7 col-sm-7 col-md-7">
					<div class="footer_text">
						<p>© Agri POS. All rights reserved.</p>
					</div>
				</div>
			<?php  } ?>	
				<div class="col-xs-5 col-sm-5 col-md-5">
					<div class="footer_link">
						<ul>
							<li><a href="https://unnatiagro.in/unnatimitra?store_id=<?php echo $store_id; ?>">Back</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</footer>
	
	
	
    
	<script type="text/javascript">
	/*
		$(document).ready( function () {
  var table = $('#data-table').DataTable({
    //"dom": '<"top"i>rt<"bottom"><"clear">'
  });
  
  $('#mySearchButton').on( 'keyup click', function () {
    table.search($('#mySearchText').val()).draw();
  } );
} );
*/
var dtable;

function destable()
{
//dtable.clear();
//dtable.draw();	
	//dtable=null;
}


$(document).ready( function () {


//dtable=$('#data-table').DataTable({
//retrieve:true,
//bDestroy:true,

});

//$('.dataTables_filter input').attr('placeholder','Search Product');

} );

</script>
	
	<!-- App functions and actions -->
        <script src="pos/view/default/js/app.min.js"></script>
        
        
        <!-- Vendors: Data tables -->
        <script src="pos/view/default/vendors/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
        
        <script src="pos/view/default/vendors/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
		
		<style>
		
		.dataTables_wrapper .table
		{
			margin: 0px !important;
		}
		</style>
  </body>
</html>