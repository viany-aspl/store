<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>Customer Activity Report</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
<i class="<?php echo $tool_tip_class; ?> " data-toggle="tooltip" style="<?php echo $tool_tip_style; ?>" title="<?php echo $tool_tip; ?>"></i>
 
    </div>
	
  </div>
  <div class="container-fluid">
  
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i>Customer Activity Report</h3>
		 <button type="button" id="button-download" class="btn btn-primary pull-right" style="margin-top: -8px !important;">
            <i class="fa fa-download"></i> Download</button>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-12">
             <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
             </div>
              <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_date_end; ?></label>
                <div class="input-group date">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              </div>
                </div></div>
            <div class="row">
                  <div class="col-sm-8">
              <div class="form-group">
                <label class="control-label" for="input-date-end">Select Store</label>
                <div class="input-group">
                  <select name="filter_store"  id="input-store" class="select2 form-control">
		<option value="">SELECT</option>
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
              </div>
             
                <div class="col-sm-4" >
              <button type="button" id="button-filter" style="margin-top:10% " class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
          </div>
        </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                 
					<td class="text-left">Type</td>
                     <td class="text-left">Count</td>
					
					
              </tr>
            </thead>
            <tbody>
             
              
					<?php foreach($orders as $order){ 
                                            if(!empty($order)){?>
                <tr>
                    <td class="text-left"><?php echo $order['_id']; ?></td>
					<td class="text-left"><?php echo $order['total']; ?></td>
                                        </tr>
             
                                        <?php } } ?>
					
					
					
              
            </tbody>
          </table>
        </div>
       
      </div>
    </div>
  
  
  
  <script>
      
      $("#input-store").select2();
      </script>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=report/customeractivity&token=<?php echo $token; ?>';
	
	
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
$('#button-download').on('click', function() {
	url = 'index.php?route=report/customeractivity/download_excel&token=<?php echo $token; ?>';
	
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) {
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}

	location = url;
});
//--></script> 
  <script type="text/javascript"><!--
              
$('.date').datetimepicker({
	pickTime: false
});
//--></script>
<?php echo $footer; ?>