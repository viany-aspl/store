<?php echo $header; ?><?php echo $column_left; ?>

 
                     <!--=========================== top category list ==========================-->
    <div class="toolbar" >      
        <nav class="toolbar__nav">
                
                <?php 
                
                $color_array = array('bg-lightBlue','bg-darkViolet','bg-darkCyan','bg-violet','bg-indigo','bg-magenta');
                $i = 0;
                $length = sizeof($color_array);
                $a=0;
                foreach($categories as $category){ ?>
                
                
                    <a class="<?php if($a==0){ echo 'active '; }  ?>green-box category" style="font-size: 10px;" 
                       data-category-id="<?= $category['category_id'] ?>" href="#"><?= $category['name'] ?></a>
                   
                
                
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
       <div class="row col-lg-6" id="products_div" style="display: none;">
  <form class="col-lg-12" style="padding-right:0px" method="post" id="pos_form" action="index.php?route=pos/pos/product_payment&token=<?php echo $token; ?>" >             
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
                    <!-- <div class="total_wrapper"></div>   --> 
                     </div>
            </div>
    
      </div>    
       <div class="col-lg-2" style="float: left;">   
          
   
           <button type="submit" id="order" href="#order_wrapper" class="btn-primary green_btn" >
                      <center>
                        <i class="icon-dollar"></i>
                        <span class="text-center"> Place Order</span>
                      </center>
               </button> 
           </div> 
    
      </form>
	  
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
                                            <button type="button" class="btn btn-link" onclick="return update_price();">Update Price</button>
                                            <button type="button" onclick="return close_model();" class="btn btn-link" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
   <?php echo $footer; ?>
<style>
.tile  .selected {
  border: 4px #4390df solid;
}
.tile  .selected:after {
  position: absolute;
  display: block;
  border-top: 28px solid #4390df;
  border-left: 28px solid transparent;
  right: 0;
  content: "";
  top: 0;
  z-index: 101;
}
.tile  .selected:before {
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
</style>

<script type="text/javascript">
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
	if((charCode==46))
	{
		return true;
	}
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
function update_price()
{
	var pid=$("#product_id").val();
    var price=$("#new_price").val();
	$("#price"+pid).val('Rs.'+price);
	
	var id=pid;
	var current_qnty=$("#qntty"+id).html();
   
	//alert(id);
    removeFromCart($("#row"+id).attr('data-key')); 
	//alert(price);
	$("#prdtr"+id).remove();
	setTimeout( function(){ 
    // Do something after 1 second 
	addToCart(id,parseInt(current_qnty),parseFloat(price));
	}  , 100 );
    
	//alert(price);
	$("#product_id").val('');
	$("#new_price").val('');
    $('.modal').hide();
	return false
}
function open_model(pid)
{ 
    var price=$("#price"+pid).val();
	
	price=price.replace('Rs.', '');
	
	price=parseFloat(price);
    $("#product_id").val(pid);
    $("#new_price").val(price);
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
    $("#pos_form").submit(function()
    {
        var product_ids=$("#cart-total").html();
        if(product_ids=='Rs.0.00')
        {
            alert('Please select a product');
            return false;
        }
        else if(product_ids=='Rs.0')
        {
            alert('Please select a product');
            return false;
        }
        else
        {
            var conf=confirm("Are you sure you want to place order ?");
            if(conf)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    });
    
});
$(document).ready(function(){
 
try{

 getItems("<?= $categories[0]['category_id'] ?>",1);
}catch(e){alert(e);}
});
$("a.category").click(
        function (){
            

getItems($(this).attr("data-category-id"),1);
        }
        );

function getItems($id, $page)
{
try{
    if($page == 1)
    {
		$("#no_more").val('');
	}
    var html = '';

    //get category list
    $.post('index.php?route=pos/pos/getCategoryItems&token=<?php echo $token; ?>&billtype=<?php echo $billtype; ?>',{ category_id: $id, page: $page }, function(data){
   

        var data = JSON.parse(data);
        
        for(var i = 0; i < data.products.length; i++)
        {
          //alert(data.products[i]['stock_text']);
             
           html += '<div id="pr'+data.products[i]['id']+'" class="col-xl-4 col-lg-6 col-sm-12 col-12 productdiv">';             
           html += '<div data-product-id="'+data.products[i]['id']+'"  data-has-option="'+data.products[i]['hasOptions']+'"  class="contacts__item" id="pr_class'+data.products[i]['id']+'">';
           html += '<div class="tile" id="col'+data.products[i]['id']+'"  data-title="'+data.products[i]['name']+'" data-price="'+data.products[i]['price_text']+'" ><div style="display: none;" class="tile-content image">';
           html += '<img style="height:103px;width:150px" src="'+data.products[i]['image']+'">';
           html += '</div><div class="contacts__info" ><strong>'; 
           html += data.products[i]['name']+"</strong><small>"+data.products[i]['stock_text']+"</small>";
           html += '</div> <button class="contacts__btn">'+data.products[i]['store_price_text'].slice(0, -3)+'</button>';
		   html +='<div class="contacts__info"><strong>'+data.products[i]['chemical_name']+'</strong></div>';
		   html +='</div></div></div>'; 
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
    });
	
}
catch(e){alert(e);}

}


function addToCart(id,quantity,price){
    //alert(price);
    $.ajax({
            url: 'index.php?route=pos/pos/addToCart&token=<?php echo $token; ?>&billtype=<?php echo $billtype; ?>&price='+price+'&product_id='+id+'&quantity='+quantity,
            type: 'post',
            data: { product_id: id, quantity: quantity,price: price },
            dataType: 'json',
            success: function(json) {

                //alert(JSON.stringify(json));
                $('.success, .warning, .attention, information, .error').remove();

                if (json['error']) { //alert('hi 11');
                        if (json['error']['option']) {
                                for (i in json['error']['option']) {
                                        $('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
                                }
                        }
                }

                if (json['success']) {    //alert('hi2');                        
                    update_cart(json['products'], json['total_data']); 
                    
                     
                    $('.total_wrapper .pull-right div').fadeOut().delay(50).fadeIn('slow');
                    $('.product_list_bottom').addClass('hide');
                    
                }
                
            }, 
         
            error:function (json)
         {
             
                alert(JSON.stringify(json));
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
	price=parseFloat(price);
    removeFromCart($("#row"+id).attr('data-key')); 
    
	setTimeout( function(){ 
    // Do something after 1 second 
	addToCart(id,parseInt(new_qnty),price);
	}  , 100 );
    if(parseInt(new_qnty)==0)
    {
        uncheck('row'+id);
    }
}
function increase_qnty(id)
{ 
var current_qnty=$("#qntty"+id).html();
    //alert(current_qnty);
var new_qnty=(parseInt(current_qnty)+1);
var price=$("#price"+id).val();
price=price.replace('Rs.', '');
price=parseFloat(price);

removeFromCart($("#row"+id).attr('data-key')); 
setTimeout( function(){ 
    // Do something after 1 second 
	addToCart(id,parseInt(new_qnty),price);
	}  , 100 );

}

function update_cart($products,$total_data)
{    
    
    var html  = '';
    for(var i=0; i< $products.length; i++)
    {      
        html += '<tr id="prdtr'+$products[i]['id']+'"><td>'+$products[i]['name']+'<br />'; 
         
        //document.getElementById('pr'+$products[i]['id']).style.border = "thick solid #0000FF";
        document.getElementById('pr_class'+$products[i]['id']).classList.add("selecteditem");
        
        for(var j=0; j < $products[i]['option'].length; j++) 
        {
            html += '- <small>'+$products[i]['option'][j]['name']+' '+ $products[i]['option'][j]['value']+ '</small><br />';
        }
		var tttax=$products[i]['tax'];
		tttax=tttax.replace('Rs.', '');
		var pprice=$products[i]['price'];
		pprice=pprice.replace('Rs.', '');
	
		pprice=parseFloat(pprice);
		//alert(pprice);
		//alert(tttax);
		//alert($products[i]['quantity']);
		var fullprice=parseFloat(pprice)+parseFloat(parseFloat(tttax)/parseFloat($products[i]['quantity']));
		fullprice='Rs.'+fullprice;
		//alert(fullprice);
        html += '</td><td class="qty"><span class="minus"  onclick="return decrease_qnty('+$products[i]['id']+');" id="minus'+$products[i]['id']+'" >-</span><span data-key="'+$products[i]['key']+'" class="qty" id="qntty'+$products[i]['id']+'" >'+$products[i]['quantity']+'</span><span class="plus"  onclick="return increase_qnty('+$products[i]['id']+');" id="plus'+$products[i]['id']+'">+</span></td>';
        html += '<td style="cursor: pointer;" onclick="return open_model('+$products[i]['id']+');">'+fullprice+'</td>';//$products[i]['price'].slice(0,-3)
        html += '<td>'+$products[i]['tax']+'</td>';
        html += '<td>'+$products[i]['total']+'</td>';
        html += '<td><a class="cart_remove" data-key="'+$products[i]['key']+'" id="row'+$products[i]['id']+'"><i class="zmdi zmdi-delete zmdi-hc-fw"></i></a></td>';
      
        html += '</tr>';
        html += '<input type="hidden" class="product_ids" name="product_id[]"  id="product_id'+$products[i]['id']+'" value="'+$products[i]['id']+'">';
        html += '<input type="hidden"  name="quantity[]"  id="quantity'+$products[i]['id']+'" value="'+$products[i]['quantity']+'">';
        html += '<input type="hidden"  name="name[]" id="name'+$products[i]['id']+'" value="'+$products[i]['name']+'">';
        html += '<input type="hidden"  name="price[]" id="price'+$products[i]['id']+'" value="'+$products[i]['price']+'">';
        html += '<input type="hidden"  name="tax[]" id="tax'+$products[i]['id']+'" value="'+$products[i]['tax']+'">';
        html += '<input type="hidden"  name="total[]" id="total'+$products[i]['id']+'" value="'+$products[i]['total']+'">';             
    }
	if(i>0)
	{
		$("#products_div").show();
	}
        else
        {
            $("#products_div").hide();
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

function removeFromCart($key)
{ //alert($key);
    $.ajax({
        url: 'index.php?route=pos/pos/removeFromCart&token=<?php echo $token; ?>',
        type: 'post',
        data: { remove: $key },
        dataType: 'json',
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
                    }
                    else
                    {
                        $("#products_div").hide();
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
             document.getElementById('pr_class'+id).style.border = "";
        },
        error(json)
        {
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
try{
var table = document.getElementById('ctable');

  rows = table.rows;//table.getElementsByTagName('tr');

  var i;
  var j;
  var cells;
  var customerId;
  for (i = 0, j = rows.length; i < j; ++i) {
    cells = rows[i].cells;
    if (!cells.length) {
      continue;
    }
//for (k = 0; k < cells.length; k++) {
      customerId = cells[5].innerHTML;//getElementsByTagName('a');//innerHTML;
       if(customerId!=''){  
         
      if(customerId.indexOf('row'+pid)!=-1){
       document.getElementById('pr_class'+pid).classList.add("selecteditem");
}

}
       
  //}

        
    }
 
}catch(e){alert(e);}

}
</script>