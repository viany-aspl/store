<?php echo $header; ?><?php echo $column_left; ?>
 

                    <div class="card">
                        <div class="card-header">
                            <h1 style="float: left;"><?php echo "Supplier Groups"; ?></h1>
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
                                <th>Supplier Group Name</th>
									<th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($supplier_groups as $supplier_group)
								{
								?>  
                            <tr>
									<td class="text-left"><?php echo $supplier_group['supplier_group_name']; ?></td>
									<td class="text-left">
										<a class="btn btn-primary" href="<?php echo $edit . "&supplier_group_id=" . $supplier_group['pre_mongified_id']; ?>" data-toggle="tooltip" title="Edit" class="btn btn-primary" style="margin-left: 5px;">
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


<?php echo $footer; ?>
<script type="text/javascript">
$('#button-filter').on('click', function() 
    {
	var url = 'index.php?route=pos/report&token=<?php echo $token; ?>';

	var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if(start_date)
        {
            url += '&filter_date_start=' + encodeURIComponent(start_date);
        }
        
        if (end_date) 
        {
            url += '&filter_date_end=' + encodeURIComponent(end_date);
        }

	location = url;
    });
</script> 
 