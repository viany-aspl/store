<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Vendor styles -->
      

        <!-- App styles -->
        <link rel="stylesheet" href="pos/view/default/css/app.min.css">

       
    </head>

    <body data-ma-theme="green">
        <main class="main">
            <div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>

            
            <section class="content" style="padding: 32px 30px 0 30px;">
                <div class="content__inner">
                   

                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Frequently Asked Questions</h2>
                            <small class="card-subtitle">Frequently Asked Questions</small>
                        </div>

                        <div class="card-block">
                            <div class="accordion" id="accordionExample">
                            <?php foreach($faqs as $key=>$faq) { ?>
                                <div class="card" style="border: 1px solid silver;padding: 5px 5px 5px 28px;border-radius: 31px;">
                                    <div class="card-header">
                                        <a class="card-title" data-toggle="collapse" data-parent="#accordionExample" href="#collapse<?php echo $faq['faq_id'];?>"><?php echo $faq['question'];?></a>
                                    </div>

                                    <div id="collapse<?php echo $faq['faq_id'];?>" class="collapse show">
                                        <div class="card-block">
                                        <span><img src="<?php echo $faq['image'];?>"></span>
                                           <?php echo $faq['answer'];?>
                                        </div>
                                    </div>
                                </div>
                               <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

              
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
        
        <script src="pos/view/default/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        

        <!-- App functions and actions -->
        <script src="pos/view/default/js/app.min.js"></script>
    </body>
</html>