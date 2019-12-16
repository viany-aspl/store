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
		<link rel="stylesheet" href="backoffice/view/stylesheet/alertify.core.css">
		<script src="backoffice/view/javascript/alertify.min.js"></script>
    </head>
    
    <body data-ma-theme="blue">
        <main class="container">
            <div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>

		<div class="row" >
			<div class="col-md-12 mt-3">
			<div class="card">
			
                        <div class="card-header">
                            <h2 class="card-title">Printer Images</h2>
                           
                        </div>

                        <div class="card-block">
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                                </ol>

                                <div class="carousel-inner" role="listbox">
									<?php $a=1; if(!empty($qusetion['image'])){ foreach($qusetion['image'] as $image) { ?>
                                    <div class="carousel-item <?php if($a==1){ ?> active <?php } ?>">
                                        <img style="max-height: 300px;" src="system/upload/printer_doc/<?php echo $image; ?>" alt="<?php echo $a; ?> slide">
                                    </div>
									<?php $a++; } } ?>
                                    
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                </a>
                            </div>
                        </div>
                    </div>
				
				
				<div class="card">
					
                            <span class="listview__item" >
                           
                                <div class="listview__content" class="col-md-8">
							
                                    <div class="listview__heading"><b>Name : </b> <?php echo $qusetion['name']; ?></div>
										<br/>
                                    <div class="listview__heading"><b>Model No : </b> <?php echo $qusetion['model']; ?></div>
										<br/>
										<div class="listview__heading"><b>Description : </b> <?php echo $qusetion['description']; ?></div>
										<br/>
										<div class="listview__heading"><b>Characters : </b> <?php echo $qusetion['character']; ?></div>
										<br/>
										<div class="listview__heading"><b>Width : </b> <?php echo $qusetion['width']; ?></div>
										<br/>
										<div class="listview__heading"><b>Colour : </b> <?php echo $qusetion['color']; ?></div>
										<br/>
										<div class="listview__heading"><b>Item Weight : </b> <?php echo $qusetion['item']; ?></div>
										<br/>
										<div class="listview__heading"><b>Warranty : </b> <?php echo $qusetion['warranty']; ?></div>
										<br/>
										<!--<div class="listview__heading"><b>Company Name : </b> <?php echo $qusetion['manufacturer_name']; ?></div>
										<br/>
										-->
										<div class="listview__heading"><b>Price : </b> <?php echo $qusetion['price']; ?></div>
										<br/>
										<div class="listview__heading"><b>Five paper rolls with the hardware : </b> <?php echo $qusetion['item']; ?></div>
										
                                </div>
									
                            </span>
							<div style="text-align: center;">
										<a onclick="return open_process_img();" href="index.php?route=mpos/printer/request&printer_id=<?php echo $qusetion['printer_id']; ?>&store_id=<?php echo $store_id; ?>"  class="btn btn-primary waves-effect" style="margin: 10px;width: 150px;">
											Request Printer
										</a>
									</div>
					</div>
				 
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
	function check_request_status(store_id)
	{
	alert(1);
		$.ajax({
			url: 'index.php?route=mpos/printer/check_status&store_id=' +  encodeURIComponent(store_id)+'&printer_id=' +  encodeURIComponent(<?php echo $qusetion['printer_id']; ?>),
			
			success: function(json) 
			{
				alert(json);
				if(json==1)
				{
					alertify.error('You have already requested for this printer');
					return false;
				}
				else
				{
					var url='index.php?route=mpos/printer/request&printer_id=<?php echo $qusetion['printer_id']; ?>&store_id=<?php echo $store_id; ?>';
					location=url;
					return true;
				}
				return false;
			},
			complete: function(json1)
			{
				
			}
		});
		//return false;
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

