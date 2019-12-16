<?php echo $header; ?><?php echo $column_left; ?>
 

                    <div class="card">
                        <div class="card-header">
                            <h1 style="float: left;"><?php echo "Suppliers"; ?></h1>
                            <div class="pull-right" style="float: right;">
									<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo "Add"; ?>" class="btn btn-primary">
									<i class="zmdi zmdi-plus"></i></a>
								</div>
                        </div>

                        
                    </div>

                <div class="card">
                    

                    <div class="card-block">
                        <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                            <tr>
                                <th>Supplier Name</th>
									<th>Supplier Email</th>
									<th>Supplier group</th>
									<th>Date Added</th>
									<th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($suppliers as $supplier){?>  
                            <tr>
									<td><?php echo $supplier['first_name'] . " " . $supplier['last_name']; ?></td>
									<td><?php echo $supplier['email']; ?></td>
									<td><?php echo $supplier['supplier_group_name']; ?></td>
									<td><?php echo date('d-m-Y',$supplier['date_added']->sec); ?></td>
									<td class="text-left">
										<a class="btn btn-primary" href="<?php echo $edit . "&supplier_id=" . $supplier['pre_mongified_id']; ?>" data-toggle="tooltip" title="Edit" class="btn btn-primary" style="margin-left: 5px;">
											<i class="zmdi zmdi-edit"></i>
										</a>
									</td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
						
                        </div>
						<div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
                    </div>
                </div>

<button class="btn btn-danger btn--action btn--fixed " onclick="return send_email();"><i class="zmdi zmdi-email"></i></button>

<?php echo $footer; ?>
<script type="text/javascript">
  function send_email() 
	{
		url = 'index.php?route=purchase/supplier/email&token=<?php echo $token; ?>';
	
    var name = $('#input-name').val();
	
	if (name) 
	{
		url += '&name=' + encodeURIComponent(name);
	}

	var supplier_group = $('#input-customer-group').val();
	
	if (supplier_group) 
	{
		url += '&supplier_group=' + encodeURIComponent(supplier_group);
	}  	
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
  $('#button-filter').on('click', function() 
  {
	url = 'index.php?route=purchase/supplier&token=<?php echo $token; ?>';
	
    var name = $('#input-name').val();
	
	if (name) 
	{
		url += '&name=' + encodeURIComponent(name);
	}

	var supplier_group = $('#input-customer-group').val();
	
	if (supplier_group) 
	{
		url += '&supplier_group=' + encodeURIComponent(supplier_group);
	}  
	location = url;
});
  <!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script>
 