<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
          <!--
          <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary">
              <i class="fa fa-plus"></i>
          </a>
          --><a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-category">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center">
                      
                   
                  </td>
                  <td class="text-left"><?php echo $column_name; ?></td>
                 
                  <td class="text-right">Sub Menu</td>
                  
                </tr>
              </thead>
              <tbody>
                <?php if ($categories) { ?>
                <?php foreach ($categories as $category) { ?>
                <tr>
                  <td class="text-center">
                      
                      <?php  if (in_array($category['category_id'], $access)) { ?>
                      <input id="checkbox<?php echo $category['category_id']; ?>" onclick="return false;" type="checkbox" name="selected[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input id="checkbox<?php echo $category['category_id']; ?>" onclick="return false;" type="checkbox" name="selected[]" value="<?php echo $category['category_id']; ?>" />
                    <?php } ?>
                    
                  </td>
                  <td class="text-left"><?php echo $category['name']; ?></td>
                  
                  <td class="text-right"><a onclick="return open_model('<?php echo $category['category_id']; ?>')" href="<?php echo $category['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                
                <!-- Modal -->
  <div class="modal fade" id="myModal<?php echo $category['category_id']; ?>" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" id="partner_cncl_btn2" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><b>Update Sub Menu (<?php echo $category['name']; ?>) for <?php echo $name; ?></b></h4>
        </div>
        <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_access; ?></label>
            <div class="col-sm-10">
              <div class="well well-sm" style="height: 150px; overflow: auto;">
                <?php foreach ($category['child'] as $permission) { //print_r($permission); ?>
                <div class="checkbox">
                  <label>
                    <?php if (in_array($permission['category_id'], $access)) { ?>
                    <input type="checkbox" name="permission<?php echo $category['category_id']; ?>[]" value="<?php echo $permission['category_id']; ?>" checked="checked" />
                    <?php echo $permission['name']; ?>
                    <?php } else { ?>
                    <input type="checkbox" name="permission<?php echo $category['category_id']; ?>[]" value="<?php echo $permission['category_id']; ?>" />
                    <?php echo $permission['name']; ?>
                    <?php } ?>
                  </label>
                </div>
                <?php } ?>
              </div>
              <a onclick="$(this).parent().find(':checkbox').prop('checked', true);">Sellect All</a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);">Un-Select All</a></div>
          </div>

            
            <div class="text-right">
                <input type="button" id="sbmt_btn<?php echo $category['category_id']; ?>"  class="btn btn-primary" onclick="return submit_form('<?php echo $category['category_id']; ?>');" value="Submit" />
                <button type="button" id="cncl_btn<?php echo $category['category_id']; ?>" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <img id="cr_img<?php echo $category['category_id']; ?>" src="view/image/processing_image.gif" style="margin-top: -10px;float: right;height: 60px;display: none;"/>
            
            </div>
        
        </div>
        
      </div>
      
    </div>
  </div>
                
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
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
function open_model(category_id)
{
$('#myModal'+category_id).modal('show');
return false;
}
function submit_form(category_id) 
{
    var favorite = [];
    $.each($("input[name='permission"+category_id+"[]']:checked"), function(){            

        favorite.push($(this).val());

    });
    
     $.ajax({
            url: 'index.php?route=user/menu/updategrouppermission&token=<?php echo $token; ?>&category_id=' +  encodeURIComponent(category_id)+'&user_group_id=<?php echo $user_group_id; ?>',
            type: 'get',
            cache:false,
            beforeSend:function()
            {
                $("#sbmt_btn"+category_id).hide();
                $("#cncl_btn"+category_id).hide();
                $("#cr_img"+category_id).show();
            },
            data:{'selected':favorite},
            success: function(json) 
            {
                $("#sbmt_btn"+category_id).show();
                $("#cncl_btn"+category_id).show();
                $("#cr_img"+category_id).hide();
                //alert(json);
                alertify.success('Menu Updated Successfully');
            },
            error:function(json)
            {
                $("#sbmt_btn"+category_id).show();
                $("#cncl_btn"+category_id).show();
                $("#cr_img"+category_id).hide();
                //alert(JSON.stringify(json));
                alertify.success('Menu Updated Successfully');
            }
        
        });
    if(favorite=='')
    {
        $('#checkbox'+category_id).prop('checked', false);              
    }
    else
    {
        $('#checkbox'+category_id).prop('checked', true); // Checks it
    }
    $('#myModal'+category_id).modal('hide');
    return false;
    
}
</script> 
<?php echo $footer; ?>