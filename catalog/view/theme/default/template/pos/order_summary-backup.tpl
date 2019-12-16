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
 <title>Invoice</title>
 <meta name="description" content="<?php echo $store_name; ?>">
  <meta name="keywords" content="unnati,agro">
  <meta name="author" content="Unnati">
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
       
          <style type="text/css" media="print">
  @page { size: portrait; }
</style>
     <div class="content__inner">
                   
<?php //print_r($order);?>
                    <div class="invoice">
                        <div class="invoice__header">
                            <img class="invoice__logo" src="../image/cache/no_image-45x45.png" alt="">
                        </div>

                        <div class="row invoice__address">
                            <div class="col-6">
                                <div class="text-right">
                                    <p>Invoice from</p>

                                    <h4><?php echo $store_name; ?></h4>

                                    <address>
                                        <?php echo $store_address; ?>
                                    </address>

                                    <?php echo $store_telephone; ?><br/>
                                    <?php echo $store_email; ?>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="text-left">
                                    <p>Invoice to</p>

                                    <h4><?php echo $firstname." ".$lastname; ?></h4>

                                    <address>
                                        
                                    </address>

                                    <?php echo $telephone; ?>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="row invoice__attrs">
                            <div class="col-3">
                                <div class="invoice__attrs__item">
                                    <small>Invoice#</small>
                                    <h3><?php echo ($order_info['invoice_prefix'].$order_info['invoice_no']); ?></h3>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="invoice__attrs__item">
                                    <small>Date</small>
                                    <h3><?php echo (date('d M, Y',$order_info['date_added']->sec)); ?></h3>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="invoice__attrs__item">
                                    <small>Order ID</small>
                                    <h3><?php echo ($order_info['order_id']); ?></h3>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="invoice__attrs__item">
                                    <small>Total</small>
                                    <h3><?php echo RUPPE_SIGN;  echo number_format($order_info['total'],2); ?></h3>
                                </div>
                            </div>
                            
                        </div>
                        <table class="table table-bordered invoice__table">
              <thead>
                <tr>
                  <th class="text-left">Product</th>
             
                  <th class="text-right">Quantity</th>
                  <th class="text-right">Unit Price</th>
                  <th class="text-right">Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $product) { ?>
                <tr>
                  <td class="text-left"><?php echo $product['name']; ?></td>
            
                  <td class="text-right"><?php echo $product['quantity']; ?></td>
                  <td class="text-right"><?php echo $product['price']; ?></td>
                  <td class="text-right"><?php echo $product['total']; ?></td>
                </tr>
                <?php } ?>
                
                <?php foreach ($totals as $totals) { ?>
                <tr>
                  <td colspan="3" class="text-right"><?php echo $totals['title']; ?>:</td>
                  <td class="text-right"><?php echo $totals['text']; ?></td>
                </tr>
                <?php } ?>
                <tr>
                  <td colspan="3" class="text-right">Cash:</td>
                  <td class="text-right"><?php echo RUPPE_SIGN;  echo number_format($order_info['cash'],2); ?></td>
                </tr>
                <tr>
                  <td colspan="3" class="text-right">Credit:</td>
                  <td class="text-right"><?php echo RUPPE_SIGN;  echo number_format($order_info['credit'],2); ?></td>
                </tr>
              </tbody>
            </table>
                        
                        <div class="invoice__remarks">
                            <h5>Tax Description</h5>
                             <table class="table table-bordered invoice__table">
              <thead>
                <tr>
                  <th class="text-left" style="font-weight: bold;">Tax Title</th>
             
                  <th class="text-right" style="font-weight: bold;">Value</th>
                  
                </tr>
              </thead>
              <tbody>
                <?php if(!empty($tax_array)) { foreach ($tax_array as $taxd) { ?>
                <tr>
                  <td class="text-left"><?php echo $taxd['title']; ?></td>
            
                  <td class="text-right"><?php echo $taxd['value']; ?></td>
                 
                </tr>
                <?php } }else
				{
					?>
                <tr>
                  <td class="text-left">NO-TAX</td>
            
                  <td class="text-right"><?php echo RUPPE_SIGN;echo '0.00'; ?></td>
                 
                </tr>
                <?php } ?>
              </tbody>
            </table>

                            <h5 class="mt-5">THANK YOU FOR YOUR BUSINESS</h5>
                            <p></p>
                        </div>

                        <footer class="invoice__footer">
                            <a href="#">Agri POS</a>
                            <a href="#">0120 4040160</a>
                            <a href="https://unnatiagro.in/">unnatiagro.in</a>
                        </footer>
                    </div>

                    <button class="btn btn-danger btn--action btn--fixed " data-ma-action="print"><i class="zmdi zmdi-print"></i></button>
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

