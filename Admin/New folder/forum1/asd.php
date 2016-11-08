<!DOCTYPE html>
	<head>
		<meta charset="utf-8">
		<title>Contact</title>
		<meta name="description" content="">
		<meta name="author" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" type="text/css" href="Assets/css/Home.css"></link>
		<link rel="stylesheet" type="text/css" href="Assets/css/Contact.css"></link>
			
		<!--script src="../Assets/js/Home.js"></script>
    <script src="../Assets/js/Jquery1.12.4.js"></script-->
	</head>
	<body>

<!-- Header -->		
			<div class="block"></div>
			<header class="headBox">
				<div class="headBack"></div>
				<div class="headLogo"></div>
			</header>

<!-- Navigation Bar -->
			<div id="Navi_container">
				<ul id="Navi_content">
					<div id="Navi_btnSidebar">
						<span></span>
						<span></span>
						<span></span>
					</div>
					<div id="Navi_logo"></div>
					<div id="Navi_inptSearch">
						<input id="Navi_inptSearch_des" type="text" placeholder="Search"></input>
					</div>	
					<div id="Navi_btns">
						<li><a class="Navi_btns1" href="AboutUs.html">About Us</a></li>
						<li><a class="Navi_btns2" href="Contact.html">Contact</a></li>
						<li><a class="Navi_btns3" href="Order.html">Order</a></li>
					</div>
				</ul>
			</div>

<!-- Page Content -->
		<section class="pageWrap">
			<H1></H1>
			<div class="container">
			<span class="contactSpan"></span>
				<div class="row">
					<div class="columns four">
                        <form action ="index.php" method = "POST">
						<p>Name</p>
<input Type="text"  name="names" size="98" maxlength="150" />
<p>Contact No. (will be hidden) </p>
<input Type="text" name="contacts" size="98" maxlength="150" />
<p>Description</p>
<input Type="text" name="desc" size="98" maxlength="150" />

<p>Post</p>
<input type="text" name = "tpost" />
<br /><br />
<input type="hidden" name ="cid" value = "<?php echo $cid; ?>"/>
</div>
					<div class="columns eight">
						<textarea id="contactMessage" name ="desc" placeholder="Description"></textarea>
						<input type ="submit" id="contactSubmit" value ="Submit">
                        
					</div>
				</div>
			<span class="contactSpan"></span>
			</div>
		</section>
	</body>
</html>