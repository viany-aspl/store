<?php //echo $header; ?>
 <link rel="stylesheet" href="view/stylesheet/pos/iconFont.css">
    <title><?= $storename; ?> POS</title>
    <link rel="stylesheet" href="view/stylesheet/pos/style.css">
    <link rel="stylesheet" href="view/stylesheet/pos/metro-bootstrap.css">       
    <script type="text/javascript" src="view/javascript/pos/jquery.min.js"></script>         
    <script src="view/javascript/pos/line.js"></script>
    <link href="view/javascript/pos/css/line.css" rel="stylesheet">
    <link href="view/stylesheet/pos/bootstrap.min.css" rel="stylesheet">
  <script type="text/javascript" src="view/javascript/pos/jquery.keyboard.min.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/pos/jquery-ui-1.8.16.custom.min.js"></script>  
  
<nav class="navbar navbar-static-top line-navbar-two" style="background-color: blue;">      
    
     
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="line-navbar-collapse-2">
          <ul class="nav navbar-nav lnt-nav-mega">
            <li class="dropdown">
                
                    <a href="" type="button" id="button-menu"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#FFF"><i class="fa fa-dedent fa-lg"></i></a>

             
              <div class="dropdown-menu" role="menu">
                <div class="lnt-dropdown-mega-menu">
                  <!-- List of categories -->
                  <ul class="lnt-category list-unstyled">
                        <li class="active"><a href="index.php?route=pos/dashboard&token=<?php echo $token;?>">DashBoard</a></li>
                    <li ><a href="#subcategory-pos">Pos</a></li>
                    <li><a href="#subcategory-customer">Customer</a></li>
                    <li><a href="#subcategory-inv">Inventory Manager</a></li>
                    <li><a href="#subcategory-report">Report </a></li>
                    <li><a href="#subcategory-product">Register New Product</a></li>
                    <li><a href="#subcategory-credit">Credit Manager</a></li>
                    <li><a href="#subcategory-gst">GST</a></li>


                  </ul>
                  <!-- Subcategory and carousel wrap -->
                  <div class="lnt-subcategroy-carousel-wrap container-fluid">
                    <div id="subcategory-pos" class="active">
                      <!-- Sub categories list-->
                      <div class="lnt-subcategory col-sm-8 col-md-8">
                        <h3 class="lnt-category-name">Point Of Sale</h3>
                        <ul class="list-unstyled col-sm-6">
                          <li><a href="index.php?route=pos/pos&token=<?php echo $token;?>">Open Billing</a></li>
                          <li><a href="index.php?route=pos/pos&token=<?php echo $token;?>">Inventory Led Billing</a></li>
                          
                        </ul>
                       
                      </div>
                      <!-- Carousel -->
                    
                    </div> <!-- /.subcategory-home -->
                    <div id="subcategory-customer">
                      <!-- Sub categories list-->
                      <div class="lnt-subcategory col-sm-8 col-md-8">
                        <h3 class="lnt-category-name">Premium Customer</h3>
                        <ul class="list-unstyled col-sm-6">
                          <li><a href="index.php?route=pos/total_credit&token=<?php echo $token;?>">Premium Farmer</a></li>
                          
                        </ul>
                        
                      </div>
                      <!-- Carousel -->
                      
                    </div> <!-- /.subcategory-sports -->
                    <div id="subcategory-inv">
                      <!-- Sub categories list-->
                      <div class="lnt-subcategory col-sm-8 col-md-8">
                        <h3 class="lnt-category-name">Inventory Manager</h3>
                        <ul class="list-unstyled col-sm-6">
                          <li><a href="index.php?route=pos/pos&token=<?php echo $token;?>">Update Price</a></li>
                          <li><a href="index.php?route=pos/pos&token=<?php echo $token;?>">Update Quantity</a></li>
                          <li><a href="index.php?route=pos/inventory_report&token=<?php echo $token;?>">My Inventory</a></li>
                          
                        </ul>
                        
                      </div>
                      <!-- Carousel -->
                      
                    </div> <!-- /.subcategory-music -->
                    <div id="subcategory-report">
                      <div class="lnt-subcategory col-sm-8 col-md-8">
                        <h3 class="lnt-category-name">Report</h3>
                        <ul class="list-unstyled col-sm-6">
                          <li><a href="index.php?route=pos/report&token=<?php echo $token;?>">Sale By Product</a></li>
                          <li><a href="index.php?route=pos/report/sale_book&token=<?php echo $token;?>">Sale Book</a></li>
                          
                        </ul>
                        
                      
                      </div>
                      <!-- Carousel -->
                      
                    </div> <!-- /.subcategory-books -->
                    <div id="subcategory-product">
                      <!-- Sub categories list-->
                      <div class="lnt-subcategory col-sm-8 col-md-8">
                        <h3 class="lnt-category-name">Register New Product </h3>
                        <ul class="list-unstyled col-sm-6">
                          <li><a href="index.php?route=pos/product&token=<?php echo $token;?>">Product Request</a></li>
                          <li><a href="index.php?route=pos/product/viewproductrequest&token=<?php echo $token;?>">View Product Request</a></li>
                         
                        </ul>
                        
                      </div>
                    
                     
                    </div> 


                          <div id="subcategory-credit">
                      <!-- Sub categories list-->
                      <div class="lnt-subcategory col-sm-8 col-md-8">
                        <h3 class="lnt-category-name">Credit </h3>
                        <ul class="list-unstyled col-sm-6">
                          <li><a href="index.php?route=pos/total_credit&token=<?php echo $token;?>">Total Credit</a></li>
                          <li><a href="index.php?route=pos/total_credit/pay_for_credit&token=<?php echo $token;?>">Pay For Credit</a></li>
                         
                        </ul>
                        
                      </div>
                    
                     
                    </div> 


                        <div id="subcategory-gst">
                      <!-- Sub categories list-->
                      <div class="lnt-subcategory col-sm-8 col-md-8">
                        <h3 class="lnt-category-name">GST </h3>
                        <ul class="list-unstyled col-sm-6">
                          <li><a href="index.php?route=pos/report/gst&token=<?php echo $token;?>">GST Report</a></li>
                          
                         
                        </ul>
                        
                      </div>
                    
                     
                    </div> 
                    
                    
                  </div> <!-- /.lnt-subcategroy-carousel-wrap -->
                </div> <!-- /.lnt-dropdown-mega-menu -->
              </div> <!-- /.dropdown-menu -->
            </li> <!-- /.dropdown -->
          </ul> <!-- /.lnt-nav-mega -->
         
          <ul>
           <li style="text-align:right;color: blue;margin-right: 123px;"><a  href="index.php?route=pos/pos/customer&token=<?= $token ?>">
                   <i class="fa fa-user" style="color:#fff;margin-right: 34px;"></i><br>
                            <span style="color:#fff">Customer</span> 
                          </a>                          
                      </li>  
              <li style="text-align:right;color: blue;margin-top:-25px;" ><a href="index.php?route=common/logout&token=<?php echo $token;?>" ><span class="hidden-xs hidden-sm hidden-md" style="color:#fff">Logout</span> <i class="fa fa-sign-out fa-lg" style="color:#fff"></i></a></li>
          </ul>
        </div> <!-- /.navbar-collapse -->
    </nav>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_profile; ?> </h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
	<i class="<?php echo $tool_tip_class; ?> " data-toggle="tooltip" style="<?php echo $tool_tip_style; ?>" title="<?php echo $tool_tip; ?>"></i>
	
    </div>
  </div>
  
<div class="container-fluid bg-light py-3">
    <div class="row" style="background-color: #f5f3f3;">
        <div class="col-md-4 mx-auto col-md-offset-4" style="border: 1px solid #ccc;border-radius: 4px;box-decoration-break: inherit;margin-bottom: 60px;">
                <div class="card card-body">
                    <h3 class="text-center mb-4">Profile</h3>
                   
                    <fieldset>
                        <div class="form-group col-md-12">
                            <input class="form-control input-lg" placeholder="Mobile/Username" name="email" type="text">
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control input-lg" placeholder="Store Name" name="store_name" value="" type="text">
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control input-lg" placeholder="Proprietor " name="proprietor" value="" type="text">
                        </div>
                        <div class="form-group col-md-12">
                              <input class="form-control input-lg" placeholder="Email " name="email" value="" type="text">
                        </div>
                        <div class="form-group col-md-12">
                            <input class="form-control input-lg" placeholder="GST" name="gst" value="" type="text">
                        </div>
                         <div class="form-group col-md-12">
                            <input class="form-control input-lg" placeholder="Store Address" name="store_address" value="" type="text">
                        </div>
                        
                         <div class="form-group col-md-12">
                             <input class="btn btn-lg btn-primary btn-block" value="Save" type="submit">
                        </div>
                        
                       
                    </fieldset>
                </div>
        </div>
    </div>
</div>
    
    
    
    


    
    
    
    
    <style>
 .metro ul, .metro ol {
    margin-left: 0px;
}


.metro .dropdown-menu a {
        padding: 5px 32px !important;
}

.metro a:hover, .metro .link:hover {

        color: #00b9f5 !important;
}
.metro .dropdown-menu li:hover {

        background: none;
        border-color: 0px solid #fff !important;

}
.metro .dropdown-menu li {
    border: 0px solid transparent;
}
.metro .dropdown-menu a {
    font-size: 13px !important;
    line-height: 10px !important;
}

.category_top_title {
    z-index: 1;
}
</style>
  <script type="text/javascript">
$('#input-store').select2();
<!--
$('#button-filter').on('click', function() {
	url = 'index.php?route=report/inventory_report&token=<?php echo $token; ?>';
	
        var filter_store = $('select[name=\'filter_store\']').val();
	
	if (filter_group!="") {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}

	var filter_date_end = $('input[name=\'filter_date_end\']').val();
	
	if (filter_date_end) {
		//url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
	}
		
	var filter_group = $('select[name=\'filter_group\']').val();
	
	if (filter_group) {
		//url += '&filter_group=' + encodeURIComponent(filter_group);
	}
	
	var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();
	
	if (filter_order_status_id != 0) {
		//url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
	}	
       
	location = url;
});
//--></script> 
<script type="text/javascript"><!--
$('#button-download').on('click', function() {
    url = 'index.php?route=report/inventory_report/download_excel&token=<?php echo $token; ?>';
    
        var filter_store = $('select[name=\'filter_store\']').val();
    
    if (filter_group!="") {
        url += '&filter_store=' + encodeURIComponent(filter_store);
    }

    var filter_date_end = $('input[name=\'filter_date_end\']').val();
    
    if (filter_date_end) {
        //url += '&filter_date_end=' + encodeURIComponent(filter_date_end);
    }
        
    var filter_group = $('select[name=\'filter_group\']').val();
    
    if (filter_group) {
        //url += '&filter_group=' + encodeURIComponent(filter_group);
    }
    
    var filter_order_status_id = $('select[name=\'filter_order_status_id\']').val();
    
    if (filter_order_status_id != 0) {
        //url += '&filter_order_status_id=' + encodeURIComponent(filter_order_status_id);
    }    
       
    //location = url;
        window.open(url, '_blank');
});
//-->

</script> 
  <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script src="view/javascript/pos/jquery.highlight.js"></script>
    <script src="view/javascript/pos/jquery.touchSwipe.min.js"></script>
    <script src="view/javascript/pos/jquery.randomColor.js"></script>
    <script src="view/javascript/pos/line.js"></script>
<?php //echo $footer; ?>
