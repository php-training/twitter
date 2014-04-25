<?php
use \Model\Followapi;
class Controller_Followapi extends Controller_Template
{

	public function action_addfollow()
	{
		try{
			$data["subnav"] = array('index'=> 'active' );
			$this->template->title = 'Add follow';
			
			//$this->template->content = View::forge('follow/addfollow',$data);
			//$data["inform"] = $_SESSION["test"];
			session_start();
			If(isset($_SESSION["test"])){
				$test = Session::get('test');
				var_dump($test);
				session_unset();
			}
			
			return Response::forge(View::forge('followapi/addfollow'));
		}catch (\Exception $e) {
		  echo $e->getMessage();
		}
		
	}
	public function action_addfollowpost(){
		if (Input::method() == 'POST')
		{				
			$test = array(
				'iduser' => Input::post('iduser'),
				'folowed_iduser' => Input::post('followed_iduser'),
							
			);				
					
		}
		session_start();
		$_SESSION["test"]=$test;
		Session::set('test', $test);
	
		Response::redirect('/Followapi/addfollow');
	}

}