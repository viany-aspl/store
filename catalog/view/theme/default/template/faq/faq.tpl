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
					<?php if(!empty($faq['image'])){ ?>
                        <img class="card-img-top" style="max-height: 300px;" src="image/<?php echo $faq['image']; ?>" alt="">
					<?php } ?>
                        <div class="card-header" style="text-align: justify;" >
                            <h2 class="card-title" style="font-size: 1.5rem;"><?php echo $faq['faq_description']['1']['question']; ?></h2>
                            
                        </div>
                        <div class="card-block"style="text-align: justify;">
                            <p><?php echo $faq['faq_description']['1']['answer']; ?></p>
                        
                        </div>
                    </div>
           </div>
		</div>
	
    </main>

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

