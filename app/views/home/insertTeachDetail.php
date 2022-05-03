<style type="text/css">



form {
	width: 70%;
	background: rgba(170, 20, 20, 0.3);
	margin: auto;
}
form h1 {
	padding: 15px;
	margin: auto;
	text-align: center;
}
.containt {
	width: 90%;
	margin: auto;
	display: flex;
}
.txt, .inp {
	width: 100%;
	height: 300px;
	display: flex;
	flex-direction: column;
}
.btn {
	width: 100%;
	display: flex;
	justify-items: space-around;
	justify-content: space-around;
}
input[type='submit'] {
	width: 100px;
	height: 50px;
	margin-top: 15px;
	background: rgba(10, 180, 45, 0.6);
	border-radius: 3px;
}

</style>


<form method="POST">
	<h1>Them teach detail</h1>
	<div class="containt">
		<div class="txt">
			<span>Ten mon hoc</span> <br>
			<span>Ten giang vien</span> <br>
			<span>Ten lop</span> <br>
		</div>

		<div class="inp">
			<select name="subject">
			<?php 
				$subject = $data['subject'];
				$lenght = count($subject);
				for($i = 0; $i<$lenght; $i++) {
					echo '<option value="' . $subject[$i]['subject_id'] . '">';
					echo $subject[$i]['subject_name'];
					echo '</option>';
				}
			 ?>
			 </select>

			 <select name="teacher">
			<?php 
				$teacher = $data['teacher'];
				$lenght = count($teacher);
				for($i = 0; $i<$lenght; $i++) {
					echo '<option value="' . $teacher[$i]['teacher_id'] . '">';
					echo $teacher[$i]['teacher_name'];
					echo '</option>';
				}
			 ?>
			 </select>
			
			<select name="class">
			<?php 
				$class = $data['class'];
				$lenght = count($class);
				for($i = 0; $i<$lenght; $i++) {
					echo '<option value="' . $class[$i]['class_id'] . '">';
					echo $class[$i]['class_name'];
					echo '</option>';
				}
			 ?>
			 </select>

		</div>
	</div>
	<div class="btn">
		<?php 
		include 'choose.php';
		?>
	</div>
</form>
