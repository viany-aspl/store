<?php echo $header; ?><?php echo $column_left; ?>
<?php //echo $token; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><!--<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>--->
        <!--<button type="submit" form="form-product" formaction="<?php echo $copy; ?>" data-toggle="tooltip" title="<?php echo $button_copy; ?>" class="btn btn-default"><i class="fa fa-copy"></i></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>--->
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
      <form  method="post" enctype="multipart/form-data" id="form-product">
              <div class="col-sm-7">  
    <div class="panel panel-default">
     <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>       
      </div>
      <div class="panel-body">
        
       
       
            
       
        <div class="table-responsive">
        <table class="table table-bordered table-hover" >
        <thead>
          <tr>
			<td style="text-align: center;">Store Name</td>
			<td style="text-align: center;">Store User</td>
			<td style="text-align: center;">Request Date</td>
           <td style="text-align: center;"><?php echo $column_username;?></td>
           <td style="text-align: center;"><?php echo $column_name;?></td>
           <td style="text-align: center;"><?php echo $column_status;?></td>
           </tr>
           </thead>
           <tbody>
            <?php if ($products) { ?>
            <?php foreach ($products as $product) { //print_r($product);?>
            <tr>
			<td style="text-align: center;"><?php echo $product['storename']; ?></td>
			<td style="text-align: center;"><?php echo $product['storeinchargename']; ?></td>
			<td style="text-align: center;"><?php echo date('Y-m-d',$product['date_added']->sec); ?></td>
           <td style="text-align: center;"><?php echo $product['username']; ?></td>
		   
           <td style="text-align: center;">
		   <a href="" title="click to see details"  data-toggle="modal" value="<?php echo $product['product_id']; ?>" 
		   onclick="return set_product_id('<?php echo $product['product_id']; ?>','<?php echo $product['model']; ?>','<?php echo $product['sku']; ?>','<?php echo $product['HSTN']; ?>','<?php echo $product['image']; ?>');" 
		   id="product_detais" data-target="#exampleModal" >
		   <?php echo $product['model']; ?></a>
		   </td>
           <td style="text-align: center;"> <?php  if($product['status']==0){?>  
               <a type="button" class="btn btn-info btn-lg" value="<?php echo $product['product_id']; ?>" data-toggle="modal" data-target="#myModal" onclick="return set_sid(<?php echo $product['product_id']; ?>);" style="padding: 3px 7px;">
           <i class="fa fa-pencil-square-o"></i> </a>
           <?php } else if($product['status']==1){ ?>
           <p>Approved</p> 
            <?php } else if($product['status']==2) {?>
            <p>Rejected</p> 
            <?php } else { ?>  <p>Already in System</p> 
           
            <?php } ?></td>
         
            </tr>
          <?php } ?>
       <?php } ?>
      </tbody>
     </table>
          </div>
                 
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Product Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">

                  <div class="form-group" id="divforimage">
                    <label class="control-label" for="input-price">Image</label>
                    <img id="image" src="" class="img-thumbnail" />
                  </div>  
                          
                          
                          
                          
                          
                          
                      <table class="table">
                    <thead>
                      <tr>
                        <td>Product</td>
                        <td>SKU</td>
                        <td>HSN</td>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                          <td id="model"></td>
                          <td id="sku"></td>
                          <td id="HSTN"></td>
                      </tr>

                    </tbody>
                  </table>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                       <!-- <button type="button" class="btn btn-primary">Save changes</button>--->
                      </div>
                    </div>
                  </div>
                </div> 
               <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">

                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title">Product Status</h4>
                    </div>
                    <div class="modal-body">

                      <p>Do You Want To?.</p>
                       <input type="hidden" name="product_id"   value="" id="product_id" class="form-control" />
                       <button type="button" class="btn btn-primary" id="approved">Approve</button>
                       <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModalCenter">
                       Resubmit
                       </button>

                        <button type="button" class="btn btn-primary" data-toggle="modal"  data-target="#exampleModalLong">
                        Already In System
                        </button>
                        </div>
                        <div class="modal-footer">


                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>

                </div>
                   
                   
                   <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Already In System</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <div class="form-group">
           <input type="hidden" name="product_id"   value="" id="product_id" class="form-control" />
                <label class="control-label" for="input-price">Comment</label>
                <input type="text" name="comments"  placeholder="Comment" id="comments" class="form-control" />
              </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="already_syatem" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div> 
                   
                   
                   
                   
                   
                   
                   
              </div>
  
                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Reason</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                          <input type="hidden" name="product_id"   value="" id="product_id" class="form-control" />
                         <!-- <input type="radio" name="other_region" id="chkNo" onclick="ShowHideDiv()" value="Product has already used" checked="checked">Product has already used<br>-->
                       <input type="radio" name="other_region" value="Image Not Clear" id="chkNos" onclick="ShowHideDiv()" > Image is not clear<br>
                       <input type="radio" name="other_region" value="other" id="chkYes" onclick="ShowHideDiv()" > Other 

                      <div id="dvPassport" style="display: none">
                          <label>Other Reason</label>
                    <input type="text" name="region_input" class="form-control" id="region_input" />

                </div>

                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="button_submite">Save changes</button>
                      </div>
                    </div>
                  </div>
                </div>
        <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
     
           </div></div></div>
          
          
          

   <div class="panel panel-default  col-sm-5">
    <div class="panel-heading">
     <h3 class="panel-title" ><i class="fa fa-list"></i> <?php echo $text_check; ?></h3>
      </div>
       <div class="panel-body">
           
           
 
          <div class="form-group">
                <label class="control-label" for="input-price"><?php echo $column_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_model; ?>"  placeholder="<?php echo $column_name; ?>" id="input-model" class="form-control" />
              </div>
               
            
             <div class="form-group">
                <label class="control-label" for="input-price"><?php echo $column_company_name; ?></label>
                <input type="text" name="filter_company_name" value="<?php echo $filter_company_name; ?>"  placeholder="<?php echo $column_company_name; ?>" id="input-company_name" class="form-control" />
              </div>
             
            
           <div class="form-group">  
                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
           </div>

             
          
            
            <table class="table table-bordered table-hover">
             <thead>
               <tr>
                 <th>Category</th>
                
                 <th>Product Name</th>
               </tr>
             </thead>
             <tbody>
                 
               <?php if ($productrequest) { ?>
                <?php foreach ($productrequest as $results) { //print_r($results); ?>
               <tr>
                   <td><?php echo  $results['cname'];?></td>
                  
                 <td><?php echo  $results['pname'];?></td>
               
               </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
             </tbody>
           </table>
            
         
          
       
       </form> 
        
      
        <div class="row">
          <div class="col-sm-6 text-left"><?php //echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php //echo $results; ?></div>
        </div>
      
  </div>
   
    </div>    
    
  <script type="text/javascript">
      
    function ShowHideDiv() {
        var chkYes = document.getElementById("chkYes");
        var dvPassport = document.getElementById("dvPassport");
        dvPassport.style.display = chkYes.checked ? "block" : "none";
    }
    
    
     function set_sid(product_id)
      {
          
          $("#product_id").val(product_id);
        
          return false;
      }  
    
    
    function set_product_id(product_id,model,sku,HSTN,image)
      {
           //var imgfilename = "./././image/catalog/image";
           //alert(imgfilename);
          $("#product_id").html(product_id);
          $("#model").html(model);
          $("#sku").html(sku);
          $("#HSTN").html(HSTN);
          //$("#image").html(image);
          
          var url='index.php?route=catalog/producttemp/get_productimagebyID&token=<?php echo $token; ?>&product_id='+product_id;
          //alert(url);
            $.ajax({ 
            type: 'get',
            url: url,
            //data: {product_id:product_id},
            cache: false,
            success: function(data) {
				//alert(data);
            $("#divforimage").html(data);
            }
                      });
                  return false;
      }  
    
    
    $('#approved').on('click', function() {
        var product_id=$('#product_id').val();
        //alert(product_id);
    $.ajax({ 
    type: 'get',
    url: 'index.php?route=catalog/producttemp/accept&token=<?php echo $token; ?>',
    data: {product_id:product_id},
    cache: false,
    success: function(data) {
       // alert(data);
        
    alert('Successfully Approved');
  //$('#exampleModalCenter').model();
    location.reload(true);
                }
              });
 });
 
 
 
  $('#already_syatem').on('click', function() {
        var product_id=$('#product_id').val();
        var comments=$('#comments').val();
        //alert(product_id);
    $.ajax({ 
    type: 'post',
    url: 'index.php?route=catalog/producttemp/already_syatem&token=<?php echo $token; ?>',
    data: {product_id:product_id,comments:comments},
    cache: false,
    success: function(data) {
    alert('Already In Systey');
  //$('#exampleModalCenter').model();
    location.reload(true);
                }
              });
 });
 
 
 $('#button_submite').on('click', function() {
        var product_id=$('#product_id').val(); 
       
        var region_input=$('#region_input').val();
         
        //var chkNo=$('#chkNo').val();
        var chkNos=$('#chkNos').val();
      
        var chkYes=$('#chkYes').val();
       // alert(chkYes);
        //if ($("#chkNo").is(":checked")) 
       // {
        //    var checkval=chkNo;
       // }
        if ($("#chkNos").is(":checked")) 
        {
            var checkval=chkNos;
        }
        if ($("#chkYes").is(":checked")) 
        {
            var checkval=chkYes;
            var values=region_input;
        }
     //alert(checkval);
    $.ajax({ 
    type: 'post',
    url: 'index.php?route=catalog/producttemp/rejected&token=<?php echo $token; ?>',
    data: {product_id:product_id,values:values,checkval:checkval},
    cache: false,
    success: function(data) {
     alert('Pending');
 location.reload(true);
                }
              });
 });
    
  <!--- 
$('#button-filter').on('click', function() {
	var url = 'index.php?route=catalog/producttemp/product_request_temp&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_model = $('input[name=\'filter_model\']').val();

	if (filter_model) {
		url += '&filter_model=' + encodeURIComponent(filter_model);
	}

	var filter_price = $('input[name=\'filter_price\']').val();

	if (filter_price) {
		url += '&filter_price=' + encodeURIComponent(filter_price);
	}

	var filter_quantity = $('input[name=\'filter_quantity\']').val();

	if (filter_quantity) {
		url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
	}

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		//url += '&filter_status=' + encodeURIComponent(filter_status);
	}

	location = url;
});
$('#button-download').on('click', function() {
	var url = 'index.php?route=catalog/producttemp/download_excel&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_company_name\']').val();

	if (filter_name) {
		url += '&filter_company_name=' + encodeURIComponent(filter_company_name);
	}

	var filter_model = $('input[name=\'filter_model\']').val();

	if (filter_model) {
		url += '&filter_model=' + encodeURIComponent(filter_model);
	}

	var filter_price = $('input[name=\'filter_price\']').val();

	if (filter_price) {
		//url += '&filter_price=' + encodeURIComponent(filter_price);
	}

	var filter_quantity = $('input[name=\'filter_quantity\']').val();

	if (filter_quantity) {
		//url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
	}

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		//url += '&filter_status=' + encodeURIComponent(filter_status);
	}
	 window.open(url, '_blank');
	//location = url;
});
//--></script> 
  <script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/producttemp/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_name\']').val(item['label']);
	}
});

$('input[name=\'filter_company_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/producttemp/autocomplete_company&token=<?php echo $token; ?>&filter_company_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['company_name'],
						value: item['company_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_company_name\']').val(item['label']);
	}
});
//--></script></div>
<?php echo $footer; ?>