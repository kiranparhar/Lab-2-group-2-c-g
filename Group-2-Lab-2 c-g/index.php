<?php
define("DB_HOST", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "group2_lab2");
// Check connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_errno) {
  echo "Failed to connect to MySQL: " . $conn->connect_error;
  exit();
}

$upload_text = "upload";

?>
<!DOCTYPE html>
<html>

<head>
  <title>Lab 2</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <script>
    function clearValue() {

      document.getElementById('update-name').value = ""
      document.getElementById('update-iso').value = ""
    }

    function upload(id) {
      document.getElementById(id).click();
    }

    function submitId(id) {
      document.getElementById(id).submit();

    }

    function onchangeId(id) {
      document.getElementById(id).click();

    }

    function updatePop(event) {
      const name = event.target.getAttribute('data-name');
      const iso = event.target.getAttribute('data-iso');
      const id = event.target.getAttribute('data-id');
      document.getElementById('update-pop').style.visibility = "visible"
      document.getElementById('update-name').value = name
      document.getElementById('update-iso').value = iso
      document.getElementById('update-id').value = id

    }



    function updateHide() {
      document.getElementById('update-pop').style.visibility = "hidden"
    }
  </script>

  <?php
  $val = 0;

  if (isset($_POST['submit'])) {
    $cname = $_POST['name'];
    $ciso = $_POST['iso'];
    $cid = $_POST['cid'];
    $sql3 = "INSERT INTO `country`(`Name`, `ISO`, `Continent_ID`) VALUES
     ('" . $cname . "', '" . $ciso . "', '" . $cid . "')";
    $conn->query($sql3);
  }

  if (isset($_POST['update'])) {
    $cname = $_POST['name'];
    $ciso = $_POST['iso'];
    $cid = $_POST['cid'];
    $sql4 = "UPDATE `country` SET `Name`='$cname',`ISO`='$ciso' WHERE `ID`='$cid'";
    $conn->query($sql4);
  }

  if (isset($_POST['replace'])) {
    $uploadfile = './' . $_POST['src'];

    move_uploaded_file($_FILES['repfile']['tmp_name'], $uploadfile);
  }

  if (isset($_POST['new'])) {
    $uploadfile = './images/' . $_FILES['newfile']['name'];

    move_uploaded_file($_FILES['newfile']['tmp_name'], $uploadfile);
    $cid = $_POST['cid'];
    $path = 'images/' . $_FILES['newfile']['name'];
    $name = 'Gallery Image';
    $desc = pathinfo($_FILES['newfile']['name'], PATHINFO_FILENAME);
    $sql6 = "INSERT INTO `countryimagesgallery`(`Country_ID`,`Image_Path`,`Name`,`Description`) VALUES('$cid','$path','$name','$desc')";
    $conn->query($sql6);
  }

  if (isset($_POST['remove'])) {

    $cid = $_POST['cid'];
    $sql5 = "DELETE FROM `countryimagesgallery` WHERE `ID`='$cid'";

    if (file_exists($_POST['src'])) {
      unlink(`/` . $_POST['src']);
    }

    $conn->query($sql5);
  }


  if (isset($_GET['Country']) && isset($_GET['name'])) {
    $Countryid = $_GET['Country'];
    $Countryname = $_GET['name']; ?>
    <div id='update-pop' class="update-pop flex-center">
      <div class="update-hide flex-center" onclick="updateHide()">X</div>
      <div>
        <form class="add" method="POST" action="?Country=<?php echo $Countryid; ?>&name=<?php echo $Countryname; ?>">
          <h2>Upadte Country</h2>
          <input id="update-id" type="hidden" name="cid">
          <label for="fname">Country Name</label><br>
          <input id='update-name' type="text" name="name" required><br><br>
          <label for="fname">Country ISO Code</label><br>
          <input id='update-iso' type="text" name="iso" required><br><br>
          <input type="submit" name="update" value="Submit">
          <button type="button" onclick="clearValue()">clear</button>
        </form>
      </div>
    </div>
    <div class="container">

      <h2 class="pageTitle">Country <?php echo $_GET['name'] ?></h2>
      <?php
      $i = 1;

      $sql = "SELECT `ID`, `Name`, `ISO`, `description`, `Continent_ID`,(SELECT `Image` FROM `countryflag` WHERE `Country_ID`=country.ID limit 1) as flag FROM `country` WHERE `Continent_ID`=" . $_GET['Country'];
      if ($result = $conn->query($sql)) {

        while ($row = $result->fetch_row()) {

          $val = $row[0];


      ?>
          <p class="listp"><b><?php echo $i; ?></b> <?php echo $row[1]; ?> ISO(<?php echo $row[2]; ?>)&nbsp;&nbsp;&nbsp; <button class="backbtn updatebtn" data-name="<?php echo $row[1] ?>" data-iso="<?php echo $row[2] ?>" data-id="<?php echo $row[0] ?>" onclick="updatePop(event)">update</button>
            <?php if ($row[5]) { ?>
              <img class="flagimg" src="data:image/png;base64,<?php echo $row[5] ?>" />
            <?php } else { ?>
              <img class="flagimg" src="images/placeholder.png" />
            <?php } ?>
          </p>
          <div class="flex pad-tb-10">
            <h4 class="h4title">Gallery :- </h4>
            <form id="new-form-<?php echo $val ?>" class="add" method="POST" action="?Country=<?php echo $Countryid; ?>&name=<?php echo $Countryname; ?>" enctype="multipart/form-data">
              <input type="file" id='new-file-<?php echo $val ?>' name="newfile" accept="image/*" hidden onchange="submitId('new-form-<?php echo $val ?>')" />
              <input type="text" name='cid' value='<?php echo $val ?>' hidden />
              <input type="hidden" name="new" value="new">
              <button class="newbtn" type="button" onclick="onchangeId('new-file-<?php echo $val ?>')">new</button>
            </form>
          </div>
          <div class="gallerydiv">
            <?php
            $sql2 = "SELECT `ID`, `Country_ID`, `Image_Path`, `Name`, `Description` FROM `countryimagesgallery` WHERE `Country_ID`=" . $row[0];
            $result2 = $conn->query($sql2);

            if (mysqli_num_rows($result2) > 0) {
              while ($row2 = $result2->fetch_row()) { ?>
                <div class="gallerycontrol">
                  <div class="flex-center">

                    <img class="galleryimg" alt="<?php echo $row2[3] ?>" src="<?php echo file_exists($row2[2]) ? $row2[2] : 'images/placeholder.png' ?>" /></div>
                  <span class="abs"><?php echo file_exists($row2[2]) ? "" : 'image is missing' ?></span>

                  <div class="flex-column">
                    <div>
                      <form id="rep-form-<?php echo $row2[0] ?>" method="POST" action="?Country=<?php echo $Countryid; ?>&name=<?php echo $Countryname; ?>" enctype="multipart/form-data">
                        <input id="rep-file-<?php echo $row2[0] ?>" type="file" name="repfile" hidden onchange="submitId('rep-form-<?php echo $row2[0] ?>')" accept="image/*" />
                        <input type="hidden" name="cid" value="<?php echo $row2[0] ?>">
                        <input type="hidden" name="src" value="<?php echo $row2[2] ?>">
                        <input type="hidden" name="replace" value="replace">
                        <button type="button" onclick="onchangeId('rep-file-<?php echo $row2[0] ?>')">replace</button>
                      </form>
                    </div>

                    <div>
                      <form method="POST" action="?Country=<?php echo $Countryid; ?>&name=<?php echo $Countryname; ?>">
                        <input type="hidden" name="src" value="<?php echo $row2[2] ?>">
                        <input type="hidden" name="cid" value="<?php echo $row2[0] ?>">
                        <input type="hidden" name="remove" value="remove">
                        <button type="submit">remove</button>
                      </form>
                    </div>


                  </div>


                </div>
              <?php  }
            } else { ?>
              <img class="galleryimg" src="images/placeholder.png" />
            <?php
            } ?>
          </div>
      <?php
          $i++;
        }
      }
      ?>
      <form class="add" method="POST" action="?Country=<?php echo $Countryid; ?>&name=<?php echo $Countryname; ?>">
        <h2>Add Country</h2>
        <input type="hidden" name="cid" value="<?php echo $_GET['Country']; ?>">
        <label for="fname">Country Name</label><br>
        <input type="text" name="name" required><br><br>
        <label for="fname">Country ISO Code</label><br>
        <input type="text" name="iso" required><br><br>
        <input type="submit" name="submit" value="Submit">
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
        $i = 1;
        $sql = "SELECT `ID`, `ISO`, `Name` FROM `continent`";
        if ($result = $conn->query($sql)) {
          while ($row = $result->fetch_row()) {
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