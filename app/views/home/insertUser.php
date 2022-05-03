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
	<h1>Them thong tin user</h1>
	<div class="containt">
		<div class="txt">
			<span>Ma user</span> <br>
			<span>Ten user</span> <br>
			<span>Gioi tinh</span> <br>
			<span>Dia chi</span> <br>
			<span>So dien thoai</span> <br>
			<span>Email</span> <br>
			<span>Ngay sinh</span> <br>
			<span>Chuc vu</span><br>
			<span>Lops</span><br>
		</div>

		<div class="inp">
			<input type="text" name="txtId" /> <br>
			<input type="text" name="txtName" /> <br>
			<div class="gd">
				<input type="radio" name="gender" value="0" /> Nu
				<input type="radio" name="gender" value="1" checked /> Nam <br>
			</div>
			<input type="text" name="txtAddress" /> <br>
			<input type="text" name="txtNumphone" /> <br>
			<input type="email" name="email" /> <br>
			<input type="text" name="txtBirthday" /> <br>
			<div class="posi">
				<input type="radio" name="position" value="1" /> SV
				<input type="radio" name="position" value="2" /> GV <br>
			</div>
			<select name="txtClassId">
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
