<?php

class Admin_AdminpgController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Session::start();        
        
        self::checksession($_SESSION['nmadmin']);
        
    }

    public function checksession($usr)
    {
        if (empty($usr)) {
            $this->_helper->_redirector('', '', 'admin');
        }
    }

    public function indexAction()
    {

    }

    public function updatesoalAction()
    {
        self::checksession($_SESSION['nmadmin']);
        $request = $this->getRequest();

        $this->view->upSoal = [];

        if ($request->getPost('submit') == 'upload') {

            $dirfile    = 'media/';
            
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination($dirfile)
            ->addValidator(
               'Extension',
               false,
               'csv,CSV'
               )
               ->addValidator(
                  'Size',
                  false,
                  array(
                     'min' =>'1b',
                     'max' =>'3MB',
                     'bytestring' => false
                  )
               );

            if(!$upload->isValid())
            {
                $this->view->param = 'Ekstensi file supprt csv , max file size 50MB';
            }
            elseif (!$upload->receive()) {

                $messages = $upload->getMessages();
                $this->view->param = $messages;
            }
            else{
                try {
                        $upload->receive();


                        chmod($dirfile . $_FILES["filesoal"]["name"], 0777);

                        Admin_Model_Aps::truncate('mst_soal');

                        $results = Admin_Model_Aps::proses_file($dirfile . $_FILES["filesoal"]["name"]);
                        // var_dump($results);
                        if($results == "00") {
                            $this->view->param          = $_FILES["filesoal"]["name"] . ", Selesai";

                            $this->view->listKejuruan   = Admin_Model_Aps::lst_kejuruan();
                            $this->view->upSoal         = Admin_Model_Aps::list_soal('All');
                        }
                        else{
                            
                            $this->view->param = $results;
                        }

                } catch (Exception $e) {
                    $this->view->param = $e->getMessage();
                }
            }


            // $this->view->param = 'ok';
        
        }

        if ($request->getPost('submit') == "logout") {
            $this->_helper->_redirector('index', 'index', 'admin');
        }
    }

    public function listsoalAction()
    {
        self::checksession($_SESSION['nmadmin']);
        $request = $this->getRequest();

        $this->view->list_soal      = [];
        $this->view->listKejuruan   = Admin_Model_Aps::lst_kejuruan();        
        $kejuruan                   = $request->getParam('pic_kejuruan');

        if ($request->getPost('submit') == 'listsoal') {

            $this->view->list_soal = Admin_Model_Aps::list_soal($kejuruan);
            $this->view->param = $kejuruan;
        
        }

        if ($request->getPost('submit') == "logout") {
            $this->_helper->_redirector('index', 'index', 'admin');
        }
        
    }
    
    public function updatepassAction()
    {
        self::checksession($_SESSION['nmadmin']);
        $this->_helper->layout->disableLayout();
        
        $request    = $this->getRequest();
        $password   = $this->getParam('passw_lama');
        $password2  = $this->getParam('passw_baru');
        $user       = $_SESSION['nmadmin'];

        if ($request->getPost('submit') == 'update') {
            if (md5($password) == $_SESSION['pasw'] ) {
                try {
                    Admin_Model_Aps::reset_password($user, md5($password2));

                    $rc = '00';

                } catch (Exception $th) {
                    $this->view->notif = '<div class="alert alert-warning" role="alert"><span class="fe fe-alert-triangle fe-16 mr-2"></span> Gagal Update! Hubungi Admin :' . $th->getMessage() . ' </div>';
                }

                if ($rc == '00') {
                    $this->_helper->_redirector('updatesoal', 'index', 'admin'); 
                }

            } else {
                $this->view->notif = '<div class="alert alert-warning" role="alert"><span class="fe fe-alert-triangle fe-16 mr-2"></span> Password Lama Anda Tidak Sesuai! </div>';
            }
            
        }

        if ($request->getPost('submit') == "logout") {
            $this->_helper->_redirector('index', 'index', 'admin');
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
