<?php //echo $header; ?><?php //echo $column_left; ?>
<style type="text/css">
	thead tr td{
		font-weight:bold;
		font-size:10px;
	}
	.table-responsive thead td {
    background: #eeeeee none repeat scroll 0 0;
    padding: 5px !important;
    text-align: center;
	border-bottom:2px solid #cccccc;
	font-size: 14.5px;
}
.table-responsive tbody td {
    padding: 5px !important;
	line-height: 1.7;
}
.panel-heading {
    background: #eeeeee none repeat scroll 0 0;
}
.page-header{
	text-align:center;
}
.table.table-bordered tbody tr:nth-child(2n) {
    background-color: #eeeeee !important;
}
.header{
	width: 100%;
}
.logo{
	width: 25%;
	float:left;
	margin-top:20px !important;
}
.company{
	width: 72%;
	float:right;
	margin-top:20px !important;
}
.logo img, .company_info p{
	width:100%;
}
.company h2{
	float:right;
}
.panel-heading{
	display:none;
}
.table-bordered, .table-bordered td {
    border: 1px solid #dddddd;
	border-collapse:collapse;
}
.company_info p{
	width: 100%;
	font-weight: bold;
	margin:0px;
}
.company_info p span{
	font-size:12px;
}
.date span{
	float:right;
}


/*mail type*/
.mail_type{
	width:100%;
}
.mail{
	float:left;
	width:50%;
}
.type{
	float:right;
	width:30%;
	font-weight:bold;
	font-size:11px;
}
.type span{
	float:right;
	font-size:11px;
	font-weight:normal;
}
/*mail type*/

.owner-date{
	width: 100%;
}
.owner{
	float:left;
	width: 50%;
}
.date{
	float:right;
	width: 15%;
	font-weight: bold;
	font-size:11px;
}
.date span{
	float:right;
	font-size:11px;
	font-weight:normal;
}
table{
	margin:10px auto;
	width:100%;
	font-family:Verdana, Geneva, sans-serif;
	font-size:10px;
	text-align:center;
}
td{
	width:auto;
}
p{
	font-size:11px;
}
p span{
	font-weight:normal;
}
#content{
	font-family:Verdana, Geneva, sans-serif;
}
.footer{
	width:100%;
}
.address
{
	width:70%;
	float:left;
}
.pageno{
	width:4%;
	float:right;
}
</style>
<div id="content">
  <div class="page-header">
    <!--<div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
    </div>-->
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-bar-chart"></i> <?php echo $text_list; ?></h3>
      </div>
      
      <div class="company_info">
      	<!--<p>Company Name: <span><?php echo $company_name?></span></p>-->
        <!--<p>Company Address: <span><?php echo $company_address?></span></p>-->
        <p>Company Owner: <span><?php echo $company_owner?></span></p>
		<div class="mail_type">
			<div class="mail"><p>Company Email: <span><?php echo $company_email?></span></p></div>
			<div class="type">Report Type:<span><?php echo $heading_title ;?></span></div>
		</div>
		<p>Date: <span><?php echo date('Y-m-d');?></span></p>
        <!--<div class="owner-date">
            <div class="owner"><p>Company Owner: <span><?php echo $company_owner?></span></p></div>
            <div class="date">Date: <span><?php echo date('Y-m-d');?></span></div>
        </div>-->
      </div><br />
      
      <div class="panel-body">
	  <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
			  <tr>
                <td class="text-center"><?php echo $column_order_id; ?></td>
                <td class="text-center"><?php echo $column_date_start; ?></td>
                <td class="text-center"><?php echo $column_date_end; ?></td>
				<td class="text-center"><?php echo $column_product; ?></td>
                <td class="text-center"><?php echo $column_supplier; ?></td>
				<td class="text-center"><?php echo $column_quantity; ?></td>
				<td class="text-center"><?php echo $column_price; ?></td>
                <td class="text-center"><?php echo "Products"; ?></td>
                <td class="text-center"><?php echo $column_total; ?></td>
              </tr>
            </thead>
            <tbody>
				<?php $grand_total = 0; ?>
				<?php for($i = 0; $i<count($received_orders); $i++){ ?>
				<tr>
					<td><?php echo $received_orders[$i]['order_id']; ?></td>
					<td><?php echo $received_orders[$i]['order_date']; ?></td>
					<td><?php echo $received_orders[$i]['receive_date']; ?></td>
					<td><?php 
						foreach($received_orders[$i]['products'] as $product)
						{
							echo $product . "<br />";
						}
					?></td>
					<td><?php 
						foreach($received_orders[$i]['suppliers'] as $supplier)
						{
							echo $supplier . "<br />";
						}
					?></td>
					<td><?php 
						foreach($received_orders[$i]['rcvd_qnty'] as $qnty)
						{
							echo $qnty . "<br />";
						}
					?></td>
					<td><?php 
						foreach($received_orders[$i]['prices'] as $price)
						{
							echo $price . "<br />";
						}
					?></td>
					<td><?php echo $received_orders[$i]['total_products']; ?></td>
					<td><?php echo $received_orders[$i]['total_price']; ?></td>
				</tr>
				<?php $grand_total += $received_orders[$i]['total_price']; ?>
				<?php } ?>
			  <tr>
                <td class="text-right" colspan ="8"><b><?php echo $grand_total_text; ?></b></td>
				<td class="text-left" ><?php echo $grand_total; ?></td>
              </tr>
			</tbody>
          </table>
		</div>
        <div class="row">
          <div class="col-sm-6 text-left"><?php //echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php //echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
 <script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php //echo $footer; ?>
</body>
</html>