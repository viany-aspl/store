<?php echo $header; ?><?php echo $column_left; ?>
 
       <div class="toolbar">
                        <div class="toolbar__label">
                            <?php echo $total_customers; ?> Customers
                        </div>

                        <div class="actions">
                            <i class="actions__item zmdi zmdi-search" data-ma-action="toolbar-search-open"></i>
                            
                        </div>

                        <div class="toolbar__search">
                            <input type="text" name="filter_telephone" placeholder="Search...">

                            <i class="toolbar__search__close zmdi zmdi-long-arrow-left" data-ma-action="toolbar-search-close"></i>
                        </div>
                    </div>
                    <div class="row groups" id="row_groups">
                        <?php foreach($customers as $customer){ ?>
                        <div class="col-xl-2 col-lg-3 col-sm-4 col-6" style="min-height: 50px;">
                            <div class="groups__item">
                                <a href="#">
                                   

                                    <div class="groups__info">
                                        <strong><?php  if(empty($customer['firstname'])){ echo "NA";} else { echo ($customer['firstname']." ".$customer['lastname']); } ?></strong>
                                        <small><?php echo ($customer['telephone']); ?></small>
                                        <strong style="display: none;">Aadhar : <?php  if(empty($customer['aadhar'])){ echo 'NA';} else { echo $customer['aadhar']; } ?></strong>
                                        <strong>Balance : <?php echo RUPPE_SIGN. number_format((float)($customer['credit']),2); ?></strong>
                                    </div>
                                </a>

                                <div class="actions">
                                    <div class="dropdown actions__item">
                                        <i class="zmdi zmdi-more-vert" data-toggle="dropdown"></i>

                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="index.php?route=pos/report/customer_trans&mobile_number=<?php echo ($customer['telephone']); ?>&aadhar=<?php echo ($customer['aadhar']); ?>&name=<?php echo ($customer['firstname']." ".$customer['lastname']); ?>&token=<?php echo $token; ?>&credit=<?php echo ($customer['credit']); ?>&customer_id=<?php echo ($customer['customer_id']); ?>&view_type=premium&pagetittle=Premium Farmer Transactions">Transactions Details</a>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                       <?php } ?>
                    </div>
					<button class="btn btn-danger btn--action btn--fixed " onclick="return send_email();"><i class="zmdi zmdi-email"></i></button>
<?php echo $footer; ?>
<script type="text/javascript">
function send_email() 
	{
		url = 'index.php?route=pos/report/premium_farmer_email&token=<?php echo $token; ?>';
	
			
		$.ajax({
		url: url,
		//dataType: 'json',
		method: 'POST',
		beforeSend: function() {
			
			$(".page-loader").addClass("important");
			$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
			$(".page-loader").show();
		},
		complete: function() 
		{
			$(".page-loader").removeClass("important");
			$("#please_wait_span").remove();
			$(".page-loader").hide();
		},
		success: function(html) 
        {
			//alert(html);
			$(".page-loader").removeClass("important");
			$("#please_wait_span").remove();
			$(".page-loader").hide();
				alertify.success(html);
				return false;
		},
		error: function(xhr, ajaxOptions, thrownError) 
		{
			$(".page-loader").removeClass("important");
			$("#please_wait_span").remove();
			$(".page-loader").hide();
			alertify.error(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			
		}
	});
			
		
		return false;
	}
    $(document).ready(function() 
{ 
    $('input[name=\'filter_telephone\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=pos/total_credit/autocomplete&token=<?php echo $token; ?>&filter_telephone=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['telephone'],
						value: item['customer_id'],
                                                name: item['name'],
                                                aadhar: item['aadhar'],
                                                credit: item['credit']
					}
				}));
			}
		});
	},
	'select': function(item) 
        {
            $("#row_groups").html('');
		$('input[name=\'filter_telephone\']').val(item['label']);
                //$('input[name=\'filter_name_id\']').val(item['value']);
                    var html='<div class="col-xl-2 col-lg-3 col-sm-4 col-6" style="min-height: 50px;">'+
                            '<div class="groups__item">'+
                                '<a href="#">'+
                                   '<div class="groups__info">'+
                                        '<strong>'+item['name']+'</strong>'+
                                        '<small>'+item['label']+'</small>'+
                                        '<strong>Aadhar :' +item['aadhar']+'</strong>'+
                                        '<small>Balance : ' +item['credit']+'</small>'+
                                    '</div>'+
                                '</a>'+
                                '<div class="actions">'+
                                    '<div class="dropdown actions__item">'+
                                        '<i class="zmdi zmdi-more-vert" data-toggle="dropdown"></i>'+
                                        '<div class="dropdown-menu dropdown-menu-right">'+
                                            '<a class="dropdown-item" href="index.php?route=pos/report/customer_trans&mobile_number='+item['label']+'&aadhar='+item['aadhar']+'&name='+item['name']+'">Transactions Details</a>'+
                                        '</div>'+
                                   '</div>'+
                                '</div>'+
                            '</div>'+
                        '</div>';
                $("#row_groups").html(html);
	}
});
});
    </script>
