<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
		 <button type="button" id="button-download" class="btn btn-primary pull-right" style="margin-top: -8px !important;">
            Download</button>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-name">Name</label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="Name" id="input-name" class="form-control" />
              </div>
              
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-name">Telephone</label>
                <input type="text" name="filter_telephone" value="<?php echo $filter_telephone; ?>" placeholder="Telephone" id="input-filter_telephone" class="form-control" />
              </div>
              
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-store">Select Store</label>
                      
                  <select name="filter_store" style="width: 100%" id="input-store" class="select2 form-control">
                   <option selected="selected" value="">SELECT STORE</option>
                  <?php foreach ($stores as $store) {   ?>
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
              <div class="form-group">
					<button type="button" style="margin-top: 23px;" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
				</div>
            </div>
			
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  
					<td class="text-center">Name</td>  
					<td class="text-center">Telephone</td>
					<td class="text-left">Credit</td>
   <td class="text-left">Reward</td>
                  <td class="text-left">Store Name</td>
                  <td class="text-left">Store ID</td>
                  <td class="text-left">Card</td>
                </tr>
              </thead>
              <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { //print_r($product); ?>
                <tr>
                  
					<td class="text-center"><?php echo $product['name']; ?></td>
                  
					
                  <td class="text-left">
						<!--<a href="#" onclick="return show_trans(<?php echo $product['telephone']; ?>,<?php echo $product['store_id']; ?>);">-->
							<?php echo $product['telephone']; ?>
						<!--</a>-->
						
						</td>
                  <td class="text-left"><?php echo $product['credit']; ?></td>
  <td class="text-left"><?php echo $product['reward']; ?></td>
                  <td class="text-left"><?php echo $product['store_name']; ?></td>
                 <td class="text-left"><?php echo $product['store_id']; ?></td>
 <td>
                     <?php if(!empty($product['unnati_mitra'])){ ?>
                     <a href="#" onclick="return open_model('<?php echo $product['telephone']; ?>','<?php echo $product['name']; ?>','<?php echo $product['dist_name']; ?>','<?php echo $product['village']; ?>','<?php echo $product['card_number']; ?>')" >View Card</a>
                <?php } ?>
                 </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>

<!-- Modal -->
  <div class="modal fade" id="myModal_create_bill" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" id="partner_cncl_btn2" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Unnati Mitra</h4>
        </div>
        <div class="modal-body">
        <div class="centerbox">
	
	<div class="white">
		<div class="logo_icon">
			<img src="view/image/mitracard/logo.png">
		</div>
		<div class="logo_text">
			<h1>Unnati Mitra</h1>
		</div>
		
		<div class="container">
			<label class="small" for="name">Name</label>
                        <input autocomplete="off" type="text" id="name" class="input" name="name" placeholder="Name"></input>
			
			
			<label class=" small" for="name">Mobile</label>
			<input autocomplete="off" type="text" id="mobile" class="input" name="mobile" placeholder="Mobile"></input>
			
			<label class=" small" for="name">District</label>
			<input autocomplete="off" type="text" id="district" class="input" name="district" placeholder="District"></input>
			
			<label class=" small" for="name">Village</label>
			<input autocomplete="off" type="text" id="village" class="input" name="village" placeholder="Village"></input>
			
			
		</div>
			
		
		
		
		
	</div>
	
     <div class="grey">
	 
		<div class="Qr_code" id="qrcode">
			<img class="image_size" src="view/image/mitracard/qrcode.png">
		</div>
		
		<div class="phone_details">
			<div class="phone_icon text-center">
				<img src="view/image/mitracard/phone_icon.png">
			</div>
			
			<div class="phone_text">
				<h3>0120 4040180</h3>
			</div>
		</div>
		
	</div>
	
	<div class="powered_box">
		<div class="serial_no">
			<p><em>card no:</em></p>
			<h1 id="card_no">1125478422</h1>
		</div>
		
		<div class="serial_no">
			<h1><em>Powered by:</em></h1>
			<img class="img-responsive" src="view/image/mitracard/unnati_agripos.png" alt="">
		</div>
		
	</div>
	
	
</div>
	
	
	



</div>
        </div>
        
      </div>
      
    </div>

  </div>
  <script src="view/javascript/jquery.qrcode-0.11.0.min.js"></script>
  <script src="view/javascript/jquery.qrcode.js"></script>
  <script type="text/javascript">
  function open_model(telephone,name,dist_name,village,card_no)
{
    $("#name").val(name);
    $("#mobile").val(telephone);
    $("#district").val(dist_name);
    $("#village").val(village);
    $("#card_no").html(card_no);
	$("#qrcode").html('');
	$("#qrcode").qrcode({
                                                        render: "canvas", 
                                                        text: card_no, 
                                                        width: 120, //二维码的宽度
                                                        height: 120,
                                                        background: "#ffffff", //二维码的后景色
                                                        foreground: "#000000", //二维码的前景色
                                                        src: '',//../../../stores/image/no_image.png
                                                        imgWidth: 50,
                                                        imgHeight: 50
                                                        });
	
	$('#myModal_create_bill').modal('show');


return false;
}
  function show_trans(filter_telephone,filter_store)
  {
	var url = 'index.php?route=sale/order&token=<?php echo $token; ?>&referer=report/report/premium_farmer';
	
	if (filter_telephone) 
	{
		url += '&filter_customer=' + encodeURIComponent(filter_telephone);
	}
	
	if (filter_store) 
	{
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
	location = url;
	  return false;
  }
 $("#input-store").select2();
$('#button-filter').on('click', function() {
	var url = 'index.php?route=report/report/premium_farmer&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
	var filter_telephone = $('input[name=\'filter_telephone\']').val();

	if (filter_telephone) {
		url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
	}
	var filter_store = $('select[name=\'filter_store\']').val();

	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}

	location = url;
});
$('#button-download').on('click', function() 
{
	var url = 'index.php?route=report/report/premium_farmer_download&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
	var filter_telephone = $('input[name=\'filter_telephone\']').val();

	if (filter_telephone) {
		url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
	}
	var filter_store = $('select[name=\'filter_store\']').val();

	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
	 window.open(url, '_blank');
	//location = url;
});
</script> 
</div>
<?php echo $footer; ?>
<link href="view/stylesheet/ca_Style.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Jura" rel="stylesheet">