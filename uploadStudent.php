<?php
// เชื่อมต่อกับฐานข้อมูล MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cpepms";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}
mysqli_set_charset($conn, "utf8");

if (isset($_FILES["file"])) {
    $targetDir = "data/";
    $targetFile = $targetDir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // เช็คประเภทไฟล์
    if ($fileType != "csv") {
        echo "ขออภัย, อนุญาตเฉพาะไฟล์ CSV เท่านั้น.";
        $uploadOk = 0;
    }

    // เช็คค่าตัวแปร $uploadOk ว่ายังคงเป็น 1 หรือไม่
    if ($uploadOk == 0) {
        echo "ขออภัย, ไฟล์ไม่ได้ถูกอัปโหลด.";
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            // อ่านข้อมูลจากไฟล์ CSV
            $csvData = file_get_contents($targetFile);
            $rows = explode("\n", $csvData);
            $rows = preg_replace("/\r\n|\r|\n/", ' ', $rows); 
            // วนลูปเพื่อบันทึกข้อมูลลงในฐานข้อมูล
            $i = 0 ;
            $prefix = array("นาย","นางสาว","นาง");
            foreach ($rows as $row) {
                $i++;
                    // $column5 เก็บ year
                    $column5 = $_POST["numberYear"];
                    // $column6 เก็บ term
                    $column6 = $_POST["numberTerm"];
                
                if($i>=8){
                $data = str_getcsv($row);
                // $column1 เก็บ student_id
                $column1 = $conn->real_escape_string($data[1]);
                $column1 = str_replace('-','', $column1);  // แก้ไขตามคอลัมน์ที่ต้องการ
                // $column2 เก็บ student_pass
                $column2 = $column1[8].$column1[9].$column1[10].$column1[11].$column1[12].$column1[13];
                // Full name เก็บ Full name
                $fullName = $conn->real_escape_string($data[2]);
                // Full name เก็บ Full name
                $fullName = str_replace($prefix,"",$fullName);
                $fullNames = explode(" ",$fullName);
                // $column3 เก็บ name
                $column3 = $fullNames[0];
                // $column4 เก็บ lastname
                $column4 = $fullNames[1];
                // $column7 เก็บ email
                $column7 = $column1."@mail.rmutt.ac.th";

                echo  $column1." ".$column2." ".$column3." ".$column4;
                echo "-----<br>";
                echo  $data[1]." ".$data[2];
                echo "-----<br>";
                if((empty( $data[0] ))){
                    echo "<br>continue<br>";
                    continue;
                } 
                if(strlen($column1) != 13){
                    echo "<br>continue<br>";
                    continue;
                }    
                //เตรียมคำสั่ง SQL
                $sql = "INSERT INTO student(student_id, student_pass, name, lastname, year, term, email)  VALUES ('$column1','$column2','$column3','$column4','$column5','$column6','$column7');";

                    // เรียกใช้คำสั่ง SQL
                    if ($conn->query($sql) === TRUE) {
                        echo "บันทึกข้อมูลสำเร็จ<br>";
                    } else {
                        echo "เกิดข้อผิดพลาด";
                    }
                }
            }
        }
    }
}
