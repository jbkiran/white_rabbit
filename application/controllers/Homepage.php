<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homepage extends CI_Controller {

	public function __construct()
    {
		parent::__construct();
		//reference directory 
		$directory_to_fetch		= './uploads/';
		
    }
	public function index()
	{
		$this->list_directory();
	}
	/*
	used for 	- listing all files from a specific directory
	date 		- 09-01-2021
	*/
	public function list_directory()
    {
		$this->load->helper('directory');
		$offset 		= 0;
		$limit 			= 10;
		$is_json		= $this->input->post('is_json',true);
		if($is_json)
		{
			$offset = $this->input->post('offset');
		}

		//fetching all files from directory , param '1' is passed to control the recursion depth
		$mapped_listing				= directory_map($directory_to_fetch,1);

		$response					= array();
		if(!empty($mapped_listing))
		{
			//slicing the array for pagination
			$paginated_data 		= array_slice($mapped_listing,$offset,$limit);
			$response['data']		= $paginated_data;
			$response['directory']	= $directory_to_fetch;
			$response['offset']		= $offset;
			$response['limit']		= $limit;
			$response['total']		= count($mapped_listing);
			$response['status'] 	= true;
			$response['message'] 	= 'files fetched from directory';
		}
		else
		{
			$response['data']		= array();
			$response['status'] 	= false;
			$response['message'] 	= 'There is no files in the directory';
		}
		if($is_json == true)
		{
			echo json_encode($response);
		}
		else
		{
			$this->load->view('welcome_message',$response);
		}
        
	}
	/*
	used for 	- removing file from the directory
	date 		- 09-01-2021
	*/
	public function remove_file()
	{
		$filename 				= $this->input->post('file_name');
		$file 					= $directory_to_fetch.$filename;
		if(is_file($file))
		{
			unlink($file);
		}
		$reponse 				= array();
		$response['status'] 	= true;
		$response['message'] 	= 'removed successfully';
        echo json_encode($response);
	}

	/*
	used for 	- uploading file to the directory
	date 		- 09-01-2021
	*/
	public function upload_file()
	{
		$response 				= array();
		$response['status'] 	= false;
		$response['message'] 	= 'error in uploading';
		//checking if file is uploaded 
		if(isset($_FILES['file']) && $_FILES['file']['error'] !== 4)
        { 
           
            $allowed_types 		=  array('txt','doc','docx','pdf','png','jpeg','jpg','gif');
            $filename 			= $_FILES['file']['name'];
            $file_extension		= pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array($file_extension,$allowed_types))
            {

				if(!file_exists($directory_to_fetch)){
					mkdir($directory_to_fetch, 0777, true);
				}
				$this->load->library('upload');

				$config                     = array();
				$config['upload_path']      =  $directory_to_fetch;
				$config['allowed_types']    = 'txt|doc|docx|pdf|png|jpeg|jpg|gif';
				$config['file_name']        = $filename;
				$config['max_size'] 		= 2000;

				$this->upload->initialize($config);
				$this->upload->do_upload('file');
				$uploaded_data 				= $this->upload->data();
				if(!empty($uploaded_data))
				{
					
					$response['status'] 	= true;
					$response['message'] 	= 'successfully uploaded';
				}
				
            }
            
		}
		echo json_encode($response);
	}
}
