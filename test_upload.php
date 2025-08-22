<?php
if (move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/" . $_FILES["file"]["name"])) {
    echo "Upload OK!";
} else {
    echo "Upload FAILED!";
}
?>
<form method="post" enctype="multipart/form-data">
  <input type="file" name="file">
  <input type="submit" value="Upload Test">
</form>
