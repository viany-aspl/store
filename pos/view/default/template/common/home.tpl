<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Vendor styles -->
        <link rel="stylesheet" href="view/default/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="view/default/vendors/bower_components/animate.css/animate.min.css">

        <!-- App styles -->
        <link rel="stylesheet" href="view/default/css/app.min.css">
    </head>

    <body data-ma-theme="blue">
        <form action="<?php echo $login_action; ?>" method="post" enctype="multipart/form-data">
        <div class="login">

            <style>
                .close
                {
                    font-size: 13px !important;
                }
            </style>
            <!-- Login -->
            <div class="login__block active" id="l-login">
                <?php if ($error_warning) { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <br/>
            <?php } ?>
            <?php if ($success) { ?>
                <div class="alert alert-success" style="margin-bottom: 20px;"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <br/>
            <?php } ?>
                <br/>
                <div class="login__block__header">
                    <i class="zmdi zmdi-account-circle"></i>
                    Hi there! Please Sign in

                    <div class="actions actions--inverse login__block__actions">
                        <div class="dropdown">
                            <i data-toggle="dropdown" class="zmdi zmdi-more-vert actions__item"></i>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" data-ma-action="login-switch" data-ma-target="#l-register" href="">Create an account</a>
                                <a class="dropdown-item" data-ma-action="login-switch" data-ma-target="#l-forget-password" href="">Forgot password?</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="login__block__body">
                    <div class="form-group form-group--float form-group--centered">
                        <input type="text"  name="username" autocomplete="off" value="<?php echo $username; ?>" class="form-control">
                        <label>Username</label>
                        <i class="form-group__bar"></i>
                    </div>

                    <div class="form-group form-group--float form-group--centered">
                        <input type="password" name="password" value="<?php echo $password; ?>" class="form-control">
                        <label>Password</label>
                        <i class="form-group__bar"></i>
                    </div>

                    <button type="submit" class="btn btn--icon login__block__btn" ><i class="zmdi zmdi-arrow-right"></i></button>
                </div>
            </div>
 
        </div>
</form>
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
        <script src="view/default/vendors/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="view/default/vendors/bower_components/tether/dist/js/tether.min.js"></script>
        <script src="view/default/vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="view/default/vendors/bower_components/Waves/dist/waves.min.js"></script>

        <!-- App functions and actions -->
        <script src="view/default/js/app.min.js"></script>
    </body>
</html>