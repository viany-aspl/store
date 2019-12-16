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
            Download Excel</button>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-mobile"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_mobile" value="<?php echo $filter_mobile; ?>" placeholder="<?php echo $entry_name; ?>" id="input-mobile" class="form-control" />
              </div>
                 
            </div>
            
            <div class="col-sm-6">

              <button type="button" id="button-filter" class="btn btn-primary pull-right"> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  
                 <td class="text-center">Date</td>  
				     <td class="text-center"> Invoice<?php //echo $column_image; ?></td>
				  
                 
                  <td class="text-center">Pos<?php //echo $column_model; ?></td>
                 
                    
                  <td  class="text-center">Earn/Redeem<?php //echo $column_quantity; ?></td>
                  
                </tr>
              </thead>
              <tbody>
                <?php if ($rewards) { ?>
                <?php foreach ($rewards as $product) { //print_r($product); ?>
                <tr>
                  <td class="text-center"><?php echo date('d-m-Y',$product['date_added']->sec); ?></td>
                  <td class="text-center"><?php echo $product['inv_no']; ?></td>
					
                  <td class="text-left"><?php echo $product['store_name']; ?></td>
                 <td class="text-left"><?php 
                                        if($product['type']=='Add')
                                        {
                                            echo '<font color="green">+'.number_format((float)($product['points']),2,'.','').'</font>';
                                        }
                                        if($product['type']=='Redeem')
                                        {
                                            echo '<font color="red">-'.number_format((float)($product['points']),2,'.','').'</font>';
                                        }
                                        ?></td>
                  
                  
                  
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
$('#button-filter').on('click', function() {
	var url = 'index.php?route=catalog/reward/unnati_mitra_statement&token=<?php echo $token; ?>';

	var filter_mobile = $('input[name=\'filter_mobile\']').val();

	if (filter_mobile) {
		url += '&mobile=' + encodeURIComponent(filter_mobile);
	}

	location = url;
});
$('#button-download').on('click', function() {
	var url = 'index.php?route=catalog/reward/unnati_mitra_statement_download_excel&token=<?php echo $token; ?>';

	var filter_mobile = $('input[name=\'filter_mobile\']').val();

	if (filter_mobile) {
		url += '&mobile=' + encodeURIComponent(filter_mobile);
	}
	 window.open(url, '_blank');
	//location = url;
});
</script> 
  <script type="text/javascript">
$('input[name=\'filter_mobile\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&filter_telephone=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['telephone'],
						value: item['telephone']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_mobile\']').val(item['label']);
	}
});
</script></div>
<?php echo $footer; ?>