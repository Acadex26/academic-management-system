<?php
include('backend/config/db.php');

$result = $conn->query("SELECT * FROM students");

while($row = $result->fetch_assoc()) {
    echo $row['name'] . " - " . $row['student_uid'] . "<br>";
}
?>