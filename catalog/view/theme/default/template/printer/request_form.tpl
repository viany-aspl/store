<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
         <!-- Vendor styles -->
        <link rel="stylesheet" href="pos/view/default/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="pos/view/default/vendors/bower_components/animate.css/animate.min.css">
        <link rel="stylesheet" href="pos/view/default/vendors/bower_components/jquery.scrollbar/jquery.scrollbar.css">
        <link rel="stylesheet" href="pos/view/default/vendors/bower_components/fullcalendar/dist/fullcalendar.min.css">
        <script src="pos/view/default/js/fusioncharts/fusioncharts.js"></script>
        <script src="pos/view/default/js/fusioncharts/fusioncharts.theme.fint.js"></script>
        
        <link rel="stylesheet" href="pos/view/default/vendors/bower_components/select2/dist/css/select2.min.css">
        <link rel="stylesheet" href="pos/view/default/vendors/bower_components/dropzone/dist/dropzone.css">
        <link rel="stylesheet" href="pos/view/default/vendors/bower_components/flatpickr/dist/flatpickr.min.css" />
        <link rel="stylesheet" href="pos/view/default/vendors/bower_components/sweetalert2/dist/sweetalert2.min.css">
        <!-- App styles -->
        <link rel="stylesheet" href="pos/view/default/css/app.min.css">
		<link rel="stylesheet" href="pos/view/default/css/style.css">
    </head>
	
	<style>
	.mt_10{margin-top:10px;} .mt_20{margin-top:20px;} .mt_30{margin-top:30px;} .mt_40{margin-top:40px;} .mt_50{margin-top:50px;}
	.mtb_10{margin:10px 0;} .mtb_20{margin:20px 0;} .mtb_30{margin:30px 0;} .mtb_40{margin:40px 0;} .mtb_50{margin:50px 0;}
	.mb_10{margin-bottom:10px;} .mb_20{margin-bottom:20px;} .mb_30{margin-bottom:30px;}
	
	.card-block__title{
	margin-bottom:15px;
	}
	
	</style>
    
    <body data-ma-theme="blue">
        <main class="container">
            <div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>

		<div class="row">
			<div class="col-md-12 mt-3">
			
				<div class="card">
				<br/>
						<form method="post"  onsubmit="return open_process_img();" action="#">
                     <div class="row" style="padding: 20px;">
							
                           <div class="col-sm-6 mb_20">
                  
                                       <h3 class="card-block__title ">Billing Name</h3>
                                  
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-account"></i></span>
                                        <div class="form-group">
                                           
                                            <input required  type="text" name="billing_name" value=""   placeholder="Name" min="10" maxlength="100"  class="form-control" />
             
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-sm-6 mb_20">
                  
                                       <h3 class="card-block__title ">Your Name</h3>
                                  
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-account-box"></i></span>
                                        <div class="form-group">
                                           
                                            <input required  type="text" name="contact_person_name" value=""   placeholder="Your Name" min="10" maxlength="10"  class="form-control" />
             
                                        </div>
                                    </div>
                                </div>
                               
                           <div class="col-sm-6 mb_20">
                  
                                       <h3 class="card-block__title ">Contact Number</h3>
                                  
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-phone"></i></span>
                                        <div class="form-group">
                                           
                                            <input required  type="text"  name="contact_number" value=""   placeholder="Your Mobile Number" class="form-control" />
             
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-sm-6 mb_20">
                  
                                       <h3 class="card-block__title ">GSTN</h3>
                                  
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-card-giftcard"></i></span>
                                        <div class="form-group">
                                           
                                            <input required  type="text" name="gstn" value="" id="gstn" placeholder="GSTN"  class="form-control" />
             
                                        </div>
                                    </div>
                                </div>
                                
                           <div class="col-sm-6 mb_20">
                  
                                       <h3 class="card-block__title ">Email</h3>
                                  
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-email"></i></span>
                                        <div class="form-group">
                                           
                                            <input required  type="text"  name="email" value=""   placeholder="Enter Email" class="form-control" />
             
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-sm-6 mb_20">
                  
                                       <h3 class="card-block__title ">Shipping Address</h3>
                                  
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-account-box"></i></span>
                                        <div class="form-group">
                                           
                                            <input required  type="text" name="shipping_address" value="" id="shipping_address" placeholder="Shipping Address"  class="form-control" />
             
                                        </div>
                                    </div>
                                </div>
                            
                                    <div class="col-sm-6 mb_20">
                  
                                       <h3 class="card-block__title ">Billing Address
												<font style="font-size: 12px;float:right;">
												<input type="checkbox" onclick="return set_permanent_address();" name="billingtoo" id="billingtoo" onclick="FillBilling(this.form)">
												Same As Shipping Address</font>
										</h3>
                                  
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="zmdi zmdi-account-box"></i></span>
                                        <div class="form-group">
                                           
                                            <input required  type="text" name="permanent_address" value="" id="permanent_address" placeholder="Billing Address"  class="form-control" />
             
                                        </div>
                                    </div>
									
                                </div>
                               <div class="col-sm-8 offset-4">
                                    <br/>
                                <button type="submit"   class="btn btn-primary">Submit</button>
								<br/><br/>
                                </div>
								
                        </div>
						</form>

                
           </div>
		</div>
	
		
    </main>
	<style>
	.important 
	{
		background-color: rgba(243, 243, 243, 0.52) !important;
	}
	</style>
	<script>
	function open_process_img()
	{
		//alert('kkk');
		$(".page-loader").addClass("important");
		$(".page-loader").show();
		return true;
	}
	function set_permanent_address()
	{
            if($("#billingtoo").prop("checked") == true)
			{
				var shipping_address=$("#shipping_address").val();
				$("#permanent_address").val(shipping_address);
            }

            else if($("#billingtoo").prop("checked") == false)
			{

				$("#permanent_address").val('');
            }
			
	}
			</script>
        <!-- Older IE warning message -->
            <!--[if IE]>
                <div class="ie-warning">
                    <h1>Warning!!</h1>
                    <p>You are using an outdated version of Internet Explorer, please upgrade to any of the following web browsers to access this website.</p>

                    <div class="ie-warning__downloads">
                        <a href="http://www.google.com/chrome">
                            <img src="img/browsers/chrome.png" alt="">
                        </a>

                        <a href="https://www.mozilla.org/en-US/firefox/new">
                            <img src="img/browsers/firefox.png" alt="">
                        </a>

                        <a href="http://www.opera.com">
                            <img src="img/browsers/opera.png" alt="">
                        </a>

                        <a href="https://support.apple.com/downloads/safari">
                            <img src="img/browsers/safari.png" alt="">
                        </a>

                        <a href="https://www.microsoft.com/en-us/windows/microsoft-edge">
                            <img src="img/browsers/edge.png" alt="">
                        </a>

                        <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie">
                            <img src="img/browsers/ie.png" alt="">
                        </a>
                    </div>
                    <p>Sorry for the inconvenience!</p>
                </div>
            <![endif]-->

        <!-- Javascript --> 
        <!-- Vendors -->
        <script src="pos/view/default/vendors/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/tether/dist/js/tether.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/Waves/dist/waves.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/jquery.scrollbar/jquery.scrollbar.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/jquery-scrollLock/jquery-scrollLock.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/Waves/dist/waves.min.js"></script>

        <script src="pos/view/default/vendors/bower_components/flot/jquery.flot.js"></script>
        <script src="pos/view/default/vendors/bower_components/flot/jquery.flot.resize.js"></script>
        <script src="pos/view/default/vendors/bower_components/flot.curvedlines/curvedLines.js"></script>
        <script src="pos/view/default/vendors/bower_components/jqvmap/dist/jquery.vmap.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/jqvmap/dist/maps/jquery.vmap.world.js"></script>
        <script src="pos/view/default/vendors/bower_components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/salvattore/dist/salvattore.min.js"></script>
        <script src="pos/view/default/vendors/jquery.sparkline/jquery.sparkline.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/moment/min/moment.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/fullcalendar/dist/fullcalendar.min.js"></script>

       <script src="pos/view/default/vendors/bower_components/select2/dist/js/select2.full.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/dropzone/dist/min/dropzone.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/moment/min/moment.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/flatpickr/dist/flatpickr.min.js"></script>
<script src="pos/view/default/vendors/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="pos/view/default/js/common.js" type="text/javascript"></script>
        
        <!-- App functions and actions -->
        <script src="pos/view/default/js/app.min.js"></script>
    </body>
</html>

