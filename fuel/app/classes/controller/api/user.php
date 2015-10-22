<?php

class Controller_Api_User extends Controller {

    /**
     * API called when user tries to login, or register using Oauth.
     * @param null $provider
     */
    public function action_authorize($provider = NULL) {
        if (is_null($provider)) {
            \Response::redirect_back();
        }
        $OAuth = new \Craftpip\OAuth\OAuth();
        try {
            $OAuth->init($provider);
            if ($OAuth->OAuth_is_callback) {
                switch ($OAuth->OAuth_state) {
                    case 'logged_in':
                        $url = dash_url;
                        $OAuth->setAttr('verified', TRUE);
                        // was registered and linked, now logged in.
                        break;
                    case 'linked':
                        $url = dash_url . 'settings/services';
                        $OAuth->setAttr('verified', TRUE);
                        // was already registered but linked.
                        break;
                    case 'registered':
                        $OAuth->setAttr('project_limit', 2);
                        $OAuth->setAttr('verified', TRUE);
                        $url = dash_url;
                        // was registered and linked.
                        break;
                }
                \Response::redirect($url);
            }
        } catch (Exception $e) {
            echo \View::forge('errors/generic_error', [
                'message' => 'Maybe your autorization code has Expired, please go back and try again.'
            ]);
        }
    }

    public function action_resendact() {
        try {
            $i = \Input::get();

            $auth = new \Craftpip\OAuth\Auth();
            $user = $auth->getByUsernameEmail($i['email']);
            if (!$user) {
                throw new Exception('');
            }

            $mail = new \Craftpip\Mail($user['id']);
            $mail->template_signup();
            if (!$mail->send()) {
                throw new Exception('');
            }

            $response = array(
                'status' => TRUE
            );
        } catch (Exception $e) {
            $e = new \Craftpip\Exception($e->getMessage(), $e->getCode());
            $response = array(
                'status' => FALSE,
                'reason' => $e->getMessage(),
            );

        }
        echo json_encode($response);

    }

    /**
     * API called when user performs login from homepage.
     */
    public function action_login() {
        try {
            $i = Input::post();
            $auth = new \Craftpip\OAuth\Auth();
            $auth->logout();
            if (\Auth::instance()->check()) {
                $response = array(
                    'status'   => TRUE,
                    'redirect' => dash_url,
                );
            } else {
                $user = $auth->getByUsernameEmail($i['email']);
                if (!$user) {
                    throw new \Craftpip\Exception('The Email or Username is not registered with us.');
                }
                $auth->setId($user['id']);
                $isVerified = $auth->getAttr('verified');
                if (!$isVerified) {
                    throw new Exception('This account is not activated, please head to your Email & activate your Gitftp account.');
                }

                $a = $auth->login($i['email'], $i['password']);
                if ($a) {
                    $response = array(
                        'status'   => TRUE,
                        'redirect' => dash_url,
                    );
                } else {
                    throw new \Craftpip\Exception('The username & password did not match.');
                }
            }
        } catch (Exception $e) {
            $e = new \Craftpip\Exception($e->getMessage(), $e->getCode());
            $response = array(
                'status' => FALSE,
                'reason' => $e->getMessage(),
            );
        }

        echo json_encode($response);
    }

    /**
     * API called from homepage when user submits the registration form.
     */
    public function post_register() {
        try {
            $i = Input::post();
            $validation = \Fuel\Core\Validation::forge();
            $validation->add('email', 'Email')->add_rule('required')->add_rule('valid_email');
            $validation->add('username', 'Username')->add_rule('required')
                ->add_rule('min_length', 6)
                ->add_rule('max_length', 18);
            $validation->add('password', 'Password')->add_rule('required')
                ->add_rule('min_length', 6)
                ->add_rule('max_length', 18);

            if ($validation->run()) {
                $email = $i['email'];
                if (\Utils::isDisposableEmail($email))
                    throw new \Craftpip\Exception("The email id: $email is a disposable Email, please use a Genuine Email-id to continue registration.");

                $auth = new \Craftpip\OAuth\Auth();
                $user_id = $auth->create_user(
                    $i['username'],
                    $i['password'],
                    $i['email'],
                    1,
                    array()
                );
                $auth->setId($user_id);
                $auth->setAttr('verified', FALSE);
                $auth->setAttr('project_limit', 2);


                $mail = new \Craftpip\Mail($user_id);
                $mail->template_signup();
                if (!$mail->send()) {
                    $auth->removeUser($user_id);
                }
            } else {
                throw new \Craftpip\Exception('Something is not right. Please try again');
            }
            $response = array(
                'status' => TRUE
            );
        } catch (Exception $e) {
            $e = new \Craftpip\Exception($e->getMessage(), $e->getCode());
            $response = array(
                'status' => FALSE,
                'reason' => $e->getMessage()
            );
        }

        echo json_encode($response);
    }

    /**
     * API call from dashboard, when user enters old and new password to changes his/hers password.
     */
    public function post_changepassword() {
        try {
            $i = Input::post();

            if (!\Auth::instance()->check()) {
                throw new Exception('Sorry, we got confused. Please try again later.', 123);
            }

            if ($i['newpassword'] !== $i['newpassword2']) {
                throw new Exception('Sorry, the new passwords do not match.', 123);
            }

            $a = \Auth::instance()->change_password($i['oldpassword'], $i['newpassword']);

            if (!$a) {
                throw new Exception('Sorry, the old password is incorrect. Please try again.', 123);
            }

            // todo: count length of password please...

            $response = array(
                'status' => TRUE,
            );
        } catch (Exception $e) {
            $e = new \Craftpip\Exception($e->getMessage(), $e->getCode());
            $response = array(
                'status' => FALSE,
                'reason' => $e->getMessage(),
            );
        }

        echo json_encode($response);
    }

    /**
     * API call from forgot password,
     * when request to send email is done.
     *
     */
    public function post_forgotpassword() {
        try {
            $i = Input::post();

            $auth = new \Craftpip\OAuth\Auth();
            $users = $auth->getByUsernameEmail($i['email']);
            if (!$users) {
                throw new Exception('Email/Username not registered with us.');
            }

            $mail = new \Craftpip\Mail($users['id']);
            $mail->template_forgotpassword();
            $mail->send();

            $response = array(
                'status'  => TRUE,
                'message' => 'asdsa',
            );
        } catch (Exception $e) {
            $response = array(
                'status' => FALSE,
                'reason' => $e->getMessage(),
            );
        }

        echo json_encode($response);
    }

    /**
     * API called from forgot password,
     * When user is redirected from email.
     *
     */
    public function post_forgotpasswordconfirmed() {
        try {
            $i = Input::post();

            $user = new \Craftpip\OAuth\Auth($i['user_id']);
            $key = $user->getAttr('forgotpassword_key');
            if ($key != $i['key']) {
                throw new Exception('Sorry, the token has expired.');
            }

            if (!(strlen($i['password']) > 5 && strlen($i['password']) < 12))
                throw new Exception('Sorry, something went wrong.');

            $user->setPassword($i['password']);
            $user->removeAttr('forgotpassword_key');
            $user->setAttr('verified', TRUE); // because password reset happened via email.
            if ($user->existAttr('verify_key'))
                $user->removeAttr('verify_key');
            $user->force_login($user->user_id);

            $response = array(
                'status'  => TRUE,
                'message' => 'Password has been changed successfully.',
            );
        } catch (Exception $e) {
            $response = array(
                'status' => FALSE,
                'reason' => $e->getMessage(),
            );
        }
        echo json_encode($response);
    }


    public function post_validate() {
        $key = \Input::post('key', NULL);
        $type = \Input::post('type', NULL);
        $auth = new \Craftpip\OAuth\Auth();

        try {
            if (!is_null($key)) {

                if (\Utils::isDisposableEmail($key) && $type == 'email') {
                    throw new \Craftpip\Exception('Please use a Genuine Email id.');
                }

                $user = $auth->getByUsernameEmail($key);
                if ($user) {
                    if ($type == 'email')
                        throw new \Craftpip\Exception('This email address is already registered.<br><a href="' . \Uri::create('forgot-password', [], ['email' => $key]) . '">Reset password</a> or <a href="' . \Uri::create('login', [], ['email' => $key]) . '">Login</a>');
                    elseif ($type == 'username')
                        throw new \Craftpip\Exception('This username is taken');
                } else {
                    $response = array(
                        'status' => FALSE,
                    );
                }
            } else {
                throw new \Craftpip\Exception('Missing parameters');
            }
        } catch (Exception $e) {
            $e = new \Craftpip\Exception($e->getMessage(), $e->getCode());
            $response = array(
                'status' => TRUE,
                'reason' => $e->getMessage()
            );
        }
        echo json_encode($response);
    }

}