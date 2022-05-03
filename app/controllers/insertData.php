<?php 

class InsertData extends Controller {

	public function chooseInsert() {
		$this->view('home/choose');
		$this->directive();
	}

	public function directive() {
		if(isset($_POST['btnInsertU'])) {
			header('location: http://localhost/mvc/public/insertData/insertUser');
		}
		if(isset($_POST['btnInsertS'])) {
			header('location: http://localhost/mvc/public/insertData/insertSubject');
		}
		if(isset($_POST['btnInsertTD'])) {
			header('location: http://localhost/mvc/public/insertData/insertTeachDetail');
		}
	}

	public function insertUser() {
		$class = $this->list_class(); 
		$this->view('home/insertUser', ['class' => $class]);
		if(isset($_POST['btnInsertU'])) {
			if(empty($_POST['txtId']) && empty($_POST['txtName']) && empty($_POST['txtAddress']) && empty($_POST['txtNumphone']) && empty($_POST['email']) && empty($_POST['gender']) && empty($_POST['txtBirthday']) ) {
				echo 'empty';
				return;
			}
			$position = $_POST['position'];
			
			//1: GV, 0: SV
			$name_table = 'teachers';
			$data_user = [
				'user_id' => $_POST['txtId'],
				'user_password' => $_POST['txtId']
			];
			$data_per = [
				'user_id' => $_POST['txtId'],
				'permission_id' => 2
			];
			$data = [
				'teacher_id' => $_POST['txtId'],
				'teacher_name' => $_POST['txtName'],
				'teacher_address' => $_POST['txtAddress'],
				'teacher_numphone' => $_POST['txtNumphone'],
				'teacher_email' => $_POST['email'],
				'teacher_gender' => (int)$_POST['gender'],
				'teacher_birthday' => $_POST['txtBirthday'],
			];
			if($position == 1) {
				$name_table = 'students';
				$data_per = [
					'user_id' => $_POST['txtId'],
					'permission_id' => 1
				];
				$data = [
					'student_id' => $_POST['txtId'],
					'student_name' => $_POST['txtName'],
					'student_address' => $_POST['txtAddress'],
					'student_numphone' => $_POST['txtNumphone'],
					'student_email' => $_POST['email'],
					'student_gender' => (int)$_POST['gender'],
					'student_birthday' => $_POST['txtBirthday'],
					'class_id' => $_POST['txtClassId']
				];
			}
			$insertData = $this->model('InsertDataModel');
			$check_exits_user = $insertData->check_exits_user($_POST['txtId']);
			if($check_exits_user == 1) return '<script> alert("Da ton tai !!") </script>';
			$result = $insertData->insertStudent($name_table, $data_user, $data_per, $data);
			var_dump($result);

		}
		$this->directive();
	}


	public function insert_user_by_flie_excel() {
		$this->view('file/importUser');

		if(isset($_POST['btnInsertU'])) {
			$file = $_FILES['file'];
			$file_tmp_name = $file['tmp_name'];
			$file_name = $file['name'];
			$arr_allow = ['xls', 'xlsx', 'xlsb', 'xlsm'];
			if(!$file_name) {
				echo 'Ban chua chn file';
				exit();
			}
			$extension = explode('.', $file_name)[1];
			if(!in_array($extension, $arr_allow)) {
				echo 'File khong hop le';
				exit();
			}
			$insertData = $this->model('InsertDataModel');
			$rows = $insertData->get_data_file_excel($file_tmp_name);
			$lenght = count($rows);
			if($lenght === 0) {
				echo 'Khong co ai trong DS';
				exit();
			}
			foreach($rows as $key => $value) {
				$position = $_POST['position'];
				$gender = $value['gender'] == 'nam' ? 1 : 0;

			//1: GV, 0: SV
				$name_table = 'teachers';
				$data_user = [
					'user_id' => $value['user_id'],
					'user_password' => $value['user_id']
				];
				$data_per = [
					'user_id' => $value['user_id'],
					'permission_id' => 2
				];
				$data = [
					'teacher_id' => $value['user_id'],
					'teacher_name' => $value['user_name'],
					'teacher_address' => $value['address'],
					'teacher_numphone' => $value['numP_phone'],
					'teacher_email' => $value['email'],
					'teacher_gender' => $gender,
					'teacher_birthday' => $value['date_birthday'],
				];
				if($position == 1) {
					$name_table = 'students';
					$data_per = [
						'user_id' => $value['user_id'],
						'permission_id' => 1
					];
					$data = [
						'teacher_id' => $value['user_id'],
						'teacher_name' => $value['user_name'],
						'teacher_address' => $value['address'],
						'teacher_numphone' => $value['numP_phone'],
						'teacher_email' => $value['email'],
						'teacher_gender' => $gender,
						'teacher_birthday' => $value['date_birthday'],
					];
				}
				$insertData = $this->model('InsertDataModel');
				$check_exits_user = $insertData->check_exits_user($value['user_id']);
				if($check_exits_user == 0) {
					$result = $insertData->insertStudent($name_table, $data_user, $data_per, $data);
				var_dump($result);
				}
				// $result = $insertData->insertStudent($name_table, $data_user, $data_per, $data);
			}
			

		}
		

	}





























	public function insertSubject() {
		$this->view('home/insertSubject');
		if(isset($_POST['btnInsertS'])) {
			$data = [
				'subject_id' => $_POST['txtSubjectId'],
				'subject_name' => $_POST['txtSubjectName'],
				'subject_credit' => $_POST['nbCredit']
			];
			$insertData = $this->model('InsertDataModel');
			$insertData->insertS($data);

		}
		$this->directive();
	}

	public function list_subject() {
		$insertData = $this->model('InsertDataModel');
		$data = $insertData->get_list_subject();
		return $data;
	}

	public function list_teacher() {
		$insertData = $this->model('InsertDataModel');
		$data = $insertData->get_list_teacher();
		return $data;
	}

	public function list_class() {
		$insertData = $this->model('InsertDataModel');
		$data = $insertData->get_list_class();
		return $data;
	}

	public function insertTeachDetail() {
		$data_subject = $this->list_subject();
		$data_teacher = $this->list_teacher();
		$data_class = $this->list_class();
		$this->view('home/insertTeachDetail', [
			'subject' => $data_subject, 
			'teacher' =>$data_teacher, 
			'class' =>$data_class
		]);


		if(isset($_POST['btnInsertTD'])) {

			$insertData = $this->model('InsertDataModel');
			$class_id = $_POST['class'];
			$subject_id = $_POST['subject'];
			$teacher_id = $_POST['teacher'];
			$data = $insertData->get_teachdetail($class_id, $subject_id, $teacher_id);

			if($data[0]['state'] == 1) {
				echo '<script> alert("Da ton tai !!") </script>';
				return;
			}
			$data = [
				'class_id' => $class_id,
				'subject_id' => $subject_id,
				'teacher_id' => $teacher_id
			];
			$insertData->add_teachDetail($data);
		}
		$this->directive();
	}





}