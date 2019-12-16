<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login to AgriPOS</title>
	
		<link rel="shortcut icon"  href="../favicon.ico"/>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<meta name="description" content="AgriPOS">
		<meta name="author" content="AgriPOS">
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
                

                <div class="login__block__body">
					<div onclick="return reload_qr();" id="qrcode_timeout" style="cursor: pointer;padding-top: 113px;font-size: 17px;background-color: #53BCA6;border-radius: 50%;height: 245px;width: 245px;text-align: center;display: none;margin-left: 10px;color: white;">
                    <!--<i  class="zmdi zmdi-refresh" style="font-size: 144px;"></i>-->
					Click to reload QR code
					</div>
                   <div id="qrcode">
						  
                <div  style="width: 264px;height: 264px;padding-top: 100px;" id="pr_img">
				
				<div class="page-loader__spinner" style="display:inline-block; vertical-align:middle; text-align:center">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
				
            </div>
			
                       <!--<img id="pr_img" src="view/image/processing_image.gif" style="width: 100%;" />-->
            </div>
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
        <script src="view/default/js/jquery.qrcode-0.11.0.min.js"></script>
        <script src="view/default/js/jquery.qrcode.js"></script>
        <script type="text/javascript">
		function reload_qr()
		{
			$("#qrcode_timeout").hide();
			$("#qrcode").html('');
			$("#qrcode").html('<div  style="width: 264px;height: 264px;padding-top: 100px;" id="pr_img"><div class="page-loader__spinner" style="display:inline-block; vertical-align:middle; text-align:center"><svg viewBox="25 25 50 50"><circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /></svg></div></div>');
				wsconnect();
		}
            	var socket;
		var host = "wss://unnatiagro.in/websocket";
		var reconnect=true;
		//var ping;
		var pingInt;
	
		function wsconnect()
		{
			try{
                           
				socket = new WebSocket(host);
				socket.onopen = function(){
                                      console.log("onopen");
					status('<p class="event">Socket Status: '+socket.readyState+' (open)');
					socket.send('login: agripos');
					//pingInt = setInterval(ping, 1000);
				}
				
				socket.onerror = function(error){
                                    console.log("onerror");
					status('<p class="event">Socket Status: '+socket.readyState+' ('+error+')');	
				}
				
				//receive message
				socket.onmessage = function(msg){
                                     console.log("onmessage");
                                     //console.log(msg);
					var data = JSON.parse(msg.data);
                                        // console.log(msg.data);
					if(data.message!=undefined)
					{
                                            $("#pr_img").hide();
											$("#qrcode").html('');
                                                  $("#qrcode").qrcode({
                                                        render: "canvas", 
                                                        text: data.message, 
                                                        width: 264, //二维码的宽度
                                                        height: 264,
                                                        background: "#ffffff", //二维码的后景色
                                                        foreground: "#000000", //二维码的前景色
                                                        src: '../../../stores/image/no_image.png',
                                                        imgWidth: 50,
                                                        imgHeight: 50
                                                        });
						//message('<p class="message">'+data.message+'</p>');
                                                
					}
					if(data.ping!=undefined)
					{
						//var time = new Date().getTime();
						//time = time - ping;
						alert('timeout');
						$('#ping').html('Ping: '+data.ping+'ms');
						
					}
					if(data.users!=undefined)
					{
						$("#qrcode").html('<div  style="width: 264px;height: 264px;padding-top: 100px;" id="pr_img"><div class="page-loader__spinner" style="display:inline-block; vertical-align:middle; text-align:center"><svg viewBox="25 25 50 50"><circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /></svg></div></div>');
						var myKeyVals ={ "token":data.token , "users": data.users };
						$.ajax({
     					type: 'POST',
     					url: "index.php?route=common/login/qrlogin",
      					data: myKeyVals,
      					dataType: "text",
      					success: function(resultData) 
						{ 
							location=resultData; 
						},
						error: function (res)
						{
							reconnect=false;
							socket.close();
							reload_qr();
						}
						});
						//message('<p class="message">'+data.token+'</p>');
                        reconnect=false;
                        socket.close();
					}
				}
				
				socket.onclose = function(e)
				{
					$("#qrcode").html('');
					$("#qrcode_timeout").show();
					
					//alert('timeout 2');
                    console.log("onclose");
					clearInterval(pingInt);
					if(reconnect)
					{
						//wsconnect();
					}
                     //redirect user
					status('<p class="event">Socket Status: '+socket.readyState+' (Closed)');
				}			
					
			} catch(exception){
                        alert(exception);
				message('<p>Error'+exception);
			}
		}
                function ping()
			{
				if(reconnect)
				{
					ping = new Date().getTime();
					socket.send('ping');
				}
			}
                      
                        
                        function message(msg)
			{
                            console.log(msg);
				//$('#chatLog').append(msg);
				//$('#chatLog').scrollTop($('#chatLog')[0].scrollHeight);
			}
			
			function status(msg)
			{
                            console.log(msg);
				//$('#status').html(msg+'</p>');
			}
			
			
			
			
$(document).ready(function() 
{
    
	
	if(!("WebSocket" in window)){	
		$('<p>Oh no, you need a browser that supports WebSockets. How about <a href="http://www.google.com/chrome">Google Chrome</a>?</p>').appendTo('#container');		
	}
	else
	{
		//The user has WebSockets					
			wsconnect();
		
	}
	//End connect()
	
});
</script>
    </body>
</html>