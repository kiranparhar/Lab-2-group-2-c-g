<?php
define("DB_HOST","localhost");
define("DB_USERNAME","root");
define("DB_PASSWORD","");
define("DB_NAME","group2_lab2");

// Check connection
$conn = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);

if ($conn -> connect_errno) {
  echo "Failed to connect to MySQL: " . $conn -> connect_error;
  exit();
}
if($_POST['id']) {
	$id= $_POST['id'];
	$table= $_POST['table'];
   $sql = "DELETE FROM ".$table." WHERE id=".$id;
  if ($conn->query($sql) === TRUE) {
	  echo "Record deleted successfully";
	} else {
	  echo "Error deleting record: " . $conn->error;
	}
}	
 ?>

