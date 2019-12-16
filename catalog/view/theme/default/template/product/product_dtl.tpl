<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title><?php echo $product_info['name']; ?></title>



    <!-- Bootstrap -->
	<link rel="stylesheet" href="pos/view/default/css/app.min.css">
    <link href="catalog/view/theme/default/template/product/css/bootstrap.min.css" rel="stylesheet">
	
	

	<link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet"> 

	<link href="catalog/view/theme/default/template/product/css/font-awesome.min.css" rel="stylesheet">

	<link href="catalog/view/theme/default/template/product/css/style.css" rel="stylesheet">



	<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/template/product/css/jquery-rating.css">
	<link rel="stylesheet" href="pos/view/default/vendors/bower_components/sweetalert2/dist/sweetalert2.min.css">


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->

    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

    <!--[if lt IE 9]>

      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>

      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    <![endif]-->

	

	
<style>


</style>

	
  </head>

  <body>

  <main class="container">
            <div class="page-loader">
                <div class="page-loader__spinner">
                    <svg viewBox="25 25 50 50">
                        <circle cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
                    </svg>
                </div>
            </div>

  </main>

  <header>

	<div class="wrapper">
	<?php if(!empty($image)){ ?>
		<img class="img-responsive" style="max-height: 200px;width: 100%;" src="image/<?php echo $image; ?>"alt="banner"> 
	<?php } ?>
	</div>

  </header>

  

    <section>

		<div class="container">

			<div class="row">

				<div class="col-xs-9 col-sm-9 col-md-11">

					<div class="heading_box">

						<h1><?php echo $product_info['name']; ?></h1>

						<p><?php 
						//print_r($product_info['category_name']);
						$cname=""; 
						foreach ($product_info['category_name'] as $cat_name)
						{
							if(strtoupper($cat_name)=='MY SHOP')
							{
								
							}
							else if($cat_name=='MyShop') 
							{
								
							}
							
							else
							{
								$cname.=strtoupper($cat_name).",";
							}
							
						}
						$cname=str_replace('MY SHOP','',$cname);
						echo rtrim($cname,",");
						
						?> </p>

						<p><?php echo $product_info['manufacturer'];  ?></p>

					</div>

				</div>

				

				<div class="col-xs-3 col-sm-3 col-md-1">

					<div class="green" id="review_count"><?php echo $review_count; ?></div>

				</div>

			</div>

		</div>

	</section>

	

	<section>

	

		<div class="container box-shadow1" style="padding: 20px 20px 0px;">
		<?php if($reward_list>0) { ?>
			<div class="text_shape">

				<img src="image/coin.png">

					<div class="shape_text">

					

					<p>Reward</p>

					</div>

				</div>
		<?php  } ?>
			<h2 class="" id="d_border" style="cursor: pointer;" onclick="return show_descr();">Product Details 
			<div id="prd_img_div" style="text-align: right;padding-right: 25px;float: right;">
				<img id="more_img" src="image/arrow_down.png" />
				
			</div>
			<input type="hidden" id="pd_val" value="h" />
			</h2> 
			<br/>
			<div style="margin-top: 3px;overflow:hidden;display: none;" id="descr">
				<?php  
				echo $description=htmlspecialchars_decode($product_info['description']); 
				//echo truncate_string($description, 50, ' ...');
				//print_r(str_word_count($description, 1));
			?>
			</div>
		</div>

		<div class="container">

			

				<div class="row">
					<?php if(!empty($view))
					{ 
						//class="col-xs-4 col-sm-4 col-md-4 box-shadow" 
					}
					else
					{
						//class="col-xs-6 col-sm-6 col-md-6 box-shadow" 
					} 
					?>
					<div class="col-xs-4 col-sm-4 col-md-4 box-shadow" >

						<div class="full_icon star_setting">

	<!-------------------------------try start---------------------------------------------->


<fieldset class="rating">
		
		<input type="radio" <?php if($review==5){ ?> checked <?php } ?> id="star5" name="rating" value="5"  />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','5')" class = "full" for="star5" title="Awesome - 5 stars"></label>
			
		<input type="radio" <?php if($review==4.5){ ?> checked <?php } ?> id="star4half" name="rating" value="4 and a half" />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','4.5')" class="half" for="star4half" title="Pretty good - 4.5 stars"></label>
			
		<input type="radio" <?php if($review==4){ ?> checked <?php } ?> id="star4" name="rating" value="4" />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','4')" class = "full" for="star4" title="Pretty good - 4 stars"></label>
			
		<input type="radio" <?php if($review==3.5){ ?> checked <?php } ?> id="star3half" name="rating" value="3 and a half"  />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','3.5')" class="half" for="star3half" title="Meh - 3.5 stars"></label>
			
		<input type="radio" <?php if($review==3){ ?> checked <?php } ?> id="star3" name="rating" value="3" />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','3')" class = "full" for="star3" title="Meh - 3 stars"></label>
			
		<input type="radio" <?php if($review==2.5){ ?> checked <?php } ?> id="star2half" name="rating" value="2 and a half" />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','2.5')" class="half" for="star2half" title="Kinda bad - 2.5 stars"></label>
		<input type="radio" <?php if($review==2){ ?> checked <?php } ?> id="star2" name="rating" value="2"  />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','2')" class = "full" for="star2" title="Kinda bad - 2 stars"></label>
			
		<input type="radio" <?php if($review==1.5){ ?> checked <?php } ?> id="star1half" name="rating" value="1 and a half" />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','1.5')" class="half" for="star1half" title="Meh - 1.5 stars"></label>
			
		<input type="radio" <?php if($review==1){ ?> checked <?php } ?> id="star1" name="rating" value="1" />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','1')" class = "full" for="star1" title="Sucks big time - 1 star"></label>
			
		<input type="radio" <?php if($review==0.5){ ?> checked <?php } ?> id="starhalf" name="rating" value="half" />
			<label onclick="return addproductrating('<?php echo $prd_id; ?>','<?php echo $str_id; ?>','0.5')" class="half" for="starhalf" title="Sucks big time - 0.5 stars"></label>
	</fieldset>

	

	<!-------------------------------try ending---------------------------------------------->

	
						</div>

						

						<div class="full_content">

							<h4>Review</h4>

						</div>

					</div>

					

					

					<div class="col-xs-4 col-sm-4 col-md-4 box-shadow">

						<div class="full_icon">
						
						
							<div id="bookmark_div" >
								<?php if(empty($bookmark))
							{ ?>
								<img style="cursor: pointer;" onclick="return addtobookmarkproduct(<?php echo $product_id; ?>,<?php echo $store_id; ?>)" src="catalog/view/theme/default/template/product/images/bookmark.png">
							
							<?php }else { ?>
							
							
								<img style="cursor: pointer;"  onclick="return remove_bookmark(<?php echo $product_id; ?>,<?php echo $store_id; ?>)" src="catalog/view/theme/default/template/product/images/bookmark_hover.png">
							<?php } ?>
							</div>
						
							<span class="red" id="bookmark_count"><?php echo $bookmark_count; ?></span>

						</div>
						

						<div class="full_content">

							<h4>Bookmark</h4>

						</div>

					</div>

					<?php if(!empty($view))
					{
					$url='https://unnati.world/stores/index.php?route=mpos/dashboard/productdetail&view=mobile&product_id='.$prd_id;
					$url=urlencode("\n").urlencode($url);
					//$product_info['name']='test';
					
					?>

					<div class="col-xs-4 col-sm-4 col-md-4 box-shadow">

						<div class="full_icon">
							<a target="_self" href="whatsapp://send?text=<?php echo $product_info['name']; ?>  <?php echo $url; ?>">

							<img src="catalog/view/theme/default/template/product/images/share.png">
						</a>

						</div>

						

						<div class="full_content">

							<h4>Share</h4>

						</div>

					</div>
					<?php } ?>
				</div>

			

		</div>

		

	</section>

	

	<section>
<!-- Default -->
                            <div class="modal fade" id="modal-default" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title pull-left">Request Form</h5>
                                        </div>
                                        <div class="modal-body">
										<input type="hidden" id="store_id" value="" />
										<input type="hidden" id="store_name" value="" />
										<input type="hidden" id="product_id" value="<?php echo $product_info['product_id']; ?>" />
										<input type="hidden" id="product_name" value="<?php echo $product_info['name']; ?>" />
												<div class="form-group">
                                        <label>Full Name</label>
                                        <input maxlength="40" autocomplete="off" class="form-control input-mask" id="full_name" name="full_name" placeholder="Full Name" type="text">
                                        <i class="form-group__bar"></i>
                                    </div>
									<div class="form-group">
                                        <label>Mobile Number</label>
                                        <input maxlength="10" autocomplete="off" class="form-control input-mask" id="mobile_number" id="mobile_number" placeholder="Mobile Number" type="text">
                                        <i class="form-group__bar"></i>
                                    </div>
                                           
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-link btn_active" style="width: 90px;color: white !important;background: #1a3141 !important;text-decoration: none;" onclick="return check_n_submit_form();">Submit</button>
                                            <button type="button" class="btn btn-link btn_active" style="background-color: red !important;width: 90px;color: white !important;text-decoration: none;" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
		<div class="container">
		<?php //if(!empty($near_by_stores)) 
		if(1) 
		{ ?>
			<h2 class="mt_60"><span><img src="catalog/view/theme/default/template/product/images/cart.png" alt="cart"></span> Available at</h2>
				<div class="table-responsive mt_10">

				<table class="table table-bordered">

					<thead>

					<tr >

						<th style="width:60%;">Seller</th>

						<th style="width:10%;">Price</th>
						<?php if(!empty($view)){ ?>
						<th style="width:30%;">Action</th>
						<?php } ?>
					</tr>

				</thead>

				<tbody>
					<?php if(!empty($near_by_stores)) {
					foreach($near_by_stores as $stores){
					?>
					<tr>

						<td style="width:60%;"><?php echo $stores['store_name']; ?> </td>

						<td style="width:10%; vertical-align:middle; text-align:center;">₹ <?php echo $stores['offer_price']; ?></td>
						<?php if(!empty($view)){ ?>
						<td style="width:30%;vertical-align:middle;" id="req_link_<?php echo ($near_by_stores[0]['store_id']); ?>">
						<button style="color: white !important;background: #1a3141 !important;" onclick="return set_store_id(<?php echo ($near_by_stores[0]['store_id']); ?>,'<?php echo ($near_by_stores[0]['store_name']); ?>');" class="btn btn-secondary btn_active" data-toggle="modal" data-target="#modal-default">Request</button>
 
					
						</td>
						<?php } ?>
					</tr>
					<?php }
					}
					else { ?>
					<tr>

						<td colspan="3">This product is currently unavailable</td>

						
						
					</tr>
					<?php } ?>
				</tbody>

				

				</table>

				</div>
			<?php } ?>
			</div>

		</div>

	</section>

		<section>
<!--
		<div class="container">

			<div class="row">

				<div class="col-xs-12 col-md-12">

					<div class="note_text">

						<div class="note_content">

							<img src="image/coin1.png">

							Earn 1 Reward point on purchase of 100 units of this product

						</div>

						

					</div>

				</div>

			</div>

		</div>
-->
		

			

	</section>

	



    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <script src="catalog/view/theme/default/template/product/js/jquery.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->

    <script src="catalog/view/theme/default/template/product/js/bootstrap.min.js"></script>

	<script src="pos/view/default/js/app.min.js"></script>
	<script src="pos/view/default/vendors/bower_components/sweetalert2/dist/sweetalert2.min.js"></script>
	<script src="pos/view/default/vendors/bower_components/remarkable-bootstrap-notify/dist/bootstrap-notify.min.js"></script>
	
	<style>
	.important 
	{
		background-color: rgba(243, 243, 243, 0.52) !important;
	}
	#swal2-title
		{
			font-size: 20px !important;
		}
		
	</style>
	<script type="text/javascript">
	function show_descr()
	{
		var pd_val=$("#pd_val").val();
		if(pd_val=="h")
		{
			$("#prd_img_div").html('<img id="more_img" src="image/arrow_up.png" />');
			$("#descr").show();
			$("#d_border").addClass("d_border");
			$("#pd_val").val('s');
		}
		else
		{
			$("#prd_img_div").html('<img id="more_img" src="image/arrow_down.png" />');
			$("#descr").hide();
			$("#d_border").removeClass("d_border");
			$("#pd_val").val('h');
			//prd_img_div
		}
		return false;
	}
	function hide_descr()
	{
		$("#more_img").show();
		$("#less_img").hide();
		$("#descr").hide();
		$("#d_border").removeClass("d_border");
		return false;
	}
	function check_n_submit_form()
	{
		var store_id=$("#store_id").val();
		var store_name=$("#store_name").val();
		var product_id=$("#product_id").val();
		var product_name=$("#product_name").val();
		var full_name=$("#full_name").val();
		var mobile_number=$("#mobile_number").val();
		if(!full_name)
		{
			alert('Please Enter your Full name');
			$("#full_name").focus();
			return false;
		}
		if(!mobile_number)
		{
			alert('Please Enter your Mobile Number');
			$("#mobile_number").focus();
			return false;
		}
		$.ajax({
            url: 'index.php?route=mpos/openretailer/product_request&store_id='+store_id+'&product_id='+product_id+'&full_name='+full_name+'&mobile_number='+mobile_number+'&product_name='+product_name+'&store_name='+store_name,
            type: 'post',
            data: { product_id: product_id,store_id: store_id,full_name: full_name,mobile_number: mobile_number,product_name: product_name,store_name: store_name},
            dataType: 'json',
			beforeSend: function() 
			{
				$(".page-loader").addClass("important");
				$(".page-loader").show();
			},
			success: function(json) 
			{
				$(".page-loader").removeClass("important");
				
				$(".page-loader").hide();
				if(json['status']=='1')
				{
					$("#modal-default").hide();
					$("#modal-default").removeClass("in");
					
					swal({
                    title: json['msg'], 
                    text: "",
                    imageUrl: 'catalog/view/theme/default/image/thumbs-up.png',
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'OK'
					});
					location.reload();
				}
				else if(json['status']=='2')
				{
					swal({
                    title: json['msg'], 
                    text: "",
                    imageUrl: 'catalog/view/theme/default/image/thumbs-up.png',
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'OK'
					});
				}
				else 
				{
					alert('Some error occur.please try again');
				}
			}, 
			error:function (json)
			{
             
                alert(JSON.stringify(json));
				$(".page-loader").removeClass("important");
				$(".page-loader").hide();
			}
        });
		
	}
	function set_store_id(store_id,store_name)
	{
		$("#store_id").val(store_id);
		$("#store_name").val(store_name);
		$("#full_name").val('');
		$("#mobile_number").val('');
		return true;
	}
	
	function addproductrating(pid,sid,rating)
	{
		$.ajax({
            url: 'index.php?route=mpos/openretailer/addproductrating&store_id='+sid+'&product_id='+pid+'&rating='+rating,
            type: 'post',
            data: { product_id: pid,store_id: sid,rating: rating},
            dataType: 'json',
			beforeSend: function() 
			{
				$(".page-loader").addClass("important");
				$(".page-loader").show();
			},
			success: function(json) 
			{
				$(".page-loader").removeClass("important");
				
				$(".page-loader").hide();
				if(json['status']=='1')
				{
					$("#review_count").html(json['review_count']);
					swal({
                    title: 'Rating Saved ', 
                    text: "",
                    imageUrl: 'catalog/view/theme/default/image/thumbs-up.png',
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'OK'
					});
				}
				else 
				{
					alert('Some error occur.please try again');
				}
			}, 
			error:function (json)
			{
             
                alert(JSON.stringify(json));
				$(".page-loader").removeClass("important");
				$(".page-loader").hide();
			}
        });
		return true;
	}
	function addtobookmarkproduct(pid,sid)
	{
		$.ajax({
            url: 'index.php?route=mpos/openretailer/addtobookmarkproduct&store_id='+sid+'&product_id='+pid,
            type: 'post',
            data: { product_id: pid,store_id: sid},
            dataType: 'json',
			beforeSend: function() 
			{
				$(".page-loader").addClass("important");
				$(".page-loader").show();
			},
			success: function(json) 
			{
				$(".page-loader").removeClass("important");
				
				$(".page-loader").hide();
				if(json['status']=='1')
				{
					$("#bookmark_count").html(json['bookmark_count']);
					
					var remove_bookmark='remove_bookmark()';
					$("#bookmark_div").html('<img style="cursor: pointer;" onclick="return remove_bookmark('+pid+','+sid+');" src="catalog/view/theme/default/template/product/images/bookmark_hover.png">');
					swal({
                    title: 'Product Bookmarked', 
                    text: "",
                    imageUrl: 'catalog/view/theme/default/image/thumbs-up.png',
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'OK'
					});
				}
				else 
				{
					alert('Some error occur.please try again');
				}
			}, 
			error:function (json)
			{
             
                alert(JSON.stringify(json));
				$(".page-loader").removeClass("important");
				$(".page-loader").hide();
			}
        });
		return false;
	}
	function remove_bookmark(pid,sid)
	{
		$.ajax({
            url: 'index.php?route=mpos/openretailer/remove_bookmark&store_id='+sid+'&product_id='+pid,
            type: 'post',
            data: { product_id: pid,store_id: sid},
            dataType: 'json',
			beforeSend: function() 
			{
				$(".page-loader").addClass("important");
				$(".page-loader").show();
			},
			success: function(json) 
			{
				$(".page-loader").removeClass("important");
				
				$(".page-loader").hide();
				if(json['status']=='1')
				{
					$("#bookmark_count").html(json['bookmark_count']);
					$("#bookmark_div").html('<img style="cursor: pointer;" onclick="return addtobookmarkproduct('+pid+','+sid+');" src="catalog/view/theme/default/template/product/images/bookmark.png">');
					swal({
                    title: 'Product removed from Bookmarked', 
                    text: "",
                    imageUrl: 'catalog/view/theme/default/image/thumbs-up.png',
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'OK'
					});
				}
				else 
				{
					alert('Some error occur.please try again');
				}
			}, 
			error:function (json)
			{
             
                alert(JSON.stringify(json));
				$(".page-loader").removeClass("important");
				$(".page-loader").hide();
			}
        });
		return false;
	}
	</script>
	<script>

	//$(document).ready(function() { 


    // Configure/customize these variables.
	/*
    var showChar = 180;  // How many characters are shown by default

    var ellipsestext = "";

    var moretext = "Read More";

    var lesstext = "Read Less";

    



    $('.more').each(function() {

        var content = $(this).html();

 		//alert(content);

        if(content.length > showChar) 
        {

 

            var c = content.substr(0, showChar);

            var h = content.substr(showChar, content.length - showChar);

 

            var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

 

            $(this).html(html);

        }

 

    });

 

    $(".morelink").click(function(){

        if($(this).hasClass("less")) {

            $(this).removeClass("less");

            $(this).html(moretext);

        } else {

            $(this).addClass("less");

            $(this).html(lesstext);

        }

        $(this).parent().prev().toggle();

        $(this).prev().toggle();

        return false;

    });
	*/
//});

	</script>
</body>

</html>