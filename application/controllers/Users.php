<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
	}
	
	public function index()
	{
			$crud = new grocery_CRUD();

			$crud->set_table('users');
			$crud->columns('name','surname','image_path');

			$crud->set_theme('bootstrap');

			try {
				$crud->display_as('name','Name')
				->display_as('surname','Last Name')
				->display_as('image_path','İmage');
			} catch (Exception $th) {
				//throw $th;
			}


			$crud->set_subject('Üyeler');

			$crud->set_field_upload('image_path','assets/uploads/images');

			$output = $crud->render();

			$this->_example_output($output);
	}

	public function _example_output($output = null)
	{
		$this->load->view('user.php',(array)$output);
	}
}
