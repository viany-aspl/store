<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <form action="" method="post" enctype="multipart/form-data"  name="myForm" class="form-horizontal">
<div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">  
          <input style="<?php if(count($rewards)==0){ ?>display: none;<?php } ?>" type="submit" onclick="return check_form();" id="button-filter" class="btn btn-primary" value="Submit" />
        </div>
      <h1>View & Assign Reward Points</h1>
      
    </div>
  </div>
    
    <div class="container" style="width: 100%;">
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
      
      
     
<div class="panel panel-default">
   <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> Reward Points</h3>
      </div>
    <div style="min-height: 400px;" class="panel-body"><!-----pos/gui_outwards------->
        
                 <div class="table-responsive">
                     <?php //print_r($stores); ?>
                        <table  id="myTable"  class=" table table-bordered mb-0 order-list text-center ">
                            <thead>
                                <tr style="width:100%">
                                <th style="width:22%;">Store</th>
                                <th style="width:20%;">Product</th>                                
									<th style="width:20%;"> SKU</th> 
                                <th style="width:12%;">Valid Till</th>
                                <th style="width:16%;">Assign Reward Points</th>
                                <th style="width:5%;">
                                    <?php //if(count($rewards)==0){ ?>
                                    <input type="button" class="btn btn-lg btn-block " id="addrow" value="Add Row" />
                                    <?php //} ?>
                                </th>
                              
                                
                            </tr>
                            </thead>
                            <tbody>
                           <?php $total=count($rewards);$a=0; foreach($rewards as $reward){ ?>
                                <tr>
                               <td> 
                                   <select required class="js-example-basic-multiple" multiple="multiple" id="store_id<?php echo $a; ?>" name="store_id[<?php echo $a; ?>][]">
                                    <?php foreach($stores as $store)
                                        { ?>
                                    <option <?php if(in_array($store['store_id'],$reward['store_id'])){ ?> selected="selected" <?php } ?> value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                                    <?php } ?>
                                   
                                   </select>
                               </td>
                                <td>
                                  
                                <input required  type="text"  name="product_name[]" value="<?php echo $reward['product_name']; ?>" id="product_name<?php echo $a; ?>" placeholder="Product"  class="form-control product_name" />
                                <input required  type="hidden"  name="product_id[]" value="<?php echo $reward['product_id']; ?>" id="product_id<?php echo $a; ?>" placeholder="Product"  class="form-control product_id" />
                                </td>
                                <td>
										<span id="product_sku_span<?php echo $a; ?>"><?php echo $reward['product_sku']; ?></span>
										<input required  type="hidden"  name="product_sku[]" value="<?php echo $reward['product_sku']; ?>" id="product_sku<?php echo $a; ?>"   class="form-control product_id" />
									</td>
                                 
                                  <td><input required style="min-width: 70px;" type="text" name="valid_till[]" value="<?php echo date('m/d/Y',$reward['valid_till']->sec); ?>" id="valid_till<?php echo $a; ?>" placeholder="Valid Till"  class="form-control date" /></td>
                                   
                                    <td><input required  type="text" onkeypress="return isNumber(event)" name="reward_points[]" value="<?php echo $reward['points']; ?>" id="reward_points<?php echo $a; ?>" placeholder="Reward Points"  class="form-control" /></td>
                                    <td> <?php 
                                    if($a==0){ ?> 
                                        <!--<input type="button" class="btn btn-lg btn-block " id="addrow" value="Add Row" /> -->
                                        <?php } ?>
                                    <?php //if($total==($a+1))
                                    //if($a!=0){ ?>
                                     
                                    <a href="index.php?route=catalog/reward/delete&token=<?php echo $token; ?>&product_id=<?php echo $reward['product_id']; ?>&points=<?php echo $reward['points']; ?>&store=<?php echo json_encode($reward['store_id']); ?>" onclick="return confirm('Are you sure ?');" class="btn btn-md btn-danger <?php echo $a; ?>">Delete</a>
                                       
                                        <?php //} ?>
                                        </td>
                            </tr>
                            
                           <?php $a++; } ?>
                            </tbody>
                        </table>
                        </div>
					
                    
        
      </div>
    </div>
  </div>
        </form>
</div> 
<script type="text/javascript">
   
   
    function check_form()
    {
        var t=0;
        $('table#myTable tbody tr').each(function()
        {
                //console.log($('#product_id'+(counter_check++)).val());
                var prdid=$("#product_id"+t).val();
                var store_id=$("#store_id"+t).val();
                var valid_till=$("#valid_till"+t).val();
                var reward_points=$("#reward_points"+t).val();
                //alert(prdid);
                
                if((store_id== undefined) || (store_id== null))
                {
                    alertify.error('Please select a store');
                    $("#store_id"+t).focus();
                    return false;
                }
                else if((prdid== undefined) || (prdid== null)|| (prdid== ''))
                {
                    alertify.error('Please select a product from the list');
                    $("#product_name"+t).focus();
                    return false;
                }
                else if((valid_till== undefined) || (valid_till== null)|| (valid_till== ''))
                {
                    alertify.error('Please select valid date');
                    //$("#valid_till"+t).focus();
                    return false;
                }
                else if((reward_points== undefined) || (reward_points== null)|| (reward_points== ''))
                {
                    alertify.error('Please enter reward points');
                    $("#reward_points"+t).focus();
                    return false;
                }
                else
                {
                    return false;
                }
            //alert(t);
            t++;
        });
        
        //return false;
    }

    function isNumber(evt)
       {
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          //console.log(charCode);
          if (charCode != 46 && charCode > 31 
            && (charCode < 48 || charCode > 57))
             return false;

          return true;
       }
       
$('#store_id').select2();
$('.date').datetimepicker({
	pickTime: false,
	minDate:new Date()
});

</script>
 <script type="text/javascript">
 $(document).ready(function() {
    $('.js-example-basic-multiple').select2();
    //$('#multiple-checkboxes').multiselect();
     $('.product').select2();
});  


   
 
$(document).ready(function () {
  
    
    var counter = <?php echo $a; ?>;
	//alert(counter);
    var counter_check = 0;
    $("#addrow").on("click", function () {
        $("#button-filter").show();
        var newRow = $("<tr>");
        var cols = "";
        var stores='';
         <?php foreach($stores as $store){ ?>
            stores=stores+'<option value="<?php echo $store["store_id"]; ?>"><?php echo $store["name"]; ?></option>';
        <?php } ?>
        cols += '<td><select required  multiple="multiple" class="form-control js-example-basic-multiple" name="store_id['+counter+'][]" id="store_id' + counter + '">'+stores+'</select></td>';
        cols += '<td><input required  type="text"  name="product_name[]" value="" id="product_name'+counter+'" placeholder="Product"  class="form-control product_name" /><input required  type="hidden"  name="product_id[]" value="" id="product_id'+counter+'" placeholder="Product"  class="form-control product_name" /></td>';
        cols +='<td><span id="product_sku_span'+ counter +'"></span><input required  type="hidden"  name="product_sku[]" value="" id="product_sku'+ counter +'"   class="form-control product_id" /></td>';
        //cols += '<td><input required type="text" class="form-control" readonly="readonly" id="unit_price" placeholder="Unit Price" name="unit_price' + counter + '"/> </td>';
        cols += '<td><input required type="text" style="min-width: 70px;" class="form-control date" name="valid_till[]" placeholder="Valid Till" id="valid_till' + counter + '"/> </td>';
        cols += '<td><input required type="text" class="form-control" onkeypress="return isNumber(event)" name="reward_points[]" placeholder="Reward Points" id="reward_points' + counter + '"/> </td>';
        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
        newRow.append(cols);
        
        $("table.order-list").append(newRow);
        
        $('.js-example-basic-multiple').select2();
         $('.product').select2();
         $('#store_id').select2();
        $('.date').datetimepicker({
	pickTime: false,
	minDate:new Date()
});
        //flatpickr(".date-picker", {minDate: "today"}); 
        $('#product_name'+counter).autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) 
            { 
				try{
					var res = this.id.split("product_name");
				$('#product_id'+res[1]).val('');
				$('#product_sku_span'+res[1]).html('');
				$('#product_sku'+res[1]).val('');
				}
				catch(e){
					//alert(e);
				}
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id'],
						sku: item['sku']
                                                
					}
				}));
			}
		});
	},
	'select': function(item) 
        {
        
        try{
            
         var res = this.id.split("product_name");  
          
        $('#product_name'+res[1]).val(item['label']);
        $('#product_id'+res[1]).val(item['value']);
        $('#product_sku_span'+res[1]).html(item['sku']);
        $('#product_sku'+res[1]).val(item['sku']);
        
    }catch(e){alert(e);}
	}
});
        counter++;
        
       
    });



    $("table.order-list").on("click", ".ibtnDel", function (event) 
    {
        $(this).closest("tr").remove();  
        var t=0;
        $('table#myTable tbody tr').each(function()
        {
            t++;
        });
        if(t>0)
        {
            $("#button-filter").show();
        }
        else
        {
            $("#button-filter").hide();
        }
        counter -= 1
    });


});



function calculateRow(row) {
    var price = +row.find('input[name^="price"]').val();

}

function calculateGrandTotal() {
    var grandTotal = 0;
    $("table.order-list").find('input[name^="price"]').each(function () {
        grandTotal += +$(this).val();
    });
    $("#grandtotal").text(grandTotal.toFixed(2));
}
</script>

  
<?php echo $footer; ?>
<?php $a=0; foreach($rewards as $reward){ ?>
<script type="text/javascript">
$('#product_name<?php echo $a; ?>').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) 
            { 
				$('#product_id<?php echo $a; ?>').val('');
				$('#product_sku_span<?php echo $a; ?>').html('');
				$('#product_sku<?php echo $a; ?>').val('');
				response($.map(json, function(item) 
				{
					console.log(item);
					return {
						label: item['name'],
						value: item['product_id'],
						sku: item['sku']
					}
				}));
			}
		});
	},
	'select': function(item) {
        
        $('#product_name<?php echo $a; ?>').val(item['label']);
        $('#product_id<?php echo $a; ?>').val(item['value']);
		$('#product_sku_span<?php echo $a; ?>').html(item['sku']);
		$('#product_sku<?php echo $a; ?>').val(item['sku'])
		
	}
});
</script>
<?php  $a++; } ?>
<style>
    .table thead th {
    border-bottom-width: 1px;
    font-size: 13px;
    padding: 6px;
}
.table-bordered td, .table-bordered th {
    border: 1px solid #c3c3c3;
    padding: 10px;
}
</style>

