<?php
require "connect.php";
if (!$con) {
    echo "connection error";
}
$email = $_POST['email'];
$password = $_POST['password'];
// $encrypted_pwd = md5($password);
$sql = "SELECT * FROM student WHERE student_id = '".$email."' AND student_pass = '".$password."' ;";
$result = mysqli_query($con, $sql);
$count = mysqli_num_rows($result);

if ($count == 1) {
    echo json_encode("Success");
} else {
    $sql2 = "SELECT * FROM teacher WHERE teacher_id = '".$email."' AND teacher_pass = '".$password."' ;";
    $result2 = mysqli_query($con, $sql2);
    $count2 = mysqli_num_rows($result2);
    if ($count2 == 1) {
        echo json_encode("Success");
    } else {
        echo json_encode("Error");
    }
}
