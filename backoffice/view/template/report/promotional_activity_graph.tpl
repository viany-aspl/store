<?php echo $header; ?><?php echo $column_left; ?>


<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo 'Promotional Activity Graph';//$heading_title; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo 'Promotional Activity Graph';//$text_list; ?></h3>
        
      </div>
      <div class="panel-body">
           <div class="well">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label" for="input-date-start"><?php echo $entry_date_start; ?></label>
                <div class="input-group date" id="date_from">
                  <input type="text" name="filter_date_start"  value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" id="input-date-start" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-date-end"><?php echo $entry_date_end; ?></label>
                <div class="input-group date" id="date_to">
                  <input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" id="input-date-end" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
                
            </div>
            <div class="col-sm-6">
              
              <div class="form-group">
                <label class="control-label" for="input-name">Select Name</label>
               <select style="width: 100%" name="filter_activity_name" id="input-name" class="form-control">
				  <option value="">Select Activity</option>
                  <?php foreach ($getactivities as $activity) { ?>
                  <?php if ($activity['activityid'] == $filter_activity_name) { ?>
                  <option value="<?php echo $activity['activityid']; ?>" selected="selected"><?php echo $activity['activityname']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $activity['activityid']; ?>"><?php echo $activity['activityname']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
			  <div class="form-group">
                <label class="control-label" for="input-date-end">Representative Mobile</label>
                <input type="text" name="filter_mobile" value="<?php echo $filter_mobile; ?>" placeholder="<?php echo $entry_name; ?>" id="input-mobile" class="form-control" />
		
                 
              </div>
             
            </div>
			<div class="col-sm-6">
              
              <div class="form-group">
                <label class="control-label" for="input-name">Select Company</label>
                 <select style="width: 100%" name="filter_company" id="input-company" class="form-control">
				  <option value="">Select Company</option>
                  <?php foreach ($companies as $company) { ?>
                  <?php if ($company['manufacturer_id'] == $filter_activity_name) { ?>
                  <option value="<?php echo $company['manufacturer_id']; ?>" selected="selected"><?php echo $company['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $company['manufacturer_id']; ?>"><?php echo $company['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>

            </div>
			 <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
          </div>
        </div>
     <div class="row">
          <?php   $msChart = new FusionCharts("column2d", "", "100%", "400", "chart-container", "json", $promationchart);
               
               // calling render method to render the chart
               $msChart->render();?>
      <div id="chart-container" class="col-lg-12 col-md-12 col-sx-12 col-sm-12">
          
      </div>
          </div>
    
		  
		  
        </div>
        
      </div>
    </div>
  </div>
  <script type="text/javascript">
  
   
   function set_image(auto_activity_id)
      {
         
          $("#auto_activity_id").val(auto_activity_id);
		  //alert(auto_activity_id);
		  var url='../image/activity/'+auto_activity_id+'-1.jpg';
		  var url2='../image/activity/'+auto_activity_id+'-2.jpg';
		  var url3='../image/activity/'+auto_activity_id+'-3.jpg';
		  var url4='../image/activity/'+auto_activity_id+'-4.jpg';
		  
          $("#img1").attr("src",url);
		  $("#img2").attr("src",url2);
		  $("#img3").attr("src",url3);
		  $("#img4").attr("src",url4);
         
		  $("#img1").show();
		  $("#text1").show();
		  $("#img2").show();
		  $("#text2").show();
		  $("#img3").show();
		  $("#text3").show();
		  $("#img4").show();
		  $("#text4").show();
		 
      }  
	  
	    $("#img1").on("error",function(){
			$(this).hide();
			$("#text1").hide();
		});
		
	    $("#img2").on("error",function(){
			$(this).hide();
				$("#text2").hide();
		});
  
  
	    $("#img3").on("error",function(){
			$(this).hide();
				$("#text3").hide();
		});
  
  
	    $("#img4").on("error",function(){
			$(this).hide();
				$("#text4").hide();
		});
  $("#input-company").select2();
  $("#input-name").select2();
$('#button-filter').on('click', function() {
	url = 'index.php?route=report/promotional_activity/promotional_activity_graph&token=<?php echo $token; ?>';
	
	var filter_date_start = $('input[name=\'filter_date_start\']').val();
	
	if (filter_date_start) { 
		url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
	
	var filter_mobile = $('input[name=\'filter_mobile\']').val();
	if (filter_mobile) {
		url += '&filter_mobile=' + encodeURIComponent(filter_mobile);
	}
	
	var filter_name = $('#input-name').val();
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

      var filter_company = $('#input-company').val();//$('input[name=\'filter_activity_name\']').val();
	if (filter_company) {
		url += '&filter_company=' + encodeURIComponent(filter_company);
	}  
	location = url;
});
//--></script> 
<script type="text/javascript">
$('#button-download').on('click', function() {
    url = 'index.php?route=report/product_sales/download_excel&token=<?php echo $token; ?>';
   
    var filter_date_start = $('input[name=\'filter_date_start\']').val();
   
    if (filter_date_start) {
        url += '&filter_date_start=' + encodeURIComponent(filter_date_start);
    }

    var filter_date_end = $('input[name=\'filter_date_end\']').val();
   
    if (filter_date_end) {
        url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
    }
    var filter_name = $('#input-name').val();
	
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	} 
        var filter_name_id = $('input[name=\'filter_name_id\']').val();
        var filter_name = $('input[name=\'filter_name\']').val();

    if (filter_name_id) {
                if(filter_name!="")
                {
        url += '&filter_name_id=' + encodeURIComponent(filter_name_id);
                }
    }
       

    if (filter_name) {
        url += '&filter_name=' + encodeURIComponent(filter_name);
    }

       
    //location = url;
    window.open(url, '_blank');
});
//--></script>

<script type="text/javascript">
    var maxDate =  new Date();
    //var minDate = new Date(maxDate.getFullYear(), maxDate.getMonth(), +1); //one day next before month
  $("#date_from").datetimepicker({
  timepicker: false,
  pickTime: false,
  maxDate: maxDate,
  closeOnDateSelect: true
}).on('dp.change', function (ev) {
   
   change_to_date();
});
$("#date_to").datetimepicker({
  showClear: true,
  timepicker: false,
  pickTime: false,
  
  maxDate: maxDate,
  closeOnDateSelect: true
}).on('dp.change', function (ev) {
   
   change_to_equal_date();
});
function change_to_equal_date()
{
var frm=$("#input-date-start").val(); 
var too=$("#input-date-end").val(); 
var fromTime = new Date(frm);
var minDate = new Date(fromTime.getFullYear(), fromTime.getMonth(), +1); //one day next before month
var maxDate =  new Date(fromTime.getFullYear(), fromTime.getMonth() +1, +0); // one day before next month
var date_to=convert(maxDate);
var toTime = new Date(too);

var millisecondsPerDay = 1000 * 60 * 60 * 24;
var millisBetween = toTime.getTime()-fromTime.getTime();
var days = millisBetween / millisecondsPerDay;

//alert(fromTime+' && '+toTime+' && '+ days);

    if(new Date(frm).getTime()>new Date(too).getTime())
    {
        $("#input-date-end").val(date_to);
        alertify.error('End date can not be less then start date');
    }
    if(days>31)
    {
        $("#input-date-end").val(date_to);
        alertify.error('There can be maximum 1 month difference between start date and end date');
    }
}
function change_to_date()
{
var frm=$("#input-date-start").val();
var fromTime = new Date(frm);
var minDate = new Date(fromTime.getFullYear(), fromTime.getMonth(), +1); //one day next before month
var maxDate =  new Date(fromTime.getFullYear(), fromTime.getMonth() +1, +0); // one day before next month
var date_to=convert(maxDate);

$("#input-date-end").val(date_to);
//$("#date_to").datetimepicker('update', "2017/09/20");
//$("#date_to").removeClass("date");

$("#date_to").datetimepicker({
  showClear: true,
  timepicker: false,
  pickTime: false,
  minDate: minDate, 
  maxDate: maxDate,
  closeOnDateSelect: true
});
    
}
$('#date_from').change(function(){
   $(this).next('input.datetimepicker').destroy();

   $("#date_to").next('input.datetimepicker').datetimepicker({
       minDate:$(this).val()
   });
});
function convert(str) {
    var date = new Date(str),
        mnth = ("0" + (date.getMonth()+1)).slice(-2),
        day  = ("0" + date.getDate()).slice(-2);
    return [ date.getFullYear(), mnth, day ].join("-");
}

</script>

  <script type="text/javascript">
/*
$('.date').datetimepicker({
	pickTime: false
});
*/
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
    
<?php echo $footer; ?>