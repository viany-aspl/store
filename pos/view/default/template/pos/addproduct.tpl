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
                        <div class="card-block">
                            
                            <form method="post" method="post" action="index.php?route=pos/product/addproduct&token=<?php echo $token; ?>">
                                <div class="col-sm-12">
                                   
                                    <div class="input-group">
                                        <div class="form-group">
                                            
                                            <select name="category_id" id="category_id" class="form-control "  >
                                                <option value=''>Select Category</option> 
                                                    <?php foreach ($categories as $category) {  ?>
                                                        <option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
                                                    <?php  } ?>
                                            </select>
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">
                                    

                                    <div class="input-group">

                                        <div class="form-group">
                                            
                                            <select name="company_name" id="company_name" class="form-control "  >
                                                <option value=''>Select Company</option> 
                 
                                                <?php foreach ($company as $ccompany) {  ?>
                                                    <option value="<?php echo $ccompany['id']; ?>"><?php echo $ccompany['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">


                                    <div class="input-group">

                                        <div class="form-group">
                                            <input type="text" class="form-control" name="productname" placeholder="Product Name" autocomplete="cc-exp" required >
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">


                                    <div class="input-group">

                                        <div class="form-group">
                                            <input type="text" class="form-control" name="sku" placeholder="SKU" autocomplete="cc-exp" required >
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">


                                    <div class="input-group">

                                        <div class="form-group">
                                            <input type="text" class="form-control" name="hstncode" placeholder="HSN Code" autocomplete="cc-exp" required>
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">
                                   

                                    <div class="input-group">
                                        <div class="form-group">
                                            <select name="gsttype" id="gst" class="form-control "  >
                                                <option value=''>Select GST</option> 
                                                <option value="0">0%</option> 
                                                <option value="5">5%</option> 
                                                <option value="12">12%</option> 
                                                <option value="18">18%</option> 
                                                <option value="28">28%</option> 
                                            </select>
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3">
                                    <label>Image</label>

                                    <div class="input-group">
                                        
                                        <div class="form-group">
                                            <input  type="file" class="form-control" name="image" placeholder="Image" autocomplete="cc-exp" required  >
                                            <i class="form-group__bar"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-3"> 
                                <button type="submit" class="btn btn-primary" style="float: right;">Add Product</button>
                                </div>
                        </form>
                        </div>
                    </div>

<?php echo $footer; ?>
