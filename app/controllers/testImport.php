<?php 	

class TestImport extends Controller {

	public function index() {
		echo 'TestImport';
	}

	public function import() {
		$this->view('file/import');
		// if (isset($_POST['sbImport'])) {
		// 	$file = $_FILES['file'];
		// 	$file_name = $file['name'];

		// 	$arr_allow = ['xls', 'xlsm', 'xlsx', 'xlsb', 'xltx', 'xltm', 'xlt', 'xml', 'xlam', 'xla', 'xlw', 'xlr'];
		// 	$extention = explode('.', $file_name)[1];
		// 	if(!in_array($extention, $arr_allow)) {
		// 		echo 'loi!';
		// 		return;
		// 	}
		// 	$handle = fopen("$file", "r");

		// 	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
		// 	{
		// 		$import="INSERT into  bangdiem(hoten,toan,van) values('$data[0]',$data[1],$data[2])";

		// 		mysql_query($import) or die(mysql_error());
		// 	}

		// 	fclose($handle);
		// 	// var_dump(fgetcsv($file));
		// 	die();
		// }
	}

}