    <?php session_start();?>
<!DOCTYPE Html>
<html>
<title></title>
<head>
<meta http-equiv="Content-type" content="text=/html; charset=utf=8"/>

</head>
<body>
<div id="wrapper">
<h2> Forum</h2>
</hr>
<div id = "content">
<?php 
include_once("connect.php");
$cid = $_GET['cid'];

echo " <a href='creat_topic.php?cid=".$cid."'>Reply
</a><br><br>";

$sql = "SELECT id FROM categories WHERE id='".$cid."' LIMIT 1 ";
$res = mysql_query($sql) or die (mysql_error());
if (mysql_num_rows($res) ==1){
$sql2 = "SELECT * FROM topics WHERE category_id='".$cid."' ORDER BY topic_reply_date DESC";
$res2 = mysql_query($sql2) or die(mysql_error());
if (mysql_num_rows($res2) > 0){
$topics = '';
$topics .= "<table width = '100%'  style='border-collapse: collapse;'>";
$topics .= "<tr><td colspan= '3'><a href = 'index.php'>Return to Forum Index</a><br><br>"."<hr /></td><tr>";
$topics .="<tr style='background-color:'#dddddd;'><td>Topic Title</td><td width= '65' align ='center'></td><td width='65' align='center'> </td></tr>";
while($row = mysql_fetch_assoc($res2)){
$tid = $row['id'];
$title = $row['topic_title'];
$views = $row['topic_views'];
$date = $row['topic_date'];
$creator = $row['topic_creator'];
$topics .="<tr><td>".$title."<br/><span class='post_info'>Posted By: ".$creator." on " .$date."</span></td></tr><td align='center'></td><td align ='center'>" . $views."</td></tr>";
$topics .= "<tr><td colspan ='3'><hr /></td></tr>";

}
$topics .="</table>"; 
echo $topics;
}
else {
echo "<a href = 'index.php'> return to Forum Index  </a><br></hr>";
echo "<p> There are no Topics in this category yet</p>";
}
}
else
{
echo "<a href = 'index.php'> | return to Forum Index  </a></hr>";
echo "<p> You are trying to view a category that does exist yet.</p>";
}


?>
</div>
</div>