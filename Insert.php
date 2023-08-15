<?php include "../inc/dbinfo.inc"; ?>
<html>
<body>
<h1>과목 추가</h1>
<?php

/* Connect to MySQL and select the database. */
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

$database = mysqli_select_db($connection, DB_DATABASE);

/* Ensure that the SUBJECT table exists. */
VerifySubjectTable($connection, DB_DATABASE);

/* If input fields are populated, add a row to the SUBJECT table. */
$subject_name = htmlentities($_POST['NAME']);
$professor = htmlentities($_POST['PROFESSOR']);
$time = htmlentities($_POST['TIME']);
$max_num = htmlentities($_POST['MAX_NUM']);

if (strlen($subject_name) || strlen($professor) || strlen($time) || strlen($max_num)) {
    AddSubject($connection, $subject_name, $professor, $time, $max_num);
}
?>

<!-- Input form -->
<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
<table border="0">
    <tr>
        <td>NAME</td>
        <td>PROFESSOR</td>
        <td>TIME</td>
        <td>MAX_NUM</td>
    </tr>
    <tr>
        <td><input type="text" name="NAME" maxlength="45" size="30" /></td>
        <td><input type="text" name="PROFESSOR" maxlength="45" size="30" /></td>
        <td><input type="text" name="TIME" maxlength="45" size="30" /></td>
        <td><input type="text" name="MAX_NUM" maxlength="45" size="30" /></td>
        <td><input type="submit" value="Add Data" /></td>
    </tr>
</table>
</form>

<!-- Display table data. -->
<table border="1" cellpadding="2" cellspacing="2">
<tr>
    <td>ID</td>
    <td>NAME</td>
    <td>PROFESSOR</td>
    <td>TIME</td>
    <td>MAX_NUM</td>
</tr>

<?php

$result = mysqli_query($connection, "SELECT * FROM SUBJECT");

while ($query_data = mysqli_fetch_row($result)) {
    echo "<tr>";
    echo "<td>", $query_data[0], "</td>",
    "<td>", $query_data[1], "</td>",
    "<td>", $query_data[2], "</td>",
    "<td>", $query_data[3], "</td>",
    "<td>", $query_data[4], "</td>";
    echo "</tr>";
}

?>

</table>

<!-- Clean up. -->
<?php

mysqli_free_result($result);
mysqli_close($connection);

?>

</body>
</html>


<?php

/* Add a subject to the table. */
function AddSubject($connection, $name, $professor, $time, $max_num) {
    $n = mysqli_real_escape_string($connection, $name);
    $p = mysqli_real_escape_string($connection, $professor);
    $t = mysqli_real_escape_string($connection, $time);
    $m = mysqli_real_escape_string($connection, $max_num);

    $query = "INSERT INTO SUBJECT (NAME, PROFESSOR, TIME, MAX_NUM) VALUES ('$n', '$p', '$t', '$m');";

    if (!mysqli_query($connection, $query)) echo("<p>Error adding subject data.</p>");
    else echo "<script>alert('과목이 추가되었습니다.');</script>";
}

/* Check whether the table exists and, if not, create it. */
function VerifySubjectTable($connection, $dbName) {
    if (!TableExists("SUBJECT", $connection, $dbName)) {
        $query = "CREATE TABLE SUBJECT (
            ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            NAME VARCHAR(45),
            PROFESSOR VARCHAR(45),
            TIME VARCHAR(45),
            MAX_NUM VARCHAR(45)
          )";

        if (!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
    }
}

/* Check for the existence of a table. */
function TableExists($tableName, $connection, $dbName) {
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $dbName);

    $checktable = mysqli_query($connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

    if (mysqli_num_rows($checktable) > 0) return true;

    return false;
}

?>