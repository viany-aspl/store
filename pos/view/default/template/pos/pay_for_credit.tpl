<?php echo $header; ?><?php echo $column_left; ?>
 
            
            <?php if ($error_warning) { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>
            <?php if ($success) { ?>
                <div class="alert alert-success" style="margin-bottom: 20px;"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>
               
                <div class="card">
                        <div class="card-block">
                            <p>Pay for Credit</p>

                            <br>
                            <form method="post">
							 
							<div class="row">
                                <div class="col-sm-12">
                                    

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-smartphone-android"></i></span>
                                        <div class="form-group">
                                            <input id="mobile_number" maxlength="10"  type="text" class="form-control" onkeypress="return isNumber(event)"  autocomplete="off"  name="mobile_number" value="<?php echo $mobile_number; ?>" placeholder="Mobile">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
								</div>
								<div class="row mt-3">
                                <div class="col-sm-12">
                                    

                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="name"  autocomplete="off"  name="name" value="<?php echo $name; ?>" placeholder="Name">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
								</div>
								<div class="row mt-3" style="display: none;">
                            <div class="col-sm-12" >
                                    <label>Aadhar</label>

                                    <div class="input-group">
                                        <span class="input-group-addon">@</span>
                                        <div class="form-group">
                                            <input type="text"  autocomplete="off"  class="form-control" name="aadhar" id="aadhar" value="N/A" placeholder="Aadhar">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
								</div>
								<div class="row mt-3">
                            <div class="col-sm-12">
                                    

                                    <div class="input-group">
                                        <span style="font-size: 10px;padding-left: 11px !important;padding-right: 10px !important;" class="input-group-addon"><?php echo RUPPE_SIGN; ?></span>
                                        <div class="form-group">
                                            <input type="text" autocomplete="off" maxlength="6"  class="form-control" onkeypress="return isNumber(event)" name="cash" id="cash" value="<?php echo $cash; ?>" placeholder="Cash">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                </div>
								<div class="row mt-3">
                                <div class="col-sm-12">
                                  <br/>  
                                <button type="submit" onclick="return check_form();" class="btn btn-primary" style="float: right;">Make Payment</button>
                                </div>
								</div>
								
                        </form>
                        </div>
                    </div>

<?php echo $footer; ?>
<script>
function check_form()
{
	var mobile_number=$("#mobile_number").val();
	if(!mobile_number)
	{
		alertify.error('Please enter mobile number');
		return false;
	}
	if(mobile_number.length<10)
	{
		alertify.error('Mbile number should be min 10 digit');
		return false;
	}
	var name=$("#name").val();
	if(!name)
	{
		alertify.error('Please enter name');
		return false;
	}
	var aadhar=$("#aadhar").val();
	if(!aadhar)
	{
		alertify.error('Please enter aadhar');
		return false;
	}
	var cash=$("#cash").val();
	if(!cash)
	{
		alertify.error('Please enter cash');
		return false;
	}
	$(".page-loader").addClass("important");
	$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
	$(".page-loader").show();
	return true;
}
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
</script>
