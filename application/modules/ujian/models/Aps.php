<?php
	class Ujian_Model_Aps
	{
		
	     // ----------------------------Server Prod ----------------------------------
		function InitDb() 
		{
			$options = array(
					Zend_Db::ALLOW_SERIALIZATION => false
			);

			$params = array(
					'host'           => 'localhost',
					'username'       => 'root',
					'password'       => '150390',
					'dbname'         => 'ppdb',
					'port'		=> '33306',
					'options'        => $options
			);

			$db = Zend_Db::factory('Pdo_Mysql', $params);
			return $db;
		}

		public static function list_soal($kejuruan)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'select * from mst_soal '
				.'where kejuruan= "' . $kejuruan . '" ';
			
			return $db->fetchAll($sql);				

		}

		public static function ins_jwbn($kd_siswa, $kejuruan, $kd_soal, $no_soal, $jawaban)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'call 2022_kirim_jawaban("' . $kd_siswa . '","' . $kejuruan . '","' . $kd_soal . '","' . $no_soal . '","' . $jawaban . '"); ';
			
			$db->query($sql);				

		}

		public static function nilai_jawaban($kdsiswa, $key)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'call 2022_nilai_jawaban("' . $kdsiswa . '", "' . $key . '"); ';
			
			$db->query($sql);				

		}

		public static function generate_topsis_pembagi($kd_siswa, $i)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'call 2022_topsis_pembagi("' . $kd_siswa . '", "' . $i . '"); ';
			
			$db->query($sql);				

		}

		public static function generate_topsis_matr_normalize($kd_siswa, $kejuruan, $i)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'call 2022_topsis_mtrx_normalisasi("'. $kd_siswa .'", "'. $kejuruan .'", "'. $i .'"); ';
			
			$db->query($sql);				

		}

		public static function generate_topsis_matr_bobot($kd_siswa, $kejuruan, $no_soal)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'call 2022_topsis_mtrx_bobot("'. $kd_siswa .'", "' . $kejuruan . '", "' . $no_soal . '"); ';
			
			$db->query($sql);				

		}

		public static function generate_topsis_minmax($kd_siswa)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'call 2022_topsis_minmax("'. $kd_siswa .'"); ';
			
			$db->query($sql);				

		}

		public static function generate_topsis_nilai_d($kd_siswa, $kejuruan)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'call 2022_topsis_nilai_d("'. $kd_siswa .'","'. $kejuruan .'"); ';
			
			$db->query($sql);				

		}

		public static function generate_topsis_ranking($kd_siswa, $kejuruan)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'call 2022_topsis_nilai_ranking("'. $kd_siswa .'","'. $kejuruan .'"); ';
			
			$db->query($sql);				

		}

		public static function get_hasil_test_profil($kd_siswa)
		{
			$db = Ujian_Model_Aps::InitDb();
			
			$sql = 'SELECT MAX(alternatif) AS points 
				FROM ranking_modal WHERE kd_siswa= "'. $kd_siswa .'"';
			
			return $db->fetchAll($sql);
		}

		public static function kirim_hasil_test_profil($kd_siswa, $nilai)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'SELECT kejuruan
				FROM ranking_modal WHERE kd_siswa= "'. $kd_siswa .'" AND alternatif= "'. $nilai .'"';
			
			return $db->fetchAll($sql);
		}

		public static function get_ranking($kd_siswa, $vw)
		{
			$db = Ujian_Model_Aps::InitDb();

			if ($vw == 'jurusan') {
				$sql = 'select kejuruan_rekomendasi as tbl from calon_siswa where kd_siswa= "'. $kd_siswa .'"';
				
			}
			elseif ($vw == 'jurusanpic') {
				$sql = 'select minat_kejuruan as tbl from calon_siswa where kd_siswa= "'. $kd_siswa .'"';
				
			}
			elseif ($vw == 'asl_sekolah') {
				$sql = 'select asal_sekolah as tbl from calon_siswa where kd_siswa= "'. $kd_siswa .'"';
				
			}

			$stmt = $db->query($sql);
			
			while (($row = $stmt->fetch()) != false)
			{
				return $row['tbl'];
			}
			
			
			
		}


		public static function totSoal()
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'select max(no_soal) as tot_soal from mst_bobot; ';
			
			$stmt = $db->query($sql);
			
			while (($row = $stmt->fetch()) != false)
			{
				return $row['tot_soal'];
			}			

		}		

		public static function lst_kejuruan($param)
		{
			$db = Ujian_Model_Aps::InitDb();

			if ('reporting' == $param) {
				$sql = 'SELECT a.kejuruan AS list_jurusan, SUM(a.points) AS jwb_benar, (SUM(a.points) - 6) AS jwb_salah, 
						SUM(a.`points` * b.kriteria) AS total_nilai
						FROM `nilai_points` a
						JOIN `mst_bobot` b
							ON a.`no_soal`=b.`no_soal`
						WHERE a.kd_siswa="' . $_SESSION['kd_casis'] . '" 
						GROUP BY a.kejuruan
					';
			} else {
				$sql = 'select kejuruan from mst_soal; ';
			}
			
			
			return $db->fetchAll($sql);		

		}		

		public static function clean_duplicate($tbls, $kdsiswa)
		{
			$db = Ujian_Model_Aps::InitDb();

			if ($tbls == 'tbl_normalisasi') {

				$sql = 'DELETE e1 FROM `mtrx_normalisasi` e1, mtrx_normalisasi e2 
					WHERE e1.`id` > e2.`id` AND e1.`no_soal` = e2.`no_soal` AND e2.`kd_soal`=e1.`kd_soal` AND e1.`kd_siswa`=e1.`kd_siswa`; ';
			
				$db->query($sql);

			} 
			elseif ($tbls == 'tbl_d') {
				$sql = 'DELETE e1 FROM `nilai_d` e1, nilai_d e2 
					WHERE e1.`id` > e2.`id` AND e1.`d_plus` = e2.`d_plus` AND e2.`kejuruan`=e1.`kejuruan` AND e1.`kd_siswa`=e1.`kd_siswa` and e1.kd_siswa = "' . $kdsiswa . '";';
			
				$db->query($sql);
			}
			elseif ($tbls == 'tbl_ranking') {
				$sql = 'DELETE e1 FROM `ranking_modal` e1, ranking_modal e2 
					WHERE e1.`id` > e2.`id` AND e2.`kejuruan`=e1.`kejuruan` AND e1.`kd_siswa`=e1.`kd_siswa` and e1.kd_siswa = "' . $kdsiswa . '";';
			
				$db->query($sql);
			}
			else {
				$sql = 'DELETE e1 FROM `mtrx_bobot` e1, mtrx_bobot e2 
					WHERE e1.`id` > e2.`id` AND e1.`no_soal` = e2.`no_soal` AND e2.`kd_soal`=e1.`kd_soal` AND e1.`kd_siswa`=e1.`kd_siswa`;';
			
				$db->query($sql);
			}
			
					

		}

	
		
	}
