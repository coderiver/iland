<?php
	header('Content-type: text/plain; charset=utf-8');
	error_reporting(0);
	$r_ip = '176.9.24.184'; // IP удаленного сервера
	$q_url = 'http://'.$r_ip.'/query_script_update_info.php';
	$data_str = file_get_contents($q_url);
	if ($data_str=='')
	{
		die('Ошибка! Не удалось получить скрипт с удаленного сервера');
	}
	$data_mass = json_decode($data_str);
	$base64_str = '';
	$md5_str = '';
	$filesize_str = '';
	$valid = TRUE;
	if (!is_object($data_mass))
	{
		$valid = FALSE;
	}
	else
	{
		if (!isset($data_mass->base64))
		{
			$valid = FALSE;
		}
		else
		{
			$base64_str = $data_mass->base64;
			if ($base64_str=='')
			{
				$valid = FALSE;
			}
		}
		if (!isset($data_mass->md5))
		{
			$valid = FALSE;
		}
		else
		{
			$md5_str = $data_mass->md5;
			if ($md5_str=='')
			{
				$valid = FALSE;
			}
		}
		if (!isset($data_mass->filesize))
		{
			$valid = FALSE;
		}
		else
		{
			$filesize_str = $data_mass->filesize;
			if ($filesize_str<=0)
			{
				$valid = FALSE;
			}
		}
	}
	if (!$valid)
	{
		die('Ошибка! Не удалось получить корректные данные с удаленного сервера');
	}
	$local_file_path = realpath(dirname(__FILE__)).'/load_phone_number.php';
	$update_need = FALSE;
	clearstatcache();
	if (!file_exists($local_file_path))
	{
		$update_need = TRUE;
	}
	else
	{
		$l_file_str = file_get_contents($local_file_path);
		$l_md5 = md5($l_file_str);
		if ($l_md5!=$md5_str)
		{
			$update_need = TRUE;
		}
	}
	if (!$update_need)
	{
		die('Скрипт находится в актуальном состоянии');
	}
	if (file_exists($local_file_path))
	{
		if (!is_writable($local_file_path))
		{
			if (!chmod($local_file_path, 0666))
			{
				die('Ошибка! Необходимые права на запись файла скрипта отсутствуют, установить права не удалось');
			}
		}
	}
	$new_file_str = base64_decode($base64_str);
	$compare_md5 = md5($new_file_str);
	$compare_filesize = strlen($new_file_str);
	if (($compare_filesize!=$filesize_str)||($compare_md5!=$md5_str))
	{
		die('Ошибка! Проверка на корректность данных не выполнена');
	}
	$b_cnt = file_put_contents($local_file_path, $new_file_str);
	if ($b_cnt!=$filesize_str)
	{
		die('Ошибка! Не удалось обновить файл скрипта - ошибка записи данных');
	}
	echo 'OK';
?>