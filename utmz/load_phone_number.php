<?php
	/*
	Главная функция, которую необходимо вызывать для определения номера (находится в самом конце этого скрипта):
	get_number_utmz
	Пример вызова:
		регион задавать только при необходимости
		$region = '';
		$number = get_number_utmz($region);
	*/

	error_reporting(0);
	
	/*
	Функция spider_detect_utmz - принимает $_SERVER['HTTP_USER_AGENT'] и возвращает имя кравлера поисковой системы или FALSE.
	*/

	function spider_detect_utmz($USER_AGENT)
	{
		$engines = array(
			array('Aport', 'Aport robot'),
			array('Google', 'Google'),
			array('msnbot', 'MSN'),
			array('Rambler', 'Rambler'),
			array('Yahoo', 'Yahoo'),
			array('AbachoBOT', 'AbachoBOT'),
			array('accoona', 'Accoona'),
			array('AcoiRobot', 'AcoiRobot'),
			array('ASPSeek', 'ASPSeek'),
			array('CrocCrawler', 'CrocCrawler'),
			array('Dumbot', 'Dumbot'),
			array('FAST-WebCrawler', 'FAST-WebCrawler'),
			array('GeonaBot', 'GeonaBot'),
			array('Gigabot', 'Gigabot'),
			array('Lycos', 'Lycos spider'),
			array('MSRBOT', 'MSRBOT'),
			array('Scooter', 'Altavista robot'),
			array('AltaVista', 'Altavista robot'),
			array('WebAlta', 'WebAlta'),
			array('IDBot', 'ID-Search Bot'),
			array('eStyle', 'eStyle Bot'),
			array('Mail.Ru', 'Mail.Ru Bot'),
			array('Scrubby', 'Scrubby robot'),
			array('Yandex', 'Yandex'),
			array('YaDirectBot', 'Yandex Direct'),
			array('Exabot', 'Exabot'),
			array('bingbot', 'bingbot')
		);
		
		foreach ($engines as $engine)
		{
			if (strstr($USER_AGENT, $engine[0]))
			{
				return $engine[1];
			}
		}

		return FALSE;
	}

	function get_remote_addr_utmz()
	{
        $serverVariables = array(
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_X_COMING_FROM',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_COMING_FROM',
            'HTTP_CLIENT_IP',
            'HTTP_FROM',
            'HTTP_VIA',
            'REMOTE_ADDR'
        );
        $value = '';
		foreach ($serverVariables as $serverVariable)
		{
            if (isset($_SERVER[$serverVariable]))
			{
				$value = $_SERVER[$serverVariable];
				break;

            }
			elseif (getenv($serverVariable))
			{
				$value = getenv($serverVariable);
            }
		}
		return $value;
	}

	function get_region_utmz($file_path, $ip)
	{
		$region = '(none)';
		if (file_exists($file_path))
		{
			$is_found = FALSE;
			$handle = fopen($file_path, 'r');
			if (!setlocale(LC_ALL, 'ru_RU.utf8'))
			{
				setlocale(LC_ALL, 'en_US.utf8');
			}
			while (($data = fgetcsv($handle, 1000, ';', '"')) !== FALSE)
			{
				$num = count($data);
				if ($num!=2)
				{
					continue;
				}
				$ip2long = ip2long($ip);
				$ip_range = $data[0];
				$ip_range = explode('-', $ip_range);
				$cnt = count($ip_range);
				switch ($cnt)
				{
					case '1':
						if ($ip==$ip_range[0])
						{
							$is_found = TRUE;
						}
						break;
					case '2':
						if (($ip2long>=ip2long($ip_range[0]))&&($ip2long<=ip2long($ip_range[1])))
						{
							$is_found = TRUE;
						}
						break;
				}
				$region_name = $data[1];
				if ($is_found)
				{
					$region = $region_name;
					break;
				}
			}
			fclose($handle);
		}
		return $region;
	} // get_region_utmz
	
	function load_se_utmz($file_path)
	{
		$se_mass = array();
		if (file_exists($file_path))
		{
			$handle = fopen($file_path, 'r');
			if (!setlocale(LC_ALL, 'ru_RU.utf8'))
			{
				setlocale(LC_ALL, 'en_US.utf8');
			}
			while (($data = fgetcsv($handle, 1000, ';', '"')) !== FALSE)
			{
				$num = count($data);
				if ($num!=2)
				{
					continue;
				}
				$se_mass[$data[0]][] = $data[1];
			}
			fclose($handle);
		}
		return $se_mass;
	} // load_se_utmz
	
	function get_number_from_referer_utmz($referer, &$utm_source, &$utm_medium, &$utm_campaign, &$utm_term, $search_engines_file_path)
	{
		$r = parse_url($referer);
		$r_host = (isset($r['host']))?$r['host']:'';
		$r_query = (isset($r['query']))?$r['query']:'';
		$r_query = str_replace('&amp;', '&', $r_query);
		// берем соответствующие параметры из гет-запроса
		if (isset($_GET['utm_source'])&&($_GET['utm_source']!=''))
		{
			$utm_source = $_GET['utm_source'];
		}
		if (isset($_GET['utm_medium'])&&($_GET['utm_medium']!=''))
		{
			$utm_medium = $_GET['utm_medium'];
		}
		if (isset($_GET['utm_campaign'])&&($_GET['utm_campaign']!=''))
		{
			$utm_campaign = $_GET['utm_campaign'];
		}
		if (isset($_GET['utm_term'])&&($_GET['utm_term']!=''))
		{
			$utm_term = $_GET['utm_term'];
		}
		// проверка на наличие gclid
		if ((!isset($_GET['utm_source']))&&(!isset($_GET['utm_medium']))&&(!isset($_GET['utm_campaign']))&&(!isset($_GET['utm_term'])))
		{
			if (isset($_GET['gclid']))
			{
				$utm_source = 'google';
				$utm_medium = 'cpc';
				$qr_mass = explode('&', $r_query);
				foreach ($qr_mass as $v)
				{
					$tv = explode('=', $v);
					if (count($tv)!=2)
					{
						continue;
					}
					if (($tv[0]=='utm_term')&&($tv[1]!=''))
					{
						$utm_term = $tv[1];
						break;
					}
				}
			}
			else
			{
				if ($r_host!='')
				{
					$utm_source = $r_host;
					$utm_campaign = $r_host;
				}
			}
		}
		// проверка трафика из поиска
		if ($r_host!='')
		{
			$se_mass = load_se_utmz($search_engines_file_path);
			$l_found = FALSE;
			$se_mass_item = array();
			foreach ($se_mass as $k => $v):
				if (strpos($r_host, $k)!==FALSE):
					$l_found = TRUE;
					$se_mass_item = $v;
					break;
				endif;
			endforeach;
			if ($l_found&&($r_query!=''))
			{
				$q_mass = explode('&', $r_query);
				$rq_mass = array();
				foreach ($q_mass as $v)
				{
					$tv = explode('=', $v);
					if (count($tv)!=2)
					{
						continue;
					}
					$rq_mass[$tv[0]] = $tv[1];
				}
				$r_found = FALSE;
				$ut = '';
				foreach ($se_mass_item as $qv)
				{
					if (isset($rq_mass[$qv]))
					{
						$r_found = TRUE;
						$ut = $rq_mass[$qv];
						if ($ut=='')
						{
							$ut = '(not provided)';
						}
						break;
					}
				}
				if ((!isset($_GET['utm_medium']))&&(!isset($_GET['gclid'])))
				{
					if ($r_found)
					{
						$utm_medium = 'organic';
					}
					else
					{
						$utm_medium = 'referral';
					}
				}
				if ($r_found)
				{
					$utm_term = $ut;
				}
				else
				{
					$utm_term = '(none)';
				}
			}
			elseif (isset($r['scheme'])&&($r['scheme']=='https')&&(strpos($r_host, '.google.')!==FALSE))
			{
				$utm_source = '(none)';
				$utm_medium = 'organic';
				$utm_campaign = '(none)';
				$utm_term = '(not provided)';
			}
		}
	} // get_number_from_referer_utmz
	
	function check_vm_item_utmz(&$is_add, $vm, $search_value, $need_decode)
	{
		if (($vm[0]=='[')&&(substr($vm, -1)==']'))
		{
			$vm = str_replace('[', '', $vm);
			$vm = str_replace(']', '', $vm);
			if ($need_decode)
			{
				$vm = urldecode($vm);
			}
			$vm = mb_strtolower($vm, 'utf-8');
			if ($search_value==$vm)
			{
				$is_add = TRUE;
				return;
			}
		}
		elseif (($vm[0]=='"')&&(substr($vm, -1)=='"'))
		{
			$vm = str_replace('"', '', $vm);
			if ($need_decode)
			{
				$vm = urldecode($vm);
			}
			$vm = mb_strtolower($vm, 'utf-8');
			$search_pattern_middle = '/^.*[^\w]'.$vm.'[^\w].*$/';
			$search_pattern_start = '/^'.$vm.'[^\w].*$/';
			$search_pattern_end = '/^.*[^\w]'.$vm.'$/';
			if ((preg_match($search_pattern_middle, $search_value)>0)||(preg_match($search_pattern_start, $search_value)>0)||(preg_match($search_pattern_end, $search_value)>0))
			{
				$is_add = TRUE;
				return;
			}
		}
		else
		{
			if ($need_decode)
			{
				$vm = urldecode($vm);
			}
			$vm = mb_strtolower($vm, 'utf-8');
			if ($vm=='(none)')
			{
				$is_add = TRUE;
				return;
			}
			if (mb_strpos($search_value, $vm, 0, 'utf-8')!==FALSE)
			{
				$is_add = TRUE;
				return;
			}
		}
	} // check_vm_item_utmz
	
	function filter_numbers_utmz($f_mass, $search_value, $search_name, $need_decode = TRUE)
	{
		$res_mass = array();
		foreach ($f_mass as $v)
		{
			$is_add = FALSE;
			if (($v[$search_name][0]=='{')&&(substr($v[$search_name], -1)=='}'))
			{
				$new_v = str_replace('{', '', $v[$search_name]);
				$new_v = str_replace('}', '', $new_v);
				$v_mass = explode('|', $new_v);
				foreach ($v_mass as $vm)
				{
					check_vm_item_utmz($is_add, $vm, $search_value, $need_decode);
					if ($is_add)
					{
						break;
					}
				}
			}
			else
			{
				check_vm_item_utmz($is_add, $v[$search_name], $search_value, $need_decode);
			}
			if ($is_add)
			{
				$res_mass[] = $v;
			}
		}
		return $res_mass;
	} // filter_numbers_utmz
	
	function filter_numbers_if_set_utmz($f_mass, $search_name)
	{
		$res_mass = array();
		foreach ($f_mass as $v)
		{
			if ($v[$search_name]!='(none)')
			{
				$res_mass[] = $v;
			}
		}
		return $res_mass;
	} // filter_numbers_if_set_utmz
	
	function calc_none_utmz(&$f_mass, &$min_count)
	{
		$min_n = -1;
		foreach ($f_mass as $k => $v)
		{
			$n_cnt = 0;
			if ($v['utm_source']=='(none)')
			{
				$n_cnt++;
			}
			if ($v['utm_medium']=='(none)')
			{
				$n_cnt++;
			}
			if ($v['utm_campaign']=='(none)')
			{
				$n_cnt++;
			}
			if ($v['utm_term']=='(none)')
			{
				$n_cnt++;
			}
			$f_mass[$k]['n_cnt'] = $n_cnt;
			if ($min_n==-1)
			{
				$min_n = $n_cnt;
			}
			if ($n_cnt<$min_n)
			{
				$min_n = $n_cnt;
			}
		}
		$min_count = 0;
		$new_mass = array();
		foreach ($f_mass as $k => $v)
		{
			if ($v['n_cnt']==$min_n)
			{
				$min_count++;
				$new_mass[] = $v;
			}
		}
		$f_mass = $new_mass;
		return $min_n;
	} // calc_none_utmz
	
	function choose_number_from_file_utmz($file_path, $utm_source, $utm_medium, $utm_campaign, $utm_term, $region)
	{
		$result_number = '';
		$utm_source = urldecode($utm_source);
		$utm_source = mb_strtolower($utm_source, 'utf-8');
		$utm_medium = urldecode($utm_medium);
		$utm_medium = mb_strtolower($utm_medium, 'utf-8');
		$utm_campaign = urldecode($utm_campaign);
		$utm_campaign = mb_strtolower($utm_campaign, 'utf-8');
		$utm_term = urldecode($utm_term);
		$utm_term = mb_strtolower($utm_term, 'utf-8');
		$region = mb_strtolower($region, 'utf-8');
		if (file_exists($file_path))
		{
			$f_numbers_mass = array();
			$handle = fopen($file_path, 'r');
			if (!setlocale(LC_ALL, 'ru_RU.utf8'))
			{
				setlocale(LC_ALL, 'en_US.utf8');
			}
			while (($data = fgetcsv($handle, 1000, ';', '"')) !== FALSE)
			{
				$num = count($data);
				if ($num!=6)
				{
					continue;
				}
				if (($data[0]=='')||($data[1]=='')||($data[2]=='')||($data[3]=='')||($data[4]=='')||($data[5]==''))
				{
					continue;
				}
				$f_numbers_mass[] = array(
					'utm_source' => $data[0],
					'utm_medium' => $data[1],
					'utm_campaign' => $data[2],
					'utm_term' => $data[3],
					'region' => $data[4],
					'number' => $data[5]
				);
			}
			fclose($handle);
			$f_cnt = count($f_numbers_mass);
			if ($f_cnt>0)
			{
				$f_mass_1 = filter_numbers_utmz($f_numbers_mass, $utm_source, 'utm_source', TRUE);
				$f_mass_2 = filter_numbers_utmz($f_mass_1, $utm_medium, 'utm_medium', TRUE);
				$f_mass_3 = filter_numbers_utmz($f_mass_2, $utm_campaign, 'utm_campaign', TRUE);
				$f_mass_4 = filter_numbers_utmz($f_mass_3, $utm_term, 'utm_term', TRUE);
				$f_mass_5 = filter_numbers_utmz($f_mass_4, $region, 'region', FALSE);
				$min_count = 0;
				$min_n = calc_none_utmz($f_mass_5, $min_count);
				if ($min_count==1)
				{
					$result_number = $f_mass_5[0]['number'];
				}
				elseif ($min_count>1)
				{
					$f_mass_6 = filter_numbers_if_set_utmz($f_mass_5, 'utm_medium'); // 2
					if (count($f_mass_6)>1)
					{
						$f_mass_6 = filter_numbers_if_set_utmz($f_mass_6, 'utm_source'); // 1
						if (count($f_mass_6)>1)
						{
							$f_mass_6 = filter_numbers_if_set_utmz($f_mass_6, 'utm_term'); // 4
							if (count($f_mass_6)>1)
							{
								$f_mass_6 = filter_numbers_if_set_utmz($f_mass_6, 'utm_campaign'); // 3
								if (count($f_mass_6)>1)
								{
									$f_mass_6 = filter_numbers_if_set_utmz($f_mass_6, 'region'); // region
								}
							}
						}
					}
					if (count($f_mass_6)>0)
					{
						$result_number = $f_mass_6[0]['number'];
					}
					else
					{
						$result_number = $f_mass_5[0]['number'];
					}
				}
			}
		}
		return $result_number;
	} // choose_number_from_file_utmz
	
	function get_connection_utmz($file_path, &$project_name, &$project_host, &$user_name)
	{
		$pn = '';
		$ph = '';
		$un = '';
		if (file_exists($file_path))
		{
			$handle = fopen($file_path, 'r');
			if (!setlocale(LC_ALL, 'ru_RU.utf8'))
			{
				setlocale(LC_ALL, 'en_US.utf8');
			}
			while (($data = fgetcsv($handle, 1000, ';', '"')) !== FALSE)
			{
				$num = count($data);
				if ($num!=3)
				{
					continue;
				}
				$data[0] = str_replace('project_name:', '', $data[0]);
				$data[1] = str_replace('project_host:', '', $data[1]);
				$data[2] = str_replace('user_name:', '', $data[2]);
				if ($data[0]!='')
				{
					$pn = $data[0];
				}
				if ($data[1]!='')
				{
					$ph = $data[1];
				}
				if ($data[2]!='')
				{
					$un = $data[2];
				}
			}
			fclose($handle);
		}
		if ($pn!='')
		{
			$project_name = $pn;
		}
		if ($ph!='')
		{
			$project_host = $ph;
		}
		if ($un!='')
		{
			$user_name = $un;
		}
	} // get_connection_utmz
	
	function mkdir_utmz($dir_path)
	{
		clearstatcache();
		if (!is_dir($dir_path))
		{
			mkdir($dir_path, 0777);
		}
	} // mkdir_utmz
	
	function write_to_log_utmz($clear_logs_time, $logs_dir_path, $utmz_file_name, $utm_source, $utm_medium, $utm_campaign, $utm_term, $region, $result_number, $remote_addr)
	{
		// чистим устаревшие логи (которые старше чем $clear_logs_time дней)
		$cl_time = mktime(0, 0, 0, intval(date('m')), intval(date('d'))-$clear_logs_time);
		$cl_date = date('Y:m:d', $cl_time);
		$ymd_mass = explode(':', $cl_date);
		$f_path = $logs_dir_path.'/'.implode('/', $ymd_mass).'/'.$utmz_file_name;
		if (file_exists($f_path))
		{
			unlink($f_path);
		}

		mkdir_utmz($logs_dir_path);
		$now_time = time();
		$ymd = date('Y:m:d', $now_time);
		$ymd_mass = explode(':', $ymd);
		$year = $ymd_mass[0];
		$month = $ymd_mass[1];
		$day = $ymd_mass[2];
		$logs_dir_path .= '/'.$year;
		mkdir_utmz($logs_dir_path);
		$logs_dir_path .= '/'.$month;
		mkdir_utmz($logs_dir_path);
		$logs_dir_path .= '/'.$day;
		mkdir_utmz($logs_dir_path);
		$logs_dir_path .= '/'.$utmz_file_name;
		$handle = fopen($logs_dir_path, "a+");
		if ($handle)
		{
			$str = '"'.$now_time.'"'.';'.'"'.$utm_source.'"'.';'.'"'.$utm_medium.'"'.';'.'"'.$utm_campaign.'"'.';'.'"'.$utm_term.'"'.';'.'"'.$region.'"'.';'.'"'.(($result_number!='')?$result_number:'(none)').'"'.';'.'"'.base64_encode($_SERVER['REQUEST_URI']).'"'.';'.'"'.$remote_addr.'"'."\n";
			fwrite($handle, $str);
			fclose($handle);
		}
	} // write_to_log_utmz
	
	/*
	Главная функция, которую необходимо вызывать для определения номера
	Пример вызова:
		регион задавать только при необходимости
		$region = '';
		$number = get_number_utmz($region);
	*/
	function get_number_utmz($region = '')
	{
		$current_locale = setlocale(LC_ALL, NULL);
		$update_time_n = 3600; // через 1 час обновлять файл с номерами
		$update_time_se = 86400; // через 24 часа обновлять файл с поисковыми системами
		$update_time_r = 86400; // через 24 часа обновлять файл с регионами
		$clear_logs_time = 10; // кол-во дней, через которое будут чиститься файлы логов
		$r_ip = '176.9.24.184'; // IP удаленного сервера
		$search_engines_file_path = realpath(dirname(__FILE__)).'/utmz_search_engines.txt';
		$regions_file_path = realpath(dirname(__FILE__)).'/utmz_regions.txt';
		$numbers_file_path = realpath(dirname(__FILE__)).'/utmz_numbers.txt';
		$connection_file_path = realpath(dirname(__FILE__)).'/utmz_connection.txt';
		$logs_dir_path = dirname(__FILE__).'/utmz_logs';
		$utmz_file_name = 'utmz_log.txt';
		
		$user_agent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
		$detect = spider_detect_utmz($user_agent);
		
		if ($detect!==FALSE)
		{
			return '';
		}

		$remote_addr = get_remote_addr_utmz();
		
		// значения по умолчанию (дальше получаем из файла)
		// название проекта в системе
		$project_name = $_SERVER['HTTP_HOST'];
		// домен проекта в системе
		$project_host = $_SERVER['HTTP_HOST'];
		// название компании проекта
		$user_name = $_SERVER['HTTP_HOST'];
		
		$result_number = '';
		
		// определение параметров подключения к удаленному серверу (получение значений project_name, project_host, user_name)
		get_connection_utmz($connection_file_path, $project_name, $project_host, $user_name);
		
		// определение региона пользователя, если регион не задан
		if ($region=='')
		{
			// определение необходимости обновления файла с регионами
			$update_need = FALSE;
			if (!file_exists($regions_file_path))
			{
				$update_need = TRUE;
			}
			else
			{
				clearstatcache();
				$mtime = filemtime($regions_file_path);
				$new_mtime = time() - $update_time_r;
				if (($mtime>0)&&($new_mtime)&&($mtime<=$new_mtime))
				{
					$update_need = TRUE;
				}
			}
			// обновление файла с регионами
			if ($update_need)
			{
				$query = array(
					'project_name' => $project_name,
					'project_host' => $project_host,
					'user_name' => $user_name
				);
				$q_url = 'http://'.$r_ip.'/query_regions_list.php?'.http_build_query($query, '', '&');
				$r_list = file_get_contents($q_url);
				if ($r_list!='')
				{
					if ($r_list=='none')
					{
						$r_list = '';
					}
					file_put_contents($regions_file_path, $r_list);
				}
			}
			$region = get_region_utmz($regions_file_path, $remote_addr);
		}
		
		$utm_source = '(none)';
		$utm_medium = '(none)';
		$utm_campaign = '(none)';
		$utm_term = '(none)';
		
		$utmz_cookie = (isset($_COOKIE['__utmz']))?$_COOKIE['__utmz']:'';
		$referer = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:'';
		
		// определение необходимости обновления файла с поисковыми системами
		$update_need = FALSE;
		if (!file_exists($search_engines_file_path))
		{
			$update_need = TRUE;
		}
		else
		{
			clearstatcache();
			$mtime = filemtime($search_engines_file_path);
			$new_mtime = time() - $update_time_se;
			if (($mtime>0)&&($new_mtime)&&($mtime<=$new_mtime))
			{
				$update_need = TRUE;
			}
		}
		// обновление файла с поисковыми системами
		if ($update_need)
		{
			$q_url = 'http://'.$r_ip.'/query_search_engines_list.php';
			$se_list = file_get_contents($q_url);
			if ($se_list!='')
			{
				if ($se_list=='none')
				{
					$se_list = '';
				}
				file_put_contents($search_engines_file_path, $se_list);
			}
		}
		
		// начало определения параметров utm*
		// задана UTMZ?
		if ($utmz_cookie!='') // Да
		{
			// Реферер равен текущему домену или отсутствует?
			$is_r = FALSE;
			if ($referer!='')
			{
				$r = parse_url($referer);
				if (is_array($r)&&(isset($r['host']))&&($r['host']==$_SERVER['HTTP_HOST']))
				{
					$is_r = TRUE;
				}
			}
			else
			{
				$is_r = TRUE;
			}
			if ($is_r) // Да
			{
				// получаем значения из UTMZ
				// парсинг значения cookie GA (__utmz) для получения целевых значений $utm_source, $utm_medium, $utm_campaign, $utm_term
				$start_pos = strpos($utmz_cookie, 'utmcsr=');
				if ($start_pos!==FALSE):
					$__utmz = substr($utmz_cookie, $start_pos, strlen($utmz_cookie) - $start_pos);
				endif;
				$__utmz = explode('|', $__utmz);
				foreach ($__utmz as $value):
					$property = explode('=', $value);
					switch ($property[0]):
						case 'utmcsr':
							$utm_source = $property[1];
							break;
						case 'utmcmd':
							$utm_medium = $property[1];
							break;
						case 'utmccn':
							$utm_campaign = $property[1];
							break;
						case 'utmctr':
							$utm_term = $property[1];
							break;
					endswitch;
				endforeach;
				// utmgclid задан?
				if (strpos($utmz_cookie, 'utmgclid=')!==FALSE) // Да
				{
					$utm_source = 'google';
					$utm_medium = 'cpc';
					$utm_campaign = 'auto';
				}
			}
			else // Нет
			{
				get_number_from_referer_utmz($referer, $utm_source, $utm_medium, $utm_campaign, $utm_term, $search_engines_file_path);
			}
		}
		else // Нет
		{
			// Реферер есть?
			if ($referer!='') // Да
			{
				get_number_from_referer_utmz($referer, $utm_source, $utm_medium, $utm_campaign, $utm_term, $search_engines_file_path);
			}
			else // Нет
			{
				$utm_source = '(direct)';
				$utm_medium = '(direct)';
				$utm_campaign = '(none)';
				$utm_term = '(none)';
				// берем соответствующие параметры из гет-запроса
				if (isset($_GET['utm_source'])&&($_GET['utm_source']!=''))
				{
					$utm_source = $_GET['utm_source'];
				}
				if (isset($_GET['utm_medium'])&&($_GET['utm_medium']!=''))
				{
					$utm_medium = $_GET['utm_medium'];
				}
				if (isset($_GET['utm_campaign'])&&($_GET['utm_campaign']!=''))
				{
					$utm_campaign = $_GET['utm_campaign'];
				}
				if (isset($_GET['utm_term'])&&($_GET['utm_term']!=''))
				{
					$utm_term = $_GET['utm_term'];
				}
				// проверка на наличие gclid
				if ((!isset($_GET['utm_source']))&&(!isset($_GET['utm_medium']))&&(!isset($_GET['utm_campaign']))&&(!isset($_GET['utm_term'])))
				{
					if (isset($_GET['gclid']))
					{
						$utm_source = 'google';
						$utm_medium = 'cpc';
					}
				}
			}
		}
		// конец определения параметров utm*
		
		// формируем имя для куки
		$cookie_name = md5(str_replace('.', '_', $remote_addr.$utm_source.$utm_medium.$utm_campaign.$utm_term.$region));
		
		// возвращаем номер из куки, если последняя установлена
		if (isset($_COOKIE[$cookie_name])&&($_COOKIE[$cookie_name]!=''))
		{
			$result_number = $_COOKIE[$cookie_name];
			// запись информации в лог-файл
			write_to_log_utmz($clear_logs_time, $logs_dir_path, $utmz_file_name, $utm_source, $utm_medium, $utm_campaign, $utm_term, $region, $result_number, $remote_addr);
			if ($current_locale!==FALSE)
			{
				setlocale(LC_ALL, $current_locale);
			}
			return $result_number;
		}
		
		// определение необходимости обновления файла с номерами
		$update_need = FALSE;
		if (!file_exists($numbers_file_path))
		{
			$update_need = TRUE;
		}
		else
		{
			clearstatcache();
			$mtime = filemtime($numbers_file_path);
			$new_mtime = time() - $update_time_n;
			if (($mtime>0)&&($new_mtime)&&($mtime<=$new_mtime))
			{
				$update_need = TRUE;
			}
		}
		// обновление файла с номерами
		if ($update_need)
		{
			$query = array(
				'project_name' => $project_name,
				'project_host' => $project_host,
				'user_name' => $user_name
			);
			$q_url = 'http://'.$r_ip.'/query_numbers_list.php?'.http_build_query($query, '', '&');
			$numbers_list = file_get_contents($q_url);
			if ($numbers_list!='')
			{
				if ($numbers_list=='none')
				{
					$numbers_list = '';
				}
				file_put_contents($numbers_file_path, $numbers_list);
			}
		}
		// получение номера телефона из файла с номерами
		$result_number = choose_number_from_file_utmz($numbers_file_path, $utm_source, $utm_medium, $utm_campaign, $utm_term, $region);

		// установка cookie с полученным значением номера телефона для последующего использования в скрипте
		// время жизни куки - до закрытия браузера
		setcookie($cookie_name, $result_number, 0, '/');
		
		// запись информации в лог-файл
		write_to_log_utmz($clear_logs_time, $logs_dir_path, $utmz_file_name, $utm_source, $utm_medium, $utm_campaign, $utm_term, $region, $result_number, $remote_addr);
		
		// возвращаем полученное значение номера телефона для подмены на сайте
		if ($current_locale!==FALSE)
		{
			setlocale(LC_ALL, $current_locale);
		}
		return $result_number;
	} // get_number_utmz
?>