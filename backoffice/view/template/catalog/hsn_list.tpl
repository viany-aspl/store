<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">      
        <a href="<?php echo $redirect; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a></div>
      <h1>HSN</h1>
      
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
        <h3 class="panel-title"><i class="fa fa-list"></i>Hsn List</h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-store">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td>SID</td>
	          <td>HSN CODE</td>
                  <td class="text-left">HSN NAME</td>
				<td class="text-left">TAX CLASS</td>
				<td style="width: 100px;" class="text-left">ACTION</td>
                </tr>
              </thead>
              <tbody>
                <?php if ($hsn) { ?>
                <?php foreach ($hsn as $un) { ?>
                <tr>
                  <td class="text-left"><?php echo $un['sid']; ?></td>
	    <td class="text-left"><?php echo $un['hsn_code']; ?></td>
                  <td class="text-left"><?php echo $un['hsn_name']; ?></td>
              <td class="text-left"><?php echo $un['tax_class_name']; ?></td>
                <td class="text-left">
				<a href="<?php echo $un['editlink']; ?>"class="btn btn-primary"><i class="fa fa-pencil"></i><a/>
				<a href="<?php echo $un['deletelink']; ?>" onclick="return confrm();" class="btn btn-danger" ><i class="fa fa-trash-o"></i><a/>
				</td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
         
      </div>
	<div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"> 
             
              <?php echo $results; ?>  </div>
        </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 
<script type="text/javascript">
function confrm()
{
	var r = confirm("Are you Sure? You want to delete this hsn code!");
	if (r == true) 
	{
		return true;
	} 
	else 
	{
		return false;
	}
	
	/*
	try{
                                     
                alertify.confirm('You want to delete this HSN Code?',
                function(e){ 
                    if(e){
                    return true;
                    
                }else{
                    alertify.error('Cancel by user'); 
                    return false;
                }
            }
                    
                        );
                 $("#alertify-ok").html('Continue');    
            }catch(e)
			{
				alert(e);
				return false;
				}
	return false;
	*/
}
</script>