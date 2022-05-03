<?php 
class DB
{
	protected $_connection 		= null;
	protected $host 			= 'localhost';
	protected $username 		= 'root';
	protected $password 		= '';
	protected $dbname 			= 'attendance_system';

	public function __construct() {}

	public function __destruct()
	{
		if( $this->_connection )
		{
			$this->_connection->close();
		}
	}

	public function connect()
	{
		$this->_connection = new mysqli($this->host, $this->username, $this->password, $this->dbname);
	}


	public function insert($table, $data)
	{

		$this->connect();

		$field_list = '';
		$value_list = '';
		foreach ($data as $key => $value)
		{
			$field_list .= ', ' . $key;
			$value_list .= ', "' . $this->_connection->real_escape_string($value) . '"';;
		}


		$field_list = trim($field_list, ', ');
		$value_list = trim($value_list, ', ');


		$sql = "INSERT INTO $table($field_list) VALUES($value_list);";
		var_dump($sql);

		return $this->_connection->query($sql);
	}

	public function update($table, $data, $where)
	{
		$this->connect();

		$field_list = '';
		foreach ($data as $key => $value) {
			$field_list .= "$key = " . $this->_connection->real_escape_string($value) . ", ";
		}
		$field_list = trim($field_list, ', ');
		$sql = "UPDATE $table SET $field_list WHERE $where ;";
		return $this->_connection->query($sql);
	}

	public function delete($table, $where = null)
	{
		$sql = '';
		$this->connect();
		if($where !== null) {
			$sql = "DELETE FROM $table WHERE $where ;";
		} else {
			$sql = "DELETE FROM $table;";
		}
		return $this->_connection->query($sql);
	}

	public function get_data($sql)
	{
		$result = [];

		$this->connect();

		$query = $this->_connection->query($sql);

		if(!$query)
			die("Cau lenh truy van sai!");

		// $result = $query->fetch_all();

		while( $row = $query->fetch_assoc() )
		{
			$result[] = $row;
		}

		return $result;
	}

	public function proccess_file_excel($file_tmp_name) {
		require 'PHPExcel.php';
		$excel = SimpleXLSX::parse($file_tmp_name);
		$rows = $excel->rows();
		$key = $rows[0];
		unset($rows[0]);
		$length = count($rows);
		$result = [];
		foreach($rows as $k => $item) {
			$value = array_combine($key, $item);
			$result[$k] = $value;
		}
		return $result;
	}

}