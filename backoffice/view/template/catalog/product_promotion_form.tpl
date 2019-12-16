<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?> </h3><b style="float: right;font-size: 25px;"><?php echo $product_name; ?></b>
      </div>
      <div class="panel-body">
		
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
        
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              
             <div class="tab-content">
              <div class="form-group required">
					<label class="col-sm-2 control-label" for="input-meta-title">Start Date</label>
					<div class="col-sm-10">
						<div class="input-group date" id="date_from">
							<input type="text" name="filter_date_start" required value="<?php echo $promotion_start_date; ?>" placeholder="Start Date" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
							<span class="input-group-btn">
								<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
					</div>
              </div>
				<div class="form-group required">
					<label class="col-sm-2 control-label" for="input-meta-title">End Date</label>
					<div class="col-sm-10">
						<div class="input-group date" id="date_to">
							<input type="text" name="filter_date_end" required value="<?php echo $promotion_end_date; ?>" placeholder="End Date" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
							<span class="input-group-btn">
								<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
							</span>
						</div>
					</div>
              </div>
               
              </div>
            </div>
            
             
              <!--end-->
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <script type="text/javascript">
$('.date').datetimepicker({
	minDate: moment(),
	pickTime: false
});

$('.time').datetimepicker({
	 "setDate": new Date(),
	pickDate: false
});

$('.datetime').datetimepicker({
	 "setDate": new Date(),
	pickDate: true,
	pickTime: true
});
</script> 
  
  </div>
<?php echo $footer; ?> 