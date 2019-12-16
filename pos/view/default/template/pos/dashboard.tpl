<?php echo $header; ?><?php echo $column_left; ?>

<header class="content__title">
                    <h1>Dashboard</h1>
                    <small>Welcome to the unique pos experience!</small>
                </header>
                <div class="row quick-stats">
                    <div class="col-sm-6 col-md-3">
                        <div class="quick-stats__item bg-light-blue">
                            <div class="quick-stats__info">
                                <h2><?php echo $order; ?></h2>
                                <small>Total Orders</small>
                            </div>

                            
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="quick-stats__item bg-amber">
                            <div class="quick-stats__info">
                                <h2><?php echo $sale; ?></h2>
                                <small>Total Sale</small>
                            </div>

                            
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="quick-stats__item bg-purple">
                            <div class="quick-stats__info">
                                <h2><?php echo $customer; ?></h2>
                                <small>Total Customers</small>
                            </div>

                            
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="quick-stats__item bg-red">
                            <div class="quick-stats__info">
                                <h2 style="font-size:11px;">Cash : <?php echo $cash; ?></h2>
                                <h2 style="font-size:11px;">Credit : <?php echo $credit; ?></h2>
                                <small>Total Sale Cash Credit</small>
                            </div>

                            
                        </div>
                    </div>
                </div>
<div class="row">
        <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Order Statistics</h2>
                                <small class="card-subtitle">Order trend</small>
                            </div>
         <?php   	$msChart2 = new FusionCharts("mscombi2D", "2", "100%", "400", "chart-container-order_count", "json", $comparasion_chart_order_count);
					$msChart2->render();?>
                            <div class="card-block" id="chart-container-order_count">
                       
                                <div class="flot-chart flot-curved-line" ></div>
                                
                                <div class="flot-chart-legends flot-chart-legends--curved"></div>
                            </div>
                        </div>
                    </div>
</div>
<div class="row">
        <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Average Sales Ticket Size</h2>
                                
                            </div>
         <?php   	$msChart5 = new FusionCharts("mscombi2D", "5", "100%", "400", "chart-container-bar_chart", "json", $comparasion_chart_bar_chart);
					$msChart5->render();?>
                            <div class="card-block" id="chart-container-bar_chart">
                       
                                <div class="flot-chart flot-curved-line" ></div>
                                
                                <div class="flot-chart-legends flot-chart-legends--curved"></div>
                            </div>
                        </div>
                    </div>
</div>

                 
                <div class="row">
                  

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Growth Rate</h2>
                                <small class="card-subtitle">Top 5 Products</small>
                            </div>
<?php   $msChartp = new FusionCharts("pie2d", "3", "100%", "400", "chart-container1", "json", $top5products);
               
               // calling render method to render the chart
               $msChartp->render();?>
                            <div class="card-block" id="chart-container1">
                                <div class="flot-chart flot-line"></div>
                                <div class="flot-chart-legends flot-chart-legends--line"></div>
                            </div>
                        </div>
                    </div>
                </div>
<!--
 <div class="row">
					<div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                
                                <small class="card-subtitle">Top 5 Category</small>
                            </div>
<?php   $msChartp_category = new FusionCharts("pie2d", "4", "100%", "400", "chart-container4", "json", $comparasion_chart_category);
               
               // calling render method to render the chart
               $msChartp_category->render();?>
                            <div class="card-block" id="chart-container4">
                                <div class="flot-chart flot-line"></div>
                                <div class="flot-chart-legends flot-chart-legends--line"></div>
                            </div>
                        </div>
                    </div>
               
</div>-->
<?php echo $footer; ?>

