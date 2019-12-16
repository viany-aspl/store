<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
       
      </div>
      
        <h3>Incoming Call</h3>
      <i class="<?php echo $tool_tip_class; ?> " data-toggle="tooltip" style="<?php echo $tool_tip_style; ?>" title="<?php echo $tool_tip; ?>"></i>
 
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
        <style>
            .form-group{
                margin-bottom: 0px  !important;
            }
           
    #accordion{
        text-align: left;
        
    }
    #titlecol{color:#ff6d18;}
        </style>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-12">
             <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-start">Start Date</label>
                <div class="input-group date">
                  <input type="text" name="filter_start_date" value="<?php echo $filter_start_date; ?>" placeholder="Start Date" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
             </div>
              <div class="col-sm-4"> 
              <div class="form-group">
                <label class="control-label" for="input-date-end">End Date</label>
                <div class="input-group date">
                  <input type="text" name="filter_end_date" value="<?php echo $filter_end_date; ?>" placeholder="End Date" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              </div>
             <div class="col-sm-2"> 
              <div class="form-group">
                <label class="control-label" for="input-date-end">Mobile Number</label>
                
                  <input type="text" name="filter_number" value="<?php echo $filter_number; ?>" placeholder="Mobile Number"  id="filter_number" class="form-control" />
                  
              </div>
              </div>
			  <!--<div class="col-sm-2"> 
              <div class="form-group">
                <label class="control-label" for="input-date-end">Status</label>
                
				<select name="filter_status" id="filter_status" class="form-control">
				<option value="">SELECT</option>
				
				<?php foreach($callstatus as $call_status) { ?>
					<option <?php if($filter_status==$call_status['STATUS_ID']) { ?>selected="selected" <?php } ?> value="<?php echo $call_status['STATUS_ID']; ?>" ><?php echo $call_status['STATUS_NAME']; ?></option>
				<?php } ?>
				</select>
                  
                 
              </div>
              </div>-->
                <div class="col-sm-2" >
              <button type="button" id="button-search" style="margin-top:19% " class="btn btn-primary pull-right"><i class="fa fa-search"></i> Search</button>
            </div>
          
          </div>
        </div>
        </div>
        <div class="row">
         <div class="col-sm-6 text-right">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left">Ticket ID</td>
                  <td class="text-left">Number</td>
                  <td class="text-left">Register Date</td>
                  <td class="text-left">Channel</td>   
                  <td class="text-left">Status</td>                                  
                </tr>
              </thead>
              <tbody>
                <?php if ($orders) { ?>
                <?php foreach ($orders as $order) { //print_r($order); ?>
                <tr>
                  <td class="text-left"><a href="#" onclick="return show_order_data(<?php echo $order['mobile']; ?>,<?php echo $order['status_id']; ?>,<?php echo $order['transid']; ?>,'<?php echo $order['channel']; ?>','<?php echo $order['Categories']; ?>','<?php echo $order['Type']; ?>','<?php echo $order['query']; ?>','<?php echo $order['first_name']; ?>','<?php echo $order['last_name']; ?>','<?php echo $order['email']; ?>','<?php echo $order['image_1']; ?>','<?php echo $order['image_2']; ?>');"><?php echo $order['transid']; ?></a></td>  
                  <td class="text-left"><a href="#" onclick="return show_order_data(<?php echo $order['mobile']; ?>,<?php echo $order['status_id']; ?>,<?php echo $order['transid']; ?>,'<?php echo $order['channel']; ?>','<?php echo $order['Categories']; ?>','<?php echo $order['Type']; ?>','<?php echo $order['query']; ?>','<?php echo $order['first_name']; ?>','<?php echo $order['last_name']; ?>','<?php echo $order['email']; ?>','<?php echo $order['image_1']; ?>','<?php echo $order['image_2']; ?>');"><?php echo $order['mobile']; ?></a></td>
                  <td class="text-left"><?php echo $order['date_added']; ?></td>
                  <td class="text-left"><?php echo $order['channel']; ?></td>
                  <td class="text-left"><?php echo $order['status']; ?></td>
                 
           
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
        <div class="row">
          <div class="col-sm-12 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-12 text-right"><?php echo $results; ?></div>
        </div>
        </div>
            <div class="col-sm-6 text-right" id="dtldiv" style="display:none">
                <!--<ul class="nav nav-tabs">
         
          <li class="active"><a href="#tab-form"  data-toggle="tab">Fill Form</a></li>
          
        </ul>-->
            <div class="tab-content">
                <div class="tab-pane active" id="tab-form" style="min-height: 200px;">
                    <div id="display_before_call">
                        Please Select a Number
                        
                    </div>
                    <div id="display_after_call" style="display: none;">
               
               <input type="hidden" name="transid" id="transid" />
               <input type="hidden" name="mobile_number" id="mobile_number" />
               <input type="hidden" name="current_call_status" id="current_call_status" />
			   <input type="hidden" name="channel" id="channel" />
               <input type="hidden" name="logged_user_data" id="logged_user_data" value="<?php echo $logged_user_data; ?>" />
               
               <div class="col-sm-12"> 
               <div class="form-group" style="text-align: left;">
                <label class="control-label" for="input-order-status">Call Status </label> - <span id="span_for_number"></span>
                <select name="call_status" onchange="return change_form(this.value);" id="call-status" class="form-control">
                 
                 <option value="" selected="selected">SELECT</option>
                 <?php foreach($callstatus as $calls) {  ?>
                 <option value="<?php echo $calls["STATUS_ID"]; ?>"><?php echo $calls["STATUS_NAME"]; ?></option>
                
                 <?php } ?>
                
                 
                </select>
              </div>
               </div>
               <div id="form_div" style="display: none;">
            
             <div class="form-group" style="text-align: left;">
				<div class="col-sm-6">
				 <div class="form-group" style="text-align: left;">
                <label class="control-label" for="input-order-status">Ticket Status </label> 
                <select name="ticket_status"  id="ticket-status" class="form-control">
                 
                 <option value="" selected="selected">SELECT</option>
                 <?php foreach($ticketsatus as $ticket) {  ?>
                 <option value="<?php echo $ticket["STATUS_ID"]; ?>"><?php echo $ticket["STATUS_NAME"]; ?></option>
                
                 <?php } ?>
                
                 
					</select>
					</div>
				</div>
				<div class="col-sm-6">
				 <div class="form-group" style="text-align: left;">
                <label class="control-label" for="input-order-status">Customer Relation </label> 
                <select name="customer_relation" onchange="return change_relation_form(this.value);" id="customer_relation" class="form-control">
                 
					<option value="" selected="selected">SELECT</option>
                 
					<option value="Farmer">Farmer</option>
					<option value="Retailer">Retailer</option>
                 
					</select>
					</div>
				</div>
				<div class="col-sm-6" id="register_mobile_div" style="display: none;">
                <label class="control-label" for="input-order-status">Registered Mobile</label>
                <input name="registered_mobile" class="form-control" id="registered_mobile" placeholder="Registered Mobile" />
              </div>
			  <div class="col-sm-6">
				 <div class="form-group" style="text-align: left;">
                <label class="control-label" for="input-order-status">Is SE visit Required </label> 
                <select name="visit_required"  id="visit_required" class="form-control">
                 
					<option value="" selected="selected">SELECT</option>
                 
					<option value="Yes">Yes</option>
					<option value="No">No</option>
                 
					</select>
					</div>
				</div>
				<div class="col-sm-6">
				 <div class="form-group" style="text-align: left;">
                <label class="control-label" for="input-order-status">Categories </label> 
					<input type="hidden" name="Category_name" id="Category_name" /> 
                <select onchange="return set_cat_name(this.value);" name="Categories"  id="Categories" class="form-control">
                 
					<option value="" selected="selected">SELECT</option>
                 <?php foreach($Categories as $Category){ ?>
					<option value="<?php echo $Category['id'] ?>"><?php echo $Category['name'] ?></option>
					
				 <?php } ?>
					</select>
					</div>
				</div>
				<div class="col-sm-6">
				 <div class="form-group" style="text-align: left;">
                <label class="control-label" for="input-order-status">Type </label> 
				<input type="hidden" name="Type_name" id="Type_name" />
                <select onchange="return set_type_name(this.value);"  name="Type"  id="Type" class="form-control">
                 
					<option value="" selected="selected">SELECT</option>
                 
					<?php foreach($CallTypes as $Type){ ?>
					<option value="<?php echo $Type['sid'] ?>"><?php echo $Type['name'] ?></option>
					
				 <?php } ?>
				 
					</select>
					</div>
				</div>
             <div class="col-sm-6">
                <label class="control-label" for="input-order-status">First Name</label>
                <input name="first_name" class="form-control" id="first_name" placeholder="First Name" />
              </div>
              </div>
              <div class="col-sm-6">
              <div class="form-group" style="text-align: left;">
                <label class="control-label" for="date-added">Last Name</label>
                
                  <input name="last_name" class="form-control" id="last_name" placeholder="Last name" />
               
              </div>
              </div>      
               <div class="col-sm-6">
              <div class="form-group" style="text-align: left;">
                <label class="control-label" for="date-added">State</label>
                
                  <input name="State" class="form-control" id="State" placeholder="State" />
               
              </div>
              </div> 
			  <div class="col-sm-6">
              <div class="form-group" style="text-align: left;">
                <label class="control-label" for="date-added">District</label>
                
                  <input name="District" class="form-control" id="District" placeholder="District" />
               
              </div>
              </div>
			  <div class="col-sm-6">
              <div class="form-group" style="text-align: left;">
                <label class="control-label" for="date-added">Village</label>
                
                  <input name="Village" class="form-control" id="Village" placeholder="Village" />
               
              </div>
              </div>
             
        <div class="col-sm-6">
               	<div class="form-group" style="text-align: left;">
                <label class="control-label" for="input-order-status">Query</label>
                <textarea name="query" class="form-control" id="query" placeholder="Query"></textarea>
              </div>
        </div>
         <div class="col-sm-6">
               	<div class="form-group" style="text-align: left;">
                <label class="control-label" for="input-order-status">Solution</label>
                <textarea name="solution" class="form-control" id="solution" placeholder="Solution" ></textarea>
              </div>
        </div>
		<div class="col-sm-6">
           <div class="form-group" style="text-align: left;">
               <div id="img_1" style="float: left;margin-right: 10px;"></div>
				 <div id="img_2" style="margin-top: 5px;"></div>
           </div>
        </div>
         
                </div>
               <div class="col-sm-6 pull-right">
						<br/>
                   <button type="button" style="display: none;" id="button-filter" class="btn btn-primary pull-right" onclick="return submitformdata();">Submit</button>
                </div>
                </div>
               </div>
     
            </div>
      </div>
        
      </div>
          
    </div>
  </div>
  <script type="text/javascript">
  
  function set_cat_name(cat_id)
  {
	  var selectedText = $("#Categories option:selected").html();
	  
	  $("#Category_name").val(selectedText);
	  return false;
  }
  function set_type_name(type_id)
  {
	  var selectedText = $("#Type option:selected").html();
	  $("#Type_name").val(selectedText);
	  return false;
  }
	function submitformdata()
    {   
        url = 'index.php?route=ccare/incommingcall/submit_incoming_call_data&token=<?php echo $token; ?>';
		var transid = $('input[name=\'transid\']').val();
	
		if (transid ) 
		{
			url += '&transid=' + encodeURIComponent(transid);
		}
		var mobile_number = $('input[name=\'mobile_number\']').val();
	
		if (mobile_number) 
		{
			url += '&mobile_number=' + encodeURIComponent(mobile_number);
		}
        var current_call_status = $('input[name=\'current_call_status\']').val();
	
		if (current_call_status) 
		{
			url += '&current_call_status=' + encodeURIComponent(current_call_status);
		}
	
		var call_status = $('select[name=\'call_status\']').val();
	
		if (call_status != '*') 
		{
			url += '&call_status=' + encodeURIComponent(call_status);
		}	
		var ticket_status = $('#ticket-status').val();

		if (ticket_status) 
		{
			url += '&ticket_status=' + encodeURIComponent(ticket_status);
		}
		var channel = $('#channel').val();

		if (channel) 
		{
			url += '&channel=' + encodeURIComponent(channel);
		}
		var Categories = $('#Categories').val();

		if (Categories) 
		{
			url += '&Categories=' + encodeURIComponent(Categories);
		}
		
		
		var Type = $('#Type').val();

		if (Type) 
		{
			url += '&Type=' + encodeURIComponent(Type);
		}
		var Category_name = $('#Category_name').val();

		if (Category_name) 
		{
			url += '&Category_name=' + encodeURIComponent(Category_name);
		}
		var Type_name = $('#Type_name').val();

		if (Type_name) 
		{
			url += '&Type_name=' + encodeURIComponent(Type_name);
		}
        if(( (call_status==27)) && (ticket_status==""))
        {
            alertify.error('Please select ticket status');
            $('#ticket-status').focus();
            return false;
        }
		var customer_relation = $('#customer_relation').val();

		if (customer_relation) 
		{
			url += '&customer_relation=' + encodeURIComponent(customer_relation);
		}
        if(( (call_status==27)) && (customer_relation==""))
        {
            alertify.error('Please select customer relation');
            $('#customer_relation').focus();
            return false;
        }
		var registered_mobile = $('#registered_mobile').val();

		if (registered_mobile) 
		{
			url += '&registered_mobile=' + encodeURIComponent(registered_mobile);
		}
        if(( (call_status==27)) &&(customer_relation=='Retailer') && (registered_mobile==""))
        {
            //alertify.error('Please enter Registered mobile number');
            //$('#registered_mobile').focus();
            //return false;
        }
		var visit_required = $('#visit_required').val();
	
		if (visit_required) 
		{
			url += '&visit_required=' + encodeURIComponent(visit_required);
		}
        if(( (call_status==27)) && (visit_required==""))
        {
            alertify.error('Please select Is SE visit Required ');
            $('#visit_required').focus();
            return false;
        }
		if(( (call_status==27)) && (Categories==""))
        {
            alertify.error('Please select Categories');
            $('#Categories').focus();
            return false;
        }
		if(( (call_status==27)) && (Type==""))
        {
            alertify.error('Please select Type');
            $('#Type').focus();
            return false;
        }
        var first_name = $('input[name=\'first_name\']').val();

		if (first_name) 
		{
			url += '&first_name=' + encodeURIComponent(first_name);
		}
        if(( (call_status==27)) && (first_name==""))
        {
            alertify.error('Please enter First name');
            $('input[name=\'first_name\']').focus();
            return false;
        }
        var last_name = $('input[name=\'last_name\']').val();

		if (last_name) 
		{
			url += '&last_name=' + encodeURIComponent(last_name);
		}
        if(((call_status==27)) && (last_name==""))
        {
            alertify.error('Please enter Last name');
            $('input[name=\'last_name\']').focus();
            return false;
        }
		var State = $('input[name=\'State\']').val();

		if (State) 
		{
			url += '&State=' + encodeURIComponent(State);
		}	
		if(( (call_status==27)) && (State==""))
        {
            alertify.error('Please enter State name');
            $('input[name=\'State\']').focus();
            return false;
        }
		var District = $('input[name=\'District\']').val();

		if (District) 
		{
			url += '&District=' + encodeURIComponent(District);
		}	
		if(( (call_status==27)) && (District==""))
        {
            alertify.error('Please enter District name');
            $('input[name=\'District\']').focus();
            return false;
        }
		var Village = $('input[name=\'Village\']').val();

		if (Village) 
		{
			url += '&village=' + encodeURIComponent(Village);
		}	
		if(( (call_status==27)) && (Village==""))
        {
            alertify.error('Please enter Village name');
            $('input[name=\'Village\']').focus();
            return false;
        }
	
        var query = $('#query').val();
	
		if (query) 
		{
			url += '&query=' + encodeURIComponent(query);
		}
        if(( (call_status==27)) && (query==""))
        {
            alertify.error('Please enter query');
            $('#query').focus();
            return false;
        }
        var solution = $('#solution').val();
	
		if (solution) 
		{
			url += '&solution=' + encodeURIComponent(solution);
		}
        if(( (call_status==27)) && (solution==""))
        {
            alertify.error('Please enter solution');
            $('#solution').focus();
            return false;
        }
		
        
        var logged_user_data = $('#logged_user_data').val();
	
		if (logged_user_data) 
		{
			url += '&logged_user_data=' + encodeURIComponent(logged_user_data);
		}
        
        
        if( (call_status==27))
        {
			if((mobile_number) && (ticket_status) && (customer_relation) && (call_status) && (first_name) )
			{
				//alert(url);
				location = url;
			}
			else
			{
			alertify.error('Please fill all the required fields');
			}
        }
        else if(call_status!="")
        {
			location = url;
        }
        //alert(url);
        return false;
    }
	function change_relation_form(valll)
	{	
		if(valll=='Farmer')
		{
			$("#register_mobile_div").hide();
			$("#first_name").val('');
			$("#last_name").val('');
			$("#registered_mobile").val('');
			return false;
		}
		else
		{
			var mobile=$("#mobile_number").val();
			 $.ajax({
            url: 'index.php?route=ccare/incommingcall/getretailer_info&token=<?php echo $token; ?>&mobile=' +  encodeURIComponent(mobile),
            dataType: 'json',
            success: function(json) 
			{
                var firstname= json['firstname'];
				var lastname=json['lastname'];
				var username=json['username'];
				if(firstname)
				{
					$("#first_name").val(firstname);
				}
				if(lastname)
				{
					$("#last_name").val(lastname);
				}
				if(username)
				{
					$("#registered_mobile").val(username);
				}
				
            }
			});
			$("#register_mobile_div").show();
			return false;
		}
	}
  
$('#button-search').on('click', function() {
	url = 'index.php?route=ccare/incommingcall&token=<?php echo $token; ?>'; 
	
	
	var filter_start_date = $('input[name=\'filter_start_date\']').val();
	
	if (filter_start_date) {
		url += '&filter_start_date=' + encodeURIComponent(filter_start_date);
	}

	var filter_end_date = $('input[name=\'filter_end_date\']').val();
	
	if (filter_end_date) {
		url += '&filter_end_date=' + encodeURIComponent(filter_end_date);
	}
	var filter_status = $('#filter_status').val();
	
	if (filter_status) {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}
	var filter_number = $('#filter_number').val();
	if (filter_number) {
		url += '&filter_number=' + encodeURIComponent(filter_number);
	}
	location = url;
});
</script> 
       <script>
          $('.date').datetimepicker({
      pickTime: false 
   });
    
	
 
  </script>
     

      <script>
        function change_form(selected_value)
        {
            if((selected_value=="27"))
            {
                $("#form_div").show(); 
                $("#button-filter").show();
			}
            else
            {
                if(selected_value!="")
                {
                    $("#button-filter").show(); 
                }
                else
                {
                     $("#button-filter").hide();
                }
                $("#form_div").hide();  
            }
            return false;
        }
        function show_order_data(mobile,current_call_status,transid,channel,Categories,Type,query,first_name,last_name,email,image_1,image_2)
        {   
			$("#form_div").hide(); 
			$("#display_after_call").hide();
			$("#dtldiv").hide();
			$("#call-status").val('');
			///////////////
			$("#channel").val('');
			$("#customer_relation").val('');
			$("#registered_mobile").val('');
			$("#register_mobile_div").hide();
			$("#first_name").val(first_name);
			$("#last_name").val(last_name);
			$("#State").val('');
			$("#District").val('');
			$("#Village").val('');
			$("#query").val(query);
			$("#solution").val('');
			$("#visit_required").val('');
			$("#Categories").val(Categories);
			$("#Type").val(Type);
            
			$("#dtldiv").show();
            $("#mobile_number").val(mobile);
            $("#transid").val(transid);
            $("#display_before_call").hide();
            $("#display_after_call").show();
            $("#current_call_status").val(current_call_status);
			$("#channel").val(channel);
            $("#span_for_number").html(mobile);
			//alert(image_1);
			if(image_1=="././../image/")
			{
				$("#img_1").hide();
			}
			else
			{
				$("#img_1").html("<a target='_blank' href='"+image_1+"' class='btn btn-primary'>Image 1</a>");
				$("#img_1").show();
			}
			if(image_2=="././../image/")
			{
				$("#img_2").hide();
			}
			else
			{
				$("#img_2").html("<a target='_blank' href='"+image_2+"' class='btn btn-primary'>Image 2</a>");
				$("#img_2").show();
			}
			
			return false; 
        }
          </script>
  <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
  <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script>

  
</div>
<?php echo $footer; ?>