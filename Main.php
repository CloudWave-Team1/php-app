<?php include "../inc/dbinfo.inc"; ?>

<html>
<body>
<h1>수강신청 시스템</h1>
<?php

$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

$database = mysqli_select_db($connection, DB_DATABASE);

VerifySubjectTable($connection, DB_DATABASE);
VerifyMySubTable($connection, DB_DATABASE);

if (isset($_POST['selected_subjects'])) {
    foreach ($_POST['selected_subjects'] as $subjectID) {
        $subjectID = mysqli_real_escape_string($connection, $subjectID);

        // Check if the SUBJECT_ID is already in MYSUB
        $checkDuplicate = mysqli_query($connection, "SELECT * FROM MYSUB WHERE SUBJECT_ID = '$subjectID'");
        if (mysqli_num_rows($checkDuplicate) > 0) {
            echo "<script>alert('이미 신청된 과목입니다.');</script>";
            continue;
        }

        $query = "INSERT INTO MYSUB (SUBJECT_ID) VALUES ('$subjectID')";
        mysqli_query($connection, $query);
    }
}

?>

<button onclick="location.href='Insert.php'">과목 추가</button>
<button onclick="location.href='Admin.php'">과목 삭제 및 더미데이터 추가</button>

<br><hr><br>

<!-- Display SUBJECT table data. -->
<form method="POST">
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>SELECT</td>
    <td>NAME</td>
    <td>PROFESSOR</td>
    <td>TIME</td>
    <td>MAX_NUM</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM SUBJECT");

while ($query_data = mysqli_fetch_row($result)) {
    echo "<tr>";
    echo "<td><input type='checkbox' name='selected_subjects[]' value='", $query_data[0], "' /></td>";
    for ($i = 1; $i < count($query_data); $i++) {
        echo "<td>", $query_data[$i], "</td>";
    }
    echo "</tr>";
}

?>
  <tr>
    <td colspan="5"><input type="submit" value="신청하기"></td>
  </tr>
</table>
</form>

<br><hr><br>

<h2>신청 과목 조회</h2>
<!-- Display MYSUB JOINed with SUBJECT table data. -->
<table border="1" cellpadding="2" cellspacing="2">
  <tr>
    <td>MY_ID</td>
    <td>NAME</td>
    <td>PROFESSOR</td>
    <td>TIME</td>
    <td>MAX_NUM</td>
  </tr>

<?php

$result = mysqli_query($connection, "SELECT M.MY_ID, S.NAME, S.PROFESSOR, S.TIME, S.MAX_NUM FROM MYSUB M JOIN SUBJECT S ON M.SUBJECT_ID = S.ID");

while ($query_data = mysqli_fetch_row($result)) {
    echo "<tr>";
    for ($i = 0; $i < count($query_data); $i++) {
        echo "<td>", $query_data[$i], "</td>";
    }
    echo "</tr>";
}

mysqli_free_result($result);
mysqli_close($connection);
?>

</table>
</body>
</html>

<?php
/* Function definitions */

function VerifySubjectTable($connection, $dbName) {
    if (!TableExists("SUBJECT", $connection, $dbName)) {
        $query = "CREATE TABLE SUBJECT (
            ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            NAME VARCHAR(45),
            PROFESSOR VARCHAR(45),
            TIME VARCHAR(45),
            MAX_NUM VARCHAR(45)
          )";

        if (!mysqli_query($connection, $query)) echo("<p>Error creating SUBJECT table.</p>");
    }
}

function VerifyMySubTable($connection, $dbName) {
    if (!TableExists("MYSUB", $connection, $dbName)) {
        $query = "CREATE TABLE MYSUB (
            MY_ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            SUBJECT_ID int(11) UNSIGNED,
            FOREIGN KEY (SUBJECT_ID) REFERENCES SUBJECT(ID)
          )";

        if (!mysqli_query($connection, $query)) echo("<p>Error creating MYSUB table.</p>");
    }
}

function TableExists($tableName, $connection, $dbName) {
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $dbName);

    $checktable = mysqli_query($connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

    if (mysqli_num_rows($checktable) > 0) return true;

    return false;
}
?>