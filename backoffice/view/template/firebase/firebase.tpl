<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default">
		<i class="fa fa-reply"></i>
		</a>
		</div>
      <h1><?php echo $heading_title; ?></h1>
     
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
      <?php if ($success) {  ?>
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-envelope"></i> <?php echo $heading_title; ?></h3>
      </div>
      <div class="panel-body">
          <?php if ($json != '') { ?>
                    <label><b>Request:</b></label>
                    <div class="json_preview">
                        <pre><?php echo json_encode($json) ?></pre>
                    </div>
                <?php } ?>
                <br/>
                <?php if ($response != '') { ?>
                    <label><b>Response:</b></label>
                    <div class="json_preview">
                        <pre><?php echo json_encode($response) ?></pre>
                    </div>
                <?php } ?>
          
        <form method="post" enctype="multipart/form-data" id="form-user" class="form-horizontal" action="index.php?route=firebase/firebase&token=<?php echo $token; ?>">
         
          <div class="form-group" >
            <label class="col-sm-2 control-label" for="input-to">User</label>
            <div class="col-sm-10">
			<!--
              <input type="text" class="form-control" id="users" name="users"  placeholder="Users">
              <input type="hidden" class="form-control" id="users_id" name="users_id">-->
			   <?php //print_r($users); ?>
			  <select required class="form-control js-example-basic-multiple" style="width: 100%;" multiple="multiple" id="users_id" name="users_id[]">
                 
				  <?php 
					
				  foreach($users as $user)
                        {
							if(!empty($user['token']))
							{
							?>
                         <option  value="<?php echo $user['token'].'----'.$user['user_id']; ?>"><?php echo $user['firstname'].' '.$user['lastname'] ; ?> (<?php echo $user['st'][0]['name']; ?>)</option>
						<?php } } ?>
                                   
                                   </select>
            </div>
          </div>
          
     
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-subject">Title</label>
            <div class="col-sm-10">
              <input type="text" id="title" required="required" name="title" class="form-control" placeholder="Enter title">
            </div>
          </div>
           <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-subject">Message</label>
            <div class="col-sm-10">
              <textarea class="form-control" required="required" rows="5" name="message" id="message" placeholder="Notification message!"></textarea>
            </div>
          </div>
			<div class="form-group required">
            <label class="col-sm-2 control-label" for="input-subject">Valid date</label>
            <div class="input-group col-sm-9 date" id="date_from" style="padding-left: 15px;">
					<input type="text" name="valid_date" required="required" value="<?php echo $valid_date; ?>" placeholder="Message till date to display" data-date-format="YYYY-MM-DD" id="input-date-valid" class="form-control" />
					<span class="input-group-btn">
						<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
				</div>
          </div>
 	
		

          <div class="form-group ">
            <label class="col-sm-2 control-label" for="input-message">Include image</label>
            <div class="col-sm-10">
              <input name="include_image" id="include_image" type="file" class="form-control">
            </div>
          </div>
            <div class="form-group required">
            
            <div class="col-sm-12">
                <div class="pull-right">
        
         <img id="processing_image" src="https://unnati.world/shop/admin/view/image/processing_image.gif" style="height: 50px;width: 50px;display: none;" />
         <button type="submit" class="btn btn-primary">Send</button>
    </div>
              
            </div>
          </div>
            <input type="hidden" name="push_type" value="individual"/>
        </form>
          <!--<br/><br/><br/><br/>
          
          <form method="post" enctype="multipart/form-data" id="form-user" class="form-horizontal" action="index.php?route=firebase/firebase&token=<?php echo $token; ?>">
            
            <h1>Send to Topic `global`</h1>
            
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-subject">Title</label>
            <div class="col-sm-10">
              <input type="text" id="title" name="title" class="form-control" placeholder="Enter title">
            </div>
          </div>
           <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-subject">Message</label>
            <div class="col-sm-10">
              <textarea class="form-control" rows="5" name="message" id="message" placeholder="Notification message!"></textarea>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-message">Include image</label>
            <div class="col-sm-10">
              <input name="include_image" id="include_image" type="checkbox">
            </div>
          </div>
            <div class="form-group required">
            
            <div class="col-sm-12">
                <div class="pull-right">
         <input type="hidden" name="push_type" value="topic"/>
         <img id="processing_image" src="https://unnati.world/shop/admin/view/image/processing_image.gif" style="height: 50px;width: 50px;display: none;" />
         <button type="submit" class="btn btn-primary">Send</button>
    </div>
              
            </div>
          </div>
           
        </form>-->
      </div>
    </div>
  </div>
    <script type="text/javascript">
	 $(document).ready(function() {
    $('.js-example-basic-multiple').select2();
    //$('#multiple-checkboxes').multiselect();
     $('.product').select2();
});  
var todayDate = new Date().getDate();
  $("#date_from").datetimepicker({
  timepicker: false,
  pickTime: false,
  minDate:new Date(),
maxDate: new Date(new Date().setDate(todayDate + 30)),
  closeOnDateSelect: true
});


$('input[name=\'users\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=firebase/firebase/autocomplete&token=<?php echo $token; ?>&users=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['user_id']
                    }
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'users\']').val(item['label']);
        $('input[name=\'users_id\']').val(item['value']);
    }
});
</script>
    
</div>
<?php echo $footer; ?>