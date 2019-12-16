<?php
class Controllermposimage extends Controller {


 public function adminmodel($model) {
      
      $admin_dir = DIR_SYSTEM;
      $admin_dir = str_replace('system/','backoffice/',$admin_dir);
      $file = $admin_dir . 'model/' . $model . '.php';      
      //$file  = DIR_APPLICATION . 'model/' . $model . '.php';
      $class = 'Model' . preg_replace('/[^a-zA-Z0-9]/', '', $model);
      
      if (file_exists($file)) {
         include_once($file);
         
         $this->registry->set('model_' . str_replace('/', '_', $model), new $class($this->registry));
      } else {
         trigger_error('Error: Could not load model ' . $model . '!');
         exit();               
      }
   }


//KYC
public function uploadKYC()
{

//upload file
            $log=new Log("upload-".date('Y-m-d').".log");
             $log->write($this->request->post);
             $log->write($this->request->files);
             //log to table
        
   
                $this->load->model('account/activity');

                $activity_data = $this->request->post;

                $this->model_account_activity->addActivity('upload', $activity_data);
            
       
        //
            
        $this->load->language('api/upload');

        $json = array();



        if (!$json) {
            if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
                $log->write("in if");
				// Sanitize the filename
                $filename = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');

                if ((utf8_strlen($filename) < 1) || (utf8_strlen($filename) > 128)) {
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

                $filetypes = explode("\n", $extension_allowed);

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

               /* if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }*/

                // Allowed file mime types
                $allowed = array();

                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

                $filetypes = explode("\n", $mime_allowed);
		$log->write($filetypes);
                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                /*if (!in_array($this->request->files['file']['type'], $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }*/

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($this->request->files['file']['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            } else {
			$log->write("in else");
                $json['error'] = $this->language->get('error_upload');
            }
        }
		$retval="0";
        if (!$json) {
            $file = $filename.".jpg";
			$log->write("in else err ".$file);
			$log->write(DIR_UPLOAD."kyc/" . $file);
           $retval= move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD."kyc/" . $file);

            // Hide the uploaded file name so people can not link to it directly.
                       
            $json['success'] = $this->language->get('text_upload');
		if($retval){
			$retval="1";
		}
		else{
			$retval="0";
			}
		
        }
$log->write($retval);
        //$this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput($retval);
    
        
//

}
//end kyc



public function upload()
{

//upload file
            $log=new Log("upload.log");
             $log->write($this->request->post);
             $log->write($this->request->files);
             //log to table
        
   
                $this->load->model('account/activity');

                $activity_data = $this->request->post;

                $this->model_account_activity->addActivity('upload', $activity_data);
            
       
        //
            
        $this->load->language('api/upload');

        $json = array();



        if (!$json) {
            if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
                // Sanitize the filename
                $filename = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');

                if ((utf8_strlen($filename) < 1) || (utf8_strlen($filename) > 128)) {
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

                $filetypes = explode("\n", $extension_allowed);

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

               /* if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }*/

                // Allowed file mime types
                $allowed = array();

                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

                $filetypes = explode("\n", $mime_allowed);
		$log->write($filetypes);
                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                /*if (!in_array($this->request->files['file']['type'], $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }*/

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($this->request->files['file']['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            } else {
                $json['error'] = $this->language->get('error_upload');
            }
        }

        if (!$json) {
            $file = $filename.".jpg";

            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD."/output/" . $file);

            // Hide the uploaded file name so people can not link to it directly.
                       
            $json['success'] = $this->language->get('text_upload');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    
        
//

}


/***********upload doc********************/
public function uploadDOC()
{

//upload file
            $log=new Log("uploadDOC-".date('Y-m-d').".log");
             $log->write($this->request->post);
             $log->write($this->request->files);
             //log to table
	$mcrypt=new MCrypt();
        $data['empid'] = $mcrypt->decrypt($_POST['empid']);
	 $data['tid'] = $mcrypt->decrypt($_POST['tid']);

        $this->load->language('api/upload');

        $json = array();

		$retval="0";
		$log->write("in  ");

			$filename = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');
            $file = $filename;
			$data['file']=$file;
			$log->write("in else err ".$file);
			$log->write(DIR_UPLOAD."/output/" . $file);
            move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD."Doc/" . $file);

            // Hide the uploaded file name so people can not link to it directly.
                       
            $json['success'] = $this->language->get('text_upload');
			$retval="1";
        
		$log->write($retval);
        //$this->response->addHeader('Content-Type: application/json');
		
		$this->load->model('account/subuser');
		$jsons = $this->model_account_subuser->upload_document($data);
		$log->write($jsons);
		

        $this->response->setOutput($retval);
    
        
//

}
/**********upload doc end**************************/
public function upload_product()
{
$this->adminmodel('openretailer/openretailer');
//upload file
            $log=new Log("upload-".date('Y-m-d').".log");
            $log->write($this->request->post);
            $log->write($this->request->files);
            $log->write($this->request->get);
            
        $this->load->language('api/upload');
        $this->request->get['category']=str_replace(' ', '_', $this->request->get['category']);
        $json = array();
        $dir="./././image/catalog/".$this->request->get['category'];
        if ( !file_exists($dir) )
        {
            $oldmask = umask(0);  // helpful when used in linux server  
            mkdir ($dir, 0744);
        }
        $log->write($dir);
        
        if (!$json) {
            if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
                // Sanitize the filename
                
                 $p_id= html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');
                 $log->write($p_id);
                 $countimage = $this->model_openretailer_openretailer->count_image($p_id);
                 $log->write($countimage);
               
                 $filename = html_entity_decode($this->request->files['file']['name'],ENT_QUOTES,'UTF-8').'-'.($countimage+1);
                 $log->write($filename);
                 
                if ((utf8_strlen($filename) < 1) || (utf8_strlen($filename) > 128)) 
		{
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

                $filetypes = explode("\n", $extension_allowed);

                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

               /* if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }*/

                // Allowed file mime types
                $allowed = array();

                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

                $filetypes = explode("\n", $mime_allowed);
        $log->write($filetypes);
                foreach ($filetypes as $filetype) {
                    $allowed[] = trim($filetype);
                }

                /*if (!in_array($this->request->files['file']['type'], $allowed)) {
                    $json['error'] = $this->language->get('error_filetype');
                }*/

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($this->request->files['file']['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) {
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            } else {
                $json['error'] = $this->language->get('error_upload');
            }
        }

        if (!$json) {
            $file = $filename.".jpg";
            $log->write($file);
             if(is_writable($dir))
                   {
                   $move=  move_uploaded_file($this->request->files['file']['tmp_name'], $dir.'/' . $file);
                    $log->write($move);
                    $prdid = $this->model_openretailer_openretailer->updateimage($p_id,"catalog/".$this->request->get['category']."/".$file);
                    $prdid = $this->model_openretailer_openretailer->insert_image($p_id,"catalog/".$this->request->get['category']."/".$file);
                   }
                   else
                   {
                   $log->write('not writable');
                   }
           
            // Hide the uploaded file name so people can not link to it directly.
                       
            $json['success'] = $this->language->get('text_upload');
        }
        $log->write($json);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    
        
//

}
	public function upload_ticket_image()
	{
		$this->adminmodel('openretailer/openretailer');
		//upload file
        $log=new Log("upload_ticket_image-".date('Y-m-d').".log");
		$log->write('upload_ticket_image called');
        $log->write($this->request->post);
        $log->write($this->request->files);
        $log->write($this->request->get);
            
        $this->load->language('api/upload');
        
        $json = array();
        $dir="./././image/ticket";
        if ( !file_exists($dir) )
        {
            $oldmask = umask(0);  // helpful when used in linux server  
            mkdir ($dir, 0744);
        }
        $log->write($dir);
        
        if (!$json) 
		{
            if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) 
			{
                // Sanitize the filename
                $log->write('in if file name not empty');
                $ticketid= $this->request->get['ticketid'];//html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');
                $log->write($ticketid);
				$countimage1=$this->model_openretailer_openretailer->count_ticket_image($ticketid);
                $countimage = count($countimage1);
                $log->write($countimage);
               
                $filename = $ticketid.'-'.($countimage+1);//.'_'.html_entity_decode($this->request->files['file']['name'],ENT_QUOTES,'UTF-8').'-'.($countimage+1);
                $log->write($filename);
                 
                if ((utf8_strlen($filename) < 1) || (utf8_strlen($filename) > 128)) 
				{
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

                $filetypes = explode("\n", $extension_allowed);

                foreach ($filetypes as $filetype) 
				{
                    $allowed[] = trim($filetype);
                }

                // Allowed file mime types
                $allowed = array();

                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

                $filetypes = explode("\n", $mime_allowed);
				$log->write($filetypes);
                foreach ($filetypes as $filetype) 
				{
                    $allowed[] = trim($filetype);
                }

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($this->request->files['file']['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) 
				{
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) 
				{
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            }
			else 
			{
				$log->write('in if file name empty');
                $json['error'] = $this->language->get('error_upload');
            }
        }

        if (!$json) 
		{
			$log->write('in if json not  empty');
            $file = $filename.".jpg";
            
            if(is_writable($dir))
            {
				$log->write('in if dir is_writable ');
				$log->write('file');
				$log->write($file);
				$log->write(' tmp_name ');
				$log->write($this->request->files['file']['tmp_name']);
				$im = file_get_contents($this->request->files['file']['tmp_name']);
				$log->write('im');
				//$log->write($im);
                $move=  move_uploaded_file($this->request->files['file']['tmp_name'], $dir.'/' . $file);
                $log->write($move);
                $prdid = $this->model_openretailer_openretailer->update_ticket_image($ticketid,"ticket/".$file);
               
			}
            else
            {
				$log->write('not writable');
            }
           
            // Hide the uploaded file name so people can not link to it directly.
                       
            $json['success'] = $this->language->get('text_upload');
        }
        $log->write($json);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
		//
	}
	
	public function upload_pro_activity_image()
	{
		$this->adminmodel('openretailer/openretailer');
		$this->adminmodel('openretailer/promotionalactivity');
		//upload file
        $log=new Log("upload_pro_activity_image-".date('Y-m-d').".log");
		$log->write('upload_pro_activity_image called');
        $log->write($this->request->post);
        $log->write($this->request->files);
        $log->write($this->request->get);
            
        $this->load->language('api/upload');
        
        $json = array();
        $dir="./././image/activity";
        if ( !file_exists($dir) )
        {
            $oldmask = umask(0);  // helpful when used in linux server  
            mkdir ($dir, 0744);
        }
        $log->write($dir);
        
        if (!$json) 
		{
            if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) 
			{
                // Sanitize the filename
                $log->write('in if file name not empty');
                $ticketid=$this->request->files['file']['name'];// $this->request->get['file'];//html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');
                $log->write($ticketid);
				$countimage1=$this->model_openretailer_promotionalactivity->count_pro_actvity_image($ticketid);
                $countimage = count($countimage1);
                $log->write($countimage);
               
                $filename = $ticketid.'-'.($countimage+1);//.'_'.html_entity_decode($this->request->files['file']['name'],ENT_QUOTES,'UTF-8').'-'.($countimage+1);
                $log->write($filename);
                 
                if ((utf8_strlen($filename) < 1) || (utf8_strlen($filename) > 128)) 
				{
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

                $filetypes = explode("\n", $extension_allowed);

                foreach ($filetypes as $filetype) 
				{
                    $allowed[] = trim($filetype);
                }

                // Allowed file mime types
                $allowed = array();

                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

                $filetypes = explode("\n", $mime_allowed);
				$log->write($filetypes);
                foreach ($filetypes as $filetype) 
				{
                    $allowed[] = trim($filetype);
                }

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($this->request->files['file']['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) 
				{
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) 
				{
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            }
			else 
			{
				$log->write('in if file name empty');
                $json['error'] = $this->language->get('error_upload');
            }
        }

        if (!$json) 
		{
			$log->write('in if json not  empty');
            $file = $filename.".jpg";
            
            if(is_writable($dir))
            {
				$log->write('in if dir is_writable ');
				$log->write('file');
				$log->write($file);
				$log->write(' tmp_name ');
				$log->write($this->request->files['file']['tmp_name']);
				$im = file_get_contents($this->request->files['file']['tmp_name']);
				$log->write('im');
				//$log->write($im);
                $move=  move_uploaded_file($this->request->files['file']['tmp_name'], $dir.'/' . $file);
                $log->write($move);
                $prdid = $this->model_openretailer_promotionalactivity->update_pro_activity_image($ticketid,"activity/".$file);
               
			}
            else
            {
				$log->write('not writable');
            }
           
            // Hide the uploaded file name so people can not link to it directly.
                       
            $json['success'] = $this->language->get('text_upload');
        }
        $log->write($json);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
		//
	}
	
	
	public function upload_profile()
	{
		$this->adminmodel('openretailer/openretailer');
		$this->adminmodel('user/user');
		$mcrypt=new MCrypt();
		//upload file
        $log=new Log("upload_profile-".date('Y-m-d').".log");
		$log->write('upload_profile called');
        $log->write($this->request->post);
        $log->write($this->request->files);
        $log->write($this->request->get);
            
        $this->load->language('api/upload');
        
        $json = array();
        $dir="./././image/profile";
        if ( !file_exists($dir) )
        {
            $oldmask = umask(0);  // helpful when used in linux server  
            mkdir ($dir, 0744);
        }
        $log->write($dir);
        
        if (!$json) 
		{
            if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) 
			{
                // Sanitize the filename
                $log->write('in if file name not empty');
                $ticketid=$this->request->files['file']['name'];// $this->request->get['file'];//html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');
                $log->write($ticketid);
				$ticketid=$mcrypt->decrypt($ticketid); 
				$log->write($ticketid);
				$countimage1=$this->model_user_user->count_upload_image($ticketid);
                $countimage = count($countimage1);
                $log->write($countimage);
               
                $filename = $ticketid.'-'.($countimage+1);//.'_'.html_entity_decode($this->request->files['file']['name'],ENT_QUOTES,'UTF-8').'-'.($countimage+1);
                $log->write($filename);
                 
                if ((utf8_strlen($filename) < 1) || (utf8_strlen($filename) > 128)) 
				{
                    $json['error'] = $this->language->get('error_filename');
                }

                // Allowed file extension types
                $allowed = array();

                $extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

                $filetypes = explode("\n", $extension_allowed);

                foreach ($filetypes as $filetype) 
				{
                    $allowed[] = trim($filetype);
                }

                // Allowed file mime types
                $allowed = array();

                $mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

                $filetypes = explode("\n", $mime_allowed);
				$log->write($filetypes);
                foreach ($filetypes as $filetype) 
				{
                    $allowed[] = trim($filetype);
                }

                // Check to see if any PHP files are trying to be uploaded
                $content = file_get_contents($this->request->files['file']['tmp_name']);

                if (preg_match('/\<\?php/i', $content)) 
				{
                    $json['error'] = $this->language->get('error_filetype');
                }

                // Return any upload error
                if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) 
				{
                    $json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
                }
            }
			else 
			{
				$log->write('in if file name empty');
                $json['error'] = $this->language->get('error_upload');
            }
        }

        if (!$json) 
		{
			$log->write('in if json not  empty');
            $file = $filename.".jpg";
            
            if(is_writable($dir))
            {
				$log->write('in if dir is_writable ');
				$log->write('file');
				$log->write($file);
				$log->write(' tmp_name ');
				$log->write($this->request->files['file']['tmp_name']);
				$im = file_get_contents($this->request->files['file']['tmp_name']);
				$log->write('im');
				//$log->write($im);
                $move=  move_uploaded_file($this->request->files['file']['tmp_name'], $dir.'/' . $file);
                $log->write($move);
                $prdid = $this->model_user_user->update_profile_image($ticketid,"profile/".$file);
               
			}
            else
            {
				$log->write('not writable');
            }
           
            // Hide the uploaded file name so people can not link to it directly.
                       
            $json['success'] = $this->language->get('text_upload');
        }
        $log->write($json);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
		//
	}
	
	
}
