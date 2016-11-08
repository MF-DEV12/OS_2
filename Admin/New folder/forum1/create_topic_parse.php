<?php


if (($_POST['topic_title'] == "") &&($_POST['topic_content'] == "")){
echo "You did not Fill in the both fields. please return to the previous page";
exit();
}
else {
include_once("connect.php");
$cid = $_POST['cid'];
$title = $_POST['topic_title'];
$content = $_POST['topic_content'];
$name = $_POST['name'];
$contact = $_POST['contact'];
$email = $_POST['email'];

$sql4 = "INSERT INTO users(name,email,contact) VALUES ('".$name."','".$email."','".$contact."')";
$res4 = mysql_query($sql4) or die(mysql_error());
$sql = "INSERT INTO topics(category_id,topic_title,topic_date,topic_reply_date) VALUES ('".$cid."','".$title."',now(),now())";
$res = mysql_query($sql) or die(mysql_error());
$new_topic_id = mysql_insert_id();
$sql2 = "INSERT INTO posts(category_id, topic_id,post_content,post_date) VALUES ('".$cid."','".$new_topic_id."','".$content."',now())";
$res2 = mysql_query($sql2) or die (mysql_error());
$sql3 = "UPDATE categories SET last_post_date=now()WHERE id = '".$cid."' LIMIT 1";
$res3 = mysql_query($sql3) or die (mysql_error());
if(($res) && ($res2) &&($res3)){
header("Location: view_category.php?cid=".$cid);
}

else{
echo "there was a problem creating your topic please try again.";
}
}

?>