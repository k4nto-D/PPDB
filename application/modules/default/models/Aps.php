<?php
	class Default_Model_Aps
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

		public static function insert_casis($kd_siswa, $nama, $ttl, $asalsekolah, $alamat, $email, $ig, $fb, $minat, $prestasi, $telp, $nmayah, $nmibu, $anakke, $statuskel, $pic_kejuruan)
		{
			$db = self::InitDb();
			$sql = 'insert into calon_siswa '
				.'values("'. $kd_siswa .'", "'. $nama .'", "'. $ttl .'", "'. $asalsekolah .'", "'. $alamat .'", "'. $email .'","' . $ig .'","' . $fb .'", "'. $minat .'", "'. $prestasi .'", "'. $telp .'", "'. $nmayah .'", "'. $nmibu .'", "'. $anakke .'", "'. $statuskel .'", "'. $pic_kejuruan .'",00.00,""); ';
			
			$db->query($sql);	
		}

		public static function ins_jwbn($kd_siswa, $kejuruan, $kd_soal, $no_soal, $jawaban)
		{
			$db = Ujian_Model_Aps::InitDb();
			$sql = 'call 2022_kirim_jawaban("' . $kd_siswa . '","' . $kejuruan . '","' . $kd_soal . '","' . $no_soal . '","' . $jawaban . '"); ';
			
			$db->query($sql);				

		}
		
	}
