<?php 

class InsertDataModel extends DB {

	public function check_exits_user($user_id) {
		$sql = "SELECT ( IF(EXISTS (SELECT user_id FROM users WHERE user_id = '{$user_id}'), 1, 0) ) state;";
		return $this->get_data($sql);
	}

	public function insertStudent($name_table, $data_user, $data_per, $data) {
		if(!$this->insert('users', $data_user))
			{echo 'user'; exit();}
		if(!$this->insert('user_permission', $data_per))
			{echo 'user_permission'; exit();}
		if(!$this->insert($name_table, $data))
			{echo 'info'; exit();}
		return  'them thanh conf';
	}

	public function checkExistSubject($subject_id) {
		$sql = "SELECT ( IF(EXISTS (SELECT subject_id FROM subjects WHERE subject_id = '{$subject_id}'), 1, 0) ) state;";
		return $this->get_data($sql);

	}

	public function insertS($data) {
		$stateExist = $this->checkExistSubject($data['subject_id']);
		if($stateExist[0]['state'] == 1) {
			echo "<script> alert('MH da ton tai !!') </script>";
			return;
		}

		if($this->insert('subjects', $data)) {
			echo "<script> alert('Them mon hoc thanh cong !!') </script>";
			return;
		}
		echo "<script> alert('Them mon hoc that bai !!') </script>";
		return;
	}

	public function get_list_subject() {
		$sql = "SELECT subject_id, subject_name FROM subjects";
		$data = $this->get_data($sql);
		if(count($data) !== 0) 
			return $data;
		return [];
	}

	public function get_list_teacher() {
		$sql = "SELECT teacher_id, teacher_name FROM teachers";
		$data = $this->get_data($sql);
		if(count($data) !== 0) 
			return $data;
		return [];
	}

	public function get_list_class() {
		$sql = "SELECT class_id, class_name FROM class";
		$data = $this->get_data($sql);
		if(count($data) !== 0) 
			return $data;
		return [];
	}

	public function get_teachdetail($class_id, $subject_id, $teacher_id) {
		$sql = "SELECT ( IF( EXISTS( SELECT TD.teach_detail_id  FROM teach_details TD WHERE TD.class_id = '{$class_id}' AND TD.subject_id = '{$subject_id}' AND TD.teacher_id = '{$teacher_id}' ), 1, 0 ) ) state;";
		$data = $this->get_data($sql);
		return $data;
	}

	public function add_teachDetail($data) {
		if(!$this->insert('teach_details', $data)) {
			echo "<script> alert('Them that bai !!') </script>";
			return;
		}
		echo "<script> alert('Them mon hoc thanh cong !!') </script>";
		return;
	}

	public function get_data_file_excel($file_tmp_name) {
		$rows = $this->proccess_file_excel($file_tmp_name);
		return $rows;
	}


}

