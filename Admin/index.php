<?php 
	session_start();
	if(isset($_SESSION['LoginType'])){
			header('location: main.php');
	}
?>
<html> 
<head> 
	<meta charset="utf-8">
	<title>Log In</title>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="Assets/css/Login.css"></link>
	<link rel="stylesheet" type="text/css" href="Assets/css/font.css"></link>	

	<script src="Assets/js/Login.js"></script>
	<script src="Assets/js/Function.js"></script>
</head>

	<div>
			<div class="background"></div>
			<div class="Logo"></div>
	</div>
	<div class="container">
		<H1>Login</H1>
		<span></span>
		<div class="loginContainer">
							<form method="POST" action="">
			<input type="text" class="LoginInput" id="Username" name="user" placeholder="Username" />
			<input type="password" class="LoginInput" id="Password" name="pass" placeholder="Password" />
			</ul>
			<input id="button" type="submit" name="submit" value="Login"/>
							</form>
		</div>
	<span></span>
	</div>

	<?php 
		include('../config/db.php');
		define('DB_HOST', $server); 
		define('DB_NAME', $db );
		define('DB_USER', $user); 
		define('DB_PASSWORD',$pass);

		$con = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD); 
		$db = mysql_select_db(DB_NAME,$con); 

		function SignIn(){ 
			$query = mysql_query("SELECT Username , Password, LoginType FROM accounts where Username = '$_POST[user]' AND Password = '$_POST[pass]'"); 
			$row = mysql_fetch_array($query);
			if($row['Username'] AND $row['Password'] && $row['LoginType']=="Supplier"){


							$_SESSION['login_user']=$row['Username'];
							$_SESSION['LoginType']=$row['LoginType'];// Initializing Session
			echo "<script> location.replace('mainSupplier.php')</script>";
			}
			else if($row['Username'] AND $row['Password'] && $row['LoginType']=="Admin"){

							$_SESSION['LoginType']=$row['LoginType'];// Initializing Session
							$_SESSION['login_user']=$row['Username']; // Initializing Session
			echo "<script> location.replace('main.php')</script>";
			}


			else{ 
				echo "<script> alert ('SORRY... YOU ENTERD WRONG ID AND PASSWORD... PLEASE RETRY...') </script>"; 
			} 
		} 

		if(isset($_POST['submit'])){ 
		SignIn(); 
		}
	?>
</body>
</html>