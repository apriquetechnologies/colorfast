<?php

class lumise_connector {
    
    public $platform;
    public $config;
    protected $order_statuses;
    
    public function __construct() {
		
		global $lumise;
		
		if (
			!isset($_COOKIE['LUMISESESSID']) || 
			empty($_COOKIE['LUMISESESSID']) || 
			$_COOKIE['LUMISESESSID'] === null
		) {
			$sessid = strtoupper(substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20));
			@setcookie('LUMISESESSID', $sessid, time() + (86400 * 30), '/');
			$_COOKIE['LUMISESESSID'] = $sessid;
		}
		
        $this->platform = 'php';
        $url = $this->url(1);
        
        $order_statuses = array(
            'pending' => 'Pending',
            'approved' => 'Approved',
            'complete' => 'Complete',
            'processing' => 'Processing',
            'cancel' => 'Cancel',
        );
        
        $this->config = array(
			"url" => $url,
			"tool_url" => $url.'editor.php',
			"logo" => $url. 'core/assets/images/logo.v3.png',
			"ajax_url" => $url.'editor.php?lumise-router=ajax',
			"admin_ajax_url" => $url.'editor.php?lumise-router=ajax',
            "checkout_url" => $url.'editor.php?lumise-router=cart',
			"assets_url" => $url.'core/',
			"load_jquery" => true,
			"root_path" => dirname(__FILE__).DS.'core'.DS,
            
            "upload_path" => '/home3/apriqnky/public_html/colorfast.in/data/',
			"upload_url" => 'https://colorfast.in/data/',
            "admin_assets_url" => $url.'core/admin/assets/',
            "admin_url" => $url.'admin.php?',
            
			"database" => array(
                "host" => 'localhost',
                "user" => 'apriqnky_colorfast',
                "pass" => 'vkL-QZCoYbK%',
                "name" => 'apriqnky_colorfast',
                "prefix" => 'cf_'
            )
		);
        
        if(isset($lumise)){
            $this->order_statuses = array(
                'pending' => $lumise->lang('Pending'),
                'complete' => $lumise->lang('Complete'),
	            'approved' => 'Approved',
                'processing' => $lumise->lang('Processing'),
                'cancel' => $lumise->lang('Cancel'),
            );
        }
        
        
        if(isset($lumise)){
            $lumise->add_action('admin-verify', array(&$this, 'admin_verify'));
            $lumise->add_action('before_order_products', array(&$this, 'update_order_status'));
            $lumise->add_action('before_orders', array(&$this, 'update_orders'));
    		$lumise->add_filter('order_status', array(&$this, 'order_status_name'));
            $lumise->add_action('toggle_admin', array(&$this, 'toggle_admin'));
        }
    }
    
    public function url($f = 0) {
	    
	    global $lumise;
	    
	    if ($f === 0)
	    	$uri = '';
	    else if ($f === 1)
	    	$uri = (dirname($_SERVER['SCRIPT_NAME']) == '/')? '/' : dirname($_SERVER['SCRIPT_NAME']).'/';
	    else $uri = $_SERVER['REQUEST_URI'];
	    
	    $scheme = 'http';
	    
	    if (
		    (isset($_SERVER['HTTP_CF_VISITOR']) && isset($_SERVER['HTTP_CF_VISITOR']->scheme) && $_SERVER['HTTP_CF_VISITOR']->scheme == 'https') ||
		    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ||
		    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || 
		    $_SERVER['SERVER_PORT'] == 443
	    ) $scheme = 'https';
	    
		return $scheme."://".$_SERVER['HTTP_HOST'].$uri;
		
	}
    
    
	public function admin_verify() {
		if (isset($_POST['nonce']))
			$this->process_login();
		
		if (isset($_POST['reset-token']))
			$this->process_reset();
		
		if (!$this->is_admin()) {
		
			include 'login.php';
			exit;
		} else if (isset($_GET['signout']) && $_GET['signout'] == 'true') {
			$this->process_logout();
		}
	}

	public function process_login() {
		global $lumise;
		$action = isset($_POST['action']) ? $_POST['action'] : '';
		$redirect = isset($_POST['redirect']) && !empty($_POST['redirect']) ? $_POST['redirect'] : $this->config['url'].'?lumise-router=admin';
		$msg = array();
		$nonce = isset($_POST['nonce']) ? $_POST['nonce'] : '';
		$limit = $this->get_session('LIMIT');
		
		if ($limit[0] >= 30 && time()-$limit[1] < 60*60) {
			header('location:'.urldecode($redirect));
			exit;
		}
		
		$admin_email = $lumise->get_option('admin_email');
			
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		if ($limit === null || !is_array($limit) || time()-$limit[1] > 60*60)
			$limit = array(0, time());
            
        $check = lumise_secure::check_nonce('LOGIN-SECURITY', $nonce);
		if (!$check) {
			$limit[0] += 1;
			$limit[1] = time();
			array_push($msg, array(
				'type' => 'error',
				'content' => $lumise->lang('Invalid login token').', '.$limit[0].' '.$lumise->lang('failed login attempts.')
			));
		}else 
		if ($action == 'login') {
			
			if (!isset($admin_email) || empty($admin_email)) {
				$limit[0] += 1;
				$limit[1] = time();
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('The admin account has not been setup').', '.$limit[0].' '.$lumise->lang('failed login attempts.')
				));
			}else{
				
				if (empty($email)) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your email is empty')
					));
				}
				else if (empty($password)) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your password is empty')
					));
				}
				else if ($admin_email != $email || $lumise->get_option('admin_password', '') != md5($password)) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your email or password is incorrect')
					));
				}
				
				if (count($msg) > 0) {
					$limit[0] += 1;
					$limit[1] = time();
					$msg[count($msg)-1]['content'] .= ', '.$limit[0].' '.$lumise->lang('failed login attempts.');
				}else{
					$this->set_session('UID', $email);
					$this->set_session('ROLE', 1);
					header('location:'.urldecode($redirect));
					exit;
				}
			}
			
		}else 
		if ($action == 'setup') {
			
			if (!isset($admin_email) || empty($admin_email)) {
				
				$password2 = $_POST['password2'];
				
				if (strpos($email, '@') === false || strpos($email, '.') === false) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your email is invalid')
					));
				}
				
				if (strlen($password) < 8) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Your password must be at least 8 characters')
					));
				}
				
				if ($password != $password2) {
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Repeat passwords do not match')
					));
				}
				
				if (count($msg) === 0) {
					$lumise->set_option('admin_email', $email);
					$lumise->set_option('admin_password', md5($password));
					$this->set_session('UID', $email);
					$this->set_session('ROLE', 1);
					header('location:'.urldecode($redirect));
					exit;
				}
				
			}else{
				$limit[0] += 1;
				$limit[1] = time();
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('The admin account has been setup').', '.$limit[0].' '.$lumise->lang('failed login attempts.')
				));
			}
		}else 
		if ($action == 'reset') {
			
			if (
				!isset($_POST['password']) || 
				empty($_POST['password']) ||
				empty($_POST['password2']) ||
				$_POST['password'] != $_POST['password2'] ||
				strlen($_POST['password']) < 8
			) {
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('Passwords do not match or less than 8 characters')
				));
				$this->set_session('login-msg', $msg);
			} else {
				$lumise->set_option('admin_password', md5(trim($_POST['password'])));
				array_push($msg, array(
					'type' => 'success',
					'content' => $lumise->lang('Your password has been changed successfully')
				));
				$this->set_session('login-msg', $msg);
				header('location:'.$lumise->cfg->admin_url.'ref=reset');
				exit;
			}
			
		}else{
			$limit[0] += 1;
			$limit[1] = time();
			array_push($msg, array(
				'type' => 'error',
				'content' => $lumise->lang('Invalid action').', '.$limit[0].' '.$lumise->lang('failed login attempts.')
			));
		}
		
		$this->set_session('LIMIT', $limit);
		$this->set_session('login-msg', $msg);
		
		if ($limit[0] >= 30 && time()-$limit[1] < 60*60) {
			header('location:'.urldecode($redirect));
			exit;
		}
		
	}
	
	public function process_logout() {
		
		global $lumise;
		
		$this->set_session('UID', null);
		$this->set_session('ROLE', null);
		
		header('location:'.$lumise->cfg->admin_url.'ref=signout');
		
	}
	
	public function process_reset() {
		
		global $lumise;
		
		$nonce = isset($_POST['reset-token']) ? $_POST['reset-token'] : '';
		$limit = $this->get_session('LIMIT');
		$email = $_POST['email'];
		$msg = array();
		
		if ($limit === null || !is_array($limit) || time()-$limit[1] > 60*60)
			$limit = array(0, time(), 0, time());
			
		if (!lumise_secure::check_nonce('RESET-SECURITY', $nonce)) {
			$limit[2] += 1;
			$limit[3] = time();
			array_push($msg, array(
				'type' => 'error',
				'content' => $lumise->lang('Invalid reset token').', '.$limit[0].' '.$lumise->lang('failed reset attempts.')
			));
		}else{
			if ($limit[2] >= 5 && time()-$limit[3] < 60*60) {
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('You have failed reseting for 5 times. For the security, please try again in ').round(60-((time()-$limit[3])/60)).' '.$lumise->lang('minutes')
				));
			}else if ($lumise->get_option('admin_email', '') != $email) {
				if ($limit[2] < 5)
					$limit[2] += 1;
				else $limit[2] = 1;
				$limit[3] = time();
				array_push($msg, array(
					'type' => 'error',
					'content' => $lumise->lang('The email does not exist').', '.$limit[2].' '.$lumise->lang('failed reset attempts.')
				));
			}else{
				
				$token = $lumise->generate_id();
				$lumise->set_option('reset_token', $token);
				$lumise->set_option('reset_expires', time()+(60*10));
				
				
				$to      =  $lumise->cfg->settings['admin_email'];
				$subject = 'Lumise - Reset control panel password';
				$message = "Please click to the link bellow to reset your password.\nThis link will expire within 10 minutes.\n".
					   $lumise->cfg->admin_url."reset-password=".$token;
				$message = wordwrap($message,70);
				$url = parse_url($lumise->cfg->url);
				$headers = 'From: no-reply@'.$url['host'] . "\r\n" .
				    'Reply-To: no-reply@'.$url['host'] . "\r\n" .
				    'X-Mailer: PHP/' . phpversion();
				
				if (mail($to, $subject, $message, $headers)) {
					$limit[2] = 0;
					$limit[3] = time();
					unset($_POST['action']);
					array_push($msg, array(
						'type' => 'success',
						'content' => $lumise->lang('A reset email has been sent, please check your inbox (including spam box)')
					));
				}else if (mail($to, $subject, $message, $headers)) {
					$limit[3] = time();
					array_push($msg, array(
						'type' => 'error',
						'content' => $lumise->lang('Could not send mail, ensure that the mail() function on your webserver can work')
					));
				}
			}
		}
		
		$this->set_session('LIMIT', $limit);
		$this->set_session('login-msg', $msg);
		
	}

    public function get_session($name) {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    public function set_session($name, $value) {
        $_SESSION[$name] = $value;
    }

    public function cookie($name) {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }
    
    public function is_admin() {
		
		// return user is admin
		
		global $lumise;
		
		return ($this->get_session('ROLE') === 1 && $this->get_session('UID') !== null);
		
	}
	
	public function is_login() {
		
		// return user id, 0 if not login
		
		global $lumise;
		return $lumise->connector->cookie('uid') || 0;
		
	}
    
    public function get_currency() {
        return "$";
    }
    
    public function filter_product($data) {
		
        return $data;
        
    }
            
    public function filter_products($products) {
		
		return $results;
        
    }
    
	public function add_to_cart($data){
		
		global $lumise;
    	return $lumise->cfg->editor_url.'cart.php';
		
	}
	public function register(){
        $user_data = array(
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'address' => $_POST['address'],
            'zipcode' => $_POST['zip'],
            'city' => $_POST['city'],
            'state' => $_POST['city'],
            'phone' => $_POST['phone'],
            'password' => md5($_POST['password'])
        );
		global $lumise;
		$db = $lumise->get_db();
		$email_exists= $db->where('email', $_POST['email'])->get('users');
		if(empty($email_exists)){
			$id = $db->insert('users', $user_data);
			$token = $this->verify_email($_POST['email']);
			$db->where('id', $id)->update('users', ['email_verified'=>$token]);
	        $this->set_session('verify_email', $user_data['email']);
			return 'success';
		}else{
			return ['error'=>'EmailID already exists'];
		}
	}

	public function verify_email($email, $code =null){
		global $lumise;
		if($code){
			$db = $lumise->get_db();
			$verifiedUserId = $db->where('email', $email)->where('email_verified', $code)->get('users', 1, ['id'])[0]['id'];
			if($verifiedUserId){
				$db->where('id', $verifiedUserId)->update('users', ['email_verified'=>1]);
				return ['status'=>'success', 'message'=>'Account verified!'];
			}
			return ['status'=>'danger', 'message'=>'Verification failed. Please try again later.'];
		}
		$token = $lumise->generate_id();
		$activationLink = 'https://www.colorfast.in/verifyEmail.php?email='.$email.'&ecode='.$token;
		$subject = 'ColorFast - Verify your email address';
		$message = "<p>Hi there,</p>
<p>To verify your email account, please click the button below.</p>
<p><a href='".$activationLink."' target='_blank'>
<div style='text-decoration:none;display:block;color:#ffffff;background-color:#f16222;border-radius:4px;width:50%;width:calc(50% - 2px);border-top:1px solid #f16222;border-right:1px solid #f16222;border-bottom:1px solid #f16222;border-left:1px solid #f16222;padding-top:15px;padding-bottom:15px;font-family:Lato,Tahoma,Verdana,Segoe,sans-serif;text-align:center;word-break:keep-all'><span style='padding-left:20px;padding-right:20px;font-size:20px;display:inline-block'><span style='font-size:16px;line-height:1.5;word-break:break-word'><span style='font-size:20px;line-height:30px'><span class='il'>Verify</span> <span class='il'>my</span> email</span></span></span></div></a>
</p>
<p>Or paste this link into your browser:</p>
<p>".$activationLink."</p>
<p></p>
<p>Thank you,</p>
<p>Team ColorFast</p>";
		$url = parse_url($lumise->cfg->url);
		$headers = 'From: ColorFast <no-reply@'.$url['host'].'>' . "\r\n" .
			'Reply-To: no-reply@'.$url['host'] . "\r\n" .
			'Content-type: text/html; charset=iso-8859-1' . "\r\n".
			'X-Mailer: PHP/' . phpversion();
		
		if(mail($email, $subject, $message, $headers)){
			return $token;
		}
	}

	public function signin($otp=null){
		global $lumise;
		$db = $lumise->get_db();
		$temp = $otp ? '' : '_temp';
		if(is_null($otp)){
			$db->where('email', $_POST['email']);
			$db->where('password', md5($_POST['password']));
		}else{
			$db->where('email', $this->get_session('user_email_temp'));
			$db->where('otp', $_POST['otp']);
			$db->where('otp_created_at <= '.$db->now('+10m')['[F]'][0]);
		}
		$user = $db->get('users', 1, ['id', 'email', 'first_name','is_admin'])[0];
		if(!empty($user)){
	        $this->set_session('user_id'.$temp, $user['id']);
	        $this->set_session('user_email'.$temp, $user['email']);
	        $this->set_session('user_first_name'.$temp, $user['first_name']);
	        $this->set_session('is_admin'.$temp, $user['is_admin']);
			if(is_null($otp)){
				$token = $lumise->generate_id(6);
				$db->where('id', $user['id'])->update('users', ['otp'=>$token, 'otp_created_at'=>date("Y-m-d H:i:s")]);
				$this->send_otp_mail($token);
			}else{
				unset($_SESSION['user_id_temp']);
				unset($_SESSION['user_email_temp']);
				unset($_SESSION['user_first_name_temp']);
				unset($_SESSION['is_admin_temp']);
			}
			return true;
		}else{
			return false;
		}
	}

	public function send_otp_mail($otp){
		global $lumise;

		$subject = 'ColorFast - Signin OTP';
		$message = '
			<div style="font-family: Helvetica,Arial,sans-serif; overflow:auto; line-height:2;">
				<div style="margin:50px auto; width:50%; padding:20px; border: 2px solid #908181; border-radius: 2%;">
					<div style="text-align: center; border-bottom: 1px solid #908181; padding-bottom: 20px;">
						<img style="width: 155px;" src="https://www.colorfast.in/assets/images/logo.png" alt="ColorFast | Delivering Unique Printing Solutions">
					</div>
					<p style="font-size:1.1em">Hi,</p>
					<p>Use the following OTP to complete your Sign In. OTP is valid for 10 minutes</p>
					<h2 style="background: #3d3c7d;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">'.$otp.'</h2>
					<p style="font-size:0.9em;">Regards,<br />ColorFast Team</p>
					<hr style="border:none;border-top:1px solid #eee" />
				</div>
			</div>';

		$url = parse_url($lumise->cfg->url);
		$headers = 'From: ColorFast <no-reply@'.$url['host'].'>' . "\r\n" .
			'Reply-To: no-reply@'.$url['host'] . "\r\n" .
			'Content-type: text/html; charset=iso-8859-1' . "\r\n".
			'X-Mailer: PHP/' . phpversion();
		mail($this->get_session('user_email_temp'), $subject, $message, $headers);
	}

	public function user_data(){
		global $lumise;
		if(!$lumise->connector->get_session('user_id')){
			return false;
		}
		$db = $lumise->get_db();
		$db->where('id', $lumise->connector->get_session('user_id'));
		$user_data = $db->get('users', 1, null);
		return $user_data[0];
	}

	public function saveDesign($data){
		global $lumise;
		$db = $lumise->get_db();
		$design_data = array(
			'design_id'		=> $data['design_id'],
			'product_id'	=> $data['product_id'],
			'design'		=> json_encode($data['design']),
			'dumb'			=> json_encode($data['dumb']),
			'user_id'		=> $data['user_id']
		);
		$db->where('design_id',$design_data['design_id']);
		$db->where('product_id',$design_data['product_id']);
		$db->where('user_id',$design_data['user_id']);
		$existing_design_id = $db->get('user_designs', 1, 'id')[0];
		if($existing_design_id){
			$db->where('id', $existing_design_id['id'])->update('user_designs', ['design'=>$design_data['design'],'dumb'=>$design_data['dumb']]);
		}else{
			$existing_design_id = $db->insert('user_designs', $design_data);
		}
		$myfile = fopen("testfile.txt", "w");
		$t = $design_data['design'];
		fwrite($myfile, $t);
		return $existing_design_id;
	}

	public function deleteDesign($data){
		global $lumise;
		$db = $lumise->get_db();
		$db->where('design_id',$data['design_id']);
		$db->where('user_id',$data['user_id']);
		if($db->delete('user_designs')) return 'successfully deleted';
		return 'Design does not exist : '.$db->getLastQuery();
	}

	public function saved_Designs($user_id){
		global $lumise;
		$db = $lumise->get_db();
		$designs = $db->where('user_id',$user_id)->where("design != ''")->get('user_designs');
		$designs_array = array();
		foreach($designs as $design){
			$designs_array[$design['design_id']] = ['design'=>json_decode($design['design']),
													'dumb'	=>json_decode($design['dumb']) ];
		}
		return $designs_array;
	}

	public function getProduct($product_id, $getFullProduct = null){
		global $lumise;
		$db = $lumise->get_db();
		$product = $db->where('id',$product_id)->get('products',1,null);
		if(is_null($getFullProduct)){
			return $product[0]['lock_movement'];
		}
		return $product[0];
	}

	public function all_products($columns){
		global $lumise;
		$db		 = $lumise->get_db();
		return $db->get('products',null,$columns);		
	}

	public function get_product_details	($product_id){
		global $lumise;
		$data = $lumise->db->rawQuery("SELECT `attributes` FROM `cf_products` WHERE `id`=$product_id");
		return $lumise->lib->dejson($data[0]['attributes'], true);
	}

    public function save_order(){
		
		global $lumise;
		$db = $lumise->get_db();
		$cart_data = $this->get_session('lumise_cart');

		$date = @date ("Y-m-d H:i:s");
		$users_id = null;
		if($lumise->connector->get_session('user_id')){
			$users_id = $lumise->connector->get_session('user_id');
		}
        
        $guest_data = array(
            'name' => $_POST['first_name'] . ' ' . $_POST['last_name'],
            'email' => $_POST['email'],
            'address' => $_POST['address'],
            'zipcode' => $_POST['zipcode'],
            'city' => $_POST['city'],
            'country' => $_POST['country'],
            'phone' => $_POST['phone'],
            'created' => $date,
            'updated' => $date
        );
        if(!isset($_POST['checked']) && isset($_POST['shipping_email'])){
			$guest_data = array(
				'name' => $_POST['shipping_first_name'] . ' ' . $_POST['shipping_last_name'],
				'email' => $_POST['shipping_email'],
				'address' => $_POST['shipping_address'],
				'zipcode' => $_POST['shipping_zipcode'],
				'city' => $_POST['shipping_city'],
				'country' => $_POST['shipping_country'],
				'phone' => $_POST['shipping_phone'],
				'created' => $date,
				'updated' => $date
			);
	        $guest_id_shipping = $db->insert ('guests_shipping', $guest_data);
		}
        $guest_id = $db->insert ('guests', $guest_data);
        
        $order_data = array(
			'total' => isset($cart_data['total']) ? $cart_data['total'] : 0 ,
			'status' => 'pending',
			'currency' => $lumise->cfg->settings['currency'],
			'users_id' => $users_id,		// Id from cf_users table
			'user_id' => $guest_id,			// Id from cf_guests table
			'user_id_shipping' => $guest_id_shipping??null,
            'payment' => $_POST['payment'],
            'txn_id' => '',
			'gst'	 =>	(isset($_POST['gst']) && $_POST['gst'] != '') ? $_POST['gst'] : null,
			'created' => $date,
			'updated' => $date
		);
        
		$order_id = $db->insert ('orders', $order_data);
        
        $order = $this->get_session('lumise_cart');
        
        $order['user'] = $guest_data;
        $order['created'] = $date;
        $order['id'] = $order_id;
        $order['payment'] = $_POST['payment'];
        $order['status'] = 'pending';
        
        $order_data['id'] = $order_id;
        
        $this->set_session('lumise_justcheckout', $order);
        
        $cart_data = $this->get_session('lumise_cart');
        
		$store = $lumise->lib->store_cart($order_id, $cart_data);
        
        if (!$store)
        	return $store;
        	
        $data = array(
            'order_id' => $order_id,
            'order_data' => $order_data,
            'user_data' => $guest_data,
        );
        
		return $data;
		
	}

	public function users($filter, $orderby, $ordering, $limit, $limit_start, $is_admin = null) {
		global $lumise;	
        $db = $lumise->get_db();


		$os = $db->prefix . 'users';
		//$os = $db->prefix . 'users';

		$where = '';

		if (is_array($filter) && isset($filter['keyword']) && !empty($filter['keyword'])) {

            $where = array();
            $fields = explode(',', $filter['fields']);
            $arr_keyword = array();
            for ($i = 0; $i < count($fields); $i++) {
                $arr_keyword[] = sprintf(" %s LIKE '%s' ", $fields[$i], $filter['keyword']);
            }

            $fields = implode(' OR ', $arr_keyword);

            $where[] = $fields;

            if (count($where) > 0)
                $where = (count($where) > 0) ? ' WHERE ' . implode(' AND ', $where) : '';
        }

		if($is_admin){
			$cond = ' where is_admin = "yes" ';
			$where .= $where == '' ? $cond : ' and'.$cond;
		}
		$orderby_str = '';
        if ($orderby != null && $ordering != null)
            $orderby_str = ' ORDER BY ' . $orderby . ' ' . $ordering;

		$sql = "SELECT SQL_CALC_FOUND_ROWS "
		. " os.*, os.id as user_id "
		. " FROM $os as os"
		//. " INNER JOIN $os as os ON os.id = ops.order_id"
		. $where
		. " GROUP BY os.id"
		. $orderby_str
		. " LIMIT $limit_start, $limit";

		$items['rows'] = $db->rawQuery($sql);
		
		$sql = "SELECT FOUND_ROWS() as total";

        $total_count = $db->rawQuery($sql);

        $items['total_count'] = $total_count[0]['total'];

        if($limit != null)
        	$items['total_page'] = ceil($total_count[0]['total'] / $limit) ;
        else $items['total_page'] = 1;
		
        return $items;
	}

    public function toggle_admin(){
//	    die('123');
        if(isset($_REQUEST['action'])){
	        
            global $lumise;
            $id = $_REQUEST['id'];
            
            switch (trim($_REQUEST['action'])) {
                
                case 'toggle_admin':

					global $lumise;
					$admin_status = isset($_POST['remove'])?'no':'yes';
					$db = $lumise->get_db();
					$db->where ('id', $id)->update ('users', array(
						'is_admin' => ($admin_status)
					));
					$status_msg = isset($_POST['remove']) ? 'User is no more an Admin':'User made admin.';
                    $msg		= array(
                        'status' => 'success',
                        'msg' => sprintf($lumise->lang($msg), $id)
                    );
        			$lumise->connector->set_session('lumise_msg', $lumise_msg);
                    $lumise->redirect($lumise->cfg->admin_url.'lumise-page=users');
                    break;
                
                default:
                    break;
            }
        }
    }

	public function orders($filter, $orderby, $ordering, $limit, $limit_start, $user_id = null, $pending = null) {
		global $lumise;
        $db = $lumise->get_db();


		$ops = $db->prefix . 'order_products';
		$os = $db->prefix . 'orders';

		$where = '';

		if($this->get_session('is_admin') == 'yes'){
			$email_parts = explode('@', $this->get_session('user_email'));
			$domain = $email_parts[1];
			$dbu = $lumise->get_db();
			$dbu->where('email', '%'.$domain, 'like');
			$sub_users = $dbu->get('users', null, ['id', 'first_name', 'last_name', 'email', 'is_admin']);
			$sub_users_array=[];
			$ids = '';
			foreach($sub_users as $array){
				$seperator = $ids == ''?'':',';
				$ids .= $seperator .$array['id'];
				$sub_users_array[$array['id']] = $array;
			}
		}else{
			$ids = $user_id;
		}

		if (is_array($filter) && isset($filter['keyword']) && !empty($filter['keyword'])) {

            $where = array();
            $fields = explode(',', $filter['fields']);
            $arr_keyword = array();
            for ($i = 0; $i < count($fields); $i++) {
                $arr_keyword[] = sprintf(" %s LIKE '%s' ", $fields[$i], $filter['keyword']);
            }

            $fields = implode(' OR ', $arr_keyword);

            $where[] = $fields;

            if (count($where) > 0)
                $where = (count($where) > 0) ? ' WHERE ' . implode(' AND ', $where) : '';
			
        }
		$where_user = '';
		if($user_id){
			$where_user = ($where=='')?' where users_id in ('.$ids.')' : ' and users_id = ('.$ids.')' ;
		}
		$where .= $where_user;
		if($pending){
			$where .= 'and status = "pending" ';
		}
		$orderby_str = '';
        if ($orderby != null && $ordering != null)
            $orderby_str = ' ORDER BY ' . $orderby . ' ' . $ordering;

		$sql = "SELECT SQL_CALC_FOUND_ROWS "
		. " os.*, os.id as order_id "
		. " FROM $ops as ops "
		. " INNER JOIN $os as os ON os.id = ops.order_id"
		. $where
		. " GROUP BY ops.order_id "
		. $orderby_str
		. " LIMIT $limit_start, $limit";

		$items['rows'] = $db->rawQuery($sql);
		
		$sql = "SELECT FOUND_ROWS() as total";

        $total_count = $db->rawQuery($sql);

        $items['total_count'] = $total_count[0]['total'];

        if($limit != null)
        	$items['total_page'] = ceil($total_count[0]['total'] / $limit) ;
        else $items['total_page'] = 1;
		if($this->get_session('is_admin') == 'yes'){
			$items['sub_users'] = $sub_users_array;
		}
//		echo '<pre>'; print_r($sub_users); die;
        return $items;
	}
	
	public function redirect($url) {

		if (empty($url))
			return;

		ob_clean();

		@header("location: " . $url);
		exit;

	}

	public function products_order($order_id, $filter, $orderby, $ordering){
		
		global $lumise;
        
        $db = $lumise->get_db();
		
		$items = array('rows' => array());

		$ops = $db->prefix . 'order_products';
		$os = $db->prefix . 'orders';
		$usertb = $db->prefix . 'guests';
		$usertb_shipping =	$usertb.'_shipping';
		$where = array();
		
		$where[] = 'ops.order_id = '. $order_id;

		if (is_array($filter) && isset($filter['keyword']) && !empty($filter['keyword'])) {

			$fields = explode(',', $filter['fields']);
			$arr_keyword = array();
			for ($i = 0; $i < count($fields); $i++) {
				$arr_keyword[] = sprintf(" %s LIKE '%s' ", $fields[$i], $filter['keyword']);
			}

			$fields = '(' . implode(' OR ', $arr_keyword) . ')';

			$where[] = $fields;
				
		}
		

		$orderby_str = '';
		if ($orderby != null && $ordering != null)
			$orderby_str = ' ORDER BY ' . $orderby . ' ' . $ordering;

		$sql = "SELECT "
			. "SQL_CALC_FOUND_ROWS *"
			. " FROM $ops as ops "
			. ' WHERE '. implode(' AND ', $where)
			. ' GROUP BY ops.id '
			. $orderby_str;
			
		$items['rows'] = $db->rawQuery($sql);
		
		$sql = "SELECT FOUND_ROWS() as total";

        $total_count = $db->rawQuery($sql);

        $items['total_count'] = $total_count[0]['total'];
        $items['total_page'] = 1;
		
		//get order data
		$sql = "SELECT "
			. "*"
			. " FROM $os as os "
			. ' WHERE id = '. $order_id;
			
			
		$order = $db->rawQuery($sql);
        $user = array();
        //get order data
        if(isset($order[0]['user_id'])){
            $sql = "SELECT "
    			. "*"
    			. " FROM $usertb as user "
    			. ' WHERE id = '. $order[0]['user_id'];
                
            $user = $db->rawQuery($sql);

		//get shipping details
            $sql_shipping = "SELECT "
    			. "*"
    			. " FROM $usertb_shipping as user "
    			. ' WHERE id = '. $order[0]['user_id'];
                
            $user_shipping = $db->rawQuery($sql_shipping );
        }
		$items['billing'] = (count($user)>0)? $user[0] : array();
        
        if(count($items['billing'])>0){
            $items['billing']['address'] = $items['billing']['address'].
            ', '.
            $items['billing']['city'].
            ', '.
            $items['billing']['country'];
        }

		if(count($user_shipping )>0){
			$items['shipping']['same'] = '';
			$items['shipping'] = $user_shipping [0];
            $items['shipping']['address'] = $items['shipping']['address'].
            ', '.
            $items['shipping']['city'].
            ', '.
            $items['shipping']['country'];
		}else{
			$items['shipping']['same'] = '<h6>Same as Billing</h6>';
			$items['shipping'] = $items['billing'];
		}

		$items['order'] = $order[0];
			
		return $items;
	}
    
    public function update_orders(){
	    
        if(isset($_REQUEST['action'])){
	        
            global $lumise;
            $id = $_REQUEST['id'];
            
            switch (trim($_REQUEST['action'])) {
                
                case 'delete':
                
                    $lumise->lib->delete_row($id, 'orders');
                    $lumise->lib->delete_order_products($id);
                    $lumise_msg = array(
                        'status' => 'success',
                        'msg' => sprintf($lumise->lang('Order #%s deleted.'), $id)
                    );
        			$lumise->connector->set_session('lumise_msg', $lumise_msg);
                    $lumise->redirect($lumise->cfg->admin_url.'lumise-page=orders');
                    break;
                
                default:
                
                    break;
            }
        }
    }
    
    public function update_order_status($order_id){
        if(isset($_POST['order_status'])){
            global $lumise;
            
            $db = $lumise->get_db();
            $db->where ('id', $order_id)->update ('orders', array(
                'status' => $lumise->lib->sql_esc($_POST['order_status']),
                'updated' => @date ("Y-m-d H:i:s")
            ));

			$db->join("orders o", "o.users_id=u.id", "LEFT");
			$db->where("o.id", $order_id);
			$user_email = $db->get("users u", 1, "u.email")[0]['email'];

			$subject = 'Order status updated';
			$message = '
				<div style="font-family: Helvetica,Arial,sans-serif; overflow:auto; line-height:2;">
					<div style="margin:50px auto; width:50%; padding:20px; border: 2px solid #908181; border-radius: 2%;">
						<div style="text-align: center; border-bottom: 1px solid #908181; padding-bottom: 20px;">
							<img style="width: 155px;" src="https://www.colorfast.in/assets/images/logo.png" alt="ColorFast | Delivering Unique Printing Solutions">
						</div>
						<p style="font-size:1.1em">Hi there,</p>
						<p>Status of your order #'.$order_id.' has been changed to <i>'.$_POST['order_status'].'</i>.</p>
						<p style="font-size:0.9em;">Regards,<br />ColorFast Team</p>
						<hr style="border:none;border-top:1px solid #eee" />
					</div>
				</div>';

			$url = parse_url($lumise->cfg->url);
			$headers = 'From: ColorFast <no-reply@'.$url['host'].'>' . "\r\n" .
			'Reply-To: no-reply@'.$url['host'] . "\r\n" .
			'Content-type: text/html; charset=iso-8859-1' . "\r\n".
			'X-Mailer: PHP/' . phpversion();

			mail($user_email, $subject, $message, $headers);
        }
    }

    public function approve_orders($order_ids, $is_admin = null){
        if(isset($_POST['oids']) && $is_admin){
            global $lumise;

			$email_parts = explode('@', $this->get_session('user_email'));
			$domain		= $email_parts[1];
			$dbu		= $lumise->get_db();
			$dbu->where('email', '%'.$domain, 'like');
			$sub_users	= $dbu->get('users', null, ['id']);

			$oids_array = explode(',',$_POST['oids']);
			$uids_array = [];
			foreach($sub_users as $array){
				$uids_array[] = $array['id'];
			}
            $db = $lumise->get_db();
			$db->where('users_id', $uids_array, 'IN');
			$db->where('id', $oids_array, 'IN');
            $db->update ('orders', array(
                'status' => 'approved',
                'updated' => @date ("Y-m-d H:i:s")
            ));

/*			$db->join("orders o", "o.users_id=u.id", "LEFT");
			$db->where("o.id", $order_id);
			$user_email = $db->get("users u", 1, "u.email")[0]['email'];

			$subject = 'Order status updated';
			$message = "<p>Hi there,</p>
			<p></p>
			<p>Status of your order #".$order_id." has been changed to <i>".$_POST['order_status']."</i>.</p>
			<p></p>
			<p>Thank you,</p>
			<p>Team ColorFast</p>";
			$url = parse_url($lumise->cfg->url);
			$headers = 'From: ColorFast <no-reply@'.$url['host'].'>' . "\r\n" .
			'Reply-To: no-reply@'.$url['host'] . "\r\n" .
			'Content-type: text/html; charset=iso-8859-1' . "\r\n".
			'X-Mailer: PHP/' . phpversion();

			mail($user_email, $subject, $message, $headers);*/
        }
    }	    

    public function statuses(){
        return $this->order_statuses;
    }
    
    public function order_status_name($status){
        return isset($this->order_statuses[$status]) ? $this->order_statuses[$status] : $status;
    }
    
	public function update() {
			
		global $lumise;
		
		$lumise_path = dirname(__FILE__);
		$update_path = $lumise->cfg->upload_path.'tmpl'.DS.'lumise';
		$backup_path = $lumise->cfg->upload_path.'update_backup';
		
		$lumise->lib->delete_dir($backup_path);
		
		$connector_content = @file_get_contents($update_path.DS.'php_connector-sample.php');
		
		if (!empty($connector_content)) {
			
			$connector_content = str_replace(array(
				'/home3/apriqnky/public_html/colorfast.in/data/',
				'https://colorfast.in/data/',
				'localhost',
                'apriqnky_colorfast',
                'vkL-QZCoYbK%',
                'apriqnky_colorfast',
                'cf_'
			), array(
				$this->config['upload_path'],
				$this->config['upload_url'],
				$this->config['database']['host'],
				$this->config['database']['user'],
				$this->config['database']['pass'],
				$this->config['database']['name'],
				$this->config['database']['prefix']
			), $connector_content);
			
			@file_put_contents($update_path.DS.'php_connector.php', $connector_content);
			
			/*
			*	Start replace files
			*/
			
			$dir = @opendir($update_path);
			$err = 0;
			
		    while (false !== ($file = @readdir($dir))) {
			    
		        if ($file != '.' && $file != '..') {
			        
			        if (is_dir($update_path.DS.$file)) {
				        
			            if (is_dir($lumise_path.DS.$file))
			            	$lumise->lib->delete_dir($lumise_path.DS.$file);
			            
			            $err += (@rename($update_path.DS.$file, $lumise_path.DS.$file) ? 0 : 1);
			            
			        } else if (is_file($update_path.DS.$file)) {
				        
				    	if (is_file($lumise_path.DS.$file))
				    		$err += (@unlink($lumise_path.DS.$file) ? 0 : 1);
				    	
				    	$err += (@rename($update_path.DS.$file, $lumise_path.DS.$file) ? 0 : 1);
				    	
			        }
		        }
		    }
			
			return $err === 0 ? true : false;
			
		}
		
		return false;
	}

}
