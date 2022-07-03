<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Session::start();
    }

    public function checksession($usr)
    {
        if (empty($usr)) {
            $this->_helper->_redirector('', '', '');
        }
    }

    public function indexAction()
    {
        $this->_helper->layout->disableLayout();

        $request = $this->getRequest();
        if ($request->getPost('daftar') == "casis") {
            $this->_helper->_redirector('form','index','default');
        }


    }
    public function formAction()
    {
        $this->_helper->layout->disableLayout();
        $request        = $this->getRequest();
        $date           = date('his');

        $nama           = $this->getParam('nama');
        $email          = $this->getParam('email');
        $telp           = $this->getParam('telp');
        $alamat        = $this->getParam('address');
        $ttl            = $this->getParam('ttl');
        $asalsekolah    = $this->getParam('asalsekolah');
        $fb             = $this->getParam('fb');
        $ig             = $this->getParam('ig');
        $minat          = $this->getParam('minat');
        $prestasi       = $this->getParam('prestasi');
        $nmayah         = $this->getParam('nmayah');
        $nmibu          = $this->getParam('nmibu');
        $anakke         = $this->getParam('anakke');
        $statuskel      = $this->getParam('statusanak');
        $pic_kejuruan   = $this->getParam('pic_kejuruan');


        if ($request->getPost('submit') == "daftar") {
            
            $kd_siswa = substr(md5($date.'001'),1,4);
            
            
            try {
                Default_Model_Aps::insert_casis($kd_siswa, $nama, $ttl, $asalsekolah, $alamat, $email, $ig, $fb, $minat, $prestasi, $telp, $nmayah, $nmibu, $anakke, $statuskel, $pic_kejuruan);
                
                $rc = '00';

            } catch (Exception $th) {
                echo $th->getMessage();
            }

            if ($rc == '00') {
                $_SESSION['nama']           = $nama;
                $_SESSION['asalsekolah']    = $asalsekolah;
                $_SESSION['kd_casis']       = $kd_siswa;
                $_SESSION['pickejuruan']    = $pic_kejuruan;

                
                $this->_helper->_redirector('mm','index','ujian');
            }
            
        }
        
        if ($request->getPost('submit') == "logout") {
            $this->_helper->_redirector('logout','index','default');
        }
    }

     public function logoutAction()
     {
          $this->_helper->layout->disableLayout();
          
          Zend_Session::destroy();
          Zend_Session::forgetMe();
          $this->_helper->_redirector('', '', '');
     }
}
