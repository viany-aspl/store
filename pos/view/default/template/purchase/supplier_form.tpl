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
                            <h1 style="float: left;">Add Supplier </h1>
                            <div class="pull-right" style="float: right;">
									<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo "Cancel"; ?>" class="btn btn-default"><i class="zmdi zmdi-mail-reply"></i></a></div>
              
								</div>
                        </div>
                <div class="card">
                        <div class="card-block">
                            
                            <form method="post" method="post" action="<?php echo $action; ?>"> 
							
                               <div class="col-sm-12">
                                    <label>Supplier Group</label> 
										<div class="input-group">
											<div class="form-group">
												<select required onchange="set_value();" name="supplier_group_id" id="input-supplier-group" class="form-control">
													<option value="">Select Supplier Group</option>
													<?php foreach ($supplier_groups as $supplier_group) { ?>
														<option value="<?php echo $supplier_group['pre_mongified_id']; ?>"<?php if(isset($supplier_group_id) && ($supplier_group['id'] == $supplier_group_id)){ ?>selected="selected"<?php } ?>><?php echo $supplier_group['supplier_group_name']; ?></option>
													<?php
													}
													?>
												</select>
												<input type="hidden" name="supplier_group_name" id="supplier_group_name" value="<?php if(!empty($supplier_group_name)){ echo $supplier_group_name; } else { echo $supplier_groups[0]['supplier_group_name']; } ?>" />
											</div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">
                                    

                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($first_name)) echo $first_name;?>" name="firstname" placeholder="First Name" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_firstname'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_firstname']; unset($_SESSION['error_firstname']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									
									<div class="col-sm-12 mt-3">
                                    
                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($last_name)) echo $last_name;?>" name="lastname" placeholder="Last Name" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_lastname'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_lastname']; unset($_SESSION['error_lastname']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12  mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($location)) echo $location;?>" name="location" placeholder="Location" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_location'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_location']; unset($_SESSION['error_location']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12  mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($gst)) echo $gst;?>" name="gst" placeholder="GST" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_gst'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_gst']; unset($_SESSION['error_gst']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($pan)) echo $pan;?>" name="pan" placeholder="Pan" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_pan'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_pan']; unset($_SESSION['error_pan']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($email)) echo $email;?>" name="email" placeholder="Email" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_email'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_email']; unset($_SESSION['error_email']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($telephone)) echo $telephone;?>" name="telephone" placeholder="Telephone" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_telephone'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_telephone']; unset($_SESSION['error_telephone']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12  mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($fax)) echo $fax;?>" name="fax" placeholder="District" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_district'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_district']; unset($_SESSION['error_district']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12  mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($account)) echo $account;?>" name="account" placeholder="Bank Account Number" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_account'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_account']; unset($_SESSION['error_account']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($bank)) echo $bank;?>" name="bank" placeholder="Bank Name" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_bank'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_bank']; unset($_SESSION['error_bank']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
									<div class="col-sm-12 mt-3">


                                    <div class="input-group">
                                       <div class="form-group">
                                           <input type="text" class="form-control" value="<?php if(isset($ifsc)) echo $ifsc;?>" name="ifsc" placeholder="IFSC Code" autocomplete="cc-exp" required >
                                           <i class="form-group__bar"></i>
												<?php if (isset($_SESSION['error_ifsc'])) { ?>
													<div class="text-danger"><?php echo $_SESSION['error_ifsc']; unset($_SESSION['error_ifsc']); ?></div>
												<?php } ?>
                                       </div>
                                    </div>
                                </div>
                                <div class="col-sm-12  mt-3">

										<div class="input-group">
											<div class="form-group">
												<select name="status" id="input-status" class="form-control">  
													<option value="1" >Enable</option>
													<option value="0" >Disable</option>     
												</select>
											</div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">
                                  
                                <button type="submit" class="btn btn-primary" style="float: right;">Add</button>
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