<?php

class Ujian_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        Zend_Session::start();
        
        self::checksession($_SESSION['nama']);
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
        
        $request    = $this->getRequest();

        if ($request->getPost('submit') == 'lihathasil') {
            try {
                
                // Ujian_Model_Aps::kirim_hasil_test_profil($_SESSION['kd_casis']);
                $rc = '00';

            } catch (Exception $th) {
                echo $th->getMessage();
            }

            if ($rc == '00') {
                $this->_helper->_redirector('reporting', 'index', 'ujian');
            }

        }
    }

    public function mmAction()
    {
        $this->view->list_soal = Ujian_Model_Aps::list_soal('MULTIMEDIA');
    }

    public function tbsmAction()
    {
        $this->view->list_soal = $this->view->list_soal = Ujian_Model_Aps::list_soal('SEPEDA MOTOR');
    }

    public function titlAction()
    {
        $this->view->list_soal = $this->view->list_soal = Ujian_Model_Aps::list_soal('KELISTRIKAN');
    }

    public function tkrAction()
    {
        $this->view->list_soal = $this->view->list_soal = Ujian_Model_Aps::list_soal('OTOMOTIF');
    }

    public function inshasilujianAction()
    {
        $this->_helper->layout->disableLayout();
        
        $request    = $this->getRequest();
        $no_soal    = $request->getParam('nosoal');
        $jawaban    = $request->getParam('jawaban');
        $kejuruan   = $request->getParam('kejuruan');

        if ($kejuruan == 'MULTIMEDIA') {
            $kd_soal = 'MM';
        }
        elseif ($kejuruan == 'SEPEDA MOTOR') {
            $kd_soal = 'TBSM';
        }
        elseif ($kejuruan == 'KELISTRIKAN') {
            $kd_soal = 'TITL';
        }
        elseif ($kejuruan == 'OTOMOTIF') {
            $kd_soal = 'TKR';
        }

        $jawabanf   = $jawaban !== '' ? $jawaban : 'x';
        
        
        try {
            Ujian_Model_Aps::ins_jwbn($_SESSION['kd_casis'], $kejuruan, $kd_soal.$no_soal, $no_soal, $jawabanf);

            $rc = '00';

        } catch (Exception $th) {
            return $this->_helper->json($th->getMessage());
        }

        if ($rc == '00') {
            return $this->_helper->json($rc);
            
        }
        
    }

    public function generatenilaipointsAction()
    {
        $this->_helper->layout->disableLayout();
        
        $request    = $this->getRequest();
        $param    = $request->getParam('param');
        $kejuruan   = $request->getParam('kejuruan');

        if ($param == 'generatepoints') {
            try {
                Ujian_Model_Aps::nilai_jawaban($_SESSION['kd_casis'], $kejuruan);

                $rc = '00';

            } catch (Exception $th) {
                return $this->_helper->json($th->getMessage());
            }

            if ($rc == '00') {
                return $this->_helper->json($rc);
                
            }
           
        }
        
    }

    public function generatenilaitopsisAction()
    {
        $this->_helper->layout->disableLayout();
        
        $request    = $this->getRequest();
        $kd_siswa   = $_SESSION['kd_casis'];
        $param      = $request->getParam('param');

        $tot_soal       = Ujian_Model_Aps::totSoal();
        $lstkejuruan    = Ujian_Model_Aps::lst_kejuruan('');
        
        if ($param == 'generatetopsis') {
            try {

                // Ujian_Model_Aps::truncate();

                for ($i=1; $i <= $tot_soal; $i++) { 
                    Ujian_Model_Aps::generate_topsis_pembagi($kd_siswa, $i);
                }

                foreach ($lstkejuruan as $key) {
                    for ($i=1; $i <= $tot_soal; $i++) { 
                        Ujian_Model_Aps::generate_topsis_matr_normalize($kd_siswa, $key['kejuruan'], $i);
                    }
                }
                // Ujian_Model_Aps::clean_duplicate('tbl_normalisasi');

                foreach ($lstkejuruan as $key) {

                    for ($i=1; $i <= $tot_soal; $i++) { 
                        Ujian_Model_Aps::generate_topsis_matr_bobot($kd_siswa, $key['kejuruan'], $i);
                    }
                }
                // Ujian_Model_Aps::clean_duplicate('tbl_bobot', $kd_siswa);

                Ujian_Model_Aps::generate_topsis_minmax($kd_siswa);

                foreach ($lstkejuruan as $key) {

                    Ujian_Model_Aps::generate_topsis_nilai_d($kd_siswa, $key['kejuruan']);
                }
                // Ujian_Model_Aps::clean_duplicate('tbl_d', $kd_siswa);

                foreach ($lstkejuruan as $key) {

                    Ujian_Model_Aps::generate_topsis_ranking($kd_siswa, $key['kejuruan']);
                }
                // Ujian_Model_Aps::clean_duplicate('tbl_ranking', $kd_siswa);
                

                $rc = '00';

            } catch (Exception $th) {
                return $this->_helper->json($th->getMessage());
            }

            if ($rc == '00') {
                return $this->_helper->json($rc);
            }
        }
        
    }

    public function reportingAction()
    {
        $request = $this->getRequest();

        // $this->view->rekomendasi = Ujian_Model_Aps::get_ranking($_SESSION['kd_casis'], 'jurusan');
        $this->view->rekomendasi = Ujian_Model_Aps::kirim_hasil_test_profil($_SESSION['kd_casis']);
        $this->view->kejuruan_pic= Ujian_Model_Aps::get_ranking($_SESSION['kd_casis'], 'jurusanpic');
        $this->view->reporting   = Ujian_Model_Aps::lst_kejuruan('reporting');

        if ($request->getPost('submit') == 'next') {
            $this->_helper->_redirector('biodataprint','index','ujian');
        }
    }

    public function biodataprintAction()
    {
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();

        // $rekomendasi_jurusan    = Ujian_Model_Aps::get_ranking($_SESSION['kd_casis'], 'jurusan');
        $rekomendasi_jurusan    = Ujian_Model_Aps::kirim_hasil_test_profil($_SESSION['kd_casis']);
        $pic_kejuruan           = Ujian_Model_Aps::get_ranking($_SESSION['kd_casis'], 'jurusanpic');

        $this->view->nama_peserta = $_SESSION['nama'];
        $this->view->asal_sekolah = Ujian_Model_Aps::get_ranking($_SESSION['kd_casis'], 'asl_sekolah');
        $this->view->recomend_jur = $rekomendasi_jurusan;
        $this->view->kejuruan_pic = $pic_kejuruan;

        // var_dump($pic_kejuruan);

        

        /* param utk link download file */
        $param = array(
            'namafile' => base64_encode("printout"),
            'rekomendasi' => base64_encode($rekomendasi_jurusan),
            'jurusan_pic' => base64_encode($pic_kejuruan),
            'kd_casis'  => base64_encode($_SESSION['kd_casis']),
            'asalsekolah' => base64_encode($_SESSION['asalsekolah']),
            'nama'  => base64_encode($_SESSION['nama'])
        );
        $this->view->filename = $param;


        if ($request->getPost('submit') == "logout") {
            $this->_helper->_redirector('logout','index','default');
        }
    }

    public function generatehasilAction()
    {
        $this->_helper->layout()->disableLayout();

        $request = $this->getRequest();

        $params = json_decode($this->getRequest()->getParam('params'));

        $this->view->namafile       = base64_decode($params->namafile);
        $this->view->rekomendasi    = base64_decode($params->rekomendasi);
        $this->view->jurusan_pic    = base64_decode($params->jurusan_pic);
        $this->view->kd_casis       = base64_decode($params->kd_casis);
        $this->view->asalsekolah    = base64_decode($params->asalsekolah);
        $this->view->nama           = base64_decode($params->nama);

        $this->view->outname        = "REKOMENDASICERTIFICATE.doc";
    }

    public function logoutAction()
    {
        $this->_helper->layout->disableLayout();
        
        Zend_Session::destroy();
        Zend_Session::forgetMe();
        $this->_helper->_redirector('', '', '');
    }
}
