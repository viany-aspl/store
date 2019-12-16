<?php echo $header; ?><?php echo $column_left; ?>
 
            
            <?php if (isset($_SESSION['unsuccess_message'])) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['unsuccess_message']; unset($_SESSION['unsuccess_message']); ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
	<?php if (isset($_SESSION['delete_unsuccess_message'])) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['delete_unsuccess_message']; unset($_SESSION['delete_unsuccess_message']); ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
                <div class="card" style="margin-bottom: 10px;">
                        <div class="card-header" style="padding-top: 10px;padding-left: 20px;padding-bottom: 10px;">
                            <h1 style="float: left;">Register Sale </h1>
                            <div class="pull-right" style="float: right;">
									<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo "Cancel"; ?>" class="btn btn-default"><i class="zmdi zmdi-mail-reply"></i></a></div>
              
								</div>
                        </div>
                <div class="card">
                        <div class="card-block" style="padding-top: 10px;">
                            
                            <form method="post" onsubmit="return check_form('submit');" method="post" action="<?php echo $action; ?>"> 
							
                               <div class="col-sm-12">
                                    <label>Category</label> 
										<div class="input-group">
											<div class="form-group">
												<select  onchange="set_value();" name="category_id" id="input-category_id" class="form-control">
													<option value="">Select Category</option>
													<?php foreach ($categories as $category) { ?>
														<option value="<?php echo $category['category_id']; ?>"<?php if(isset($category_id) && ($category['category_id'] == $category_id)){ ?>selected="selected"<?php } ?>><?php echo $category['name']; ?></option>
													<?php
													}
													?>
												</select>
												<input type="hidden" name="category_name" id="category_name" value="<?php if(!empty($category_name)){ echo $category_name; } else { echo $categories['name']; } ?>" />
											</div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">
										<label class="custom-control custom-radio">
											<span class="custom-control-description">I want to Sell</span>
											<input name="action" value="sell" class="custom-control-input" required type="radio">
											<span class="custom-control-indicator"></span>
                                
										</label>
										<label class="custom-control custom-radio">
											<span class="custom-control-description">I want to Rent</span>
											<input name="action" value="rent" class="custom-control-input" required type="radio">
											<span class="custom-control-indicator"></span>
											
										</label>
                                </div>
									
									<div class="col-sm-12 mt-3">
                                    
                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" name="product_name" id="product_name" class="form-control" value="<?php echo $product_name; ?>" placeholder="Product Name">
                                           <input type="hidden" id="product_id" name="product_id" value="<?php echo $filter_product_id; ?>" >
                                       </div>
                                    </div>
                                </div>
								
								<div class="row">
									<div class="col-sm-6  mt-3">
										
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-calendar"></i></span>
                                        <div class="form-group">
                                            <input type="text" id="validate" readonly="readonly" name="validate" value="<?php echo $validate; ?>"class="form-control date-picker" placeholder="Offer valid till">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
									
                                </div>
									<div class="col-sm-6  mt-3">
										<div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" onkeypress="return isNumber(event)" name="quantity" id="quantity" placeholder="Min Qty" autocomplete="off"  >
                                           <i class="form-group__bar"></i>
												
                                       </div>
                                    </div>
                                </div>
								</div>
								<div class="row">
									
									<div class="col-sm-6  mt-3">
										<div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" onkeypress="return isNumber(event)" name="offer_price" id="offer_price" placeholder="Expected offer price" autocomplete="off"  >
                                           <i class="form-group__bar"></i>
												
                                       </div>
                                    </div>
                                </div>
								
									<div class="col-sm-6" style="margin-top: 28px;" >
										<label class="custom-control custom-checkbox">
											<span class="custom-control-description">Price Negotiable</span>
											<input name="negotiation" value="1" class="custom-control-input" type="checkbox">
											<span class="custom-control-indicator"></span>
											
										</label>
                                </div>
								</div>
								<div class="row mt-3">
								<label class="col-sm-12" style="color: silver;" >Share Details With </label>
									<div class="col-sm-6">
										<label class="custom-control custom-checkbox">
											<span class="custom-control-description">Reailers</span>
											<input name="share_detail[]" id="Reailers" value="Reailers" class="custom-control-input"  type="checkbox">
											<span class="custom-control-indicator"></span>
											
										</label>
                                </div>
								<div class="col-sm-6">
										<label class="custom-control custom-checkbox">
											<span class="custom-control-description">Unnati</span>
											<input name="share_detail[]" id="Unnati" value="Unnati" class="custom-control-input"  type="checkbox">
											<span class="custom-control-indicator"></span>
											
										</label>
                                </div>
								</div>
								<br/>
								<h3 class="card-block__title">Remarks</h3>
								
								<div class="form-group">
                                <textarea id="remarks" name="remarks" style="overflow: hidden; word-wrap: break-word; height: 50px;" class="form-control" placeholder="Remarks"></textarea>
                                <i class="form-group__bar"></i>
                            </div>
							
                                <div class="col-sm-12 mt-1">
										<input type="hidden" name="lat" id="lat" value="" />
										<input type="hidden" name="lng" id="lng" value="" />
                                <button type="submit" class="btn btn-primary" onclick="return check_form();" style="float: right;">Submit</button>
                                </div>
								
                        </form>
                        </div>
                    </div>
<style>
	label
	{
		font-weight: bold !important;
	}
	.important 
	{
    background-color: rgba(243, 243, 243, 0.52) !important;
	}
	</style>
<?php echo $footer; ?>
<script>

function check_form(actt)
{
	var lat=$("#lat").val();
	var lng=$("#lng").val();
	var category_id=$("#input-category_id").val();
	var product_id=$("#product_id").val();
	var validate=$("#validate").val();
	var quantity=$("#quantity").val();
	var offer_price=$("#offer_price").val();
	
	if((!lat) || (!lng))
	{
		alertify.error('Please share your location');
		getLocation();
		return false;
	}
	else if(!category_id)
	{
		alertify.error('Please select category');
		$("#input-category_id").focus();
		return false;
	}
	else if(!product_id)
	{
		alertify.error('Please select product');
		$("#product_name").focus();
		
		return false;
	}
	else if(!validate)
	{
		alertify.error('Please select validate date');
		
		return false;
	}
	else if(!quantity)
	{
		alertify.error('Please enter quantity');
		$("#quantity").focus();
		return false;
	}
	else if(!offer_price)
	{
		alertify.error('Please enter offer price');
		$("#offer_price").focus();
		
		return false;
	}
	if(($("#Reailers").prop('checked') != true) && ($("#Unnati").prop('checked') != true))
	{
		alertify.error('Please select atleast one for Share details');
		$("#Reailers").focus();
		return false;
	}
	
	else
	{
		if(actt=="submit")
		{
			$(".page-loader").addClass("important");
			$(".page-loader").append('<span id="please_wait_span" style="margin-top: 73px; text-align: center; margin-right: 0px;" class="loading_text">Please wait. Please do not close your browser or click back button ..</span>');
			$(".page-loader").show();
			return true;
		}
		else
		{
			return true;
		}
	}
	return false;
}
</script>
<script type="text/javascript">

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
	if((charCode==46))
	{
		return true;
	}
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
$(document).ready(function() 
{ 
    $('input[name=\'product_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=pos/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			beforeSend: function()
			{
				$('input[name=\'product_id\']').val('');
			},
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
		$('input[name=\'product_name\']').val(item['label']);
                $('input[name=\'product_id\']').val(item['value']);
	}
});
});
function set_value()
{
	var selectedText = $("#input-category_id option:selected").html();
	$("#category_name").val(selectedText);
}
getLocation();

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) 
{
	$("#lat").val(position.coords.latitude);
	$("#lng").val(position.coords.longitude);
    
}
</script>