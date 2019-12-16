<!DOCTYPE html>
<html lang="en">
    <head>
	<link rel="shortcut icon"  href="../favicon.ico"/>
	<title><?php echo $title; ?></title>
	
	<script type="text/javascript">
            var timerStart = Date.now();
        </script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<meta name="description" content="AgriPOS">
		<meta name="author" content="AgriPOS">
		
		<script src="view/default/js/jquery-2.1.1.min.js"></script>
         <!-- Vendor styles -->
        <link rel="stylesheet" href="view/default/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="view/default/vendors/bower_components/animate.css/animate.min.css">
        <link rel="stylesheet" href="view/default/vendors/bower_components/jquery.scrollbar/jquery.scrollbar.css">
        <link rel="stylesheet" href="view/default/vendors/bower_components/fullcalendar/dist/fullcalendar.min.css">
        <script src="view/default/js/fusioncharts/fusioncharts.js"></script>
        <script src="view/default/js/fusioncharts/fusioncharts.theme.fint.js"></script>
        
        <link rel="stylesheet" href="view/default/vendors/bower_components/select2/dist/css/select2.min.css">
        <link rel="stylesheet" href="view/default/vendors/bower_components/dropzone/dist/dropzone.css">
        <link rel="stylesheet" href="view/default/vendors/bower_components/flatpickr/dist/flatpickr.min.css" />
		<link rel="stylesheet" href="view/default/css/alertify.core.css">
		<script src="view/default/js/alertify.min.js"></script>
		
        <link rel="stylesheet" href="view/default/vendors/bower_components/sweetalert2/dist/sweetalert2.min.css">
        <!-- App styles -->
        <link rel="stylesheet" href="view/default/css/app.min.css">
		 <link rel="stylesheet" href="view/default/css/style.css">
    </head>
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
    <body data-ma-theme="blue">
        <main class="main">
            <div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>

            <header class="header">
                <div class="navigation-trigger" data-ma-action="aside-open" data-ma-target=".sidebar">
                    <div class="navigation-trigger__inner">
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                        <i class="navigation-trigger__line"></i>
                    </div>
                </div>

                <div class="header__logo hidden-sm-down">
                    <h1><a href="#">Agri POS</a></h1>
                </div>

                <ul class="top-nav">
                  

                   

                    <li class="dropdown ">
                        <a href="" data-toggle="dropdown"><i class="zmdi zmdi-more-vert"></i></a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-item theme-switch">
                               <a class="dropdown-item" href="index.php?route=common/logout">Logout</a>

                             
                            </div>
                           
                        </div>
                    </li>

 <li class="hidden-xs-down">
                        <a href="" data-ma-action="aside-open" data-ma-target=".chat" class="top-nav__notify">
                            <i class="zmdi  zmdi-pin-help"></i>
                        </a>
                    </li>

                </ul>
            </header>