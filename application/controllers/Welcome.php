<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Homepage extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
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

		//reference directory 
		$directory_to_fetch			= './uploads/';

		//fetching all files from directory , param '1' is passed to control the recursion depth
		$mapped_listing				= directory_map($directory_to_fetch,1);

		$response					= array();
		if(!empty($mapped_listing))
		{
			$paginated_data 		= array_slice($mapped_listing,$start_point,$limit);
			$response['data']		= $paginated_data;
			$response['status'] 	= true;
			$response['message'] 	= 'files fetched from directory';
		}
		else
		{
			$response['data']		= array();
			$response['status'] 	= false;
			$response['message'] 	= 'There is no files in the directory';
		}
        $this->load->view('welcome_message');
    }
}
