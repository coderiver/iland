<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru-ru" lang="ru-ru">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>test script</title>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push (['_setAccount', 'UA-XXXXXXXX-XX']);
		_gaq.push (['_addOrganic', 'yandex.ru', 'query']);
		_gaq.push (['_addOrganic', 'images.yandex.ru', 'text']);
		_gaq.push (['_addOrganic', 'blogs.yandex.ru', 'text']);
		_gaq.push (['_addOrganic', 'video.yandex.ru', 'text']);
		_gaq.push (['_addOrganic', 'mail.ru', 'q']);
		_gaq.push (['_addOrganic', 'go.mail.ru', 'q']);
		_gaq.push (['_addOrganic', 'google.com.ua', 'q']);
		_gaq.push (['_addOrganic', 'images.google.ru', 'q']);
		_gaq.push (['_addOrganic', 'maps.google.ru', 'q']);
		_gaq.push (['_addOrganic', 'rambler.ru', 'words']);
		_gaq.push (['_addOrganic', 'nova.rambler.ru', 'query']);
		_gaq.push (['_addOrganic', 'nova.rambler.ru', 'words']);
		_gaq.push (['_addOrganic', 'gogo.ru', 'q']);
		_gaq.push (['_addOrganic', 'nigma.ru', 's']);
		_gaq.push (['_addOrganic', 'search.qip.ru', 'query']);
		_gaq.push (['_addOrganic', 'webalta.ru', 'q']);
		_gaq.push (['_addOrganic', 'sm.aport.ru', 'r']);
		_gaq.push (['_addOrganic', 'meta.ua', 'q']);
		_gaq.push (['_addOrganic', 'search.bigmir.net', 'z']);
		_gaq.push (['_addOrganic', 'search.i.ua', 'q']);
		_gaq.push (['_addOrganic', 'index.online.ua', 'q']);
		_gaq.push (['_addOrganic', 'web20.a.ua', 'query']);
		_gaq.push (['_addOrganic', 'search.ukr.net', 'search_query']);
		_gaq.push (['_addOrganic', 'search.com.ua', 'q']);
		_gaq.push (['_addOrganic', 'search.ua', 'q']);
		_gaq.push (['_addOrganic', 'poisk.ru', 'text']);
		_gaq.push (['_addOrganic', 'go.km.ru', 'sq']);
		_gaq.push (['_addOrganic', 'liveinternet.ru', 'ask']);
		_gaq.push (['_addOrganic', 'gde.ru', 'keywords']);
		_gaq.push (['_addOrganic', 'affiliates.quintura.com', 'request']);
		_gaq.push (['_addOrganic', 'akavita.by', 'z']);
		_gaq.push (['_addOrganic', 'search.tut.by', 'query']);
		_gaq.push (['_addOrganic', 'all.by', 'query']);
		_gaq.push (['_trackPageview']);
		_gaq.push (['_trackPageLoadTime']);
	 
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>	
</head>
<body>
	<?php
		require_once(realpath(dirname(__FILE__)).'/load_phone_number.php');
		// регион задавать только при необходимости
		$region = '';
		$number = get_number_utmz($region);
	?>
	<p id="phone_number">
		<?php
			if ($number!='')
			{
				// форматирование номера телефона в соответствии с требуемым шаблоном (зависит от верстки сайта)
				if (strlen($number)=='9'):
					$number = '<a href="tel:0'.$number.'">(0'.substr($number, 0, 2).') '.substr($number, 2, 3).' '.substr($number, 5, 2).' '.substr($number, 7, 2).'</a>';
				endif;	
				echo $number;
			}
			else
			{
				echo '1234567890';
			}
		?>
	</p>
</body>
</html>