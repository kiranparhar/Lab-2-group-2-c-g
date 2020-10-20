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
if(isset($_POST['submit'])) {
  $cname= $_POST['name'];
  $ciso= $_POST['iso'];
  $cid= $_POST['cid'];
  $sql3 = "INSERT INTO `country`(`Name`, `ISO`, `Continent_ID`) VALUES
     ('".$cname."', '".$ciso."', '".$cid."')";
   $conn->query($sql3);
}

if(isset($_GET['Country']) && isset($_GET['name'])) {
$Countryid=$_GET['Country'];
$Countryname=$_GET['name']; ?>
<div class="container">
    <h2 class="pageTitle">Country <?php echo $_GET['name']?></h2>
    <?php 
  $i=1;
  $sql = "SELECT `ID`, `Name`, `ISO`, `description`, `Continent_ID`,(SELECT `Image` FROM `countryflag` WHERE `Country_ID`=country.ID limit 1) as flag FROM `country` WHERE `Continent_ID`=".$_GET['Country'];
   if ($result = $conn -> query($sql)) {
    while ($row = $result -> fetch_row()) { 
    ?>
   <p class="listp"><b><?php echo $i; ?></b>  <?php echo $row[1]; ?> ISO(<?php echo $row[2]; ?>)&nbsp;&nbsp;&nbsp;
   <?php if($row[5]) {?>
   <img class="flagimg" src="data:image/png;base64,<?php echo $row[5]?>"/>
   <?php } else {?>
    <img class="flagimg" src="images/placeholder.png"/>
   <?php }?>
   </p>
   <h4 class="h4title" >Gallery :- </h4>
   <div class="gallerydiv">
   <?php 
      $sql2 = "SELECT `ID`, `Country_ID`, `Image_Path`, `Name`, `Description` FROM `countryimagesgallery` WHERE `Country_ID`=".$row[0];
      if ($result2 = $conn -> query($sql2)) {
      while ($row2 = $result2 -> fetch_row()) { ?>
       <img class="galleryimg"  alt="<?php echo $row2[3]?>" src="<?php echo $row2[2]?>"/>
    <?php  } } ?>
   </div>
   <div class="btngroup">
   <a href="edit.php?Countryid=<?php echo $row[0]; ?>&name=<?php echo $row[1]; ?>" class="btn">Edit</a>
   <a href="javascript:void(0)" rel="<?php echo $row[0]; ?>"  table="country" class="btn deletedata">Delete</a>
   </div>
   <hr>
  <?php 
    $i++;
    }
  }
  ?>
  <form  class="add" method="POST" action="?Country=<?php echo $Countryid; ?>&name=<?php echo $Countryname; ?>">
  <h2>Add Country</h2>
  <input type="hidden"  name="cid" value="<?php echo $_GET['Country']; ?>">
  <label for="fname">Country Name</label><br>
  <input  class="inputclass" type="text" id="cname" name="name" required><br><br>
   <label for="fname">Country ISO Code</label><br>
  <input class="inputclass" type="text" id="ciso" name="iso" required><br><br>
  <input type="submit"  class="btn" name="submit" value="Submit">
  <a href="javascript:void(0)" class="btn" id="clearbtn"  >Clear</a>
 </form> 
  <a href="./" class="backbtn">Back to home</a>
</div>
<?php } else { ?>
<div class="container">
    <h2 class="pageTitle">Continent</h2>
  <table>
  <tr>
    <th>S.No</th>
    <th>ISO</th>
    <th>Continent</th>
  </tr>
  <?php 
  $i=1;
  $sql = "SELECT `ID`, `ISO`, `Name` FROM `continent`";
 if ($result = $conn -> query($sql)) {
  while ($row = $result -> fetch_row()) { 
  ?>
  <tr>
    <td><?php echo $i; ?></td>
    <td><?php echo $row[1]; ?></td>
    <td><a href="?Country=<?php echo $row[0]; ?>&name=<?php echo $row[2]; ?>"><?php echo $row[2]; ?></a></td>
  </tr>
<?php 
  $i++;
   }
}
 ?> 
</table>
</div>
<?php } ?>

</body>
</html>