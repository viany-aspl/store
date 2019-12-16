<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-location" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-location" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
       
            
             <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_price; ?></label>
            <div class="col-sm-10">
              <input type="text" name="price" value="<?php echo $price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-name" class="form-control" />
              <?php if ($error_price) { ?>
              <div class="text-danger"><?php echo $error_price; ?></div>
              <?php } ?>
            </div>
          </div>
            
            
            
             <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_mn; ?></label>
            <div class="col-sm-10">
              <input type="text" name="manufacturer_name" value="<?php echo $manufacturer_name; ?>" placeholder="<?php echo $entry_mn; ?>" id="input-name" class="form-control" />
              <?php if ($error_mn) { ?>
              <div class="text-danger"><?php echo $error_mn; ?></div>
              <?php } ?>
            </div>
          </div>
            
             <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_model; ?></label>
            <div class="col-sm-10">
              <input type="text" name="model" value="<?php echo $model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-name" class="form-control" />
              <?php if ($error_model) { ?>
              <div class="text-danger"><?php echo $error_model; ?></div>
              <?php } ?>
            </div>
          </div>
             <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_description; ?></label>
            <div class="col-sm-10">
              <input type="text" name="description" value="<?php echo $description; ?>" placeholder="<?php echo $entry_description; ?>" id="input-name" class="form-control" />
              <?php if ($error_description) { ?>
              <div class="text-danger"><?php echo $error_description; ?></div>
              <?php } ?>
            </div>
          </div>
            
           
             <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_character; ?></label>
            <div class="col-sm-10">
              <input type="text" name="character" value="<?php echo $character; ?>" placeholder="<?php echo $entry_character; ?>" id="input-name" class="form-control" />
              <?php if ($error_character) { ?>
              <div class="text-danger"><?php echo $error_character; ?></div>
              <?php } ?>
            </div>
          </div>
             <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_width; ?></label>
            <div class="col-sm-10">
              <input type="text" name="width" value="<?php echo $width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-name" class="form-control" />
              <?php if ($error_width) { ?>
              <div class="text-danger"><?php echo $error_width; ?></div>
              <?php } ?>
            </div>
          </div>
            
            
             <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_color; ?></label>
            <div class="col-sm-10">
              <input type="text" name="color" value="<?php echo $color; ?>" placeholder="<?php echo $entry_color; ?>" id="input-name" class="form-control" />
              <?php if ($error_color) { ?>
              <div class="text-danger"><?php echo $error_color; ?></div>
              <?php } ?>
            </div>
          </div>
             <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_item; ?></label>
            <div class="col-sm-10">
              <input type="text" name="item" value="<?php echo $item; ?>" placeholder="<?php echo $entry_item; ?>" id="input-name" class="form-control" />
              <?php if ($error_item) { ?>
              <div class="text-danger"><?php echo $error_item; ?></div>
              <?php } ?>
            </div>
          </div>
              <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_warranty; ?></label>
            <div class="col-sm-10">
              <input type="text" name="warranty" value="<?php echo $warranty; ?>" placeholder="<?php echo $entry_warranty; ?>" id="input-name" class="form-control" />
              <?php if ($error_warranty) { ?>
              <div class="text-danger"><?php echo $error_warranty; ?></div>
              <?php } ?>
            </div>
          </div>
              <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_mh; ?></label>
            <div class="col-sm-10">
              <input type="text" name="manufacturer_helpdesk" onkeypress="return isNumber(event)" value="<?php echo $manufacturer_helpdesk; ?>" placeholder="<?php echo $entry_mh; ?>" id="input-name" class="form-control" />
              <?php if ($error_mh) { ?>
              <div class="text-danger"><?php echo $error_mh; ?></div>
              <?php } ?>
            </div>
          </div>
              <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_mail; ?></label>
            <div class="col-sm-10">
              <input type="email" name="mail" value="<?php echo $mail; ?>" placeholder="<?php echo $entry_mail; ?>" id="input-name" class="form-control" />
              <?php if ($error_mail) { ?>
              <div class="text-danger"><?php echo $error_mail; ?></div>
              <?php } ?>
            </div>
          </div>
              <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_ma; ?></label>
            <div class="col-sm-10">
              <input type="text" name="manufacturer_address" value="<?php echo $manufacturer_address; ?>" placeholder="<?php echo $entry_ma; ?>" id="input-name" class="form-control" />
              <?php if ($error_ma) { ?>
              <div class="text-danger"><?php echo $error_ma; ?></div>
              <?php } ?>
            </div>
          </div>
              <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_image; ?></label>
            <div class="col-sm-10">
                <input type="file" name="image" id="file" value="<?php echo $image; ?>" placeholder="<?php echo $entry_image; ?>" id="input-name" class="form-control" style="padding: 0px"/>
              <input type="hidden" name="image_h" value="<?php echo $image; ?>"  id="input-GST_doc_h" class="form-control" />
                  <?php if ($error_image) { ?>
              <div class="text-danger"><?php echo $error_image; ?></div>
              <?php } ?>
            </div>
          </div>
            
             <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name">Image 2<?php //echo $entry_image; ?></label>
            <div class="col-sm-10">
              <input type="file" name="image1" id="file1" value="<?php echo $image1; ?>" placeholder="<?php //echo $entry_image; ?>" id="input-name" class="form-control" style="padding: 0px"/>
              <input type="hidden" name="image1_h" value="<?php echo $image1; ?>"  id="input-GST_doc_h" class="form-control" />
<?php if ($error_image) { ?>
              <div class="text-danger"><?php echo $error_image; ?></div>
              <?php } ?>
            
</div>
          </div>



           <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name">Image 3<?php //echo $entry_image; ?></label>
            <div class="col-sm-10">
              <input type="file" name="image2" id="file2" value="<?php echo $image2; ?>" placeholder="<?php //echo $entry_image; ?>" id="input-name" class="form-control" style="padding: 0px"/>
              <input type="hidden" name="image2_h" value="<?php echo $image2; ?>"  id="input-GST_doc_h" class="form-control" />
<?php if ($error_image) { ?>
              <div class="text-danger"><?php echo $error_image; ?></div>
              <?php } ?>
           
 </div>
          </div>
             
            
        
        </form>
      </div>
    </div>
  </div>
 <script type="text/javascript">
 
 function getwarehouse(data){
 var store_id=data;

        $.ajax({ 
        type: 'post',
        url: 'index.php?route=location/location_cluster/getwarehouse&token=<?php echo $token; ?>',
        data: 'store_id='+store_id,
        cache: false,
        success: function(data) {
            //alert(data);
        $("#zone_id").html(data);
       }


   });
     
     
     
}

 function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
 
 function displayPreview(files) {

    var reader = new FileReader();
    var img = new Image();
    reader.onload = function (e) {
        img.src = e.target.result;
        fileSize = Math.round(files.size / 1024);
        if(fileSize<600 && !fileSize==''){
             //alert("Image size is " + fileSize + " kB");
           return false;
        }
        else{
               alert("File size should not more than 600 kB"); 
             }
};
    reader.readAsDataURL(files);
}
$("#file").change(function () {
    var file = this.files[0];

    displayPreview(file);






});

function displayPreview1(files) {

    var reader = new FileReader();
    var img = new Image();
    reader.onload = function (e) {
        img.src = e.target.result;
        fileSize = Math.round(files.size / 1024);
        if(fileSize<600 && !fileSize==''){
           //alert("Image size is " + fileSize + " kB");
           return false;
        }
        else{
               alert("File size should not more than 600 kB"); 
             }
};
    reader.readAsDataURL(files);
}
$("#file1").change(function () {
    var file = this.files[0];

    displayPreview1(file);






});


function displayPreview2(files) {

    var reader = new FileReader();
    var img = new Image();
    reader.onload = function (e) {
        img.src = e.target.result;
        fileSize = Math.round(files.size / 1024);
        if(fileSize<600){
          //alert("Image size is " + fileSize + " kB");
           return false;
        }
        else{
               alert("File size should not more than 600 kB"); 
             }
};
    reader.readAsDataURL(files);
}
$("#file2").change(function () {
    var file = this.files[0];

    displayPreview2(file);






});
 </script></div>
<?php echo $footer; ?>


