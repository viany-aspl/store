<?php
class ControllerToolBackup extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('tool/backup');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('tool/backup');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'tool/backup')) {
			if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
				$content = file_get_contents($this->request->files['import']['tmp_name']);
			} else {
				$content = false;
			}

			if ($content) {
				$this->model_tool_backup->restore($content);

				$this->session->data['success'] = $this->language->get('text_success');

				$this->response->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->error['warning'] = $this->language->get('error_empty');
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_select_all'] = $this->language->get('text_select_all');
		$data['text_unselect_all'] = $this->language->get('text_unselect_all');

		$data['entry_restore'] = $this->language->get('entry_restore');
		$data['entry_backup'] = $this->language->get('entry_backup');

		$data['button_backup'] = $this->language->get('button_backup');
		$data['button_restore'] = $this->language->get('button_restore');

		if (isset($this->session->data['error'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['restore'] = $this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL');

		$data['backup'] = $this->url->link('tool/backup/backup', 'token=' . $this->session->data['token'], 'SSL');

		$data['tables'] = $this->model_tool_backup->getTables();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('tool/backup.tpl', $data));
	}
/*
	public function backup() {
		$this->load->language('tool/backup');

		if (!isset($this->request->post['backup'])) {
			$this->session->data['error'] = $this->language->get('error_backup');

			$this->response->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
		} elseif ($this->user->hasPermission('modify', 'tool/backup')) {
                    
                    
			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=' . date('Y-m-d_H-i-s', time()) . '_backup.csv');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			$this->load->model('tool/backup');
                        $this->download($this->request->post['backup']);
			//$this->response->setOutput();
		} else {
			$this->session->data['error'] = $this->language->get('error_permission');

			$this->response->redirect($this->url->link('tool/backup', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}*/
        
        public function  backup()
        {
            $tables=$this->request->post['backup'];
            $fiveMBs = 5 * 1024 * 1024;
            $filename="";
            $fileIO = fopen("php://temp/maxmemory:".$fiveMBs, 'w+');
            foreach ($tables as $table) {
               if(!empty($table))
                {  
            $filename.=$table."-";       
            $cnt=  $this->db->getcount($table,array());             
            $limit=10000;
            if($cnt<$limit)
            {
                $limit=$cnt;
            }
            $page=round($cnt/$limit);                                    
           
            $tbname=array('name'=>$table);
            fputcsv($fileIO,$tbname);
            for($icount=0;$icount<$page;$icount++)
            { 
                $col = $this->db->dump('select',$table,'','','','','',$limit,'',($icount*$limit));
                
                $col=iterator_to_array($col); 
                foreach(array_keys($col[array_keys($col)[0]]) as $key){
		    $keys[0][$key] = $key;
		}
                if($icount==0){
                $col = array_merge($keys,  $col);}                
		foreach ($col as $fields) {
                    
                      foreach(array_keys($fields) as $key){
                          
                    
                        if((is_array($fields[$key])) && !empty($fields[$key]))
                        {
                            $fields[$key]= json_encode($fields[$key]); 
                        }
                        
                        if($fields[$key] instanceof MongoDate)
                        {
                            $fields[$key]= date('Y-m-d h:i:s',($fields[$key]->sec));
                        }
                    
		    
                      }
                      fputcsv($fileIO,$fields);
		}
            }
                }
            }
            //exit;
            fseek($fileIO,0);                     
                    //                                        
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment;filename="'.$filename.date('d-M-y').'.csv"');
            header('Cache-Control: max-age=0');
            fpassthru($fileIO);
            fclose($fileIO);
                
             
           
        }
       
        
}