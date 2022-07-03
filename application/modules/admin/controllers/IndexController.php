<?php

class Admin_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Session::start();
    }

    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        
        $request    = $this->getRequest();
        $usr        = $this->getParam('email');
        $password   = $this->getParam('password');

        if ($request->getPost('submit') == 'login') {
            try {
                
                $data = Admin_Model_Aps::loginx($usr);

                if (!empty($data)) {
                    foreach($data as $value) {

                        if ($value['password'] == md5($password)) {
                            
                            $_SESSION['nmadmin']    = $value['email'];
                            $_SESSION['pasw']       = $value['password'];

                            $this->_helper->_redirector('updatesoal', 'adminpg', 'admin');             

                        }else {
                            $this->view->notif = '<div class="alert alert-warning" role="alert"><span class="fe fe-alert-triangle fe-16 mr-2"></span> Password Anda Tidak Sesuai! </div>';
                        }
                    }
                } else {
                    $this->view->notif = '<div class="alert alert-warning" role="alert"><span class="fe fe-alert-triangle fe-16 mr-2"></span> Data Anda Tidak Sesuai! </div>';
                }

                

            } catch (Exception $th) {
                $this->view->notif = '<div class="alert alert-warning" role="alert"><span class="fe fe-alert-triangle fe-16 mr-2"></span> Data Anda Tidak Sesuai! ' . $th->getMessage() . '</div>';
            }
        }
    }

    public function logoutAction()
    {
        $this->_helper->layout->disableLayout();
        
        Zend_Session::destroy();
        Zend_Session::forgetMe();
        $this->_helper->_redirector('index', 'index', 'admin');
    }
}
