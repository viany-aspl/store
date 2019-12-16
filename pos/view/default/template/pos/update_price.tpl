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
                            <input type="text" name="filter_product" placeholder="Search Products...">

                            <i class="toolbar__search__close zmdi zmdi-long-arrow-left" onclick="return getpreviositems();" data-ma-action="toolbar-search-close"></i>
                        </div>
     </div>  
	 <input type="hidden" name="cat_id" id="cat_id" />
	 <input type="hidden" name="current_page" id="current_page" />
	 <input type="hidden" name="no_more" id="no_more" />
                     <div class="contacts row " style="min-height: 300px;">
                    <div class="col-md-2">
					  <div class="card animation-demo">
                            <div class="card-header">
                                <h2 class="card-title">Flippers</h2>                                
                            </div>

                            <div class="card-block">
                                <img class="animated" src="demo/img/headers/sm/6.png" alt="">

                                <div class="row">
                                    <div class="col-sm-6">
                                        <button class="btn btn-secondary">flip</button>
                                    </div>
                                    <div class="col-sm-6">
                                        <button class="btn btn-secondary">flipInX</button>
                                    </div>
                                    
                                </div>
								 
                            </div>
                        </div>
                    </div>
                
            </div>
                            
 <!-- Ignore backdrop click -->
                            <div class="modal" id="modal-backdrop-ignore" data-backdrop="static" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title pull-left">Update Price (<span id='p_name'></span>)</h5>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                            
                                                <input type="number" min="0.01" step="0.01" class="form-control numberinput" placeholder="New Price" name="new_price" id="new_price" />
                                            <i class="form-group__bar"></i>
                                            <input type="hidden" name="category_id" id="category_id" />
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
 <style>
    
.numberinput{ 
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0; 
}
 </style>
   <?php echo $footer; ?>
                    

<script type="text/javascript">
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
function getpreviositems()
{
    //alert('kk');
    location.reload();
}
function update_price()
{
    var new_price=$("#new_price").val();
    var prd_id=$("#product_id").val();
    var cat_id=$("#category_id").val();
    if(new_price)
    {
        var p_name=$("#p_name").html();
        var cnfrm=confirm('Are you sure ? You want to update Price for '+p_name);
        if(cnfrm)
        {
            $.ajax({
			url: 'index.php?route=pos/inventory_manager/update_price&token=<?php echo $token; ?>&product_id=' +prd_id+'&new_price=' +  encodeURIComponent(new_price),
			dataType: 'json',
			beforeSend: function() 
			{
			
			$(".page-loader").addClass("important");
			$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
			$(".page-loader").show();
			},
			complete: function() 
			{
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
			},
			success: function(json) 
            { //alert(json);
				
                            close_model();
                            if(!cat_id)
                            {
                                $("#price"+prd_id).html('<?php echo RUPPE_SIGN; ?>'+json);
                            }
                            else
                            {
                                getItems(cat_id, 1);
                            }
				$(".page-loader").removeClass("important");
				$("#please_wait_span").remove();
				$(".page-loader").hide();
                            alertnotify('Price successfully updated');
                            
			}
		});
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}
function open_model(catid,pid,p_name)
{ 
    $("#category_id").val(catid);
    $("#product_id").val(pid);
    $("#p_name").html(p_name);
    $('.modal').show();
       
}
function close_model()
{
    $("#new_price").val('');
    $("#category_id").val('');
    $("#product_id").val('');
    $("#p_name").html('');
    $('.modal').hide();
}
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
});
function getItems($id, $page)
{
try{
	if($page == 1)
    {
		$("#no_more").val('');
	}
    var html = '';
    //get category list
	
			
			$(".page-loader").addClass("important");
			$(".page-loader").append('<?php echo please_wait_span_display; ?> ');
			$(".page-loader").show();
		
    $.post('index.php?route=pos/inventory_manager/getCategoryItems&token=<?php echo $token; ?>',{ category_id: $id, page: $page }, function(data)
    {
        var data = JSON.parse(data);
        for(var i = 0; i < data.products.length; i++)
        {
            //alert(data.products[i]['stock_text']);
            html += "<div class='col-md-2' onclick='return open_model("+$id+","+data.products[i]['id']+",`"+data.products[i]['name']+"`);' >";
            html += '<div data-product-id="'+data.products[i]['id']+'" class="card animation-demo">';
            html += '<div class="card-header">';
            html += "<h2 class='card-title'  style='font-size: 13px;min-height: 29px;height: 29px;overflow: hidden;'>"+data.products[i]['name']+"</h2>";                              
            html += '</div>';
            html += '<div class="card-block">';
            html += '<img class="animated" style="height: 90px;width: 100%;object-fit:contain;margin-bottom:5px;display: none;" src="'+data.products[i]['image']+'" alt="">';
            html += '<div class="row">';
            html += '<div class="col-sm-6">';
            html += '<button class="btn btn-secondary">'+data.products[i]['stock_text']+'</button>';
            html += '</div>';
            html += '<div class="col-sm-6" style="padding-left: 5px;margin-left: -10px;">';
            html += '<button class="btn btn-secondary" id="price'+data.products[i]['id']+'">'+data.products[i]['store_price_text']+'</button>';
            html += '</div>';
			html +='<div class="contacts__info" style="width: 100%;text-align: center;"><br><strong>'+data.products[i]['chemical_name']+'</strong></div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
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
			$('.contacts').html('<span class="no_more_found" id="no_more_found" style="text-align: right;width: 100%;font-weight: bold;text-align: center;color: rgb(33, 150, 243);font-size: 14px;">No product found in this category!</span>');
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

$(document).ready(function() 
{ 
    $('input[name=\'filter_product\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=pos/inventory_manager/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						
						value: item['id'],
                                                image: item['image'],
                                                price_text: item['price_text'],
                                                stock_text: item['stock_text'],
                                                tax: item['tax'],
                                                store_price_text: item['store_price_text'],
												chemical_name: item['chemical_name']
					}
				}));
			}
		});
	},
	'select': function(item) 
        {
            $("#row_groups").html('');
		$('input[name=\'filter_product\']').val(item['label']);
               
                    var html='';
                    html += "<div class='col-md-2' onclick='return open_model(``,"+item['value']+",`"+item['label']+"`);' >";
                    html += '<div data-product-id="'+item['value']+'" class="card animation-demo">';
                    html += '<div class="card-header">';
                    html += '<h2 class="card-title" style="font-size: 13px;min-height: 29px;height: 29px;overflow: hidden;">'+item['label']+'</h2>';                              
                    html += '</div>';
                    html += '<div class="card-block">';
                    html += '<img class="animated" style="height: 90px;width: 100%;object-fit:contain;margin-bottom:5px; display: none;" src="'+item['image']+'" alt="">';
                    html += '<div class="row">';
                    html += '<div class="col-sm-6">';
                    html += '<button class="btn btn-secondary">'+item['stock_text']+'</button>';
                    html += '</div>';
                    html += '<div class="col-sm-6">';
                    html += '<button class="btn btn-secondary" id="price'+item['value']+'">'+item['store_price_text'].slice(0, -3)+'</button>';
					html += '</div>';
					html +='<div class="contacts__info" style="width: 100%;text-align: center;"><br><strong>'+item['chemical_name']+'</strong></div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $(".contacts").html(html);
	}
});
});
</script> 



    
    