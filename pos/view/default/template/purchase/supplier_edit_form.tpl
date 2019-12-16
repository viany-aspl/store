<?php echo $header; ?><?php echo $column_left; ?>
 
            
            <?php if (isset($_SESSION['unsuccess_message'])) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['unsuccess_message']; unset($_SESSION['unsuccess_message']); ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
	<?php if (isset($_SESSION['delete_unsuccess_message'])) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['delete_unsuccess_message']; unset($_SESSION['delete_unsuccess_message']); ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
                <div class="card">
                        <div class="card-header">
                            <h1 style="float: left;">Update Supplier </h1>
                            <div class="pull-right" style="float: right;">
									<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo "Cancel"; ?>" class="btn btn-default"><i class="zmdi zmdi-mail-reply"></i></a></div>
              
								</div>
                        </div>
                <div class="card">
                        <div class="card-block">
                            
                            <form method="post" method="post" action="<?php echo $action; ?>"> 
									<input type="hidden" name="supplier_id" id="supplier_id" value="<?php echo $supplier_id; ?>" />
                               <div class="col-sm-12">

<label for="input-supplier-group" class="zmd-label-floating">Supplier Group</label>
										<div class="input-group">
											<div class="form-group">
												<select required onchange="set_value();" name="supplier_group_id" id="input-supplier-group" class="form-control">
                            
													<?php foreach ($supplier_groups as $supplier_group) { ?>
														<option value="<?php echo $supplier_group['pre_mongified_id']; ?>"<?php if($supplier_group['pre_mongified_id'] == $supplier_info['supplier_group_id']){?>selected="selected"<?php } ?>><?php echo $supplier_group['supplier_group_name']; ?></option>
													<?php } ?>
												</select>
												<input type="hidden" name="supplier_group_name" id="supplier_group_name" value="<?php if(!empty($supplier_group_name)){ echo $supplier_group_name; } else { echo $supplier_info['supplier_group_name']; } ?>" />
											</div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['first_name'])) echo $supplier_info['first_name'];?>" name="firstname" placeholder="First Name" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_firstname'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_firstname']; unset($_SESSION['error_firstname']); ?></div>
												<?php } ?>
<span class="zmd-help">First Name</span>

                                       </div>
                                    </div>
                                </div>
									
									<div class="col-sm-12 mt-3">
                                    

                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['last_name'])) echo $supplier_info['last_name'];?>" name="lastname" placeholder="Last Name" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_lastname'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_lastname']; unset($_SESSION['error_lastname']); ?></div>
												<?php } ?>
<span class="zmd-help">Last Name</span>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['location'])) echo $supplier_info['location']; ?>" name="location" placeholder="Location" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_location'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_location']; unset($_SESSION['error_location']); ?></div>
												<?php } ?>
<span class="zmd-help">Address</span>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['gst'])) echo $supplier_info['gst'];?>" name="gst" placeholder="GST" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_gst'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_gst']; unset($_SESSION['error_gst']); ?></div>
												<?php } ?>
<span class="zmd-help">GST</span>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['pan'])) echo $supplier_info['pan'];?>" name="pan" placeholder="Pan" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_pan'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_pan']; unset($_SESSION['error_pan']); ?></div>
												<?php } ?>
<span class="zmd-help">Pan</span>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['email'])) echo $supplier_info['email'];?>" name="email" placeholder="Email" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_email'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_email']; unset($_SESSION['error_email']); ?></div>
												<?php } ?>
<span class="zmd-help">Email</span>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['telephone'])) echo $supplier_info['telephone'];?>" name="telephone" placeholder="Telephone" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_telephone'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_telephone']; unset($_SESSION['error_telephone']); ?></div>
												<?php } ?>
<span class="zmd-help">Telephone</span>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['fax'])) echo $supplier_info['fax']; ?>" name="fax" placeholder="District" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_district'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_district']; unset($_SESSION['error_district']); ?></div>
												<?php } ?>
<span class="zmd-help">District</span>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['ACC_ID'])) echo $supplier_info['ACC_ID']; ?>" name="account" placeholder="Bank Account Number" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_account'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_account']; unset($_SESSION['error_account']); ?></div>
												<?php } ?>
<span class="zmd-help">Bank Account Number</span>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['BANK_NAME'])) echo $supplier_info['BANK_NAME']; ?>" name="bank" placeholder="Bank Name" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_bank'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_bank']; unset($_SESSION['error_bank']); ?></div>
												<?php } ?>
<span class="zmd-help">Bank Name</span>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($supplier_info['IFSC_CODE'])) echo $supplier_info['IFSC_CODE']; ?>" name="ifsc" placeholder="IFSC Code" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_ifsc'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_ifsc']; unset($_SESSION['error_ifsc']); ?></div>
												<?php } ?>
<span class="zmd-help">IFSC Code</span>
                                       </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">

										<div class="input-group">
											<div class="form-group">
												<select name="status" id="input-status" class="form-control">  
													<option value="1"  <?php if($supplier_info['status']==1) { ?> selected="selected" <?php } ?>>Enable</option>
													<option value="0"  <?php if($supplier_info['status']==0) { ?> selected="selected" <?php } ?>>Disable</option>     
												</select>
<span class="zmd-help">Status</span>
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
<script>
function set_value()
{
	var selectedText = $("#input-supplier-group option:selected").html();
	$("#supplier_group_name").val(selectedText);
}

</script>