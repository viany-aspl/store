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
                
				<div class="card">
                        <div class="card-header">
                            <h1 style="float: left;">Update Supplier Group</h1>
                            <div class="pull-right" style="float: right;">
									<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo "Cancel"; ?>" class="btn btn-default"><i class="zmdi zmdi-mail-reply"></i></a></div>
              
								</div>
                        </div>

                        
                    </div>			  
				<div class="card">
                        <div class="card-block">
                            
                            <form method="post" method="post" action="<?php echo $action; ?>"> 
                                <input type="hidden" name="supplier_group_id" value="<?php echo $supplier_group_info['pre_mongified_id']; ?>" />
                                <div class="col-sm-12">


                                    <div class="input-group">
                                        
                                        <div class="form-group">
                                            <input type="text" class="form-control" value="<?php echo $supplier_group_info['supplier_group_name'];?>" name="sgname" placeholder="Supplier Group Name" autocomplete="cc-exp" required >
                                            <i class="form-group__bar"></i>
											<?php if (isset($_SESSION['name_error'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['name_error']; unset($_SESSION['name_error']); ?></div>
											<?php } ?>
<span class="zmd-help">Group Name</span>
                                        </div>
                                    </div>
                                </div>
								<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                        
                                        <div class="form-group">
                                            <textarea name="sgdescription" rows="5" placeholder="<?php echo "Description"; ?>" id="" class="form-control"><?php echo $supplier_group_info['supplier_group_desc'];?></textarea>
                                            <i class="form-group__bar"></i>
<span class="zmd-help">Description</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-sm-12 mt-3">
                                <button type="submit" class="btn btn-primary" style="float: right;">Update</button>
                                </div>
                        </form>
                        </div>
                    </div>

<?php echo $footer; ?>
