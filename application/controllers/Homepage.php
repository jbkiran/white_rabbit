<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homepage extends CI_Controller {

	public function __construct()
    {
		parent::__construct();
		//reference directory 
		$directory_to_fetch			= './uploads/';
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

		$is_json	= $this->input->post('is_json',true);

		//fetching all files from directory , param '1' is passed to control the recursion depth
		$mapped_listing				= directory_map($directory_to_fetch,1);

		$response					= array();
		if(!empty($mapped_listing))
		{
			$paginated_data 		= array_slice($mapped_listing,$start_point,$limit);
			$response['data']		= $paginated_data;
			$response['directory']	= $directory_to_fetch;
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
	public function remove_file()
	{
		$filename 	= $this->input->post('file_name');
		$file 		= $directory_to_fetch.$filename;
		if(is_file($file))
		{
			unlink($file);
		}
		$reponse 				= array();
		$response['status'] 	= true;
		$response['message'] 	= 'removed successfully';
        echo json_encode($response);
	}

	public function upload_file()
	{
		if(isset($_FILES['lecture_image']) && $_FILES['lecture_image']['error'] !== 4)
        { 
           
            $allowed =  array('gif','png' ,'jpg', 'jpeg');
            $filename = $_FILES['lecture_image']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed))
            {

                $lecture_id                         = $this->input->post('lecture_id', true);

                $version                            = rand(0,300);
                if($this->upload_course_lecture_image_to_localserver(array('course_id'=>$this->input->post('course_id', true), 'lecture_id'=>$lecture_id )))
                {
                    $save['cl_lecture_image']               =  $lecture_id.".webp?v=".$version;
                }
            }
            
        }
	}
}
