<?php include "../inc/dbinfo.inc"; ?>

<html>
<body>
<h1>Admin Page</h1>

<?php

$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

$database = mysqli_select_db($connection, DB_DATABASE);
VerifySubjectTable($connection, DB_DATABASE);

// Handle delete all data button click
if (isset($_POST['delete_all'])) {
    $query_mysub = "DELETE FROM MYSUB";
    $query_subject = "DELETE FROM SUBJECT";

    mysqli_query($connection, $query_mysub);
    mysqli_query($connection, $query_subject);
}

// Handle insert dummy data button click
if (isset($_POST['insert_dummy'])) {
    $dummyData = [
        ['네트워크', '김유택', '월,화 오전11:00~오후1:00', 30],
        ['리눅스', '김유택', '월,화 오후2:00~오후4:00', 30],
        ['데이터베이스', '김유택', '수,목 오전11:00~오후1:00', 30],
        ['파이썬', '김유택', '수,목 오후2:00~오후4:00', 30],
        ['가상화', '정철', '수,금 오후2:00~오후4:00', 25],
        ['쿠버네티스', 'dangtong', '수,목 오후4:00~오후6:00', 31],
        ['퍼블릭 클라우드', '유승현', '금 오전10:00~오후4:00', '30'],
        ['자동화', '신인철', '월,수,금 오전10:00~오후1:00', '30']
    ];

    foreach ($dummyData as $data) {
        $name = mysqli_real_escape_string($connection, $data[0]);
        $professor = mysqli_real_escape_string($connection, $data[1]);
        $time = mysqli_real_escape_string($connection, $data[2]);
        $maxNum = $data[3];

        $query = "INSERT INTO SUBJECT (NAME, PROFESSOR, TIME, MAX_NUM) VALUES ('$name', '$professor', '$time', '$maxNum')";
        mysqli_query($connection, $query);
    }
}

?>

<form method="POST">
    <input type="submit" name="delete_all" value="전체 삭제">
    <input type="submit" name="insert_dummy" value="더미 데이터 추가">
</form>

</body>
</html>

<?php

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

function TableExists($tableName, $connection, $dbName) {
    $t = mysqli_real_escape_string($connection, $tableName);
    $d = mysqli_real_escape_string($connection, $dbName);

    $checktable = mysqli_query($connection,
        "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

    if (mysqli_num_rows($checktable) > 0) return true;

    return false;
}
?>