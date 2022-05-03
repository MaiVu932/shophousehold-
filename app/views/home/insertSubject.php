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
	<h1>Them mon hoc</h1>
	<div class="containt">
		<div class="txt">
			<span>Ma Mon hoc</span> <br>
			<span>Ten Mon hoc</span> <br>
			<span>So tin chi</span> <br>
		</div>

		<div class="inp">
			<input type="text" name="txtSubjectId" /> <br>
			<input type="text" name="txtSubjectName" /> <br>
			<input type="number" name="nbCredit" /> <br>
		</div>
	</div>
	<div class="btn">
		<?php 
		include 'choose.php';
		?>
	</div>
</form>
