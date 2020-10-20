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
	
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Lab 2</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php 
$msg ="";
if(isset($_POST['submit'])) {
	$cid =$_POST['cid'];
	$cname =$_POST['name'];
	$ciso =$_POST['iso'];
	$Description =$_POST['Description'];
	$sql = "UPDATE `country` SET `Name`='".$cname."',`ISO`='".$ciso."',`description`='".$Description."' WHERE id=".$cid;
if($conn->query($sql) === TRUE) {
	if($_FILES['imagename']['name']!="") {
				$file_name=$_FILES['imagename']['name'];
				$loc= "images/".$file_name;
				move_uploaded_file($_FILES['imagename']['tmp_name'],$loc);
				$sql3 = "INSERT INTO `countryimagesgallery`( `Country_ID`, `Image_Path`, `Name`) VALUES 
			 ('".$cid."', '".$loc."', '".$file_name."')";
			 $conn->query($sql3);
      	}
	if($_FILES['flag']['name']!="") {
		     $file_name2=$_FILES['flag']['name'];
				$loc2= "images/".$file_name2;
				move_uploaded_file($_FILES['flag']['tmp_name'],$loc2);
				$image = base64_encode(file_get_contents($loc2));
				//echo $image;
				$sql5 = "SELECT count(*) as counts FROM `countryflag` WHERE `Country_ID`=".$cid;
				//$result = $conn -> query($sql5);
				$result3 = $conn -> query($sql5);
				while ($row5 = $result3 -> fetch_row()) {
				if($row5[0]==0) {
						// echo "iiiiii";
						$sql6 = "INSERT INTO `countryflag`(`Country_ID`, `Image`) VALUES 
					 ('".$cid."', '".$image."')";
					 $conn->query($sql6);
				}  else {
						// echo "bye";
					  	$sql4 = "UPDATE `countryflag` SET `image`='".$image."' WHERE Country_ID=".$cid;
						 $conn->query($sql4);
				   }
					
					 }
      }
  $msg = "Record updated successfully";
} else {
  $msg = "Error updating record: " . $conn->error;
}

}

if(isset($_GET['Countryid']) && isset($_GET['name'])) {
$Countryid=$_GET['Countryid'];
$Countryname=$_GET['name']; ?>
<div class="container">
    
	  <?php 
  $i=1;
  $sql = "SELECT `ID`, `Name`, `ISO`, `description`, `Continent_ID`,(SELECT `Image` FROM `countryflag` WHERE `Country_ID`=country.ID limit 1) as flag FROM `country` WHERE `ID`=".$Countryid;
	 if ($result = $conn -> query($sql)) {
	  while ($row = $result -> fetch_row()) { 
	  ?>
	  <h2 class="pageTitle">Edit Country <?php echo $Countryname; ?>
	<?php if($row[5]) {?>
	 <img class="flagimg" src="data:image/png;base64,<?php echo $row[5]?>"/>
	 <?php } else {?>
	  <img class="flagimg" src="images/placeholder.png"/>
	 <?php }?>
	</h2>
	  <form  class="add2" method="POST" action="?Countryid=<?php echo $row[0]; ?>&name=<?php echo $Countryname; ?>" enctype="multipart/form-data">
	  <input type="hidden" class="inputclass" name="cid" value="<?php echo $row[0]; ?>">
	  <label for="fname">Country Flag</label><br>
	  <input type="file"  class="inputclass" id="flag" name="flag"><br><br>
	  <label for="fname">Country Name</label><br>
	  <input type="text"  class="inputclass" id="cname" name="name" value="<?php echo $row[1]; ?>" required><br><br>
	  <label for="fname">Country ISO Code</label><br>
	  <input type="text"  class="inputclass" id="ciso" value="<?php echo $row[2]; ?>" name="iso" required><br><br>
	  <label for="fname">Country Description</label><br>
	  <textarea id="Description"  class="textareaclass" name="Description" rows="4" cols="50"><?php echo $row[3]; ?></textarea>
	  <hr>
	  <h4 class="h4title" >Gallery :- </h4>
		 <div class="gallerydiv">
		 <?php 
			  $sql2 = "SELECT `ID`, `Country_ID`, `Image_Path`, `Name`, `Description` FROM `countryimagesgallery` WHERE `Country_ID`=".$row[0];
			  if ($result2 = $conn -> query($sql2)) {
				while ($row2 = $result2 -> fetch_row()) { ?>
				<div class="imagediv">
				 <img class="galleryimg"  alt="<?php echo $row2[3]?>" src="<?php echo $row2[2]?>"/>
				 <a href="javascript:void(0)" class="deleteimage" rel="<?php echo $row2[0]?>" table="countryimagesgallery">X</a>
				 </div>
		  <?php  } } ?>
		  <p><label>Add New Image</label>
		  <input type="file" name="imagename"></p>
		 </div>
		 <p><?php echo  $msg; ?></p>
	  <input type="submit"  class="btn" name="submit" value="Update">
	  <a href="javascript:void(0)" class="btn" id="clearbtn"  >Clear</a>
	  <a href="./" class="backbtn">Back to home</a>
	 </form> 
	 
	 
	<?php 
	  }
	}
  ?>
  
</div>
<?php } ?>

</body>
</html>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#clearbtn").click(function(){
    $("#cname").val("");
    $("#ciso").val("");
    $("#Description").text("");
  });
  $('.deleteimage').click(function () {
     let _this = $(this);
    let id = $(this).attr("rel");
     let table = $(this).attr("table");
     swal({
       title: "Are you sure?",
       text: "Once deleted, you will not be able to recover this data!",
       icon: "warning",
       buttons: true,
       dangerMode: true,
     })
       .then((data) => {
         if (data) {
           $.ajax({
             url: "delete.php",
             type: "post",
             data: { id: id, table: table },
             cache: false,
             success: function (response) {
                 swal("Proof! Your data has been deleted!", {
                 icon: "success",
               });
               setTimeout(() => {
                window.location.reload();
                },1000);
               
             }, error(error) {
               console.error(error);
             }

           });
         }
       });
   });
});
</script>
