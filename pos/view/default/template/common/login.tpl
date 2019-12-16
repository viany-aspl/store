<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>AgriPOS</title>



    <!-- Bootstrap -->

    <link href="view/default/lcss/bootstrap.min.css" rel="stylesheet">

	<link href="view/default/lcss/style.css" rel="stylesheet">
	<link rel="stylesheet" href="view/default/vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
        <link rel="stylesheet" href="view/default/vendors/bower_components/animate.css/animate.min.css">

        <!-- App styles -->
        <link rel="stylesheet" href="view/default/css/app.min.css">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->

    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if lt IE 9]>

      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>

      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->

  </head>

  <body>

  

	<div class="for_green">

	</div>

	<div class="container">

			<div class="row">

				<div class="col-md-3">

					<div class="logo">

						<div class="col-xs-6 col-sm-6 col-md-6">

							<img src="../../../stores/image/no_image.png" alt="logo">

						</div>

						<div class="col-xs-6 col-sm-6 col-md-6 no_space">

						

						</div>

					</div>

				</div>

				<div class="col-md-9">

				</div>

			</div>

		</div>

    

	<div class="container">

		<div class="inner_space">

			<div class="inner_container">

				<div class="row">
				
					<div class="col-xs-12 col-sm-5 col-md-5">

						<div class="Qr_code" style="text-align: center;min-height: 268px;">

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

					<div class="col-xs-12 col-sm-7 col-md-7">

						<div class="content_left">

							<h4>To use AgriPOS on your computer with internet facility</h4>
							<h5>Open AgriPOS on your Android Phone</h5>

							<ul class="mt_10">

								<li>Go to settings and select AgriPOS Web Login</li>
								<li>Scan the QR code using your phone</li>
								<li>The system will automatically login in</li>
								<li>Use the POS machine on your desktop / laptop</li>
								

							</ul>

						</div>

					</div>

					

					

					

				</div>

				

			</div>

		

		</div>

	

	

	

	</div>

	
<footer class="text-center m_20">
        <p>© Agri POS. All rights reserved.</p>
	</footer>
	

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <script src="view/default/ljs/jquery.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->

    <script src="view/default/ljs/bootstrap.min.js"></script>
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