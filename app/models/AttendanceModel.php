<?php 
class AttendanceModel extends DB {
	private $attendance_id_last = null;
	public function index() {}

	public function add_attendance_time($class_id, $subject_id, $teacher_id, $attendance_time, $latitude, $longitude, $radius) {
		$data = [
			'class_id' => $class_id,
			'subject_id' => $subject_id,
			'teacher_id' => $teacher_id,
			'attendance_time' => $attendance_time,
			'latitude' => $latitude,
			'longitude' => $longitude,
			'radius' => $radius
		];

		$result = $this->insert('attendances', $data);

		if($result) {
			// lay id cuoi cung tu bang attendance_student
			$sql = " SELECT attendance_id FROM attendances ";
			$sql .= " WHERE class_id = '{$class_id}' AND subject_id = '{$subject_id}' AND teacher_id = '{$teacher_id}' ";
			$sql .= " ORDER BY attendance_id DESC LIMIT 1; ";
			$this->attendance_id_last = $this->get_data($sql)[0]['attendance_id'];

			// them du lieu vao attendance_student
			$sql = " INSERT INTO attendance_student( attendance_id, student_id ) ";
			$sql .= " SELECT A.attendance_id, S.student_id FROM students S, attendances A ";
			$sql .= " WHERE S.class_id = '{$class_id}' AND  A.attendance_id = {$this->attendance_id_last}; ";
			return $this->insert_by_sql($sql) ? true : false;
		}
		return false;
	}


	// Get list attendance student - ok
	public function get_list_attendance_student($class_id, $subject_id, $teacher_id) {
		$sql = " SELECT attendance_id
		FROM attendances
		WHERE class_id = '{$class_id}'
		AND subject_id = '{$subject_id}'
		AND teacher_id = '{$teacher_id}'
		ORDER BY attendance_id DESC
		LIMIT 1 ";
		$data = $this->get_data($sql);
		if(count($data) !== 0 ) {
			$attendance_id_last = (int)$data[0]['attendance_id'];
			$sql = "SELECT S.student_id, S.student_name, ATS.student_attendance_state 
			FROM students S, attendance_student ATS, attendances A 
			WHERE A.attendance_id = {$attendance_id_last}
			AND S.student_id = ATS.student_id 
			AND A.attendance_id = ATS.attendance_id 
			ORDER BY S.student_name ASC;  ";
			return $this->get_data($sql);
		}
	}



	public function get_check_exist_in_room($student_id) {
		$sql = "SELECT IF (EXISTS (SELECT attendance_id FROM attendance_student WHERE student_id = '{$student_id}'), 1, 0) AS state;";
		return $this->get_data($sql);
	}

	// kiem tra id da diem danh hay chua trong attendance_student
	public function get_exist_in_attendance_student($student_id) {
		$sql = " SELECT student_attendance_state AS is_exist_in_attendance_student FROM attendance_student ";
		$sql .= " WHERE student_id = '{$student_id}'";
		$sql .= " ORDER BY attendance_id DESC LIMIT 1; ";
		return $this->get_data($sql);
	}


	public function get_state_turn_on_attendance($class_id, $subject_id, $teacher_id, $day) {
		$sql = " SELECT ( IF( EXISTS(SELECT attendance_id FROM attendances WHERE class_id = '{$class_id}' ";
		$sql .= " AND subject_id = '{$subject_id}' AND teacher_id = '{$teacher_id}' AND DAY(attendance_time) = {$day}), 1, 0)) AS state; ";
		 // return $this->get_data($sql);
		$this->connect();
		return $this->_connection->query($sql)->fetch_assoc();
	}




	public function get_info_details_attendance($student_id, $current_date) {

		$sql = " 
		SELECT B1.student_id, B1.subject_name,B1.total_session, B1.teacher_name, B1.learn_session, B2.leave_session, B1.latitude, B1.longitude, B1.radius
		FROM
		( SELECT ATS.student_id,SB.subject_id, SB.subject_name,IF( SB.subject_credit = 3, 12, 8 ) total_session ,  T.teacher_name, COUNT(A.attendance_id) AS learn_session, A.latitude, A.longitude, A.radius
		FROM attendance_student ATS, attendances A, subjects SB, teachers T
		WHERE ATS.student_id = '{$student_id}'
		AND ATS.attendance_id = A.attendance_id 
		AND A.subject_id = SB.subject_id
		AND A.teacher_id = T.teacher_id
		AND A.subject_id = (SELECT A.subject_id
		FROM attendance_student ATS, attendances A 
		WHERE ATS.student_id = '{$student_id}'
		AND ATS.attendance_id = A.attendance_id) ) B1
		LEFT JOIN
		(SELECT COUNT(LL.list_leave_id) leave_session, LL.subject_id
		FROM list_leave LL
		WHERE LL.student_id = '{$student_id}'
		AND LL.subject_id = (SELECT A.subject_id
		FROM attendance_student ATS, attendances A 
		WHERE ATS.student_id = '{$student_id}'
		AND ATS.attendance_id = A.attendance_id)
		AND LL.leave_date <= '{$current_date}') B2
		ON B1.subject_id = B2.subject_id ";	

		return $this->get_data($sql);
	}


	// lay ngay, gio, phut diem danh cuoi
	public function get_datetime_attendance_last( $class_id, $subject_id, $teacher_id, $day ) {
		$sql = " SELECT DAY(attendance_time) AS day, HOUR(attendance_time) AS hour, MINUTE(attendance_time) as minute ";
		$sql .= " FROM attendances AS A  WHERE class_id = '{$class_id}' AND ";
		$sql .= "  subject_id = '{$subject_id}' AND teacher_id = '{$teacher_id}' ORDER BY attendance_id DESC LIMIT 1 ;";
		$datetime = $this->get_data($sql);

		if(count($datetime) === 0) {
			$datetime = [
				0 => [
					'day' => 0,
					'hour' => 0,
					'minute' => 0
				]
			];
		}

		$sql = " SELECT count(*) num FROM attendances WHERE DAY(attendance_time) = {$day} AND subject_id = '{$subject_id}' ";
		$sql .=" AND class_id = '{$class_id}' AND teacher_id = '{$teacher_id}'; ";

		$count = $this->get_data($sql);
		return ['datetime' => $datetime[0], 'count' => $count[0]];
	}


	//Cap nhat diem danh sv

	public function update_state_attendance_student($student_id) {
		$data = ['student_attendance_state' => 1];
		return $this->update('attendance_student', $data, "student_id = '{$student_id}' ");
	}


	private function insert_by_sql($sql)
	{
		$this->connect();
		return $this->_connection->query($sql);
	}


	// public function on_check_stop_attendance($class_id, $subject_id, $teacher_id) {
	// 	$sql = " SELECT attendance_id FROM attendances ";
	// 	$sql .= " WHERE class_id = '{$class_id}' AND subject_id = '{$subject_id}' AND teacher_id = '{$teacher_id}' ";
	// 	$sql .= " ORDER BY attendance_id DESC LIMIT 1; ";
	// 	$data = $this->get_data($sql);
	// 	if(count($data) !== 0) {
	// 		// 1: Chua tat diem danh | 0: Da tat diem danh
	// 		$last_id = (int)$data[0]['attendance_id'];
	// 		$sql = "SELECT ( IF ( EXISTS ( SELECT attendance_id FROM attendance_student WHERE attendance_id = {$last_id}  ), 1, 0 )) AS state; ";
	// 		$state = $this->get_data($sql)[0];
	// 		return $state;
	// 	}
	// 	return ['state' => -1];
	// }

	// public function on_check_stop_attendance($class_id, $subject_id, $teacher_id) {
	// 	$sql = " SELECT attendance_id FROM attendances ";
	// 	$sql .= " WHERE class_id = '{$class_id}' AND subject_id = '{$subject_id}' AND teacher_id = '{$teacher_id}' ";
	// 	$sql .= " ORDER BY attendance_id DESC LIMIT 1; ";
	// 	$data = $this->get_data($sql);

	// 	if(count($data) !== 0) {
	// 		// 1: Chua tat diem danh | 0: Da tat diem danh
	// 		$last_id = (int)$data[0]['attendance_id'];
	// 		$sql = "SELECT ( IF ( EXISTS ( SELECT attendance_id FROM attendance_student WHERE attendance_id = {$last_id}  ), 1, 0 )) AS state; ";
	// 		$state = $this->get_data($sql)[0];
	// 		return $state;
	// 	}
	// 	return ['state' => -1];
	// }


	// 1: Chua tat diem danh | 0: Da tat diem danh
	// public function on_check_stop_attendance($attendance_id_last) {
	// 	$sql = "SELECT ( IF ( EXISTS ( SELECT attendance_id FROM attendance_student WHERE attendance_id = {$attendance_id_last}  ), 1, 0 )) AS state; ";
	// 	$state = $this->get_data($sql)[0];
	// 	return $state;
	// }




	public function get_check_exist_in_take_leave($class_id, $subject_id, $current_date, $attendance_id_last) {
		$sql = " SELECT T1.student_id
		FROM
		(SELECT L.student_id, L.take_leave_date
		FROM leaves L, students S
		WHERE L.take_leave_date = '{$current_date}'
		AND L.subject_id = '{$subject_id}'
		AND L.student_id = S.student_id
		AND S.class_id = '{$class_id}'
		AND NOT EXISTS (
		SELECT LL.student_id
		FROM list_leave LL
		WHERE LL.leave_id = L.leave_id)  ) T1
		INNER JOIN (
		SELECT ATS.student_id
		FROM attendance_student ATS
		WHERE ATS.attendance_id = {$attendance_id_last}) T2
		ON T1.student_id = T2.student_id ";
		
		$data = $this->get_data($sql);
		if(count($data) !== 0) {
			return ['state' => true];
		}
		return ['state' => false];
	}


	public function post_handmade_at($student_id) {
		$sql = " UPDATE attendance_student SET student_attendance_state = 2 WHERE student_id = '{$student_id}' ";

		$this->connect();
		$state = $this->_connection->query($sql);


		if($state) {
			return ['state' => 1];
		}
		return ['state' => -1];
	}

	// public function is_exist_student_handmade($class_id, $subject_id) {
	// 	$sql = " SELECT ( IF ( EXISTS ( SELECT ATS.attendance_student_id FROM attendance_student ATS, attendances A WHERE A.class_id = '{$class_id}' AND A.subject_id = '{$subject_id}' AND A.attendance_id = ATS.attendance_id AND ATS.student_attendance_state = 2), 1, 0 ) ) is_exist_student_handmade; ";
	// 	echo $sql;
	// 	die();
	// 	return $data = $this->get_data($sql)[0];
	// }

	public function is_exist_student_handmade($attendance_id_last) {
		$sql = "  SELECT ( IF ( EXISTS ( SELECT ATS.attendance_student_id FROM attendance_student ATS WHERE ATS.attendance_id = ${attendance_id_last} AND ATS.student_attendance_state = 2), 1, 0 ) ) is_exist_student_handmade;  ";
		return $data = $this->get_data($sql)[0];
	}


	// public function get_list_student_handmade($class_id, $subject_id) {
	// 	$sql = " SELECT attendance_id FROM attendances ";
	// 	$sql .= " WHERE class_id = '{$class_id}' AND subject_id = '{$subject_id}'  ";
	// 	$sql .= " ORDER BY attendance_id DESC LIMIT 1; ";

	// 	$data = $this->get_data($sql);
	// 	$attendance_id_last = null;

	// 	if(count($data) !== 0) {
	// 		$attendance_id_last = (int)$data[0]['attendance_id'];
	// 	}

	// 	$sql = " SELECT ATS.student_id, S.student_name
	// 	FROM attendance_student ATS, students S 
	// 	WHERE ATS.attendance_id = {$attendance_id_last} AND ATS.student_attendance_state = 2 AND ATS.student_id = S.student_id; ";

	// 	$data = $this->get_data($sql);

	// 	if(count($data) !== 0) {
	// 		return $data;
	// 	}
	// 	return [];
	// }

	public function get_list_student_handmade($attendance_id_last) {
		if($attendance_id_last) {
			$sql = " SELECT ATS.student_id, S.student_name
			FROM attendance_student ATS, students S 
			WHERE ATS.attendance_id = {$attendance_id_last} AND ATS.student_attendance_state = 2 AND ATS.student_id = S.student_id; ";
			$data = $this->get_data($sql);
		}
		if(count($data) !== 0) {
			return $data;
		}
		return [];
	}

	//1 co mat
	public function update_state_when_attendance_handmade($student_id, $state) {
		$sql = " UPDATE attendance_student SET student_attendance_state = {$state} WHERE student_id = '{$student_id}'; ";
		$this->connect();
		$d = $this->_connection->query($sql);
		if($d) {
			return ['state' => 1];
		}
		return ['state' => -1];
	}


}