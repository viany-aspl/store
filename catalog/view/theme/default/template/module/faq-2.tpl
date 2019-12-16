<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/jquery.mobile-1.4.5.min.css">
<script src="catalog/view/theme/default/javascript/jquery-1.11.3.min.js"></script>
<script src="catalog/view/theme/default/javascript/jquery.mobile-1.4.5.min.js"></script>
<title>Frequently Asked Questions</title>
</head>
<body>
<style type="text/css">
.ui-icon-plus:after,.ui-icon-minus:after
{
background-color: orange !important;
}
</style>
<div data-role="page" id="pageone" >
<h1 style="text-align:center">FAQ’s </h1>
  <div data-role="main" class="ui-content">
   <?php foreach($faqs as $key=>$faq) { ?>
     <div data-role="collapsible">
      <h1><?php echo $faq['question'];?></h1>
	  <?php if(!empty($faq['image'])) { ?>
	  <span><img src="<?php echo $faq['image'];?>"></span>
	  <?php } ?>
      <p> <?php echo $faq['answer'];?><br></p>
    </div>
   
    <?php } ?>
    
  </div>
</div>
</body>
</html>