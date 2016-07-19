<?Php
class pdohelper extends PDO
{
	public $db;
	function __construct($dsn,$username,$passwd,$options=NULL)
	{
		$this->db=parent::__construct($dsn,$username,$passwd,$options);
		//$this->db=new PDO("mysql:host={$this->config['db_host']};dbname={$this->config['db_name']};charset=utf8",$this->config['db_user'],$this->config['db_password'],array(PDO::ATTR_PERSISTENT => true));

	}
	function query($q,$fetch='all')
	{
		$st=parent::query($q);

		if($st===false)
		{
			$errorinfo=$this->errorInfo();
			//trigger_error("SQL error: {$errorinfo[2]}",E_USER_WARNING);
			throw new Exception("SQL error: {$errorinfo[2]}");
			//return false;
		}
		elseif($fetch===false)
			return $st;
		elseif($fetch=='column')
			return $st->fetch(PDO::FETCH_COLUMN);
		elseif($fetch=='all')
			return $st->fetchAll(PDO::FETCH_ASSOC);
		elseif($fetch=='all_column')
			return $st->fetchAll(PDO::FETCH_COLUMN);
		elseif($fetch=='key_pair')
			return $st->fetchAll(PDO::FETCH_KEY_PAIR);
	}
	function execute($st,$parameters,$fetch=false)
	{
		if($st->execute($parameters)===false)
		{
			$errorinfo=$st->errorInfo();
			throw new Exception("SQL error: {$errorinfo[2]}");
			return false;
		}
		elseif($fetch=='single')
			return $st->fetch(PDO::FETCH_COLUMN);
		elseif($fetch=='all')
			return $st->fetchAll(PDO::FETCH_ASSOC);
		elseif($fetch=='key_pair')
			return $st->fetchAll(PDO::FETCH_KEY_PAIR);
	}
}