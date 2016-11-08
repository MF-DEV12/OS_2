<!DOCTYPE Html>
<html>
<title></title>
<head>
<meta http-equiv="Content-type" content="text=/html; charset=utf=8"/>
	<link rel="stylesheet" type="text/css" href="Assets/css/Home.css"></link>
		<link rel="stylesheet" type="text/css" href="Assets/css/Contact.css"></link>
</head>
<body>
<div id="wrapper">
<h2> Forum</h2>
    <a href = "topic_real.php">Create Topic</a>
    <br><br>
</hr>
<div id = "content">
<?php 
include_once("connect.php");
$sql = "SELECT * FROM categories ORDER BY category_title ASC";
$res = mysql_query($sql) or die(mysql_error());
$categories = "";
if(mysql_num_rows($res)>0)
{
while ($row = mysql_fetch_assoc($res)){
$id = $row['id'];
$title = $row['category_title'];
$description = $row['category_description'];
$categories .="<a href='view_category.php?cid=".$id."' class = cat_links'>" .$title." - <font size='-1'>" . $description."</a><br>"; 
}
echo $categories;
}
else {
echo "<P> There are no Categories available yet </p>";
}


?>

</div>
</div>
    
    