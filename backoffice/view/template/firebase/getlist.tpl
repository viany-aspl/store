<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
	<div class="pull-right">
		<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary">
			<i class="fa fa-plus"></i>
		</a>
    </div>
      <h1><?php echo $heading_title; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
        <!--<button type="button" id="button-download" class="btn btn-primary pull-right" style="margin-top: -8px;"> Download</button>-->
      </div>
      <div class="panel-body">
        <!--<div class="well">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-end">Select Store</label>
           
                      
                  <select name="filter_store" style="width:100%;" id="input-store" class="select2 form-control">
                   <option selected="selected" value="">SELECT STORE</option>
                  <?php foreach ($stores as $store) { //echo $store['store_id'];  ?>
                  <?php if ($store['store_id'] == $filter_store) {
					  $selected_store=$store['name'];
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
            <div class="col-sm-6">
                
              
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>-->
        <div class="table-responsive">
          <br/> 
          <table class="table table-bordered">
            <thead>
              <tr>
					<td class="text-left">SI ID </td>
					<td class="text-left">Tittle</td>
					<td class="text-left" style="max-width: 200px;">Message</td>
					<td class="text-right">Image</td>
					<td class="text-right">Date Time</td>
					<td class="text-right">Receiver</td> 
					<td class="text-right">Action</td>
              </tr>
            </thead>
            <tbody>
              <?php if ($orders) {  if($_GET["page"]=="") {$aa=1;} else if($_GET["page"]=="1") {$aa=1;}
              else{ $aa=(($_GET["page"]-1)*20)+1; }?>
              <?php foreach ($orders as $order) { ?>
              <tr>
					<td class="text-left"><?php echo $aa; ?></td>
					<td class="text-left"><?php echo $order['title']; ?></td>
					<td class="text-left"><?php echo $order['message']; ?></td>
					<td class="text-right"><a target="_blank" href="<?php echo $order['image_url']; ?>"><img style="width: 100px;height: 100px;" src="<?php echo $order['image_url']; ?>" /></a></td>
					<td class="text-right"><?php echo $order['publishedDate']; ?></td>
					<td class="text-right"><?php echo $order['name']; ?></td>
					<td class="text-right"><a href="<?php echo $order['delete']; ?>" ><i class="fa fa-trash fa-2x"></i></a></td>
              </tr>
              <?php 
              $aa++;
              } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="5"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"> 
             
              <?php echo $results; ?>  </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
$('#input-store').select2();
<!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=report/inventory_report&token=<?php echo $token; ?>';
	
        var filter_store = $('select[name=\'filter_store\']').val();
	
	if (filter_group!="") {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		//url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
		
	var filter_group = $('select[name=\'filter_group\']').val();
	
	if (filter_group) {
		//url += '&filter_group=' + encodeURIComponent(filter_group);
	}
	
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();
	
	if (filter_order_status_id != 0) {
		//url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}	
       
	location = url;
});
//--></script> 
<script type="text/javascript"><!--
$('#button-download').on('click', function() {
    url = 'index.php?route=report/inventory_report/download_excel&token=<?php echo $token; ?>';
    
        var filter_store = $('select[name=\'filter_store\']').val();
    
    if (filter_group!="") {
        url += '&filter_store=' + encodeURIComponent(filter_store);
    }

    var filter_date_end = $('input[name=\'filter_date_end\']').val();
    
    if (filter_date_end) {
        //url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
    }
        
    var filter_group = $('select[name=\'filter_group\']').val();
    
    if (filter_group) {
        //url += '&filter_group=' + encodeURIComponent(filter_group);
    }
    
    var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();
    
    if (filter_order_status_id != 0) {
        //url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
    }    
       
    //location = url;
        window.open(url, '_blank');
});
//-->

</script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>