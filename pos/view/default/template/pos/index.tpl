<?php echo $header; ?><?php echo $column_left; ?>

 
                     <!--=========================== top category list ==========================-->
    <div class="toolbar" >      
        <nav class="toolbar__nav">
           <?php 
                $color_array = array('bg-lightBlue','bg-darkViolet','bg-darkCyan','bg-violet','bg-indigo','bg-magenta');
                $i = 0;
                $length = sizeof($color_array);
                $a=0;
                foreach($categories as $category)
				{ ?>
					<a class="btn btn-primary waves-effectk cat_list <?php if($a==0){ echo 'active '; }  ?>green-box category" id="cat_idd<?= $category['category_id'] ?>" style="font-size: 10px;color: white;" 
                       data-category-id="<?= $category['category_id'] ?>" href="#"><?= $category['name'] ?>
					</a>
              <?php $i++; if($i == $length) $i=0; $a++;} ?>
       </nav>
		<div class="actions">
           <i class="actions__item zmdi zmdi-search" data-ma-action="toolbar-search-open"></i>
       </div>
		<div class="toolbar__search">
			<input type="text" placeholder="Search Products...">
				<i class="toolbar__search__close zmdi zmdi-long-arrow-left" data-ma-action="toolbar-search-close"></i>
       </div>
    </div>   
	<input type="hidden" name="cat_id" id="cat_id" />
	<input type="hidden" name="current_page" id="current_page" />
	<input type="hidden" name="no_more" id="no_more" />
	<div class="row" style="min-height: 250px;">
      
	<div class="contacts row col-lg-6">
              
    </div>  
	<div class="row col-lg-6" id="no_products_div" style="padding: 40px 23px 10px 25%;">
		
			<i class="zmdi zmdi-shopping-cart-plus zmdi-hc-5x"></i>
			<span style="font-size: 18px;margin-top: 60px;color: rgb(33, 150, 243);margin-left: -120px;">Select a product to procceed.</span>
		
	</div>
	<div class="row col-lg-6" id="products_div" style="display: none;">
		<form class="col-lg-12" style="padding-right:0px" method="post" id="pos_form" action="index.php?route=pos/pos/product_payment&token=<?php echo $token; ?>&pagetittle=Order Payment" >             
		<input type="hidden" name="billtype" id="billtype" value="<?php echo $billtype; ?>" />
		<div class="col-lg-10" style="float: left; padding:0px;">
			<div class="card" style="min-height: 200px;">
				<div class="table-responsive">
					<table id="ctable" class='table table-bordered mb-0 cart_table' >
						<thead>
							<tr>
								<th>Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Tax</th>
                            <th>Total</th>
                           <th></th>
                          </tr>  
						</thead>  
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
           <div class="card">
              <div class="table-responsive">
                  <table class='table table-bordered mb-0'>
						<thead>
                          <tr>
                            <th style="text-align:center;">Sub total</th>
                            
                            <th style="text-align:center;">Tax</th>
                            
                            <th style="text-align:center;">Order Totals</th>
                           
                          </tr> 
						</thead>  
						<tbody id="total_wrapper">
							<tr>
								<td style="text-align:center;"><span id="sub-total"><?= $default_amount; ?></span></td>
								<td style="text-align:center;"><span id="tax-total"><?= $default_amount; ?></span></td>
								<td style="text-align:center;"><span id="cart-total"><?= $default_amount; ?></span></td>
							</tr> 
						</tbody>
                  </table>
              </div>
           </div>
		</div>    
       <div class="col-lg-2" style="float: left;">   
			<button type="submit" id="order" href="#order_wrapper" class="btn-primary green_btn" >
				<center>
                  <i class="icon-dollar"></i>
						<span class="text-center"> Proceed to Bill</span>
              </center>
           </button> 
       </div> 
    </form>
	</div> 
	</div>

<div class="modal modal_qnty" id="modal-backdrop-ignore" data-backdrop="static" tabindex="-1" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title pull-left">Update Quantity </h5>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="text" onkeypress="return isNumber(event)" maxlength="6" class="form-control " placeholder="New Quantity" name="new_qnty" id="new_qnty" />
						<i class="form-group__bar"></i>
						<input type="hidden" name="product_id" id="product_id_qnty" />
                  </div>
              </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary waves-effect" onclick="return update_qnty();">Update Quantity</button>
					<button type="button" onclick="return close_model_qnty();" class="btn btn-primary waves-effectk" style="background-color: #F34621;" data-dismiss="modal">Close</button>
				</div>
			</div>
       </div>
	</div>	
	<div class="modal" id="modal-backdrop-ignore" data-backdrop="static" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title pull-left">Update Price </h5>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<input type="text" onkeypress="return isNumber(event)" maxlength="6" class="form-control " placeholder="New Price" name="new_price" id="new_price" />
						<i class="form-group__bar"></i>
						<input type="hidden" name="product_id" id="product_id" />
                  </div>
              </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary waves-effect" onclick="return update_price();">Update Price</button>
					<button type="button" onclick="return close_model();" class="btn btn-primary waves-effectk" style="background-color: #F34621;" data-dismiss="modal">Close</button>
				</div>
			</div>
       </div>
	</div>
	
   <?php echo $footer; ?>
	<style>
	.tile  .selected 
	{
		border: 4px #4390df solid;
	}
	.tile  .selected:after 
	{
		position: absolute;
		display: block;
		border-top: 28px solid #4390df;
		border-left: 28px solid transparent;
		right: 0;
		content: "";
		top: 0;
		z-index: 101;
	}
	.tile  .selected:before 
	{
		position: absolute;
		display: block;
		content: "\e003";
		color: #fff;
		right: 0;
		font-family: 'metroSysIcons';
		font-size: 9pt;
		font-weight: normal;
		z-index: 102;
		top: 0;
	}
	.selecteditem
	{
		border: 4px solid #2196f3;
	}
	.plus
	{
		cursor: pointer;
		color: green;
		font-size: 18px;
	}
	.minus
	{
		cursor: pointer;
		color: red;
		font-size: 19px;
	}
	.cart_remove
	{
		color: red !important;
		font-size: 25px;
	}
	.qty
	{
		font-size: 15px;
	}
	.important 
	{
		background-color: rgba(243, 243, 243, 0.52) !important;
	}
	.active
	{
		background-color: rgb(243, 70, 33) !important;
	}
</style>

<script type="text/javascript">
	function add_to_favourite(pid)
	{
		$.ajax({
            url: 'index.php?route=pos/pos/add_to_favourite&token=<?php echo $token; ?>&product_id='+pid,
            type: 'post',
            data: { product_id: pid},
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
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
				if(json=='1')
				{
					var favourite='<span class="favourite"  onclick="return already_added_to_favourite('+pid+');" id="favourite'+pid+'" ><i class="zmdi zmdi-favorite"></i></span>';
					$("#fav_top_span_"+pid).html(favourite);
					alertify.success('Product added to favourite.');
				}
				else 
				{
					alertify.error('Some error occur.please try again');
				}
			}, 
			error:function (json)
			{
             
                alert(JSON.stringify(json));
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
			}
        });
		return false;
	}
	function already_added_to_favourite(pid)
	{
		$.ajax({
            url: 'index.php?route=pos/pos/remove_favourite&token=<?php echo $token; ?>&product_id='+pid,
            type: 'post',
            data: { product_id: pid},
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
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
				if(json=='1')
				{
					var favourite='<span class="favourite"  onclick="return add_to_favourite('+pid+');" id="favourite'+pid+'" ><i class="zmdi zmdi-favorite-outline"></i></span>';
					$("#fav_top_span_"+pid).html(favourite);
					alertify.success('Product removed from favourite.');
				}
				else 
				{
					alertify.error('Some error occur.please try again');
				}
			}, 
			error:function (json)
			{
				alert(JSON.stringify(json));
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
			}
        });
		return false;
	}
	function isNumber(evt) 
	{
		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if((charCode==46))
		{
			return true;
		}
		if (charCode > 31 && (charCode < 48 || charCode > 57)) 
		{
			return false;
		}
		return true;
	}
	function update_price()
	{
		var pid=$("#product_id").val();
		var price=$("#new_price").val();
		if(price=='0')
		{
			alertify.error('Price can not be 0');
			return false;
		}
		if(price=='')
		{
			alertify.error('Price can not be empty');
			return false;
		}
		
		var ttax=$("#tax"+pid).val().replace('Rs.', '');
		ttax=ttax.replace(',', '');
		ttax=parseFloat(ttax).toFixed(2);
		//alert(ttax);
		var oldprice=$("#price"+pid).val();
		oldprice=oldprice.replace('Rs.', '');
		oldprice=oldprice.replace(',', '');
		oldprice=parseFloat(oldprice).toFixed(2);
		//alert(oldprice);
		var tax_percent=parseFloat((parseFloat(ttax)*100)/parseFloat(oldprice));
		//alert(tax_percent);
		var new_price=parseFloat((parseFloat(price)*100)/(100+parseFloat(tax_percent)));
		new_price=new_price.toFixed(2);
		//alert(new_price);
		var id=pid;
		var current_qnty=$("#qntty"+id).html();
		$("#price"+pid).val('Rs.'+price);
		//alert(id);
		removeFromCart($("#row"+id).attr('data-key')); 
		//alert(price);
		$("#prdtr"+id).remove();
		setTimeout( function()
		{ 
			// Do something after 1 second 
			addToCart(id,parseInt(current_qnty),parseFloat(new_price));
		}  , 1000 );
    
		//alert(price);
		$("#product_id").val('');
		$("#new_price").val('');
		$('.modal').hide();
		return false
	}
	function update_qnty()
	{
		var current_qnty=$("#new_qnty").val();
		if(current_qnty=='0')
		{
			alertify.error('Quantity can not be 0');
			return false;
		}
		if(current_qnty=='')
		{
			alertify.error('Quantity can not be empty');
			return false;
		}
		var pid=$("#product_id_qnty").val();
		
		var oldprice=$("#price"+pid).val();
		oldprice=oldprice.replace('Rs.', '');
		oldprice=oldprice.replace(',', '');
		oldprice=parseFloat(oldprice).toFixed(2);
		//alert(oldprice);
		
		var new_price=oldprice;
		//alert(new_price);
		var id=pid;
		
		$("#quantity"+pid).val(current_qnty);
		//alert(id);
		removeFromCart($("#row"+id).attr('data-key')); 
		//alert(price);
		$("#prdtr"+id).remove();
		setTimeout( function()
		{ 
			// Do something after 1 second 
			addToCart(id,parseInt(current_qnty),parseFloat(new_price));
		}  , 1000 );
    
		//alert(price);
		$("#product_id_qnty").val('');
		$("#new_qnty").val('');
		$('.modal_qnty').hide();
		return false
	}
	function open_model(pid)
	{ 
		var price=$("#price"+pid).val();
		price=price.replace('Rs.', '');
		price=price.replace(',', '');
		price=parseFloat(price);
	
		var quantity=parseInt($("#quantity"+pid).val());
		var ttax=$("#tax"+pid).val().replace('Rs.', '');
		ttax=parseFloat(ttax);
		var perpeicetax=parseFloat(ttax)/parseFloat(quantity);
		
		var fullprice=parseFloat(price)+parseFloat(perpeicetax);
		fullprice=parseFloat(fullprice);
	
		$("#product_id").val(pid);
		$("#new_price").val(fullprice);
		$('.modal').show();
		return false;
       
	}
	function close_model()
	{
		$("#product_id").val('');
		$("#new_price").val('');
		$('.modal').hide();
		return false;
	}
	function open_model_qnty(pid)
	{ 
		//alert(pid);
		var price=$("#price"+pid).val();
		price=price.replace('Rs.', '');
		price=price.replace(',', '');
		price=parseFloat(price);
	
		var quantity=parseInt($("#quantity"+pid).val());
		var ttax=$("#tax"+pid).val().replace('Rs.', '');
		ttax=parseFloat(ttax);
		var perpeicetax=parseFloat(ttax)/parseFloat(quantity);
		
		var fullprice=parseFloat(price)+parseFloat(perpeicetax);
		fullprice=parseFloat(fullprice);
	
		$("#product_id_qnty").val(pid);
		$("#new_qnty").val(quantity);
		$('.modal_qnty').show();
		return false;
       
	}
	function close_model_qnty()
	{
		$("#product_id_qnty").val('');
		$("#new_qnty").val('');
		$('.modal_qnty').hide();
		return false;
	}

	$(window).scroll(function() 
	{
		if($(window).scrollTop() == $(document).height() - $(window).height()) 
		{
			// ajax call get data from server and append to the div
			var catid=$("#cat_id").val();
			var page=$("#current_page").val();
			var no_more=$("#no_more").val();
			//$(".no_more_found").html(''); 
			if((catid) && (page) && (!no_more))
			{
				getItems(catid, page);
			}
		}
	});
 
	$(document).ready(function()
	{
		//$("#pos_form").submit(function()
		$("#order").click(function()
		{
			//return false;
			var product_ids=$("#cart-total").html();
			if(product_ids=='Rs.0.00')
			{
				alertify.error('Please select a product');
				return false;
			}
			else if(product_ids=='Rs.0')
			{
				alertify.error('Please select a product');
				return false;
			}
			else
			{
				alertify.confirm('Are you sure you want to place order ?',
                function(e)
				{ 
                    if(e)
					{
						//alertify.success(e); 
						$(".page-loader").addClass("important");
						$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
						$(".page-loader").show();
						$("#pos_form").submit();
						return true;
					}
					else
					{
						//alertify.error(''); 
						return false;
					}
					return false;
				}
				);
				$("#alertify-ok").html('Continue');
				return false;
				/*
				var conf=confirm("Are you sure you want to place order ?");
				if(conf)
				{
					return true;
				}
				else
				{
					return false;
				}
				*/
			}
		});
    
	});
	$(document).ready(function()
	{
		try
		{
			getItems("<?= $categories[0]['category_id'] ?>",1);
		}
		catch(e)
		{
			alert(e);
		}
	});
	$("a.category").click(function ()
	{
		getItems($(this).attr("data-category-id"),1);
    }
    );

	function getItems($id, $page)
	{
		$(".cat_list").removeClass("active");
		$("#cat_idd"+$id).addClass("active");
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
			//get category list
			$.post('index.php?route=pos/pos/getCategoryItems&token=<?php echo $token; ?>&billtype=<?php echo $billtype; ?>',{ category_id: $id, page: $page }, function(data)
			{
				$("#cat_id").val($id);
				var data = JSON.parse(data);
				for(var i = 0; i < data.products.length; i++)
				{
					//alert(data.products[i]['stock_text']);
					//alert(data.products[i]['favourite']);
					if(data.products[i]['favourite']=='0')
					{
						var favourite='<span style="float: right;margin-top: -17px;color: red;" id="fav_top_span_'+data.products[i]['id']+'"><span class="favourite"  onclick="return add_to_favourite('+data.products[i]['id']+');" id="favourite'+data.products[i]['id']+'" ><i class="zmdi zmdi-favorite-outline"></i></span></span>';
					}
					else
					{
						var favourite='<span style="float: right;margin-top: -17px;color: red;" id="fav_top_span_'+data.products[i]['id']+'"><span class="favourite"  onclick="return already_added_to_favourite('+data.products[i]['id']+');" id="favourite'+data.products[i]['id']+'" ><i class="zmdi zmdi-favorite"></i></span></span>';
					}
					var shareproduct='<a target="_blank" href="https://web.whatsapp.com/send?text=https://unnatiagro.in/product/product.php?'+data.products[i]['pid']+'" data-action="share/whatsapp/share"><i style="float: right;font-size: 16px;color: rgb(33, 150, 243);margin-top: -15px;" class="zmdi zmdi-share"></i></a>';
					html += '<div id="pr'+data.products[i]['id']+'" class="col-xl-4 col-lg-6 col-sm-12 col-12 productdiv">';  
		   
					html += '<div data-product-id="'+data.products[i]['id']+'"  data-has-option="'+data.products[i]['hasOptions']+'"  class="contacts__item" id="pr_class'+data.products[i]['id']+'">';
					html +=favourite;
					
					html += '<div class="tile" id="col'+data.products[i]['id']+'"  data-title="'+data.products[i]['name']+'" data-price="'+data.products[i]['price_text']+'" ><div style="display: none;" class="tile-content image">';
					html += '<img style="height:103px;width:150px" src="'+data.products[i]['image']+'">';
					html += '</div><div class="contacts__info" ><strong>'; 
					html += data.products[i]['name']+"</strong><small> Qty. "+data.products[i]['stock_text']+"</small>";
					html += '</div> <button class="contacts__btn">'+data.products[i]['store_price_text']+'</button>';
					html +='<div class="contacts__info"><strong>'+data.products[i]['chemical_name']+'</strong></div>';
					html +='</div>'; 
					html +=shareproduct;
					html +='</div></div>'; 
				}  
				if(i>0)
				{	
					$page++;  //check is start page 
					if($page == 2)
					{
						$('.contacts').html(html);
						//$('.cart_table tbody tr').remove();
					}
					else
					{
						$('.contacts').append(html);
					}
					$("div.tile").click(
                
						function ()
						{ 
							var id=($(this).attr("id")).replace("col", "");
							var quantity=1;
							addToCart(id,quantity,'');
						}
					);
					var table = document.getElementById('ctable');
					if(table.rows.length>1)
					{
						for(var i = 0; i < data.products.length; i++)
						{
							product_select(data.products[i]['id']);
						}
					}
					$("#cat_id").val($id);
					$("#current_page").val($page);
				}
				else
				{
					if($page >1)
					{
						$('.contacts').append('<span class="no_more_found" id="no_more_found" style="text-align: right;width: 100%;font-weight: bold;text-align: center;color: rgb(33, 150, 243);font-size: 14px;">No more products found in this category!</span>');
						$("#no_more").val('no_more');
					}
					else
					{
						$('.contacts').html('<span class="no_more_found" id="no_more_found" id="no_more_found" style="text-align: right;width: 100%;font-weight: bold;text-align: center;color: rgb(33, 150, 243);font-size: 14px;">No product found in this category!</span>');
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
	function decrease_qnty(id)
	{
		var current_qnty=$("#qntty"+id).html();
		//alert(current_qnty);
		var new_qnty=(parseInt(current_qnty)-1);
		var price=$("#price"+id).val();
		price=price.replace('Rs.', '');
		price=price.replace(',', '');
		price=parseFloat(price);
		$("#please_wait_span").remove();
		$(".page-loader").addClass("important");
		$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
		$(".page-loader").show();
		removeFromCart($("#row"+id).attr('data-key')); 
		$("#prdtr"+id).remove();	
		setTimeout( function()
		{ 
			// Do something after 1 second
			addToCart(id,parseInt(new_qnty),price);
		}  , 1000 );
		if(parseInt(new_qnty)==0)
		{
			uncheck('row'+id);
		}
	}
	function increase_qnty(id)
	{ 
		var current_qnty=$("#qntty"+id).html();

		var new_qnty=(parseInt(current_qnty)+1);
		var price=$("#price"+id).val();
		price=price.replace('Rs.', '');
		price=price.replace(',', '');
		price=parseFloat(price);
		$("#please_wait_span").remove();
		$(".page-loader").addClass("important");
		$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
		$(".page-loader").show();
		removeFromCart($("#row"+id).attr('data-key')); 
		$("#prdtr"+id).remove();
		setTimeout( function()
		{ 
			// Do something after 1 second 
			addToCart(id,parseInt(new_qnty),price);
		}  , 1000 );

	}

	function update_cart($products,$total_data)
	{    
		var html  = '';
		for(var i=0; i< $products.length; i++)
		{       
			html += '<tr id="prdtr'+$products[i]['id']+'"><td>'+$products[i]['name']+'<br />'; 
         
			//document.getElementById('pr'+$products[i]['id']).style.border = "thick solid #0000FF";
			try{
			document.getElementById('pr_class'+$products[i]['id']).classList.add("selecteditem");
			}
			catch(err)
			{
				//alert(err+$products[i]['id']);
			}
		
			ttax=$products[i]['tax'].replace('Rs.', '');
			ttax=parseFloat(ttax);
			var perpeicetax=parseFloat(ttax)/parseFloat($products[i]['quantity']);
		
			var pprice=$products[i]['price'].replace('Rs.', '');
			pprice=pprice.replace(',', '');
			pprice=parseFloat(pprice);
			var fullprice=parseFloat(pprice)+parseFloat(perpeicetax);
			fullprice=parseFloat(fullprice);
		
			for(var j=0; j < $products[i]['option'].length; j++) 
			{
				html += '- <small>'+$products[i]['option'][j]['name']+' '+ $products[i]['option'][j]['value']+ '</small><br />';
			}
			html += '</td><td class="qty"><span class="minus"  onclick="return decrease_qnty('+$products[i]['id']+');" id="minus'+$products[i]['id']+'" >-</span><span style="margin-left: 6px;margin-right: 6px;cursor: pointer;" onclick="return open_model_qnty('+$products[i]['id']+');" data-key="'+$products[i]['key']+'" class="qty" id="qntty'+$products[i]['id']+'" >'+$products[i]['quantity']+'</span><span class="plus"  onclick="return increase_qnty('+$products[i]['id']+');" id="plus'+$products[i]['id']+'">+</span></td>';
			html += '<td style="cursor: pointer;" onclick="return open_model('+$products[i]['id']+');">Rs.'+fullprice+'</td>';//$products[i]['price'].slice(0,-3)
			html += '<td>'+$products[i]['tax']+'</td>';
			html += '<td>'+$products[i]['total']+'</td>';
			html += '<td><a class="cart_remove" data-key="'+$products[i]['key']+'" id="row'+$products[i]['id']+'"><i class="zmdi zmdi-delete zmdi-hc-fw"></i></a></td>';
      
			html += '<input type="hidden" class="product_ids" name="product_id[]"  id="product_id'+$products[i]['id']+'" value="'+$products[i]['id']+'">';
			html += '<input type="hidden" class="category_id" name="category_id[]"  id="category_id'+$products[i]['category_id']+'" value="'+$products[i]['category_id']+'">';
			html += '<input type="hidden"  name="quantity[]"  id="quantity'+$products[i]['id']+'" value="'+$products[i]['quantity']+'">';
			html += '<input type="hidden"  name="name[]" id="name'+$products[i]['id']+'" value="'+$products[i]['name']+'">';
			html += '<input type="hidden"  name="price[]" id="price'+$products[i]['id']+'" value="'+$products[i]['price']+'">';
			html += '<input type="hidden"  name="tax[]" id="tax'+$products[i]['id']+'" value="'+$products[i]['tax']+'">';
			html += '<input type="hidden"  name="total[]" id="total'+$products[i]['id']+'" value="'+$products[i]['total']+'">';       
			html += '</tr>';
             
		}
		if(i>0)
		{
			$("#products_div").show();
			$("#no_products_div").hide();
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
				ttax = '<td style="text-align:center;"><span id="tax-total">'+ rsstr+lottax+'</span><input type="hidden" id="ttax" name="ttax" value="'+rsstr+lottax+'"></td>';
            
			}
        
			else if($total_data[i].code=='sub_total')
			{
				subtotal= '<td style="text-align:center;"><span id="sub-total">'+$total_data[i].text+'</span><input type="hidden" id="subtotal" name="subtotal" value="'+$total_data[i].text+'"></td>';
			}
			else if($total_data[i].code=='total')
			{
				total = '<td style="text-align:center;"><span id="cart-total">'+$total_data[i].text+'</span> <input type="hidden" id="total" name="total" value="'+$total_data[i].text+'" ></td>';
			}
        
		} 
    
		// alert(ttax);
		if(ttax=='0')
		{
			ttax = '<td style="text-align:center;"><span id="cart-total">₹0.00</span><input type="hidden" id="ttax" name="ttax" value="₹0.00"></td>';
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
			uncheck($(this).attr('id'));
			removeFromCart($(this).attr('data-key'));        
			$(this).parentsUntil('tbody').remove();    
        
		});
	}

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
						ttax = '<td style="text-align:center;"><span id="tax-total">'+ rsstr+lottax+'</span><input type="hidden" id="ttax" name="ttax" value="'+rsstr+lottax+'"></td>';
            
					}
        
					else if($total_data[i].code=='sub_total')
					{
						subtotal= '<td style="text-align:center;"><span id="sub-total">'+$total_data[i].text+'</span><input type="hidden" id="subtotal" name="subtotal" value="'+$total_data[i].text+'"></td>';
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
						total = '<td style="text-align:center;"><span id="cart-total">'+$total_data[i].text+'</span> <input type="hidden" id="total" name="total" value="'+$total_data[i].text+'" ></td>';
					}
        
				} 
				if(ttax=='0')
				{
					ttax = '<td style="text-align:center;"><span id="cart-total">₹0.00</span><input type="hidden" id="ttax" name="ttax" value="₹0.00"></td>';
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

	function uncheck (cid)
	{
		str=cid;
		var pid=str.replace('row','');
		//alert(pid);
		$('#pr_class'+pid).removeClass("selecteditem");
	//document.getElementById('pr'+pid).style.border = "";
	}
	function product_select(pid)
	{
		try
		{
			var table = document.getElementById('ctable');
			rows = table.rows;//table.getElementsByTagName('tr');
			var i;
			var j;
			var cells;
			var customerId;
			for (i = 0, j = rows.length; i < j; ++i) 
			{
				cells = rows[i].cells;
				if (!cells.length) 
				{
					continue;
				}
				//for (k = 0; k < cells.length; k++) {
				customerId = cells[5].innerHTML;//getElementsByTagName('a');//innerHTML;
				if(customerId!='')
				{  
					if(customerId.indexOf('row'+pid)!=-1)
					{
						document.getElementById('pr_class'+pid).classList.add("selecteditem");
					}
				}
				//}
			}
 
		}
		catch(e)
		{
			alert(e);
		}
	}
</script>