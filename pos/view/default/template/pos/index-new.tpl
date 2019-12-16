<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>POS Terminal</title>



    <!-- Bootstrap -->

    <link href="/pos/view/default/template/pos/pos/css/bootstrap.min.css" rel="stylesheet">

	<link href="/pos/view/default/template/pos/pos/css/font-awesome.min.css" rel="stylesheet">

	<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet"> 

	<link href="/pos/view/default/template/pos/pos/css/select2-bootstrap.min.css" rel="stylesheet">

	<link href="/pos/view/default/template/pos/pos/css/style.css" rel="stylesheet">		


        
        <script src="view/default/js/alertify.min.js"></script>
		
        <link rel="stylesheet" href="view/default/vendors/bower_components/sweetalert2/dist/sweetalert2.min.css">
        
	
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
  <div class="container-fluid">

	<div class="container-fluid mt_10">

		<div class="row">

			<div class="col-xs-12 col-sm-12 col-md-11 no_space">

				<div class="mr_10 pull-right">

					<button type="button" class="btn btn-danger">

						<i class="fa fa-chevron-left"></i>

					</button>

				</div>

				<div class="mr_10 pull-right">

					<button type="button" class="btn btn-danger ">

						<i class="fa fa-close"></i>

					</button>

				</div>

				

			</div>

			<div class="col-xs-12 col-sm-12 col-md-1  mt_10">

				<div class="pull-right"><?php echo date('d/m/Y'); ?></div>

			</div>

		</div>

	</div>

</div>

	<section >

		<div class="container-fluid">

			<div class="row">
				<div class="col-md-5">

					 <div class="box">

						<div class="box_header b_border">

							<div class="row">

								<div class="col-xs-12 col-sm-6 col-md-5">

									<div class="form-group no_space mb_10">
										<input type="hidden" name="cat_id" id="cat_id" />
										<input type="hidden" name="current_page" id="current_page" />
										<input type="hidden" name="no_more" id="no_more" />
										
										<div class="input-group">
											<select onchange="return getItemsByCatId(this.value,1);" class="form-control w_255">
												<?php foreach($categories as $category)
												{
												?>
													<option value="<?= $category['category_id'] ?>"><?= $category['name'] ?></option>
												<?php 
												}
												?>
											</select>

										</div>

									</div>

								</div>

							<div class="col-md-1">

								<a href="javascript:toggleDiv('myContent');">
								 <div class="circle-plus closed">

									  <div class="circle">

										<div class="vertical" ></div>

										<div class="horizontal"></div>

									  </div>

									</div>

								 </a>	

							</div>

							</div>	

						</div>	

						<div class="product_items main">

							<div class="box_body1">

								<div class="row no_space"> 

									<div class="col-md-12 no_space">

										<div id="myContent" class="no_space">

												<div class="fix_hight_pro_box">

													
												</div>
										</div>

									</div>

								</div>

							</div>

						</div>

					 </div>

				</div>

				<div class="col-md-7">

					<div class="box">

						<div class="box_header b_border">

							<h4>POS Terminal <span>#AS0004</span> </h4>

							

							<div class="pull-right box_tool">

								<a href="#" class="btn btn-success">button</a>

							</div>
						</div>
						<div class="box_body">

							<div class="row">

								<div class="col-md-6">

									<div class="form-group">

										<div class="input-group">

											<div class="input-group-addon"><i class="fa fa-user"></i></div>

											<select class="form-control">

											  <option selected="selected w_206">orange</option>

											  <option>white</option>
											</select>
											<div class="input-group-addon">

												<button type="button" class="btn bg_none" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i></button>

											</div>

										</div>

									</div>

								</div>

							<div class="col-md-6">

								<div class="form-group">

									<div class="input-group">

										<div class="input-group-addon">

											<i class="fa fa-barcode"></i>

										</div>

											<input type="text" class="form-control" id="" placeholder="Enter Product name / SKU / Scan bar code">

										<div class="input-group-addon">.00</div>

									</div>

								</div>

							</div>

							</div>

						<div class="row">

							<div class="col-md-12">
								<div class="row col-lg-6" id="no_products_div" style="padding: 40px 23px 10px 25%;display:none;">
		
									<i class="zmdi zmdi-shopping-cart-plus zmdi-hc-5x"></i>
									<span style="font-size: 18px;margin-top: 60px;color: rgb(33, 150, 243);margin-left: -120px;">Select a product to procceed.</span>
		
								</div>
								<div id="products_div"  class="pos_product_div">

								<table id="ctable"  class="table table-cont cart_table">

									  <thead style="transform: translateY(-1px) !important;">

										<tr>

										  <th style="width:23%;">Product </th>

										  <th style="width:23%;">Quantity</th>

										  <th style="width:23%;">Price</th>
										  <th style="width:23%;">Tax</th>
										  <th style="width:23%;">Price+Tax</th>
										  <th style="width:23%;">Subtotal</th>

										  <th style="width:8%;"><button type="button" class="btn btn1 bg_none" ><i class="fa fa-close"></i></button></th>

										</tr>

									  </thead>

									  <tbody>
									<tr>

										<td colspan="6">
											<span style="font-size: 18px;margin-top: 60px;color: rgb(33, 150, 243);margin-left: 0px;">Select a product to procceed.</span>
										</td>
										
										</tr>

									  </tbody>

								</table>

								</div>

							</div>

						</div>

						<div class="row">

							<div class="col-md-12 mt_15">

								<div class="grey_panel">

									<table>

										<tbody>

											<tr class="side_border">

												<td>

													<div class="col-sm-1 col-xs-3 col-md-1 no_space">

													<b>Items:

														</br>

													<span id="items_count">0</span></b>

													</div>

													

													<div class="col-sm-2 col-xs-3 col-md-1 no_space">

													<b>Total:

														</br>

													<span id="items_sub_total">0</span></b>

													</div>
													<div class="col-sm-2 col-xs-6 col-md-2 no_space">

													<b>Discount(-):

														<div class="wrapper">

															<i class="fa  fa-info-circle"></i>

															<div class="tooltip">I am a tooltip!</div>

														</div>

													  

														</br>

														

													<span>0</span></b>

													</div>

													<div class="col-sm-2 col-xs-6 col-md-3 no_space">

													<b>Order Tax(+):

														<div class="wrapper">

															<i class="fa  fa-info-circle"></i>

															<div class="tooltip">Set 'Default Sale Tax' for all sales in Business Settings. Click on the edit icon below to add/update Order Tax.</div>

														</div>

														</br>

													<span id="items_total_tax">0</span></b>

													</div>
													<div class="col-sm-2 col-xs-6 col-md-2 no_space">

													<b>Shipping(+):

														<div class="wrapper">

															<i class="fa  fa-info-circle"></i>

															<div class="tooltip">Set 'Default Sale Tax' for all sales in Business Settings. Click on the edit icon below to add/update Order Tax.</div>

														</div>

													  

														</br>



													<span>0</span></b>

													</div>
													<div class="col-sm-3 col-xs-12 col-md-3 no_space">

													<b>Total Payable: 

														</br>

													<input type="hidden" name="final_total" id="final_total_input" value="0">

													<span id="total_payable" class="text-success lead"><strong>0</strong></span></b>

													

													</div>

												</td>

											</tr>

											<tr class="mt_10">

												<td>
													<!--
													<div class="col-xs-6 col-sm-2 col-md-2">

														<button type="button" class="btn btn-warning flat_btn full_width">Draft</button>

														<button type="button" class="btn btn-info flat_btn flat_btn full_width mt_10">Quotation</button>

													</div>

													

													<div class="col-xs-6 col-sm-2 col-md-3 mb_10">

														<button type="button" class="btn btn-warning bg-maroon full_width">

															<div class="text-center">

																<i class="fa fa-check"></i>

																Card

															</div>

														</button>

														<button type="button" class="btn btn-info bg_red full_width mt_10">Suspend</button>

													</div>
													
													
													<div class="col-xs-12 col-sm-2 col-md-4 mb_10">

														<button type="button" class="btn btn-success bg-navy navy_height full_width">

															<div class="text-center">

																<i class="fa fa-check"></i>

																<b>Multiple Pay</b>

															</div>

														</button>

													</div>
													-->
													<div class="col-xs-12 col-sm-2 col-md-3">

														<button type="button" class="btn btn-success green navy_height full_width">

															<div class="text-center">

																<i class="fa  fa-money"></i>

																<b>Pay Now</b>

															</div>

														</button>
													</div>

												</td>

											</tr>

										</tbody>

									</table>

								</div>

							</div>

						</div>

						</div>

					</div>

				</div>

			</div>

		</div>

	</section>

	<!-- Modal -->

<div id="myModal" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <!-- Modal content-->

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title">Modal Header</h4>

      </div>

      <div class="modal-body">

        <p>Some text in the modal.</p>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

      </div>

    </div>

  </div>

</div>

	
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->

    <script src="view/default/template/pos/pos/js/bootstrap.min.js"></script>

	<script src="view/default/template/pos/pos/js/select2.min.js"></script>

	<script>
	$(document).ready(function()
	{
		getItemsByCatId(44,1);
	});
	/////////////////////////////
	function update_cart_by_price_qnty(qnty,id,kky,price,tax)
	{
		qnty=$("#qty-"+id).val();
		
		price=$("#price-"+id).val();
		var tax_rate=$("#tax_rate"+id).val();
		
		price=price.replace('Rs.', '');
		price=price.replace(',', '');
		price=parseFloat(price);
		
		var base_price=p=parseFloat((price*100)/(100+parseFloat(tax_rate)));
		
		var ttax=tax.replace('Rs.', '');
		ttax=ttax.replace(',', '');
		ttax=parseFloat(ttax).toFixed(2);
		base_price=parseFloat(base_price).toFixed(2);
		removeFromCart(kky);
		//alert(qnty);	
		//alert(tax_rate);	
		//alert(price);	
		//alert(base_price);	
		
		setTimeout( function()
		{
			addToCart(id,parseInt(qnty),base_price);
		}  , 1000 );
	}
	
	//////////////////////////////
	function update_cart($products,$total_data)
	{    
		var html  = '';
		for(var i=0; i< $products.length; i++)
		{   
			//alert(JSON.stringify($products[i]));
			ttax=$products[i]['tax'].replace('Rs.', '');
			ttax=parseFloat(ttax);
			var perpeicetax=parseFloat(ttax)/parseFloat($products[i]['quantity']);
		
			var pprice=$products[i]['price'].replace('Rs.', '');
			pprice=pprice.replace(',', '');
			pprice=parseFloat(pprice).toFixed(2);
			
			var tax_rate=0;
			var per_piece_tax=parseFloat((parseFloat(ttax))/parseFloat($products[i]['quantity']));
			
			tax_rate=((parseFloat(per_piece_tax)/parseFloat(pprice))*(100));
			tax_rate=parseFloat(tax_rate).toFixed(0);
			//alert(ttax);
			//alert(per_piece_tax);
			//alert(pprice);
			
			var fullprice=parseFloat(pprice)+parseFloat(perpeicetax);
			fullprice=parseFloat(fullprice).toFixed(2);
			
			var kky="'"+$products[i]['key']+"'";
			var ktax="'"+$products[i]['tax']+"'";
			var newprice="'"+fullprice+"'";
			
			html += '<tr><td style="width:22.5%;"><div class="ib-info-meta">';
			html += '<span class="title">'+$products[i]['name']+'</span>';
			html += '</div></td>';
			html += '<td style="width:22.5%;"><div class="item-block ib-qty">';
			html += '<input type="text" id="qty-'+$products[i]['id']+'" onblur="return update_cart_by_price_qnty(this.value,'+$products[i]['id']+','+kky+','+newprice+','+ktax+')" value="'+$products[i]['quantity']+'" class="qty" />';
			html += '</div></td>';
			
			html += '<td style="width:22.5%;"><div class="item-block ib-total-price">';
			html += '<span class="total_text">Rs.'+pprice+'</span>';
			html += '</div></td>';
			html += '<td style="width:22.5%;"><div class="item-block ib-total-price">';
			html += '<span class="total_text">'+$products[i]['tax']+'</span>';
			html += '</div></td>';
			html += '<td style="width:22.5%;"><div class="item-block">';
			html += '<input type="text" id="price-'+$products[i]['id']+'" onblur="return update_cart_by_price_qnty('+$products[i]['quantity']+','+$products[i]['id']+','+kky+',this.value,'+ktax+')" class="form-control pos_unit_price_inc_tax input_number valid" value="'+fullprice+'">';
			html += '</div></td>';
			html += '<td style="width:22.5%;"><div class="item-block ib-total-price">';
			html += '<input type="hidden" class="form-control pos_line_total " value="'+$products[i]['total']+'">';
			html += '<span class="total_text">'+$products[i]['total']+'</span>';
			html += '</div></td>';
			html += '<td style="width:10%;">';
			
			
			html += '<a href="#" onclick="return removeFromCart('+kky+');" class="cart_remove" data-key="'+$products[i]['key']+'" id="row'+$products[i]['id']+'"><i class="fa fa-close"></i></a>';
			
			html += '<input type="hidden" class="product_ids" name="product_id[]"  id="product_id'+$products[i]['id']+'" value="'+$products[i]['id']+'">';
			html += '<input type="hidden" class="category_id" name="category_id[]"  id="category_id'+$products[i]['category_id']+'" value="'+$products[i]['category_id']+'">';
			html += '<input type="hidden"  name="quantity[]"  id="quantity'+$products[i]['id']+'" value="'+$products[i]['quantity']+'">';
			html += '<input type="hidden"  name="name[]" id="name'+$products[i]['id']+'" value="'+$products[i]['name']+'">';
			html += '<input type="hidden"  name="price[]" id="price'+$products[i]['id']+'" value="'+$products[i]['price']+'">';
			html += '<input type="hidden"  name="tax[]" id="tax'+$products[i]['id']+'" value="'+$products[i]['tax']+'">';
			html += '<input type="hidden"  name="total[]" id="total'+$products[i]['id']+'" value="'+$products[i]['total']+'">';  
			html += '<input type="hidden"  name="tax_rate[]" id="tax_rate'+$products[i]['id']+'" value="'+tax_rate+'">'; 
			html += '</td></tr>';
			
		}
		if(i>0)
		{
			$("#products_div").show();
			$("#no_products_div").hide();
			$("#items_count").html(i);
		}
        else
        {
            $("#products_div").hide();
			$("#no_products_div").show();
        }
		$('.cart_table tbody').html(html);

		//total data
		var ttax=0;
		var subtotal=0;
		var total=0;
		var lottax=0;      
		var html = '<tr>';
    
		for(var i=0; i < $total_data.length; i++)
		{
			if($total_data[i].code=='tax')
			{
				var rsstr=$total_data[i].text.substring(0,3);            
				var str=($total_data[i].text.slice(3));//.slice(0,-8); 
				str=str.replace(',','');
				var ft=parseFloat(str);
            
				ft=parseFloat(ft.toFixed(2));
				lottax+=ft; 
				lottax=parseFloat(lottax.toFixed(2));
				//alert(JSON.stringify($total_data[i].text));
				//alert(JSON.stringify(str));
				//alert(JSON.stringify(ft));
				//alert(JSON.stringify(lottax));
				//alert(lottax);
				ttax=rsstr+lottax+'<input type="hidden" id="ttax" name="ttax" value="'+rsstr+lottax+'">';
				$("#items_total_tax").html(ttax);
				//ttax = '<td style="text-align:center;"><span id="tax-total">'+ rsstr+lottax+'</span><input type="hidden" id="ttax" name="ttax" value="'+rsstr+lottax+'"></td>';
            
			}
        
			else if($total_data[i].code=='sub_total')
			{
				$("#items_sub_total").html($total_data[i].text+'<input type="hidden" id="subtotal" name="subtotal" value="'+$total_data[i].text+'">');
				//subtotal= '<td style="text-align:center;"><span id="sub-total">'+$total_data[i].text+'</span><input type="hidden" id="subtotal" name="subtotal" value="'+$total_data[i].text+'"></td>';
			}
			else if($total_data[i].code=='total')
			{
				$("#total_payable").html($total_data[i].text+'<input type="hidden" id="total" name="total" value="'+$total_data[i].text+'" >');
				//total = '<td style="text-align:center;"><span id="cart-total">'+$total_data[i].text+'</span> <input type="hidden" id="total" name="total" value="'+$total_data[i].text+'" ></td>';
			}
        
		} 
    
		// alert(ttax);
		if(ttax=='0')
		{
			$("#items_total_tax").html('Rs.0.00<input type="hidden" id="ttax" name="ttax" value="Rs.0.00">');
			//ttax = '<td style="text-align:center;"><span id="cart-total">₹0.00</span><input type="hidden" id="ttax" name="ttax" value="₹0.00"></td>';
		}
		html+=subtotal;
		html+=ttax;
		html+=total;
		html += '</tr>';
		ttax=0;
		$('#total_wrapper').html(html);
		//remove  btn
		$('.cart_remove').click(function()
		{ 
			//alert($(this).attr('id'));
			//uncheck($(this).attr('id'));
			//removeFromCart($(this).attr('data-key'));        
			$(this).parentsUntil('tbody').remove();    
        
		});
	}
	//////////////////
	function removeFromCart($key,$by='')
	{ 	
		//alert($key);
		$.ajax({
			url: 'index.php?route=pos/pos/removeFromCart&token=<?php echo $token; ?>&remove='+ $key,
			type: 'post',
			data: { remove: $key },
			dataType: 'json',
			beforeSend: function() 
			{
				//$(".page-loader").addClass("important");
				//$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
				//$(".page-loader").show();
			},
			complete: function() 
			{
				//$(".page-loader").removeClass("important");
				//$("#please_wait_span").remove();
				//$(".page-loader").hide();
			},
			success: function(json) 
			{ 
				$('#total_wrapper').html('');
				//alert(JSON.stringify(json));
            
				var ttax=0;
				var subtotal=0;
				var total=0;
				var lottax=0;
				var html='<tr>';
				$total_data = json['total_data'];
				var ttax=0;
				var subtotal=0;
				var total=0;
				var lottax=0;      
				var html = '<tr>';
				
				var current_count=$("#items_count").html();
				var new_count=(parseInt(current_count)-1);
				$("#items_count").html(new_count)
				
				for(var i=0; i < $total_data.length; i++)
				{
					if($total_data[i].code=='tax')
					{
						var rsstr=$total_data[i].text.substring(0,3);            
						var str=($total_data[i].text.slice(3));//.slice(0,-8); 
						str=str.replace(',','');
						var ft=parseFloat(str);
            
						ft=parseFloat(ft.toFixed(2));
						lottax+=ft; 
						lottax=parseFloat(lottax.toFixed(2));  
						ttax=rsstr+lottax+'<input type="hidden" id="ttax" name="ttax" value="'+rsstr+lottax+'">';
						$("#items_total_tax").html(ttax);
						//ttax = '<td style="text-align:center;"><span id="tax-total">'+ rsstr+lottax+'</span><input type="hidden" id="ttax" name="ttax" value="'+rsstr+lottax+'"></td>';
            
					}
        
					else if($total_data[i].code=='sub_total')
					{
						$("#items_sub_total").html($total_data[i].text+'<input type="hidden" id="subtotal" name="subtotal" value="'+$total_data[i].text+'">');
						//subtotal= '<td style="text-align:center;"><span id="sub-total">'+$total_data[i].text+'</span><input type="hidden" id="subtotal" name="subtotal" value="'+$total_data[i].text+'"></td>';
					}
					else if($total_data[i].code=='total')
					{
						//alert(JSON.stringify($total_data[i].value));
						if($total_data[i].value>0)
						{
							$("#products_div").show();
							$("#no_products_div").hide();
						}
						else
						{
							$("#products_div").hide();
							$("#no_products_div").show();
						}
						$("#total_payable").html($total_data[i].text+'<input type="hidden" id="total" name="total" value="'+$total_data[i].text+'" >');
						//total = '<td style="text-align:center;"><span id="cart-total">'+$total_data[i].text+'</span> <input type="hidden" id="total" name="total" value="'+$total_data[i].text+'" ></td>';
					}
        
				} 
				if(ttax=='0')
				{
					$("#items_total_tax").html('Rs.0.00<input type="hidden" id="ttax" name="ttax" value="Rs.0.00">');
					//ttax = '<td style="text-align:center;"><span id="cart-total">₹0.00</span><input type="hidden" id="ttax" name="ttax" value="₹0.00"></td>';
				}
				html+=subtotal;
				html+=ttax;
            
				html+=total;
				html += '</tr>';
           
				$('#total_wrapper').html(html);
				//document.getElementById('pr'+id).classList.remove("selecteditem");
				//document.getElementById('pr_class'+id).style.border = "";
				if($by)
				{
					$(".page-loader").removeClass("important");
					$("#please_wait_span").remove();
					$(".page-loader").hide();
				}
			},
			error(json)
			{
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
				alert(JSON.stringify(json));
			}
		});
		return false;
	}
	//////////////////////////////
	function addToCart(id,quantity,price)
	{
		//alert(price);
		var cat_id=$("#cat_id").val();
		$.ajax({
            url: 'index.php?route=pos/pos/addToCart&token=<?php echo $token; ?>&billtype=<?php echo $billtype; ?>&price='+price+'&product_id='+id+'&quantity='+quantity+'&cat_id='+cat_id,
            type: 'post',
            data: { product_id: id, quantity: quantity,price: price,cat_id: cat_id },
            dataType: 'json',
			beforeSend: function() 
			{
				$("#please_wait_span").remove();
				$(".page-loader").addClass("important");
				$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
				$(".page-loader").show();
			},
            success: function(json) 
			{
				//alert(JSON.stringify(json));
                $('.success, .warning, .attention, information, .error').remove();

                if (json['error']) 
				{ 
					//alert('hi 11');
                    if (json['error']['option']) 
					{
                        for (i in json['error']['option']) 
						{
                            $('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                        }
                    }
                }
				if (json['success']) 
				{    
					//alert('hi2');                        
                    update_cart(json['products'], json['total_data']); 
                    $('.total_wrapper .pull-right div').fadeOut().delay(50).fadeIn('slow');
                    $('.product_list_bottom').addClass('hide');
                    
                }
                $(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
            }, 
			error:function (json)
			{
				alert(JSON.stringify(json));
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
			}
		});
	}
	//////////////////////////////
	function getItemsByCatId($id,$page)
	{
		//alert($id);
		try
		{
			if($page == 1)
			{
				$("#no_more").val('');
			}
			var html = '';
			$(".page-loader").addClass("important");
			$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
			$(".page-loader").show();
			$.post('index.php?route=pos/pos/getCategoryItems&category_id='+$id+'&token=<?php echo $token; ?>&billtype=<?php echo $billtype; ?>',{ category_id: $id, page: $page }, function(data)
			{
				$("#cat_id").val($id);
				var data = JSON.parse(data);
				for(var i = 0; i < data.products.length; i++)
				{
					if(data.products[i]['favourite']=='0')
					{
						var favourite='<span style="float: right;margin-top: -17px;color: red;" id="fav_top_span_'+data.products[i]['id']+'"><span class="favourite"  onclick="return add_to_favourite('+data.products[i]['id']+');" id="favourite'+data.products[i]['id']+'" ><i class="zmdi zmdi-favorite-outline"></i></span></span>';
					}
					else
					{
						var favourite='<span style="float: right;margin-top: -17px;color: red;" id="fav_top_span_'+data.products[i]['id']+'"><span class="favourite"  onclick="return already_added_to_favourite('+data.products[i]['id']+');" id="favourite'+data.products[i]['id']+'" ><i class="zmdi zmdi-favorite"></i></span></span>';
					}
					var shareproduct='<a target="_blank" href="https://web.whatsapp.com/send?text=https://unnatiagro.in/product/product.php?'+data.products[i]['pid']+'" data-action="share/whatsapp/share"><i style="float: right;font-size: 16px;color: rgb(33, 150, 243);margin-top: -15px;" class="zmdi zmdi-share"></i></a>';
					
					html+= '<div id="col'+data.products[i]['id']+'" onclick="return addToCart('+data.products[i]['id']+',1,0);" class="product_item title" style="min-height: 155px;" data-toggle="tooltip" title="'+data.products[i]['name']+'">';
					html+= '<div class="pro_image_div">';
					//'+data.products[i]['image']+'
					html+= '<img class="img-responsive" src="view/default/template/pos/pos/images/pro_1.jpg"></img>';
					html+= '</div>';
					
					html+= '<div class="text">';
					html+= data.products[i]['name'];
					html+= '</div>';
					
					html+= '<div class="text">';
					html+= data.products[i]['stock_text'];
					html+= '</div>';
					
					html+= '<div class="text">';
					html+= data.products[i]['store_price_text'];
					html+= '</div>';
					
					html+= '<div class="text">';
					html+= data.products[i]['chemical_name'];
					html+= '</div>';
					
					html+= '</div>';
				}
				if(i>0)
				{	
					$page++;  //check is start page 
					if($page == 2)
					{
						$('.fix_hight_pro_box').html(html);
						//$('.cart_table tbody tr').remove();
					}
					else
					{
						$('.fix_hight_pro_box').append(html);
					}
					$("div.tile").click(
                
						function ()
						{ 
							var id=($(this).attr("id")).replace("col", "");
							var quantity=1;
							//addToCart(id,quantity,'');
						}
					);
					var table = document.getElementById('ctable');
					if(table.rows.length>1)
					{
						for(var i = 0; i < data.products.length; i++)
						{
							//product_select(data.products[i]['id']);
						}
					}
					$("#cat_id").val($id);
					$("#current_page").val($page);
				}
				else
				{
					if($page >1)
					{
						$('.fix_hight_pro_box').append('<span class="no_more_found" id="no_more_found" style="text-align: right;width: 100%;font-weight: bold;text-align: center;color: rgb(33, 150, 243);font-size: 14px;">No more products found in this category!</span>');
						$("#no_more").val('no_more');
					}
					else
					{
						$('.fix_hight_pro_box').html('<span class="no_more_found" id="no_more_found" id="no_more_found" style="text-align: right;width: 100%;font-weight: bold;text-align: center;color: rgb(33, 150, 243);font-size: 14px;">No product found in this category!</span>');
					}
				}
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
			});
		}
		catch(e)
		{
			alert(e);
		}
	}
///////////////////////////////////////////	
		
		$('select').select2({

  createTag: function (params) {

    var term = $.trim(params.term);



    if (term === '') {

      return null;

    }



    return {

      id: term,

      text: term,

      newTag: true // add additional parameters

    }

  }

});

	</script>

<script>

// popovers initialization - on hover

$('[data-toggle="popover-hover"]').popover({

  html: true,

  trigger: 'hover',

  placement: 'bottom',

  content: function () { return '<img src="' + $(this).data('img') + '" />'; }

});



// popovers initialization - on click

$('[data-toggle="popover-click"]').popover({

  html: true,

  trigger: 'click',

  placement: 'bottom',

  content: function () { return '<img src="' + $(this).data('img') + '" />'; }

});

</script>





<script type="text/javascript">

$('.circle-plus').on('click', function(){

  $(this).toggleClass('closed'); 

})

</script>



<script>

$(document).ready(function(){

  $('[data-toggle="tooltip"]').tooltip();   

});

</script>



<script type="text/javascript">

function toggleDiv(divId) {

   $("#"+divId).toggle();

}

</script>



<script type="text/javascript">

	$(document).ready(function(){

		

		var highestBox = 0;

			$('.text').each(function(){  

					if($(this).height() > highestBox){  

					highestBox = $(this).height();  

			}

		});    

		$('.text').height(highestBox);



	});

</script>



  </body>

</html>