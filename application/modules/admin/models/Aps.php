<?php
	class Admin_Model_Aps
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

		public static function loginx($email)
		{
			$db = self::InitDb();
			$sql = 'SELECT * FROM admin_web WHERE email= "' . $email . '"';
			
			return $db->fetchAll($sql);				

		}

		public static function lst_kejuruan()
		{
			$db = self::InitDb();
			$sql = 'select distinct kejuruan from mst_soal; ';
			
			return $db->fetchAll($sql);		

		}

		public static function list_soal($kejuruan)
		{
			$db = self::InitDb();

			if ($kejuruan == 'All') {
				$sql = 'select * from mst_soal ';

			} else {
				$sql = 'select * from mst_soal '
					.'where kejuruan= "' . $kejuruan . '" ';
			}		
			
			
			return $db->fetchAll($sql);				

		}

		public static function truncate($table)
		{
			$db = self::InitDb();
			
			$sql = 'truncate ' . $table;		
			
			
			$db->query($sql);				

		}

		public static function reset_password($user, $password)
		{
			$db = self::InitDb();
			
			$sql = 'update admin_web set password= "' . $password . '" ;'
				.'where email= "' . $user . '"';
			
			$db->query($sql);				

		}

		public function proses_file($path_file)
		{
			$fileopen	= fopen($path_file, "r");
			$isifile 	= fread($fileopen, filesize($path_file));
			$params 	= explode("\n",$isifile);

			$i = 0;

			foreach($params as $isifilex) {
				if ($i++ == 0) continue; //skip row
				if (empty($isifilex)) continue; //skip row

				$exp 		= explode('|',$isifilex);

				$kd_soal	= $exp[0];
				$kejuruan	= $exp[1];
				$no_soal	= $exp[2];
				$soal		= $exp[3];
				$jawaban_a	= $exp[4];
				$jawaban_b	= $exp[5];
				$jawaban_c	= $exp[6];
				$jawaban_d	= $exp[7];
				$jawaban_e	= $exp[8];
				$kunci_jwb	= $exp[9];

				try {

					self::insert_data_file($kd_soal, $kejuruan, $no_soal, $soal, $jawaban_a, $jawaban_b, $jawaban_c, $jawaban_d, $jawaban_e, $kunci_jwb);

				} catch (Exception $e) {
					return ": CALL DB ERROR ".$e->getMessage();
				}

			}
			return "00";
		}

		public function insert_data_file(
			$kd_soal, $kejuruan, $no_soal, $soal, $jawaban_a, $jawaban_b, $jawaban_c, $jawaban_d, $jawaban_e, $kunci_jwb
		) {

			$db = self::InitDb();
			
			$text = "insert into mst_soal "
				. "values ('" . $kd_soal . "','" . $kejuruan . "','" . $no_soal . "','" . $soal . "','" . $jawaban_a . "','" . $jawaban_b . "','" . $jawaban_c . "','" . $jawaban_d . "','" . $jawaban_e . "','" . $kunci_jwb . "');
			";

			$db->query($text);
		}

	
		
	}
