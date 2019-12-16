<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>Incoming Call Summary Report</h1>
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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i>Incoming Call Summary List</h3>
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
             
                <div class="col-sm-4" >
              <button type="button" id="button-filter" style="margin-top:10% " class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
            </div>
          
          </div>
        </div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
					<td class="text-left">Date</td>
					<td class="text-left">Total Call</td>
					<td class="text-left">Missed Call</td>
					<td class="text-left">Busy</td>
					<td class="text-left">Call Later</td>
					<td class="text-left">Not Picking</td>
					<td class="text-left">Attempt Later</td>
					<td class="text-left">To be attempted</td>
					<td class="text-left">Not reachable</td>
					<td class="text-left">Switch Off</td>
					<td class="text-left">Enquiry</td>
					<td class="text-left">General Call</td>
					<td class="text-left">Recieved Call</td>
					<td class="text-left">Not Interested</td>
					<td class="text-left">Answered</td>
              </tr>
            </thead>
            <tbody>
              <?php if ($activities) { ?>
              <?php foreach ($activities as $activity) { ?>
              <tr>
					<td class="text-left"><?php echo ($activity['_id']['yearMonthDay']); ?></td>
					<td class="text-left"><?php echo $activity['count']; ?></td>
					<td class="text-left"><?php echo $activity['missedcall']; ?></td>
					<td class="text-left"><?php echo $activity['Busy']; ?></td>
					<td class="text-left"><?php echo $activity['CallLater']; ?></td>
					<td class="text-left"><?php echo $activity['NotPicking']; ?></td>
					<td class="text-left"><?php echo $activity['AttemptLater']; ?></td>
					<td class="text-left"><?php echo $activity['Tobeattempted']; ?></td>
					<td class="text-left"><?php echo $activity['Notreachable']; ?></td>
					<td class="text-left"><?php echo $activity['SwitchOff']; ?></td>
					<td class="text-left"><?php echo $activity['Enquiry']; ?></td>
					<td class="text-left"><?php echo $activity['GeneralCall']; ?></td>
					<td class="text-left"><?php echo $activity['RecievedCall']; ?></td>
					<td class="text-left"><?php echo $activity['NotInterested']; ?></td>
					<td class="text-left"><?php echo $activity['Answered']; ?></td>
					
              </tr>
              <?php } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
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
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=report/incommingcall_report&token=<?php echo $token; ?>';
	
	
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
$('#button-download').on('click', function() {
	url = 'index.php?route=report/incommingcall_report/download_excel&token=<?php echo $token; ?>';
	
	
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
//--></script></div>
<?php echo $footer; ?>