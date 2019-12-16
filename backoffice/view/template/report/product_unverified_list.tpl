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
		 <!--<button type="button" id="button-download" class="btn btn-primary pull-right" style="margin-top: -8px !important;">
            Download Excel</button>-->
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <!--<div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              
            </div>-->
            
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
              
            </div>
			<div class="col-sm-2">
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
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                <td class="text-center">Product ID</td>  
				  <td class="text-center"><?php echo $column_image; ?></td>
				  
                  <td class="text-left"><?php echo $column_name; ?></td>
                  <td class="text-left"><?php echo $column_model; ?></td>
                  <td class="text-left"><?php echo $column_price; ?></td>
<td class="text-left"><?php echo $column_price_tax; ?></td>
                    
                  <td  class="text-right"><?php echo $column_quantity; ?></td>
                  <td class="text-left">Store</td>
                 <td class="text-left">Status</td>
                </tr>
              </thead>
              <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { //print_r($product); ?>
                <tr>
                  <td class="text-center"><?php if (in_array($product['product_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
                    <?php } ?></td>
					<td class="text-center"><?php echo $product['product_id']; ?></td>
                  <td class="text-center"><?php if ($product['image']) { ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-thumbnail" />
                    <?php } else { ?>
                    <span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
                    <?php } ?></td>
					
                  <td class="text-left"><?php echo $product['name']; ?></td>
                  <td class="text-left"><?php echo $product['model']; ?></td>
<td class="text-left"><?php if ($product['special_tax']) { ?>
	                <span style="text-decoration: line-through;"><?php echo $product['price']; ?></span><br/>
	                <span class="text-danger"><?php echo $product['special_tax']; ?></span>
	                <?php } else { ?>
	                <?php echo $product['price']; ?>	               
					<?php } ?></td>


                  <td class="text-left"><?php if ($product['special']) { ?>
                    <span style="text-decoration: line-through;"><?php echo $product['price_tax']; ?></span><br/>
                    <div class="text-danger"><?php echo $product['special']; ?></div>
                    <?php } else { ?>
                    <?php echo $product['price_tax']; ?>
                    <?php } ?></td>
                  <td class="text-right"><?php 
                  $productsquantity=0;
                  $ararra=explode('<br/>',$product['squantity']);
                  foreach($ararra as $prd_qunt1)
                  {
                    $ararra=explode('-',$prd_qunt1);
                    $productsquantity=$productsquantity+end($ararra);
                  }
                  //echo $productsquantity;
                  if ($product['quantity'] <= 0) { ?>
                    <span data-toggle="tooltip" title="<?php echo $product['squantity']; ?>" class="label label-warning"><?php echo $productsquantity//$product['quantity']; ?></span>
                    <?php } elseif ($product['quantity'] <= 5) { ?>
                    <span data-toggle="tooltip" title="<?php echo $product['squantity']; ?>" class="label label-danger"><?php echo $productsquantity//$product['quantity']; ?></span>
                    <?php } else { ?>
                    <span data-toggle="tooltip" title="<?php echo $product['squantity']; ?>" class="label label-success"><?php echo $productsquantity//$product['quantity']; ?></span>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $product['store_name']; ?></td>
                 <td class="text-left"><?php echo $product['status']; ?></td>
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
  </div>
  <script type="text/javascript">
  $("#input-store").select2();
$('#button-filter').on('click', function() {
	var url = 'index.php?route=report/report&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_store = $('select[name=\'filter_store\']').val();

	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}

	location = url;
});
$('#button-download').on('click', function() 
{
	var url = 'index.php?route=report/report/download_excel&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_store = $('select[name=\'filter_store\']').val();

	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
	 window.open(url, '_blank');
	//location = url;
});
</script> 
  <script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete_unverified&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
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
	}
});

//--></script></div>
<?php echo $footer; ?>