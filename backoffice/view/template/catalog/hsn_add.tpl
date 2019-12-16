<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button onclick="return check_form();" type="button" form="form-store" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title ?></h1>
      <!--<ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>">HSN ADD</a></li>
        <?php } ?>
      </ul>-->
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-store" class="form-horizontal">
		<?php if(!empty($hsncode['sid'])){ ?>
		<input type="hidden" name="sid" value="<?php echo $hsncode['sid']; ?>" class="form-control"   />
        <?php } ?> 
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-meta-title">HSN Code</label>
				
                <div class="col-sm-10">
                    <input type="text" name="hsn_code" value="<?php echo $hsncode['hsn_code']; ?>" placeholder="Add HSN Code" id="hsn_code" class="form-control"  required="required" onkeypress="return event.charCode >= 48 &amp;&amp; event.charCode <= 57 || event.keyCode==8 || event.keyCode==46" />
                  <?php if ($error_meta_title) { ?>
                  <div class="text-danger"><?php echo $error_meta_title; ?></div>
                  <?php } ?>
                </div>
            </div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-meta-title">HSN Name</label>
                <div class="col-sm-10">
                    <input type="text" name="hsn_name" value="<?php echo $hsncode['hsn_name']; ?>" placeholder="Add HSN" id="hsn_name" class="form-control"  required="required" />
                  <?php if ($error_meta_title) { ?>
                  <div class="text-danger"><?php echo $error_meta_title; ?></div>
                  <?php } ?>
                </div>
            </div>
			<div class="form-group required">
                <label class="col-sm-2 control-label" for="input-meta-title">Tax Class</label>
                <div class="col-sm-10">
				<input type="hidden" name="tax_class_name" id="tax_class_name" value="<?php echo $hsncode['tax_class_name']; ?>" />
                   <select required="required" name="tax_class_id" id="input-tax-class" class="form-control" onchange="change_value_by_tax(this.value)">
                    <option value="">SELECT</option>
                    <?php foreach ($tax_classes as $tax_class) { ?>
                    <?php if ($tax_class['tax_class_id'] == $hsncode['tax_class_id']) { ?>
                    <option name="<?php echo $tax_class['title']; ?>" value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                    <?php } else { ?>
                    <option name="<?php echo $tax_class['title']; ?>" value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                </div> 
            </div>
           
         
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">

	function change_value_by_tax(value)
	{
		var class_name=$("#input-tax-class option:selected").text();
        $("#tax_class_name").val(class_name);
		
	}
		  </script>
  <script type="text/javascript">
function check_form()
{

var hsn_code=$("#hsn_code").val();
var hsn_name=$("#hsn_name").val();
//alert(hsn_code);
//return false;
if(hsn_code=="")
{
 $("#hsn_code").focus();
 return false;
}
else if(hsn_name=="")
{
 $("#hsn_name").focus();
 return false;
}
else
{
 $("#form-store").submit();
 return true;
}
}

</script> 
  </div>
<?php echo $footer; ?>