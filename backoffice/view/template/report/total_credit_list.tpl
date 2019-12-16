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
			<div class="col-sm-4">
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
                  
					
                  <td class="text-left">Store Name</td>
                  <td class="text-left">Store ID</td>
					<td class="text-left">Credit</td>
                  
                </tr>
              </thead>
              <tbody>
                <?php if ($products) { ?>
                <?php foreach ($products as $product) { //print_r($product); ?>
                <tr>
                  
                 <td class="text-left"><?php echo $product['store_name']; ?></td>
                 <td class="text-left"><?php echo $product['store_id']; ?></td>
					<td class="text-left">
						<a title="View Details" href="index.php?route=report/report/premium_farmer&token=<?php echo $token; ?>&filter_store=<?php echo $product['store_id']; ?>">
							<?php echo $product['credit']; ?>
						</a>
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
  </div>
  <script type="text/javascript">
  
 $("#input-store").select2();
$('#button-filter').on('click', function() {
	var url = 'index.php?route=report/report/total_credit&token=<?php echo $token; ?>';
	var filter_store = $('select[name=\'filter_store\']').val();

	if (filter_store) 
	{
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}

	location = url;
});
$('#button-download').on('click', function() 
{
	var url = 'index.php?route=report/report/download_excel_total_credit&token=<?php echo $token; ?>';

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