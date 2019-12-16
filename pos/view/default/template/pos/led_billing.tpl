<?php echo $header; ?><?php echo $column_left; ?>

 
                     <!--=========================== top category list ==========================-->
    <div class="toolbar">      
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
  <div class="row">
      
  <div class="contacts row col-6">
              
     </div>           
       
  <form method="post" id="pos_form" action="index.php?route=pos/pos/product_payment&token=<?php echo $token; ?>" >             
   <div class="col-10">
        
<div class="card" style="min-height: 200px;">
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
                     <div class="card">
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
       <div class="col-2">   
          
   
           <button style="padding: 0px 12px;text-align: center;vertical-align: middle !important;background-color: #d9d9d9;border: 1px transparent solid;color: #222222;border-radius: 0;cursor: pointer;display: inline-block;outline: none;font-family: 'Segoe UI Light_', 'Open Sans Light', Verdana, Arial, Helvetica, sans-serif;font-size: 14px;line-height: auto;margin: auto;text-align: left;font-size: 14pt;width: auto;letter-spacing: 45px;word-break: break-all;background: #60a917;color: white;width: 100%;" 
                   type="submit" id="order" href="#order_wrapper" class="btn-primary" >
                      <center>
                        <i class="icon-dollar"></i>
                        <span class="text-center"> place Order</span>
                      </center>
               </button> 
           </div> 
    
      </form>
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
                     </style>

<script type="text/javascript">
    $(document).ready(function(){
    $("#pos_form").submit(function(){
        var conf=confirm("Are you sure you want to place order ?");
        if(conf)
        {
            return true;
        }
        else
        {
            return false;
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
    
    var html = '';

    //get category list
    $.post('index.php?route=pos/pos/getCategoryItems&token=<?php echo $token; ?>',{ category_id: $id, page: $page }, function(data){
   

        var data = JSON.parse(data);
        
        for(var i = 0; i < data.products.length; i++)
        {
          //alert(data.products[i]['stock_text']);
             
           html += '<div id="pr'+data.products[i]['id']+'" class="col-xl-4 col-lg-6 col-sm-12 col-12">';             
           html += '<div data-product-id="'+data.products[i]['id']+'"  data-has-option="'+data.products[i]['hasOptions']+'"  class="contacts__item">';
           html += '<div class="tile" id="col'+data.products[i]['id']+'"  data-title="'+data.products[i]['name']+'" data-price="'+data.products[i]['price_text']+'" ><div class="tile-content image">';
           html += '<img style="height:103px;width:150px" src="'+data.products[i]['image']+'">';
           html += '</div><div class="contacts__info" ><strong>'; 
           html += data.products[i]['name']+"</strong><small>"+data.products[i]['stock_text']+"</small>";
           html += '</div> <button class="contacts__btn">'+data.products[i]['store_price_text'].slice(0, -3)+'</button></div></div></div>'; 
        }        
        $page++;        
       
         
        //check is start page 
        if($page == 2){
        
            $('.contacts').html(html);
            
           
             //$('.cart_table tbody tr').remove();

     

        }else{
           
            $('.contacts').append(html);
          
            
        }
        
        $("div.tile").click(
        function (){
            

var id=($(this).attr("id")).replace("col", "");
var quantity=1;
addToCart(id,quantity);
        }
        );
    var table = document.getElementById('ctable');
    if(table.rows.length>1){
            for(var i = 0; i < data.products.length; i++)
            {
                product_select(data.products[i]['id']);
            }
        }
    });
}
catch(e){alert(e);}

}


function addToCart(id,quantity){
    $.ajax({
            url: 'index.php?route=pos/pos/addToCart&token=<?php echo $token; ?>',
            type: 'post',
            data: { product_id: id, quantity: quantity },
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
                    //alert(json['products']);
                     
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


function update_cart($products,$total_data){    
    //alert(JSON.stringify($products));
    var html  = '';

    for(var i=0; i< $products.length; i++){       
        html += '<tr><td>'+$products[i]['name']+'<br />'; 
         
        document.getElementById('pr'+$products[i]['id']).style.border = "thick solid #0000FF";
        //option
        for(var j=0; j < $products[i]['option'].length; j++) {
            html += '- <small>'+$products[i]['option'][j]['name']+' '+ $products[i]['option'][j]['value']+ '</small><br />';
        }
        html += '</td><td class="qty"><span class="minus">-</span><span data-key="'+$products[i]['key']+'" class="qty">'+$products[i]['quantity']+'</span><span class="plus">+</span></td>';
        html += '<td>'+$products[i]['price'].slice(0,-3)+'</td>';
        html += '<td>'+$products[i]['tax'].slice(0,-3)+'</td>';
        html += '<td>'+$products[i]['total'].slice(0,-3)+'</td>';
        html += '<td><a class="cart_remove" data-key="'+$products[i]['key']+'" id="row'+$products[i]['id']+'"><i class="zmdi zmdi-delete zmdi-hc-fw"></i></a></td>';
      
       html += '</tr>';
       html += '<input type="hidden"  name="product_id[]"  id="product_id'+$products[i]['id']+'" value="'+$products[i]['id']+'">';
       html += '<input type="hidden"  name="quantity[]"  id="quantity'+$products[i]['id']+'" value="'+$products[i]['quantity']+'">';
       html += '<input type="hidden"  name="name[]" id="name'+$products[i]['id']+'" value="'+$products[i]['name']+'">';
       html += '<input type="hidden"  name="price[]" id="price'+$products[i]['id']+'" value="'+$products[i]['price']+'">';
       html += '<input type="hidden"  name="tax[]" id="tax'+$products[i]['id']+'" value="'+$products[i]['tax']+'">';
       html += '<input type="hidden"  name="total[]" id="total'+$products[i]['id']+'" value="'+$products[i]['total']+'">';             
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
            var str=($total_data[i].text.slice(3)).slice(0,-3);
            var ft=parseFloat(str);
            lottax+=ft;

            ttax = '<td style="text-align:center;"><span id="cart-total">'+ rsstr+lottax+'</span><input type="hidden" id="ttax" name="ttax" value="'+rsstr+lottax+'"></td>';

        }
        else if($total_data[i].code=='sub_total')
        {
            subtotal= '<td style="text-align:center;"><span id="cart-total">'+$total_data[i].text.slice(0,-3)+'</span><input type="hidden" id="subtotal" name="subtotal" value="'+$total_data[i].text.slice(0,-3)+'"></td>';
        }
        else if($total_data[i].code=='total')
        {
            total = '<td style="text-align:center;"><span id="cart-total">'+$total_data[i].text.slice(0,-3)+'</span> <input type="hidden" id="total" name="total" value="'+$total_data[i].text.slice(0,-3)+'" ></td>';
        }
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
        //alert('clicked');
        uncheck($(this).attr('id'));
        removeFromCart($(this).attr('data-key'));        
        $(this).parentsUntil('tbody').remove();    
        
    });
}

function removeFromCart($key){ //alert($key);
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
            for(var i=0; i < $total_data.length; i++)
            {
                //alert($total_data[i].code);
                if(i==0)
                {
                   ttax = '<td style="text-align:center;"><span id="cart-total">Rs.0</span><input type="hidden" id="ttax" name="ttax" value="Rs.0"></td>'; 
                }
                if($total_data[i].code=='tax')
                {
                    var rsstr=$total_data[i].text.substring(0,3);
                    var str=($total_data[i].text.slice(3)).slice(0,-3);
                    var ft=parseFloat(str);
                    lottax+=ft;
                    
                    ttax = '<td style="text-align:center;"><span id="cart-total">'+ rsstr+lottax+'</span><input type="hidden" id="ttax" name="ttax" value="'+rsstr+lottax+'"></td>';

                }
                
                if($total_data[i].code=='sub_total')
                {
                    subtotal= '<td style="text-align:center;"><span id="cart-total">'+$total_data[i].text.slice(0,-3)+'</span><input type="hidden" id="subtotal" name="subtotal" value="'+$total_data[i].text.slice(0,-3)+'"></td>';
                }
                else if($total_data[i].code=='total')
                { 
                    
                    total = '<td style="text-align:center;"><span id="cart-total">'+$total_data[i].text.slice(0,-3)+'</span> <input type="hidden" id="total" name="total" value="'+$total_data[i].text.slice(0,-3)+'" ></td>';
                }
                
            }
            
            html+=subtotal;
            html+=ttax;
            
            html+=total;
            html += '</tr>';
           
            $('#total_wrapper').html(html);
        },
        error(json)
        {
            alert(JSON.stringify(json));
        }
    });
}

function uncheck (cid)
{
    str=cid;
    var pid=str.replace('row','');
    $('#col'+pid).removeClass("selected");

  
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
       document.getElementById('pr'+pid).style.border = "thick solid #0000FF";
}

}
       
  //}

        
    }
 
}catch(e){alert(e);}

}
</script> 



    
    