

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

	<!--body id="body-color"> 
        <div class="Background"></div>
        <div class="Logo"></div>
		<div id="Sign-In" class="container">
			<form method="POST" action="">
				<input type="text" name="user" class="LoginInput"/><h6>Username</h6>
				<input type="password" name="pass" class="LoginInput"/><h6>Password</h6>
				<input id="button" type="submit" name="submit" value="Log-In"/>
			</form>
		</div-->

		<?php 
		 	include('config/db.php');
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
				
				
				echo "<script> location.replace('mainSupplier.php')</script>";
				}
				else if($row['Username'] AND $row['Password'] && $row['LoginType']=="Admin"){
				
				
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