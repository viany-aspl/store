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
        <main class="main">
            <div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>



<div class="row">
        <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Sales Statistics</h2>
                                <small class="card-subtitle">Sale trend</small>
                            </div>
         <?php   	$msChart = new FusionCharts("mscombi2D", "1", "100%", "400", "chart-container", "json", $comparasion_chart);
					$msChart->render();?>
                            <div class="card-block" id="chart-container">
                       
                                <div class="flot-chart flot-curved-line" ></div>
                                
                                <div class="flot-chart-legends flot-chart-legends--curved"></div>
                            </div>
                        </div>
                    </div>
</div>
<div class="row">
        <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Order Statistics</h2>
                                <small class="card-subtitle">Order trend</small>
                            </div>
         <?php   	$msChart2 = new FusionCharts("mscombi2D", "2", "100%", "400", "chart-container-order_count", "json", $comparasion_chart_order_count);
					$msChart2->render();?>
                            <div class="card-block" id="chart-container-order_count">
                       
                                <div class="flot-chart flot-curved-line" ></div>
                                
                                <div class="flot-chart-legends flot-chart-legends--curved"></div>
                            </div>
                        </div>
                    </div>
</div>
<div class="row">
        <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Average Sales Ticket Size</h2>
                                
                            </div>
         <?php   	$msChart5 = new FusionCharts("mscombi2D", "5", "100%", "400", "chart-container-bar_chart", "json", $comparasion_chart_bar_chart);
					$msChart5->render();?>
                            <div class="card-block" id="chart-container-bar_chart">
                       
                                <div class="flot-chart flot-curved-line" ></div>
                                
                                <div class="flot-chart-legends flot-chart-legends--curved"></div>
                            </div>
                        </div>
                    </div>
</div>
                 <div class="row">
                  

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Growth Rate</h2>
                                <small class="card-subtitle">Top 5 Products</small>
                            </div>
<?php   $msChartp = new FusionCharts("pie2d", "3", "100%", "400", "chart-container1", "json", $top5products);
               
               // calling render method to render the chart
               $msChartp->render();?>
                            <div class="card-block" id="chart-container1">
                                <div class="flot-chart flot-line"></div>
                                <div class="flot-chart-legends flot-chart-legends--line"></div>
                            </div>
                        </div>
                    </div>
					 </div>
					 <div class="row">
					<div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                
                                <small class="card-subtitle">Top 5 Category</small>
                            </div>
<?php   $msChartp_category = new FusionCharts("pie2d", "4", "100%", "400", "chart-container-category", "json", $comparasion_chart_category);
               
               // calling render method to render the chart
               $msChartp_category->render();?>
                            <div class="card-block" id="chart-container-category">
                                <div class="flot-chart flot-line"></div>
                                <div class="flot-chart-legends flot-chart-legends--line"></div>
                            </div>
                        </div>
                    </div>
               
</div>


				
				
				            </section>
<footer class="footer hidden-xs-down">
                    <p>© Agri POS. All rights reserved.</p>

                    
                </footer>
            </section>
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

