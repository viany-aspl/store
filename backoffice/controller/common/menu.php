<?php
class ControllerCommonMenu extends Controller 
{
    public function index() 
    {
	$this->load->language('common/menu');
	$this->load->model('user/user');
	$this->load->model('tool/image');
	$user_info = $this->model_user_user->getUser($this->user->getId());
	if ($user_info) 
        {
            $data['firstname'] = $user_info['firstname'];
            $data['lastname'] = $user_info['lastname'];
            $data['user_group'] = $user_info['user_group'];
            if (is_file(DIR_IMAGE . $user_info['image'])) 
            {
		$data['image'] = $this->model_tool_image->resize($user_info['image'], 45, 45);
            }
            else 
            {
		$data['image'] = '';
            }
	} 
        else 
        {
            $data['firstname'] = '';
            $data['lastname'] = '';
            $data['user_group'] = '';
            $data['image'] = '';
        }			

	$data['token'] = $this->session->data['token'];
	// Menu
	if($data['user_group']['user_group_id']=='1')
        {
            $data['menus'][] = array(
				'id'       => 'menu-dashboard',
				'icon'	   => 'fa-dashboard',
				'name'	   => $this->language->get('text_dashboard'),
				'href'     => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
				'children' => array()
			);

        }
        else
        {
            if ($this->user->hasPermission('access', 'common/dashboard')) 
            {
		$data['menus'][] = array(
				'id'       => 'menu-dashboard',
				'icon'	   => 'fa-dashboard',
				'name'	   => $this->language->get('text_dashboard'),
				'href'     => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true),
				'children' => array()
			);
            }
        }
        // Catalog
	$catalog = array();
	if ($this->user->hasPermission('access', 'catalog/category')) 
        {
            $catalog[] = array(
			'name'	   => $this->language->get('text_category'),
                        'href'     => $this->url->link('catalog/category', 'token=' . $this->session->data['token'], true),
			'children' => array()		
			);
	}
        if ($this->user->hasPermission('access', 'catalog/producttemp')) 
        {
            $catalog[] = array(
                            'name'	   => "Product Requests",
                            'href'     => $this->url->link('catalog/producttemp/product_request_temp', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}


	
	
            if ($this->user->hasPermission('access', 'catalog/product')) 
            {
		$catalog[] = array(
				'name'	   => $this->language->get('text_product'),
				'href'     => $this->url->link('catalog/product', 'token=' . $this->session->data['token'], true),
				'children' => array()		
				);
            }
            if ($this->user->hasPermission('access', 'catalog/reward')) 
            {
		$catalog[] = array(
				'name'	   => 'Products Reward',
				'href'     => $this->url->link('catalog/reward', 'token=' . $this->session->data['token'], true),
				'children' => array()		
				);
            }
			if ($this->user->hasPermission('access', 'catalog/reward')) 
            {
				$catalog[] = array(
				'name'	   => 'Products Reward (Expired)',
				'href'     => $this->url->link('catalog/reward/expired', 'token=' . $this->session->data['token'], true),
				'children' => array()		
				);
            }
            if ($this->user->hasPermission('access', 'catalog/reward')) 
            {
		$catalog[] = array(
				'name'	   => 'Unnati Mitra Statement',
				'href'     => $this->url->link('catalog/reward/unnati_mitra_statement', 'token=' . $this->session->data['token'], true),
				'children' => array()		
				);
            }
            if ($this->user->hasPermission('access', 'catalog/hsn')) 
            {
		$catalog[] = array(
				'name'	   => 'Products HSN',
				'href'     => $this->url->link('catalog/hsn', 'token=' . $this->session->data['token'], true),
				'children' => array()		
				);
            }
			if ($this->user->hasPermission('access', 'catalog/upload')) 
            {
		$catalog[] = array(
				'name'	   => 'Upload Products',
				'href'     => $this->url->link('catalog/upload', 'token=' . $this->session->data['token'], true),
				'children' => array()		
				);
            }
		if ($this->user->hasPermission('access', 'catalog/dashboardcategory')) 
        {
            $catalog[] = array(
			'name'	   => 'Dashboard Categories',
                        'href'     => $this->url->link('catalog/dashboardcategory', 'token=' . $this->session->data['token'], true),
			'children' => array()		
			);
	}
        if ($this->user->hasPermission('access', 'catalog/dashboardproduct')) 
        {
            $catalog[] = array(
			'name'	   => 'Dashboard Product',
                        'href'     => $this->url->link('catalog/dashboardproduct', 'token=' . $this->session->data['token'], true),
			'children' => array()		
			);
	}
	if ($this->user->hasPermission('access', 'catalog/productcatalogcategory')) 
        {
            $catalog[] = array(
			'name'	   => 'Catalog Product Categories',
                        'href'     => $this->url->link('catalog/productcatalogcategory', 'token=' . $this->session->data['token'], true),
			'children' => array()		
			);
	}

	if ($this->user->hasPermission('access', 'catalog/imgmarque')) 
        {
            $catalog[] = array(
                            'name'	   => "Mobile Dashboard Slider",
                            'href'     => $this->url->link('catalog/imgmarque', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}
	if ($this->user->hasPermission('access', 'catalog/manufacturer')) 
        {
            $catalog[] = array(
                            'name'	   => "Manufacturers",
                            'href'     => $this->url->link('catalog/manufacturer', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}
	
            if ($catalog) 
            {
		$data['menus'][] = array(
                                    'id'       => 'menu-catalog',
                                    'icon'	   => 'fa-tags', 
                                    'name'	   => $this->language->get('text_catalog'),
                                    'href'     => '',
                                    'children' => $catalog
				);		
            }
        
	$faq=array();	

if ($this->user->hasPermission('access', 'catalog/faq')) 
        {
            $faq[] = array(
                            'name'	   => "FAQ",
                            'href'     => $this->url->link('catalog/faq', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}


if ($this->user->hasPermission('access', 'catalog/faq')) 
        {
            $faq[] = array(
                            'name'	   => "FAQ Categories",
                            'href'     => $this->url->link('catalog/faq/cat_getList', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}

  if ($faq) 
            {
		$data['menus'][] = array(
                                    'id'       => 'menu-catalog',
                                    'icon'	   => 'fa-question-circle', 
                                    'name'	   => 'FAQ',
                                    'href'     => '',
                                    'children' => $faq
				);		
            }

$printer=array();
if ($this->user->hasPermission('access', 'printer/printer')) 
        {
            $printer[] = array(
                            'name'	   => "Printer",
                            'href'     => $this->url->link('printer/printer', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}

if ($printer) 
            {
		$data['menus'][] = array(
                                    'id'       => 'menu-catalog',
                                    'icon'	   => 'fa-print', 
                                    'name'	   => 'Printer',
                                    'href'     => '',
                                    'children' => $printer
				);		
            }

		$supplier=array();
	if ($this->user->hasPermission('access', 'purchase/supplier')) 
        {
            $supplier[] = array(
                            'name'	   => 'Supplier',
                            'href'     => $this->url->link('purchase/supplier', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
	}
	if ($this->user->hasPermission('access', 'purchase/supplier_group')) 
        {
            $supplier[] = array(
                            'name'	   => 'Supplier Group',
                            'href'     => $this->url->link('purchase/supplier_group', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
        }
        if($supplier)
        {
            $supplier_po[] = array(
                            'name'	   =>'Supplier' ,
                            'href'     => '',
                            'children' => $supplier
                            );	
        }
		if ($this->user->hasPermission('access', 'purchaseorder/purchase_order')) 
        {
            $supplier_po[] = array(
                            'name'	   => 'Create PO',
                            'href'     => $this->url->link('purchaseorder/purchase_order', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
        }
		if ($supplier_po) 
            {
		$data['menus'][] = array(
                                    'id'       => 'menu-catalog',
                                    'icon'	   => 'fa-tags', 
                                    'name'	   => 'Supplier PO',
                                    'href'     => '',
                                    'children' => $supplier_po
				);		
            }
	// Extension
	$extension = array();
       
		if ($this->user->hasPermission('access', 'firebase/firebase')) 
        {
            $extension[] = array(
                            'name'	   => 'Notifications',
                            'href'     => $this->url->link('firebase/firebase/getlist', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}
        if ($this->user->hasPermission('access', 'extension/payment')) 
        {
            $extension[] = array(
                            'name'	   => $this->language->get('text_payment'),
                            'href'     => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}
	 
        
        if ($this->user->hasPermission('access', 'extension/total')) 
        {
            $extension[] = array(
                                'name'	   => $this->language->get('text_total'),
				'href'     => $this->url->link('extension/total', 'token=' . $this->session->data['token'], true),
				'children' => array()		
				);
	}
       
        if ($extension) 
        {
		$data['menus'][] = array(
                                    'id'       => 'menu-extension',
                                    'icon'	   => 'fa-tags', 
                                    'name'	   => 'Extension',
                                    'href'     => '',
                                    'children' => $extension
				);		
        }
        
	
	/////////////partner start here//////////////
	$partnerinventory=array();
	if ($this->user->hasPermission('access', 'purchase/purchase_order')) 
        {
            $partnerinventory[]= array(
                                    'name'	   => '<span style="font-size: 13px !important;"> Requisition</span>',  
                                    'href'     => $this->url->link('purchase/purchase_order', 'token=' . $this->session->data['token'], true),
                                    'children' => array()		
				);	
	}
     
	if ($partnerinventory) 
        {
            $data['menus'][] = array(
                                    'id'       => 'inventory',
                                    'icon'	   => 'fa fa-truck  fa-fw', 
                                    'name'	   => '<span style="font-size: 13px !important;">Purchase order/indent</span>', 
                                    'href'     => '',
                                    'children' => $partnerinventory
                                    );
	}
        
	// Sales
	$sale = array();
	if ($this->user->hasPermission('access', 'sale/order')) 
        {
            $sale[] = array(
                        'name'	   => $this->language->get('text_order'),
                        'href'     => $this->url->link('sale/order', 'token=' . $this->session->data['token'], true),
                        'children' => array()		
                        );	
	}
	
	// Customer
	$customer = array();
	if ($this->user->hasPermission('access', 'sale/customer')) 
        {
            $customer[] = array(
                            'name'	   => $this->language->get('text_customer'),
                            'href'     => $this->url->link('sale/customer', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
        }
        if ($this->user->hasPermission('access', 'sale/customer_group')) 
        {
            $customer[] = array(
                            'name'	   => $this->language->get('text_customer_group'),
                            'href'     => $this->url->link('sale/customer_group', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}
	/*
	if ($customer) 
        {
            $sale[] = array(
			'name'	   => $this->language->get('text_customer'),
			'href'     => '',
			'children' => $customer		
			);		
        }
		*/
        if ($sale) 
        {
            $data['menus'][] = array(
                                    'id'       => 'menu-sale',
                                    'icon'	   => 'fa fa-shopping-cart  fa-fw', 
                                    'name'	   => $this->language->get('text_sale'),
                                    'href'     => '',
                                    'children' => $sale
				);
        }
        /*
	// Marketing
	$marketing = array();
	if ($this->user->hasPermission('access', 'marketing/marketing')) 
        {
            $marketing[] = array(
                            'name'	   => $this->language->get('text_marketing'),
                            'href'     => $this->url->link('marketing/marketing', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
        }
	if ($this->user->hasPermission('access', 'marketing/affiliate')) 
        {
            $marketing[] = array(
                            'name'	   => $this->language->get('text_affiliate'),
                            'href'     => $this->url->link('marketing/affiliate', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}
	if ($this->user->hasPermission('access', 'marketing/coupon')) 
        {	
            $marketing[] = array(
                            'name'	   => $this->language->get('text_coupon'),
                            'href'     => $this->url->link('marketing/coupon', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
	}
	if ($this->user->hasPermission('access', 'marketing/contact')) 
        {
            $marketing[] = array(
                            'name'	   => $this->language->get('text_contact'),
                            'href'     => $this->url->link('marketing/contact', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );
	}
	if ($marketing) 
        {
            $data['menus'][] = array(
                                'id'       => 'menu-marketing',
                                'icon'	   => 'fa-share-alt', 
                                'name'	   => $this->language->get('text_marketing'),
                                'href'     => '',
                                'children' => $marketing
				);	
        }
	*/
	// System
	$system = array();
	if ($this->user->hasPermission('access', 'setting/setting')) 
        {
            $system[] = array(
                            'name'	   => $this->language->get('text_setting'),
                            'href'     => $this->url->link('setting/store', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
	}
	/*
	if ($this->user->hasPermission('access', 'company/company')) 
        { 
            $system[] = array(
			'name'	   => 'Companies',
			'href'     => $this->url->link('company/company', 'token=' . $this->session->data['token'], true),
			'children' => array()		
			);	
	}
	if ($this->user->hasPermission('access', 'unit/unit')) 
        {
            $system[] = array(
                            'name'	   => 'Factory Units',
                            'href'     => $this->url->link('unit/unit', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
        }
		*/
        if ($this->user->hasPermission('access', 'catalog/storemenu')) 
        {
            $system[] = array(
                            'name'	   => 'Store App Menu',
                            'href'     => $this->url->link('catalog/storemenu', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
        }
        if ($this->user->hasPermission('access', 'setting/billcontrol')) 
        {
            $system[] = array(
                            'name'	   => 'Billing Control',
                            'href'     => $this->url->link('setting/billcontrol', 'token=' . $this->session->data['token'], true),
                            'children' => array()		
                            );	
        }
        // Users
        $user = array();
	if ($this->user->hasPermission('access', 'user/user')) 
        {
            $user[] = array(
                        'name'	   => $this->language->get('text_users'),
                        'href'     => $this->url->link('user/user', 'token=' . $this->session->data['token'], true),
                        'children' => array()		
                        );	
        }
        if ($this->user->hasPermission('access', 'user/user_permission')) 
        {	
            $user[] = array(
                        'name'	   => $this->language->get('text_user_group'),
                        'href'     => $this->url->link('user/user_permission', 'token=' . $this->session->data['token'], true),
                        'children' => array()		
                        );	
        }
        
	if ($user) 
        {
            $system[] = array(
                            'name'	   => $this->language->get('text_users'),
                            'href'     => '',
                            'children' => $user		
                            );
	}
        // Localisation
        $localisation = array();
	if ($this->user->hasPermission('access', 'localisation/location')) 
        {
            $localisation[] = array(
                                'name'	   => $this->language->get('text_location'),
                                'href'     => $this->url->link('localisation/location', 'token=' . $this->session->data['token'], true),
                                'children' => array()		
				);	
        }
	if ($this->user->hasPermission('access', 'localisation/language')) 
        {
            $localisation[] = array(
                                'name'	   => $this->language->get('text_language'),
                                'href'     => $this->url->link('localisation/language', 'token=' . $this->session->data['token'], true),
                                'children' => array()		
				);
        }
	if ($this->user->hasPermission('access', 'localisation/currency')) 
        {
            $localisation[] = array(
                                'name'	   => $this->language->get('text_currency'),
                                'href'     => $this->url->link('localisation/currency', 'token=' . $this->session->data['token'], true),
                                'children' => array()		
				);
        }
	if ($this->user->hasPermission('access', 'localisation/stock_status')) 
        {
            $localisation[] = array(
                                'name'	   => $this->language->get('text_stock_status'),
                                'href'     => $this->url->link('localisation/stock_status', 'token=' . $this->session->data['token'], true),
                                'children' => array()		
				);
	}
	if ($this->user->hasPermission('access', 'localisation/order_status')) 
        {
            $localisation[] = array(
                                'name'	   => $this->language->get('text_order_status'),
                                'href'     => $this->url->link('localisation/order_status', 'token=' . $this->session->data['token'], true),
                                'children' => array()		
				);
        }
	
	if ($this->user->hasPermission('access', 'localisation/country')) 
        {
            $localisation[] = array(
                                'name'	   => $this->language->get('text_country'),
                                'href'     => $this->url->link('localisation/country', 'token=' . $this->session->data['token'], true),
                                'children' => array()		
				);
	}
        /*
	if ($this->user->hasPermission('access', 'localisation/zone')) 
        {
            $localisation[] = array(
				'name'	   => $this->language->get('text_zone'),
				'href'     => $this->url->link('localisation/zone', 'token=' . $this->session->data['token'], true),
				'children' => array()		
				);
	}
	if ($this->user->hasPermission('access', 'localisation/geo_zone')) 
        {
            $localisation[] = array(
                                'name'	   => $this->language->get('text_geo_zone'),
                                'href'     => $this->url->link('localisation/geo_zone', 'token=' . $this->session->data['token'], true),
                                'children' => array()
				);
	}
        */
	// Tax		
	$tax = array();
	if ($this->user->hasPermission('access', 'localisation/tax_class')) 
        {
            $tax[] = array(
			'name'	   => $this->language->get('text_tax_class'),
                        'href'     => $this->url->link('localisation/tax_class', 'token=' . $this->session->data['token'], true),
                        'children' => array()
                        );
        }
        if ($this->user->hasPermission('access', 'localisation/tax_rate')) 
        {
            $tax[] = array(
			'name'	   => $this->language->get('text_tax_rate'),
			'href'     => $this->url->link('localisation/tax_rate', 'token=' . $this->session->data['token'], true),
			'children' => array()
			);
	}
	if ($tax) 
        {	
            $localisation[] = array(
				'name'	   => $this->language->get('text_tax'),
				'href'     => '',
				'children' => $tax		
				);
	}
	if ($this->user->hasPermission('access', 'localisation/length_class')) 
        {
            $localisation[] = array(
				'name'	   => $this->language->get('text_length_class'),
				'href'     => $this->url->link('localisation/length_class', 'token=' . $this->session->data['token'], true),
				'children' => array()
				);
	}
	if ($this->user->hasPermission('access', 'localisation/weight_class')) 
        {
            $localisation[] = array(
				'name'	   => $this->language->get('text_weight_class'),
				'href'     => $this->url->link('localisation/weight_class', 'token=' . $this->session->data['token'], true),
				'children' => array()
				);
	}
	if ($localisation) 
        {
            $system[] = array(
                            'name'	   => $this->language->get('text_localisation'),
                            'href'     => '',
                            'children' => $localisation	
                            );
	}
	// Tools	
	$tool = array();
	
	if ($this->user->hasPermission('access', 'tool/backup')) 
        {
            $tool[] = array(
			'name'	   => $this->language->get('text_backup'),
			'href'     => $this->url->link('tool/backup', 'token=' . $this->session->data['token'], true),
			'children' => array()		
			);
	}
	if ($this->user->hasPermission('access', 'tool/error_log')) 
        {
            $tool[] = array(
			'name'	   => $this->language->get('text_error_log'),
			'href'     => $this->url->link('tool/error_log', 'token=' . $this->session->data['token'], true),
			'children' => array()		
			);
	}
	if ($tool) 
        {
            $system[] = array(
                            'name'	   => $this->language->get('text_tools'),
                            'href'     => '',
                            'children' => $tool	
                            );
	}
	if ($system) 
        {
            $data['menus'][] = array(
				'id'       => 'menu-system',
				'icon'	   => 'fa-cog', 
				'name'	   => $this->language->get('text_system'),
				'href'     => '',
				'children' => $system
				);
	}
	// Report
	$report = array();
	// Report Sales
	$report_sale = array();	
        if ($this->user->hasPermission('access', 'report/sale_summary')) 
        {
            $report_sale[] = array(
				'name'	   => 'Sale Summary',
				'href'     => $this->url->link('report/sale_summary', 'token=' . $this->session->data['token'], true),
				'children' => array()
				);
	}
	if ($this->user->hasPermission('access', 'report/sale_drill')) 
        {
            $report_sale[] = array(
				'name'	   => 'Sale Drilldown Report',
				'href'     => $this->url->link('report/sale_drill', 'token=' . $this->session->data['token'], true),
				'children' => array()
				);
	}
        if ($report_sale) 
        {
            $report[] = array(
                            'name'      => $this->language->get('text_sale'),
                            'href'      => '',
                            'children'  => $report_sale
                            );			
	}
	
	//call center reports
	if ($this->user->hasPermission('access', 'report/incommingcall_report')) {
				$app_cc_report[] = array(
					'name'	   => 'Call Summary',
					'href'     => $this->url->link('report/incommingcall_report', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			
if ($app_cc_report) {	
				$report[] = array(
					'name'	   => 'Call Center',
					'href'     => '',
					'children' => $app_cc_report
				);		
			}
			
			
			
			//////////////Promtional Activity///////////////////
				if ($this->user->hasPermission('access', 'report/promotional_activity')) {
				$promotional_activity[] = array(
					'name'	   => 'Promotional Activity',
					'href'     => $this->url->link('report/promotional_activity', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			
			
			
			if ($this->user->hasPermission('access', 'report/promotional_activity')) {
				$promotional_activity[] = array(
					'name'	   => 'Promotional Activity Graph',
					'href'     => $this->url->link('report/promotional_activity/promotional_activity_graph', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			
if ($promotional_activity) {	
				$report[] = array(
					'name'	   => 'Promotional Activity',
					'href'     => '',
					'children' => $promotional_activity
				);		
			}
			
			//call center reports
	if ($this->user->hasPermission('access', 'report/whatsappinv')) {
				$whatsappinv[] = array(
					'name'	   => 'WhatsApp Invoice',
					'href'     => $this->url->link('report/whatsappinv', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			
if ($whatsappinv) {	
				$report[] = array(
					'name'	   => 'WhatsApp Invoice',
					'href'     => '',
					'children' => $whatsappinv
				);		
			}
			if ($this->user->hasPermission('access', 'report/printer_request')) {
				$printer_request[] = array(
					'name'	   => 'Printer Request',
					'href'     => $this->url->link('report/printer_request', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			
if ($printer_request) {	
				$report[] = array(
					'name'	   => 'Printer Request',
					'href'     => '',
					'children' => $printer_request
				);		
			}
			if ($this->user->hasPermission('access', 'report/web_login')) {
				$web_login[] = array(
					'name'	   => 'Web Login Trans',
					'href'     => $this->url->link('report/web_login', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			
if ($web_login) {	
				$report[] = array(
					'name'	   => 'Web Login Trans',
					'href'     => '',
					'children' => $web_login
				);		
			}
	//
	if ($this->user->hasPermission('access', 'customertrans/transation')) {
				$customertrans[] = array(
					'name'	   => 'Customer Trans',
					'href'     => $this->url->link('customertrans/transation', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			
if ($customertrans) {	
				$report[] = array(
					'name'	   => 'Customer Trans',
					'href'     => '',
					'children' => $customertrans
				);		
			}
	// Report Products			
	$report_product = array();	
	
	if ($this->user->hasPermission('access', 'report/report')) 
        {

            $report_product[] = array(
                                    'name'	   => 'Un-Verified Products',
                                    'href'     => $this->url->link('report/report', 'token=' . $this->session->data['token'], true),
                                    'children' => array()	
                                    );
	}
	
        if ($this->user->hasPermission('access', 'report/product_sales')) 
        {

            $report_product[] = array(
                                    'name'	   => 'Product wise order count',
                                    'href'     => $this->url->link('report/product_sales', 'token=' . $this->session->data['token'], true),
                                    'children' => array()	
                                    );
	}
        if ($this->user->hasPermission('access', 'report/product_sales')) 
        {
            $report_product[] = array(
                                    'name'	   => 'Product  sales quantity',
                                    'href'     => $this->url->link('report/product_sales/sales_qnty', 'token=' . $this->session->data['token'], true),
                                    'children' => array()	
                                    );
	}
        
        
        
	if ($report_product) 
        {	
            $report[] = array(
                            'name'	   => $this->language->get('text_product'),
                            'href'     => '',
                            'children' => $report_product	
                            );		
	}
	$report_referral=array();
       if ($this->user->hasPermission('access', 'report/report')) 
        {

            $report_referral[] = array(
                                    'name'	   => 'Referral Report',
                                    'href'     => $this->url->link('report/report/referral', 'token=' . $this->session->data['token'], true),
                                    'children' => array()	
                                    );
	}
	if ($report_referral) 
        {	
            $report[] = array(
                            'name'	   => 'Referral',
                            'href'     => '',
                            'children' => $report_referral	
                            );		
	}
	$report_customer=array();
	if ($this->user->hasPermission('access', 'report/report')) 
    {
		$report_customer[] = array(
                                    'name'	   => 'Premium Farmer',
                                    'href'     => $this->url->link('report/report/premium_farmer', 'token=' . $this->session->data['token'], true),
                                    'children' => array()	
                                    );
	}
	if ($this->user->hasPermission('access', 'report/report')) 
    {
		$report_customer[] = array(
                                    'name'	   => 'Total Credit',
                                    'href'     => $this->url->link('report/report/total_credit', 'token=' . $this->session->data['token'], true),
                                    'children' => array()	
                                    );
	}
	if ($report_customer) 
    {	
            $report[] = array(
                            'name'	   => 'Customer',
                            'href'     => '',
                            'children' => $report_customer	
                            );		
	}
	//inventory report
	$inv_report = array();
	if ($this->user->hasPermission('access', 'report/inventory_report')) 
        {
            $inv_report[] = array(
                                'name'	   => 'Center Inventory Report',
                                'href'     => $this->url->link('report/inventory_report', 'token=' . $this->session->data['token'], true),
				'children' => array()
				);
	}
        if ($this->user->hasPermission('access', 'report/inventory_report')) 
        {
            $inv_report[] = array(
                            'name'	   => 'Center Inventory Report (Product Wise)',
                            'href'     => $this->url->link('report/inventory_report/product_wise', 'token=' . $this->session->data['token'], true),
                            'children' => array()
                            );
	}
	
        if($user_info['user_group']!='Customer_care')
        {
            if ($this->user->hasPermission('access', 'report/inventory_report')) 
            {
                $inv_report[] = array(
                            'name'	   => 'Center Inventory (Prev. days report)',
                            'href'     => $this->url->link('report/inventory_report/old_report', 'token=' . $this->session->data['token'], true),
                            'children' => array()
                            );
            }
        }

      
	if ($this->user->hasPermission('access', 'report/inventory_ledger')) 
        {
            $inv_report[] = array(
				'name'	   => 'Product Ledger',
				'href'     => $this->url->link('report/inventory_ledger', 'token=' . $this->session->data['token'], true),
				'children' => array()
				);
	} 
        if ($inv_report) 
        {	
            $report[] = array(
                            'name'	   => 'Inventory',
                            'href'     => '',
                            'children' => $inv_report
                            );		
	}			
	if ($this->user->hasPermission('access', 'report/stores')) 
        {
            $stores_users_report[] = array(
					'name'	   => "Center's users",
					'href'     => $this->url->link('report/stores', 'token=' . $this->session->data['token'], true),
					'children' => array()
                                    );
	}
	if ($stores_users_report) 
        {	
            $report[] = array(
                            'name'	   => "Center's users",
                            'href'     => '',
                            'children' => $stores_users_report
                            );		
	} 
			
                        /* 
                        if ($this->user->hasPermission('access', 'storewisereport/storereport')) {
				$Store_report[] = array(
					'name'	   => 'Store report',
					'href'     => $this->url->link('storewisereport/storereport', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			if ($this->user->hasPermission('access', 'report/leadger')) {
				$Store_report[] = array(
					'name'	   => 'Store Ledger',
					'href'     => $this->url->link('report/leadger', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			
			if ($Store_report) {	
				$report[] = array(
					'name'	   => 'Store report',
					'href'     => '',
					'children' => $Store_report
				);		
			}
			
                        */
			
			if ($report) {	
				$data['menus'][] = array(
					'id'       => 'menu-report',
					'icon'	   => 'fa-bar-chart-o', 
					'name'	   => $this->language->get('text_reports'),
					'href'     => '',
					'children' => $report
				);	
			}
			if ($this->user->hasPermission('access', 'ccare/incommingcall')) {
				$app_cc_report[] = array(
					'name'	   => 'Incoming Call',
					'href'     => $this->url->link('ccare/incommingcall', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			} 
			if ($this->user->hasPermission('access', 'ccare/incommingcall')) {
				$app_cc_report[] = array(
					'name'	   => 'Open Tickets',
					'href'     => $this->url->link('ccare/incommingcall/open', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			}
			if ($this->user->hasPermission('access', 'ccare/incommingcall')) {
				$app_cc_report[] = array(
					'name'	   => 'Resolved/Closed Tickets',
					'href'     => $this->url->link('ccare/incommingcall/resolved_closed', 'token=' . $this->session->data['token'], true),
					'children' => array()
				);
			}
			if ($app_cc_report) {	
				$data['menus'][] = array(
					'name'	   => 'Call Center',
					'href'     => '',
					'id'       => 'menu-report',
					'icon'	   => 'fa-phone', 
					'children' => $app_cc_report
				);		
			}
        return $this->load->view('common/menu.tpl', $data);
    }
}
