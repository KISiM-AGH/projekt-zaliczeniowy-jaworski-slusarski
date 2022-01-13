<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		(!$this->session->userdata('id')) ? redirect('logowanie') : null;

		$this->load->model('Auth_model', 'auth');

		$data['logs'] = $this->auth->getUserLogs($this->session->userdata('id'));
		$data['last_login'] = $this->auth->getUserLastLogin($this->session->userdata('id'));
		$this->load->view('index_page', $data);
	}

	public function login()
	{
		($this->session->userdata('id')) ? redirect(base_url()) : null;

		$ip = $this->input->ip_address();
		$data['msg'] = null;
		$data['error'] = null;
		$logError = null;

		$this->load->library('form_validation');

		$this->load->helper('captcha');
		$config = array(
            'img_url' => base_url() . 'assets/captcha/',
            'img_path' => 'assets/captcha/',
            'img_height' => 45,
            'word_length' => 6,
            'img_width' => 100,
            'font_size' => 18
        );
        $captcha = create_captcha($config);

		$data['captcha'] = $captcha['image'];

		$form = $this->input->post();
		if(isset($form['next']))
		{
			$this->load->model('Auth_model', 'auth');

			$this->form_validation->set_rules('login', 'Nazwa użytkownika', 'required', array('required'=>'Nazwa użytkownika nie może pozostać pusta.'));
			$this->form_validation->set_rules('pin', 'Kod dostępu', 'required', array('required'=>'Kod dostępu nie może pozostać pusty.'));
			$this->form_validation->set_rules('gacode', 'Kod 2FA', 'required', array('required'=>'Kod 2FA nie może pozostać pusty.'));
			if ($this->form_validation->run() == FALSE)
			{
				$data['error'] = validation_errors();
			}
			else
			{
				if(!$this->auth->userExist($form['login']))
				{
					$data['error'] = 'Podany użytkownik nie istnieje, spróbuj ponownie.';
				}
				else
				{
					$userData = $this->auth->getUserLoginData($form['login']);

					if(!password_verify($form['pin'], $userData[0]->pin))
					{
						$logError = 1;
						$data['error'] = 'Podano błędny kod dostępu, spróbuj ponownie.';
					}
					else
					{
						$timeToBlock = date('Y-m-d H:i:s', strtotime('-15 minutes'));
						$failedAttempts = $this->auth->getCountFailedAttempt($userData[0]->id, $timeToBlock);
						
						if($failedAttempts >= 3)
						{
							$logError = 4;
							$data['error'] = 'W ciągu ostatnich 15 minut wykryto 3 próby błędnego hasła, spróbuj ponownie później.';
						}
						else
						{
							$this->load->library('GoogleAuthenticator');
							$GAobj = new GoogleAuthenticator();
							$secret = $userData[0]->ga_secret;
							$oneCode = $form['gacode'];
									
							$checkResult = $GAobj->verifyCode($secret, $oneCode, 2);
							if (!$checkResult)
							{
								$logError = 2;
								$data['error'] = 'Podano błędny kod 2FA, spróbuj ponownie.';
							}
							else
							{
								if(strtolower($this->session->userdata('captcha')) == strtolower($form['captcha']))
								{
									$this->auth->addLoginLog($userData[0]->id, $ip, 0);

									$this->session->set_userdata('id', $userData[0]->id);
									$this->auth->setLastLogin($userData[0]->id);
									redirect(base_url());
								}
								else
								{
									$logError = 3;
									$data['error'] = 'Kod z obrazka nie zgadza się, spróbuj ponownie.';
								}
							}
						}
					}

					if($logError)
						$this->auth->addLoginLog($userData[0]->id, $ip, $logError);
				}
			}
		}

		$this->session->unset_userdata('captcha');
		$this->session->set_userdata('captcha', $captcha['word']);

		$this->load->view('login_page', $data);
	}

	public function register()
	{
		($this->session->userdata('id')) ? redirect('') : null;

		$data['secret'] = null;
		$data['qrcode'] = null;
		$data['error'] = null;

		$this->load->library('form_validation');

		$form = $this->input->post();
		if(isset($form['register']))
		{
			$this->load->model('Auth_model', 'auth');
			$this->form_validation->set_rules('login', 'Nazwa użytkownika', 'required', array('required'=>'Nazwa użytkownika nie może pozostać pusta.'));
			$this->form_validation->set_rules('pin', 'Kod dostępu', 'required', array('required'=>'Kod dostępu nie może pozostać pusty.'));
			if ($this->form_validation->run() == FALSE)
			{
				$data['error'] = validation_errors();
			}
			else
			{
				if($this->auth->userExist($form['login']))
				{
					$data['error'] = 'Niestety ten login jest już zajęty, spróbuj ponownie.';
				}
				else
				{
					$form['pin'] = password_hash($form['pin'], PASSWORD_DEFAULT);
					$userID = $this->auth->registerUser($form);
					if($userID) {
						$this->load->library('GoogleAuthenticator');
						$GAobj = new GoogleAuthenticator();
						$code = $GAobj->createSecret();
						$QRcode = $GAobj->getQRCodeGoogleUrl('BS_app', $code);
						$this->auth->generateSecret($userID, $code);
						$data['secret'] = $code;
						$data['qrcode'] = $QRcode;
					}
				}

			}
		}

		$this->load->view('register_page', $data);
	}

	public function logout()
	{
		(!$this->session->userdata('id')) ? redirect('admin/logowanie') : null;

		$this->session->unset_userdata('id');
		redirect('logowanie');
	}
}
