<?php
require 'config.php';
if(isset($config['locale']))
{
	$domain='videoplayer';
	$locale=$config['locale'];
	$locale_path=dirname(__FILE__).'/locale';
	if(!file_exists($file=$locale_path."/$locale/LC_MESSAGES/$domain.mo"))
		echo sprintf("<p>No translation found for locale %s. It should be placed in %s</p>",$locale,$file);
	else
	{
		putenv('LC_MESSAGES='.$locale);
		setlocale(LC_MESSAGES,$locale);
		// Specify location of translation tables
		bindtextdomain($domain,$this->locale_path);
		// Choose domain
		textdomain($domain);
	}
}
require 'pdohelper.php';
$db=new pdohelper("mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",$config['db_user'],$config['db_password'],array(PDO::ATTR_PERSISTENT => true));