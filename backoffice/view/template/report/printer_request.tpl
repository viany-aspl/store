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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
        <button type="button" id="button-download" class="btn btn-primary pull-right" style="margin-top: -8px !important;">
            Download</button>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-start">Start Date</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="Start Date" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
				<div class="form-group">
                <label class="control-label" for="input-date-end">Select Store</label>
                
                      
                  <select name="filter_store" style="width: 100%;" id="input-store" class="form-control">
				  <option value="" selected="selected">SELECT STORE</option>
                  <?php foreach ($stores as $store) { ?>
                  <?php if ($store['store_id'] == $filter_store) { ?>
                  <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
                 
              </div>
            </div>
            <div class="col-sm-6">
              
              <div class="form-group">
                <label class="control-label" for="input-date-end">End Date</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="End Date" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <div class="table-responsive">
	

          <table class="table table-bordered">
            <thead>
              <tr>
					<td class="text-left">Request Number</td>
                <td class="text-left">Store Name</td>
                <td class="text-left">Store ID</td>
                <td class="text-right">Printer Name</td>
				<td class="text-right">Printer ID</td>
                <td  class="text-right">Billing Name</td>
				<td class="text-right">Contact Person Name</td>
				<td class="text-right">Contact Number</td>
				<td class="text-right">GSTN</td>
				<td class="text-right">Email ID</td>
				<td class="text-right">Shipping Adddress</td>
				<td class="text-right">Billing Adddress</td>
                <td class="text-right">Date</td>
                
              </tr>            
			  </thead>
            <tbody>
              <?php if ($products) { ?>
              <?php foreach ($products as $order) { ?>
              <tr>
                <td class="text-left"><?php echo $order['sid']; ?></td>
                <td class="text-left"><?php echo $order['store_name']; ?></td>
                <td class="text-right"><?php echo $order['store_id']; ?></td>
                <td  class="text-right"><?php echo $order['printer_name']; ?></td>
				<td  class="text-right"><?php echo $order['printer_id']; ?></td>
				<td  class="text-right"><?php echo $order['billing_name']; ?></td>
				<td  class="text-right"><?php echo $order['contact_person_name']; ?></td>
				<td  class="text-right"><?php echo $order['contact_number']; ?></td>
				<td  class="text-right"><?php echo $order['gstn']; ?></td>
				<td  class="text-right"><?php echo $order['email']; ?></td>
				<td  class="text-right"><?php echo $order['shipping_address']; ?></td>
				<td  class="text-right"><?php echo $order['permanent_address']; ?></td>
				
                <td class="text-right"><?php echo $order['request_time']; ?></td>
              </tr>              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="6"> 
		
		<?php echo $text_no_results; ?>

	</td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right">
		
		<?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
  $("#input-store").select2();
  <!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=<?php echo $link; ?>&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
		
	var filter_store = $('select[name=\'filter_store\']').val();
	
	if (filter_store != 0) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}	

	location = url;
});
//--></script> 
 <script type="text/javascript"><!--
$('#button-download').on('click', function() {
	url = 'index.php?route=<?php echo $link; ?>/download_excel&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
	var filter_store = $('select[name=\'filter_store\']').val();
	
	if (filter_store != 0) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}	
        window.open(url, '_blank');
	//location = url;
});
//--></script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>