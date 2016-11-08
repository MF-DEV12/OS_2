<?php


if (($_POST['tpost'] == "") &&($_POST['desc'] == "")){
echo "You did not Fill in the both fields. please return to the previous page";
exit();
}
else {
include_once("connect.php");
$name = $_POST['names'];
$contact = $_POST['contacts'];
$desc = $_POST['desc'];
$tpost = $_POST['tpost'];
    $cid = $_POST['cid'];

    

$sql4 = "INSERT INTO categories(category_title,category_description,last_post_date) VALUES ('".$tpost."','".$desc."',now())";
$res4 = mysql_query($sql4) or die(mysql_error());
if($res4){
header("Location: index.php");
}

else{
echo "<script> alert('there was a problem creating your topic please try again.')</script>";
}
}

?>