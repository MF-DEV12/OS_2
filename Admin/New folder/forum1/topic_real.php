

<!DOCTYPE Html>
<html>
<title></title>
<head>
<meta http-equiv="Content-type" content="text=/html; charset=utf=8"/>
<link rel="stylesheet" type="text/css" href="Assets/css/Home.css"></link>
		<link rel="stylesheet" type="text/css" href="Assets/css/Contact.css"></link>
	
</head>
<body>
    <!-- Header -->		
			<div class="block"></div>
			<header class="headBox">
				<div class="headBack"></div>
				<div class="headLogo"></div>
			</header>
    
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
    
    
<section class="pageWrap">
			<H1></H1>
			<div class="container">
			<span class="contactSpan"></span>
				<div class="row">
					<div class="columns four">
<form action ="real_parse.php" method ="post">


<p>Name</p>
<input Type="text"  name="names" size="50%" maxlength="150" />
<p>Contact No. (will be hidden) </p>
<input Type="text" name="contacts" size="50%" maxlength="150" />
<p>Post</p>
<input type="text" name = "tpost" size ="50%"/>
<br /><br />    
<p>Description</p>
<textarea name="desc" rows="5" cols="75"></textarea>


<input type="hidden" name ="cid" value = "<?php echo $cid; ?>"/>
    </div>
                        <div class="columns eight">
<input type = "submit" name = "topic_submit" value = "Create" />        
</div>
</div>
                    <span class="contactSpan"></span>
                </div>
    </section>
    