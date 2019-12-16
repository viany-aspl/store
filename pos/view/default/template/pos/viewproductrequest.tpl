<?php echo $header; ?><?php echo $column_left; ?>

<?php if ($error_warning) { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>
            <?php if ($success) { ?>
                <div class="alert alert-success" style="margin-bottom: 20px;"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php } ?>
                <p>View Product Request</p>
                <div class="card">
                    <div class="card-block">
                        
                        
      <div class="panel-body">
         
        <div class="table-responsive">
       
          <table class="table table-bordered">
            <thead>
              <tr>
                <td class="text-left">SI ID <?php //echo $column_Si_Id; ?></td>
	<td class="text-left">Product Name<?php //echo $column_title; ?></td>
                <td class="text-left">Status <?php //echo $column_title; ?></td>
               
              </tr>
            </thead>
            <tbody>
              <?php if ($product) {  if($_GET["page"]=="") {$aa=1;} else if($_GET["page"]=="1") {$aa=1;}
              else{ $aa=(($_GET["page"]-1)*20)+1; }?>
              <?php foreach ($product as $pro) {  
                
                  ?>
              <tr>
                <td class="text-left"><?php echo $aa; ?></td>
	<td class="text-left"><?php echo $pro['name']; ?></td>
                <td class="text-left"><?php echo $pro['status']; ?></td>
                
              </tr>
              <?php 
              $aa++;
              } ?>
              <?php } else { ?>
              <tr>
                <td class="text-center" colspan="5"><?php echo $text_no_results; ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
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