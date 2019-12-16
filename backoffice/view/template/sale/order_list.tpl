<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
          <!--
        <button type="submit" id="button-shipping" form="form-order" formaction="<?php echo $shipping; ?>" data-toggle="tooltip" title="<?php echo $button_shipping_print; ?>" class="btn btn-info"><i class="fa fa-truck"></i></button>
        <button type="submit" id="button-invoice" form="form-order" formaction="<?php echo $invoice; ?>" data-toggle="tooltip" title="<?php echo $button_invoice_print; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>
        <a <?php if($group=="11") { echo "style='display:none;'"; } ?> href="<?php echo $insert; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
      -->
      </div>
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
	<button type="button" id="button-download" class="btn btn-primary pull-right" style="margin-top: -8px !important; margin-right: 10px !important;">
            <i class="fa fa-download"></i>Download Excel</button>
			
			<button type="button" id="button-download-item" class="btn btn-primary pull-right" style="margin-top: -8px !important; margin-right: 10px !important;">
            <i class="fa fa-download"></i>Download Excel (Item Wise)</button>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-order-id"><?php echo $entry_order_id; ?></label>
                <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="<?php echo $entry_order_id; ?>" id="input-order-id" class="form-control" />
              </div>
              
	<div class="form-group">
                <label class="control-label" for="input-date-end">Select Store</label>
                  
                  <select style="width: 100%" name="filter_store" id="input-store"  class="select2 form-control">
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
                <div class="form-group" style="">
                <label class="control-label" for="input-customer">Customer Mobile</label>
                <input type="text" name="filter_customer" maxlength="10" onkeypress="return isNumber(event)" value="<?php echo $filter_customer; ?>" placeholder="Customer Mobile" id="input-customer" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-order-status"><?php echo $entry_order_status;  ?></label>
                <select name="filter_order_status" id="input-order-status" class="form-control">
                  <option value="*">Completed</option>
                  <?php if ($filter_order_status == '0') { ?>
                  <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_missing; ?></option>
                  <?php } ?>
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $filter_order_status) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
             
	<div class="form-group">
                <label class="control-label" for="input-order-status">Pay Type</label>
	<?php //print_r($payment_methods); ?>
                <select name="filter_payment" id="input-payment" class="form-control">
                  
                 
                  <option value="" selected="selected">ALL</option>
                  
                  <?php foreach ($payment_methods as $payment_method) { ?>
                  <?php if ($payment_method == $filter_payment) { ?>
                  <option value="<?php echo $payment_method; ?>" selected="selected"><?php echo $payment_method; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $payment_method; ?>"><?php echo $payment_method; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
                <div class="form-group">
                <label class="control-label" for="input-name">Product Name</label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                <input type="hidden" name="filter_name_id"  value="<?php echo $filter_name_id; ?>" id="filter_name_id"/>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-added">Start date</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="Start date" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-modified">End date</label>
                <div class="input-group date">
                  <input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" placeholder="End date" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
			  <div class="form-group">
                <label class="control-label" for="input-order-status">Bill Type</label>
	
                <select name="billtype" id="input-billtype" class="form-control">
                  
                 
                  <option selected="selected" value="">ALL</option>
                  
                  <option value="2" <?php if ($billtype == '2') { ?> selected="selected" <?php } ?> >Inventory Led Billing</option>
                  <option value="1" <?php if ($billtype == '1') { ?> selected="selected" <?php } ?> >Open</option>
                </select>
              </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-right"><?php echo $column_order_id; ?></td>
                  <td class="text-right">Invoice No.</td>
	     
                  <td class="text-left"><?php echo $column_customer; ?></td>
                  <td class="text-left"><?php echo $column_status; ?></td>
                  <td class="text-left">Store</td>
                  <td class="text-right"><?php echo $column_total; ?></td>
					<td class="text-left">Pay Type</td>
					<td class="text-left">Bill Type</td>
                  <td class="text-left"><?php echo $column_date_added; ?></td>
                  <td class="text-left"><?php echo $column_date_modified; ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($orders) { ?>
                <?php foreach ($orders as $order) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($order['order_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" />
                    <?php } ?>
                    <input type="hidden" name="shipping_code[]" value="<?php echo $order['shipping_code']; ?>" /></td>
                  <td class="text-right"><?php echo $order['order_id']; ?></td>
                  <td class="text-right"><?php echo $order['invoice_no']; ?></td>
	    
                  <td class="text-left"><?php echo $order['customer']; ?></td>
                  <td class="text-left"><?php echo $order['status']; ?></td>
                  <td class="text-left"><?php echo $order['store_name']; ?></td>
                  <td class="text-right"><?php echo $order['total']; ?></td>
					<td class="text-right"><?php echo $order['pay_method']; ?></td>
					<td class="text-right"><?php if($order['billtype']==1){ echo 'Open Billing'; } else { echo 'Inventory Led Billing';} ?></td>
                  <td class="text-left"><?php echo $order['date_added']; ?></td>
                  <td class="text-left"><?php echo $order['date_modified']; ?></td>
                  <td class="text-right">
                      <a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a> 
                      <!--<a <?php if($group=="11") { echo "style='display:none;'"; } ?> href="<?php echo $order['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a> 
                      <a <?php if($group=="11") { echo "style='display:none;'"; } ?> href="<?php echo $order['delete']; ?>" id="button-delete<?php echo $order['order_id']; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                      -->
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
      function isNumber(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
        $('#button-filter').on('click', function() 
        {
	url = 'index.php?route=sale/order&token=<?php echo $token; ?>';
	
	var filter_order_id = $('input[name=\'filter_order_id\']').val();
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	var filter_payment = $('select[name=\'filter_payment\']').val();
	if (filter_payment) {
		url += '&filter_payment=' + encodeURIComponent(filter_payment);
	}
        var filter_name_id = $('input[name=\'filter_name_id\']').val();
        var filter_name = $('input[name=\'filter_name\']').val();

        if (filter_name_id) 
        {
            if(filter_name!="")
            {
                url += '&filter_name_id=' + encodeURIComponent(filter_name_id);
            }
        }
        if (filter_name) 
        {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }
	var filter_customer = $('input[name=\'filter_customer\']').val();
	
	if (filter_customer) {
                if(filter_customer.length<10)
                {
                    alertify.error('Customer Mobile must be 10 digit');
                    $('input[name=\'filter_customer\']').focus();
                    return false;
                }
                else
                {
                    url += '&filter_customer=' + encodeURIComponent(filter_customer);
                }
	}
	
	var filter_order_status = $('select[name=\'filter_order_status\']').val();
	
	if (filter_order_status != '*') {
		url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
	}		
	
	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	var filter_store = $('#input-store').val();
	
	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}
	var billtype = $('#input-billtype').val();
	
	if (billtype) {
		url += '&billtype=' + encodeURIComponent(billtype);
	}
				
	location = url;
});
</script> 

<script type="text/javascript">
$('#button-download-item').on('click', function() {
	url = 'index.php?route=sale/order/download_excel_item_wise&token=<?php echo $token; ?>';
	
	var filter_order_id = $('input[name=\'filter_order_id\']').val();
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	var filter_payment = $('select[name=\'filter_payment\']').val();
	if (filter_payment) {
		url += '&filter_payment=' + encodeURIComponent(filter_payment);
	}
        var filter_name_id = $('input[name=\'filter_name_id\']').val();
        var filter_name = $('input[name=\'filter_name\']').val();

        if (filter_name_id) 
        {
            if(filter_name!="")
            {
                url += '&filter_name_id=' + encodeURIComponent(filter_name_id);
            }
        }
        if (filter_name) 
        {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }
	var filter_customer = $('input[name=\'filter_customer\']').val();
	
	if (filter_customer) {
                if(filter_customer.length<10)
                {
                    alertify.error('Customer Mobile must be 10 digit');
                    $('input[name=\'filter_customer\']').focus();
                    return false;
                }
                else
                {
                    url += '&filter_customer=' + encodeURIComponent(filter_customer);
                }
	}
	
	var filter_order_status = $('select[name=\'filter_order_status\']').val();
	
	if (filter_order_status != '*') {
		url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
	}		
	
	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	var filter_store = $('#input-store').val();
	
	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}
	var billtype = $('#input-billtype').val();
	
	if (billtype) {
		url += '&billtype=' + encodeURIComponent(billtype);
	}
			
	 window.open(url, '_blank');			
	//location = url;
});
$('#button-download').on('click', function() {
	url = 'index.php?route=sale/order/download_excel&token=<?php echo $token; ?>';
	
	var filter_order_id = $('input[name=\'filter_order_id\']').val();
	
	if (filter_order_id) {
		url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
	}
	var filter_payment = $('select[name=\'filter_payment\']').val();
	if (filter_payment) {
		url += '&filter_payment=' + encodeURIComponent(filter_payment);
	}
        var filter_name_id = $('input[name=\'filter_name_id\']').val();
        var filter_name = $('input[name=\'filter_name\']').val();

        if (filter_name_id) 
        {
            if(filter_name!="")
            {
                url += '&filter_name_id=' + encodeURIComponent(filter_name_id);
            }
        }
        if (filter_name) 
        {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }
	var filter_customer = $('input[name=\'filter_customer\']').val();
	
	if (filter_customer) {
                if(filter_customer.length<10)
                {
                    alertify.error('Customer Mobile must be 10 digit');
                    $('input[name=\'filter_customer\']').focus();
                    return false;
                }
                else
                {
                    url += '&filter_customer=' + encodeURIComponent(filter_customer);
                }
	}
	
	var filter_order_status = $('select[name=\'filter_order_status\']').val();
	
	if (filter_order_status != '*') {
		url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
	}		
	
	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}
	var filter_store = $('#input-store').val();
	
	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();
	
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}
	var billtype = $('#input-billtype').val();
	
	if (billtype) {
		url += '&billtype=' + encodeURIComponent(billtype);
	}
			
	 window.open(url, '_blank');			
	//location = url;
});
$("#input-store").select2();
</script>
<script type="text/javascript">
$('input[name=\'filter_name\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
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
                $('input[name=\'filter_name_id\']').val(item['value']);
    }
});
</script>
  <script type="text/javascript">
$('input[name=\'filter_customer1\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_customer\']').val(item['label']);
	}	
});
</script> 
  <script type="text/javascript">
$('input[name^=\'selected\']').on('change', function() {
	$('#button-shipping, #button-invoice').prop('disabled', true);
	
	var selected = $('input[name^=\'selected\']:checked');
	
	if (selected.length) {
		$('#button-invoice').prop('disabled', false);
	}
	
	for (i = 0; i < selected.length; i++) {
		if ($(selected[i]).parent().find('input[name^=\'shipping_code\']').val()) {
			$('#button-shipping').prop('disabled', false);
			
			break;
		}
	}
});

$('input[name^=\'selected\']:first').trigger('change');

$('a[id^=\'button-delete\']').on('click', function(e) {
	e.preventDefault();
	
	if (confirm('<?php echo $text_confirm; ?>')) {
		location = $(this).attr('href');
	}
});
</script> 
  <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
  <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script>

<script>
var currentTime = new Date() 
var minDate = new Date(currentTime.getFullYear(), currentTime.getMonth(), +1); //one day next before month
var maxDate =  new Date(currentTime.getFullYear(), currentTime.getMonth() +2, +0); // one day before next month
$('.date').datepicker({ 
minDate: minDate, 
maxDate: maxDate 
});
</script>

</div>
<?php echo $footer; ?>