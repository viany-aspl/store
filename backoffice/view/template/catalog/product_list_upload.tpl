<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <!--<div class="pull-right">
        <button  type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div> -->
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
      
        
      <div class="panel-body">
        <form  method="post" enctype="multipart/form-data" id="form-restore">  
        
        <div class="well">
          <div class="row">
              
             <div class="col-sm-5">
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_category; ?></label>
                <select name="category_id" id="category_id" class="form-control">
                  <option value="">Select Category</option>
                
                <?php foreach($categories as $category){?>
                  
                  <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
               
                <?php } ?>
                 
                
                 
                </select>
              </div>
          
            </div> 
              
              
              
            <div class="col-sm-5">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input  class="btn btn-primary " id="choosefile" type="file" name="tfile" id="input-import" />
              </div>
             
            </div>
           <div class="col-sm-2">
               <input class="form-control btn btn-primary" type="submit" name="submit" value="UPLOAD" style="margin-top: 32px;">
           </div>
          </div>
        </div>
           </form>
       <!-- <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>-->
      </div>
             
    </div>
  </div>
 </div>
<script type="text/javascript">
$('#upload').on('click', function() {
   alert("hello");
    document.getElementById("form-restore").submit();

 });
 </script>
<?php echo $footer; ?>