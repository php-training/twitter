<?php 
use \Model\Likeapi;

class Controller_Likeapi extends Controller_Rest
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Fuel
 * @version    1.7
 * @author     PHP-Training Team
 * @license    MIT License
 * @copyright  do.hoc@mulodo.com
 * @link       http://fuelphp.com
 */
 
/**
 * The LikeAPI Controller.
 *
 * A basic controller example.  Has examples of how to set the
 * response body and status.
 *
 * @package  app
 * @extends  Controller
 */
{

	private $idmsg = '';
	private $token = '';
	
	public function action_index()
	{
		if(Input::method() == 'POST')
		{
			//format
			$this->format = 'xml';
			$this->idmsg = Input::post('idmsg');
			$this->token = Input::post('token');
			
			//validation
			if(!empty($this->token))
			{
				$view =
			} else {
				return $this->response(
					array(
						'error' => array(
							'status' 	=> '401',
							'message' 	=> 'Please login before like this post',
						)
					)
				);
			}
			
			
		}
	}
}
?>