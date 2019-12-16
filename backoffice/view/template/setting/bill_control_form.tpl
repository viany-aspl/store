<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
         <div class="row">
                <label class="col-sm-8 control-label" style="text-align: right;font-size: 12px;color: red;"> Billing Status (All Stores)</label>
                <div class="col-sm-1">
				<img id="processing_img" src="view/image/processing_image.gif" style="width: 50px;display: none" >
				
				<span id="processing_txt" style="display: none;">Please Wait.. </span>
                  <label class="switch" id="switch"   >
						
						<input type="checkbox" value="<?php echo $currentstatusALL; ?>" <?php if($currentstatusALL=='1'){ ?> checked <?php } ?> id="billing_control" >
						<span class="slider round"style="width: 60px;"></span>
					</label>
					
				
                </div>
              
              </div>
      </div>
      <h1> Billing Control </h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> Billing Control</h3>

	<!-- <button type="button" style="margin-top: -10px;" id="button-download" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Download</button> -->

      
        
           </div>     
       
           
      <div class="panel-body">

	<div class="well">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-end">Store Name</label>
                <div class="input-group">
                
                  <span class="input-group-btn">
                      
                  <input type="text" style="text-transform: uppercase" value="<?php echo $filter_store; ?>" placeholder="Store Name" name="filter_store" id="filter_store" class="form-control" />
                  
                
                  </span></div>
              </div>
              
            </div>
            <div class="col-sm-6">
                
              
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> Filter</button>
            </div>
          </div>
        </div>

        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-store">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  
                  <td class="text-left">Store Name</td>
                    <td class="text-left">Store ID</td>
					<td class="text-left">Telephone</td>
					<td class="text-left">Billing Closed Msg</td>
                    <td class="text-left">Billing Status</td>
                    
                </tr>
              </thead>
              <tbody>
                <?php if ($stores) { ?>
                <?php foreach ($stores as $store) { //print_r($store["store_id"]); ?>
                <tr>
                  
					<td class="text-left"><?php echo strtoupper( $store['name']); ?></td>
					<td class="text-left"><?php echo $store['store_id']; ?></td>
					<td class="text-left"><?php echo $store['config_telephone']; ?></td>
					<td class="text-left"><?php echo $store['msg']; ?></td>
                  <td class="text-left">
						<img id="processing_img" class="processing_img" src="view/image/processing_image.gif" style="width: 50px;display: none" >
							
                  <label class="switch" id="switch">
						
						<input onchange="return change_status('<?php echo $store['currentstatus']; ?>','<?php echo $store['store_id']; ?>');" type="checkbox" value="<?php echo $store['currentstatus']; ?>" <?php if($store['currentstatus']=='1'){ ?> checked <?php } ?> id="billing_control<?php echo $store['store_id']; ?>" >
						<span class="slider round"></span>
					</label>
					</td>
                  
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
        </form>
      </div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"> 
             
              <?php echo $results; ?>  </div>
        </div>
    </div>
  </div>
</div>

         <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" onclick="return reset_billing();" id="partner_cncl_btn2" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><b>Billing Closed Message</b></h4>
        </div>
        <div class="modal-body">
        <div class="form-group">
            
            <div class="col-sm-12">
              <input type="hidden" name="b_store_id" id="b_store_id" value="" />
				<textarea class="form-control" placeholder="Billing Closed Message" name="closed_message" id="closed_message"></textarea>
              </div>
          </div>

            
            <div class="text-right " style="height: 106px;">
			<br/>
                <input type="button" style="margin-top: 10px;" id="sbmt_btn"  class="btn btn-primary" onclick="return submit_form();" value="Submit" />
                <button type="button"  style="margin-top: 10px;" id="cncl_btn" onclick="return reset_billing();" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <img class="cr_img_close" id="cr_img_close" src="view/image/processing_image.gif" style="margin-top: 10px;float: right;height: 60px;display: none;"/>
            <br/><br/>
            </div>
        
        </div>
        
      </div>
      
    </div>
  </div>

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 32px;
}

.switch input {display:none;}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
<script type="text/javascript">
	function reset_billing()
	{
		var b_store_id=$("#b_store_id").val();
		var currentstatus=$("#billing_control"+b_store_id).val();
		if(currentstatus=='0')
		{
			$('#billing_control'+b_store_id).prop('checked', false);
		}
		if(currentstatus=='1')
		{
			$('#billing_control'+b_store_id).prop('checked', true);
		}
		return false;
	}
	function submit_form()
	{
		var closed_message=$("#closed_message").val();
		var b_store_id=$("#b_store_id").val();
		if(!b_store_id)
		{
			alertify.error('No Stores Selecetd');
			return false;
		}
		if(!closed_message)
		{
			alertify.error('Please enter a billing closed message');
			return false;
		}
		alertify.confirm('Are you Sure ! You want to close the Billing for all users?', function (e) 
		{
		if (e) 
		{  
			if(b_store_id=='ALL')
			{
				var sbmt_url='index.php?route=setting/billcontrol/updatestatusALL&token=<?php echo $token; ?>&currentstatus=' + encodeURIComponent(1)+'&closed_message=' + encodeURIComponent(closed_message);
			}
			else
			{
				var sbmt_url='index.php?route=setting/billcontrol/updatestatus&token=<?php echo $token; ?>&currentstatus=' + encodeURIComponent(1)+'&closed_message=' + encodeURIComponent(closed_message)+'&store_id=' + encodeURIComponent(b_store_id);
			}
			
			$.ajax({
			url: sbmt_url,
		
			beforeSend: function() 
			{
				$("#sbmt_btn").hide();
				$("#cncl_btn").hide();
				if(b_store_id=='ALL')
				{
					$("#switch").hide();
					$("#cr_img_close").show();
					
				}
				else
				{
					$(".switch").hide();
					$(".cr_img_close").show();
				}
				
			
			},
			complete: function() 
			{
				alertify.success('Updated Successfully');
			},
			success: function(html) 
			{
				
				location.reload();
			},
			error: function(xhr, ajaxOptions, thrownError) 
			{
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
			});
  
		} ///////if of confirm end here
		else 
		{
			alertify.error('Canceled by User');
			return false;
		}
		});
		return false;
	}
 function change_status(currentstatus,store_id)
 {
	if(currentstatus=='1')
	{
		$("#closed_message").val("");
		$("#b_store_id").val(store_id);
		$('#myModal').modal('show');
		
		$('#billing_control'+store_id).prop('checked', true);
		
		return false;
	}
	else
	{
	alertify.confirm('Are you Sure ! You want to open the Billing ?', function (e) 
	{
    if (e) 
	{  
	  $.ajax({
		url: 'index.php?route=setting/billcontrol/updatestatus&token=<?php echo $token; ?>&currentstatus=' + encodeURIComponent(currentstatus)+'&store_id=' + encodeURIComponent(store_id),
		
		beforeSend: function() 
		{
			$(".switch").hide();
			$(".processing_img").show();
		},
		complete: function() 
		{
			alertify.success('Updated Successfully');
		},
		success: function(html) 
		{	
			location.reload();
		},
		error: function(xhr, ajaxOptions, thrownError) 
		{
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
  
    } ///////if of confirm end here
	else 
	{
		if(currentstatus=='0')
		{
			$('#billing_control'+store_id).prop('checked', false);
		}
		if(currentstatus=='1')
		{
			$('#billing_control'+store_id).prop('checked', true);
		}
		//location.reload();
        alertify.error('Canceled by User');
		return false;
    }
	});
	}
}
  $('#billing_control').on('change', function () 
  {
	var currentstatus=this.value
	//alert(currentstatus);
	if(currentstatus=='1')
	{
		$("#closed_message").val("");
		$("#b_store_id").val("ALL");
		$('#myModal').modal('show');
		if(currentstatus=='0')
		{
			$('#billing_control').prop('checked', false);
		}
		if(currentstatus=='1')
		{
			$('#billing_control').prop('checked', true);
		}
		return false;
	}
	else
	{
		
	alertify.confirm('Are you Sure ! You want to open the Billing  for all users?', function (e) 
	{
    if (e) 
	{  
	  $.ajax({
		url: 'index.php?route=setting/billcontrol/updatestatusALL&token=<?php echo $token; ?>&currentstatus=' + encodeURIComponent(currentstatus),
		
		beforeSend: function() {
			$("#switch").hide();
			$("#processing_img").show();
			
			
		},
		complete: function() {
			alertify.success('Updated Successfully');
		},
		success: function(html) {
			location.reload();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
  
    } ///////if of confirm end here
	else 
	{
		if(currentstatus=='0')
		{
			$('#billing_control').prop('checked', false);
		}
		if(currentstatus=='1')
		{
			$('#billing_control').prop('checked', true);
		}
		//location.reload();
        alertify.error('Canceled by User');
		return false;
    }
	});
	}
});
 
$('#button-filter').on('click', function() {
	url = 'index.php?route=setting/billcontrol&token=<?php echo $token; ?>';
	
        	var filter_store = $('#filter_store').val();
	
	if (filter_store!="") {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}

	
       
	location = url;
});

</script> 

<script type="text/javascript">
$('input[name=\'filter_store\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=setting/store/autocomplete&token=<?php echo $token; ?>&filter_store=' +  encodeURIComponent(request),
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['name'],
                        value: item['store_id']
                    }
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'filter_store\']').val(item['label']);
                //$('input[name=\'filter_name_id\']').val(item['value']);
    }
});
</script>
<?php echo $footer; ?> 