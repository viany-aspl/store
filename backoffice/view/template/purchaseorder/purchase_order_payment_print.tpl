<!DOCTYPE html>
<html lang="en">
<head>
    <title></title>
    <link href="https://unnati.world/shop/admin/view/stylesheet/pos/bootstrap.min.css" rel="stylesheet" />
    
    <style>
        td{
            border-left:2px #000000 solid;
            padding: 10px;
            
        }
        body{
            padding: 30px;
        }
       

*{ margin:0;
    padding:0;
    font-family: 'Rubik', sans-serif;
    font-size:14px;
    color:#484545;
    line-height:25px;
    letter-spacing:0.05em;
}

.top_mar-21 {

}
.mar_lft30 {
margin-left:30px;
}
.list li {
font-size:12px !important;
}
.size16 {
font-size:16px !important;
}

.top_mar20 {
margin-top:20px;
}
strong
{
font-weight: bold;
}
h3
{
font-weight: bold;
}
    </style>
</head>
<body >
    <div class="container top_mar20" style="width: 89%;">
        
        <div class="row">
            <div class="col-sm-12">
               <div class="table-responsive">
                   <center> <h3 style="text-align: center;">PAYMENT ADVICE</h3></center>
                   
<table class="table table-bordered" style="margin-bottom: 0px;">
                       
                    <tr>
                        <td style="width: 100%;" class="col-sm-6 size16">
		<center>
                           <strong>Akshamaala Solutions Pvt. Ltd</strong><br />
                            F-35, 1<sup>st</sup> Floor, Sector-8, Noida, Uttar Pradesh 201301<br />
                            Ph: +91 120-4040160, <u>accounts@unnati.world</u>, <u>www.unnati.world</u> <br />
                            PAN No: AAICA9806D<br />
                            CIN No: U72200DL2010PTC209266<br />
                            GSTN : 09AAICA9806D1ZM 
		</center>
                        </td>
                        
                    </tr>
	<tr>
                        <td style="width: 100%;" class="col-sm-6 size16">
                           <strong>To :</strong><br />
                           <?php echo $supplier_name; ?><br />
		
                           <?php echo $supplier_address; ?>
                        </td>
                        
                    </tr>
	<tr>
                          <td style="width: 100%;" class="col-sm-6 size16">
                              <strong>Details of Invoice : </strong><br />
                           Invoice Number : 
		 <?php echo $invoice_number; ?><br />
                           Invoice Amount : 
		 <?php echo $invoice_amount; ?><br /> 
		Invoice Date  : 
		 <?php echo $invoice_date; ?><br />
		
                          
                          </td>
                      </tr>
	<tr>
                        <td style="width: 100%;" class="col-sm-6 size16">
                           <strong>From :</strong><br />
                           <?php echo $bank_name; ?><br />
		
                        </td>
                        
                    </tr>
                      <tr>
                          <td style="width: 100%;" class="col-sm-6 size16">
                              <strong>Beneficiary Details : </strong><br />
                          Beneficiary Account Number : 
		 <?php echo $supplier_ac; ?><br />
                           Beneficiary IFSC Code :
		 <?php echo $supplier_ifsc; ?><br />
		Amount :
		 <?php echo $amount; ?><br />
		Bank Ref No  :
		 <?php echo $tr_number; ?><br />

                          
                          </td>
                      </tr>
                      </table>
                </div>
            </div>
        </div>
    </div>


    
</body>
</html>