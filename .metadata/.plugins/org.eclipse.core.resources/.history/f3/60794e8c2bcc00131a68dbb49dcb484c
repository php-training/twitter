<?php
	/**
	 * 
	 * Copyright 2014 by tran.hoa@mulodo.com.
	 * All right reserved.
	 * This is the controller of the API for login, signup and reissue password for iSociety (Twitter like system)
	 * For Mulodo Vietnam PHP Training - April 2014
	 */

	/**
	 * Section: Developing an API by using Fuel PHP Framework
	 * Fuel Version: 1.7
	 */
	class Controller_UserAPI extends Controller{
		/**
		 * ERROR CODE
		 */
		protected $STATUS_OK         = 200;
		protected $STATUS_DB_ERR     = 500;
		protected $STATUS_VALID_ERR  = 401;
		protected $STATUS_EXST_ACC   = 402;
		protected $EXPIRE_PERIOD     = 172800; //2 days
		
		/**
		 * the index action: for testing only
		 */
		public function action_index(){
			echo "Hi! I'm the user API of iSociety. :)";
			
		}
		
		/**
		 * the login api
		 * input: email and encrypted password via POST
		 * output: login status in XML format  
		 */
		public function action_login(){
			
			$error_code = $this -> STATUS_OK; //default OK
			
			$token=""; //null
				
			$val = Validation::forge('login_alidation'); //check the validation, if not set VALID_ERR
				
			$val -> add_field('email', 'Email', 'required|valid_email');
			$val -> add_field('password','Password','required|min_length[3]|max_length[10]');
			
			if (!$val -> run()){ //failed, VALID_ERR
				$error_code = $this -> STATUS_VALID_ERR;
			}				
			else{ //valid parameters, thus get the parameters and continue 
					
				$email    = Input :: Post('email');
				$password = md5(Input :: Post('password'));
					
				try{
					$entry = Model_User::find('all', array (
							'where' => array (
									array ('email', $email), array ('password',$password)
							)
					));
						
					if (count($entry) == 0) { //login failed
						$error_code = $this -> STATUS_EXST_ACC;
							
					}
					else{
						
						$token = hash_hmac('sha1',time(),uniqid(),false); //generate token
						
						foreach ($entry as $user){ //run 1 time since there is only one user. 
							$dbtoken   = new Model_Login_Token();	//if no error is found, insert token into login_token table
							$dbtoken -> iduser   = $user -> id; //id user
							$dbtoken -> token    = $token;
							$dbtoken -> due_date = date('Y-m-d h:i:s' ,time() + $this->EXPIRE_PERIOD); //2 days
							$dbtoken -> save();
						}
					}
				
				}
				catch (Database_Exception $dbe){
					$error_code = $this -> STATUS_DB_ERR;
				}

			}
			
			
			$result = array("error" => array("status" => $error_code,"message" => ""),"token" => $token); //print result in XML format
			print_r(Format :: forge($result) -> to_xml());

		}
		
		/**
		 * the resetpwd api
		 * input: email via POST
		 * output: reset password status in XML format 
		 */
		public function action_resetpwd(){
			//echo "Hi! I'm resetting your password";
			
		}
		
		/**
		 * the signup api
		 * input: email and encrypted password via POST
		 * output: the status of the API in XML format
		 */
		public function action_signup(){
			
			//echo "Hi! I'm signing up an account for you.";
		

			$error_code = $this -> STATUS_OK; 

			$val = Validation::forge('signup_alidation'); //check the validation, if not set VALID_ERR
			
			$val -> add_field('email', 'Email', 'required|valid_email');
			$val -> add_field('password','Password','required|min_length[3]|max_length[10]');
			
			if (!$val->run()){
				$error_code = $this -> STATUS_VALID_ERR; // invalid parameter
			}
			
			else{ //ok
				
				$email    = Input :: post('email');
				$pwd      = md5(Input :: post('password')); 	//get the post variable first
						
				try{
					
					if (count(Model_User :: find('all', array(
							'where' => array(
									array('email',$email))
					)))>0) //validate paramenter 401
								$error_code = $this -> STATUS_EXST_ACC;
				
							//then create a user model to insert into database
							else{
									
								$newuser             = new Model_User();
								//sync data
								$newuser -> email    = 	$email;
								$newuser -> password = 	$pwd;
								$newuser -> created_at = time();
								//insert into databaseateime
								$newuser -> save();
									
							}
				}
				
				catch (Database_Exception $dbe){
					
					$error_code = $this -> STATUS_DB_ERR;
				}	
			}
			
			
			$result = array("error" => array("status" => $error_code,"message" => "")); //print result in XML format
			print_r(Format :: forge($result) -> to_xml());
			
		}
		
		/**
		 * the logout api
		 * input: email via POST
		 * output: the status of the API in XML format
		 */
		public function action_logout(){
			//echo "Hi! I'm loging out.";
			
			$error_code = $this -> STATUS_OK;

			$val = Validation :: forge('logout_validation'); //validation			
			$val -> add_field ('token','Login_token','required|min_length[5]');
			if (!$val -> run()){ //failed
				$error_code = $this -> STATUS_VALID_ERR;
			}
			else{ // validation pass, go the next step
				$token = Input :: post ('token');
				
				try{
					$entry = Model_Login_Token ::find('all', array (
											'where' => array (//search the database for token 
											array ('token', $token),
											array ('due_date', '>=', date ('Y-m-d h:i:s', time())),
											)
											));
					
					
					if (!count($entry)>0) { //token is not existed
						$error_code = $this -> STATUS_EXST_ACC;
					}
					else { //continue to expire the token
					
						foreach ($entry as $dbtoken){ //only one
							$dbtoken -> due_date = date ('Y-m-d h:i:s', time());
							$dbtoken -> save(); //updated
						}
					}
				}
				catch (Database_Exception $dbe){
					$error_code = $this -> STATUS_DB_ERR;
				}
			}
			
			$result = array ("error" => array ("status" => $error_code, "message" => ""));
			print_r(Format :: forge ($result) -> to_xml()); //print the result in xml
		}
		
		/**
		 * the changepassword api
		 * input: reset_token and the new password via POST
		 * output: the status of the API in XML format
		 */
		public function action_changepwd(){
			
		}
		
		/**
		 * the activate user api
		 * input: activate_token
		 * output: the status of the API in XML format
		 */
		public function action_actusr(){
			
		}
	}	
?>