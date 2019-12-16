<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Unnati Mitra</title>
	<link rel="stylesheet" href="pos/view/default/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
    <!-- Bootstrap -->
    <link href="catalog/view/theme/default/template/unnatimitra/css/bootstrap.min.css" rel="stylesheet">


	<link href="catalog/view/theme/default/template/unnatimitra/css/style.css" rel="stylesheet">
<!-- App styles -->
        <link rel="stylesheet" href="pos/view/default/css/app.min.css">
		<link rel="stylesheet" href="pos/view/default/css/style.css">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
	<div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>
	<div class="for_green">
	</div>
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<div class="logo">
						<a href="https://unnatiagro.in/unnatimitra?store_id=<?php echo $store_id; ?>"><img class="img-responsive" src="catalog/view/theme/default/template/unnatimitra/images/logo.png" alt="logo"></a>
				</div>
			</div>
			<div class="col-md-9">
			</div>
		</div>
	</div>
    
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1 ">
				<div class="inner_space1">
					<div class="inner_container">
						<div class="row">
							<div class="col-md-12">
								<div class="content_area">
									<!---<h4>To use WhatsApp on your computer:</h4>
									<ul class="mt_10">
										<li>Open WhatsApp on your phone</li>
										<li>Point your phone to this screen to capture the code</li>
										<li>Open WhatsApp on your phone</li>
									</ul>--->
									
									
									
									<div class="row">
										<div class="col-md-12">
											<div class="table-responsive" style="overflow-x: inherit !important;min-height: 120px;">
												<form action="index.php?route=mpos/unnatimitra/getotp" method="post">
                        <div class="col-sm-6">
                                    <div class="input-group">
                                        
                                        <div class="form-group">
                                            <input class="form-control" onkeypress="return isNumber(event)" maxlength="10" autocomplete="off" name="mobile" required="required" id="mobile" placeholder="Your Mobile Number" type="text">
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>

                                </div>
                            <div class="col-sm-6" style="text-align: center;">
                            <br/>
                            <input type="hidden" name="sid" value="<?php echo $sid; ?>" />
                            <button type="submit" class="btn btn-primary waves-effect">Submit</button>
                        </div>
                        </form>
											</div>
										</div>
									</div>
									
								</div>
							</div>
							
							<!--<div class="col-xs-12 col-sm-5 col-md-5">
								<div class="Qr_code">
									<img src="images/qr_code.jpg" alt="">
								</div>
							</div>--->
				
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
	
	<footer>
        <div class="container mt_40">
			<div class="row">
				<div class="col-xs-7 col-sm-7 col-md-7">
					<div class="footer_text">
						<p>© Agri POS. All rights reserved.</p>
					</div>
				</div>
				
				<div class="col-xs-5 col-sm-5 col-md-5">
					<div class="footer_link">
						<ul>
							<li><a href="https://unnatiagro.in/unnatimitra?store_id=<?php echo $store_id; ?>">Back</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</footer>
	
	
	
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="catalog/view/theme/default/template/unnatimitra/js/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="catalog/view/theme/default/template/unnatimitra/js/bootstrap.min.js"></script>
	<script>
        function isNumber(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          //console.log(charCode);
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
        </script>
	<script>
	var obj = document.getElementById('partitioned');
obj.addEventListener("keydown", stopCarret); 
obj.addEventListener("keyup", stopCarret); 

function stopCarret() {
	if (obj.value.length > 3){
		setCaretPosition(obj, 3);
	}
}

function setCaretPosition(elem, caretPos) {
    if(elem != null) {
        if(elem.createTextRange) {
            var range = elem.createTextRange();
            range.move('character', caretPos);
            range.select();
        }
        else {
            if(elem.selectionStart) {
                elem.focus();
                elem.setSelectionRange(caretPos, caretPos);
            }
            else
                elem.focus();
        }
    }
}
	</script>
	<!-- App functions and actions -->
        <script src="pos/view/default/js/app.min.js"></script>
        
        
        <!-- Vendors: Data tables -->
        <script src="pos/view/default/vendors/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="pos/view/default/vendors/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
        
        <script src="pos/view/default/vendors/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
  </body>
</html>