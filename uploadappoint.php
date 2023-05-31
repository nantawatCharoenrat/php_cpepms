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
            foreach ($rows as $row) {
                $i++;
                if($i>=2){
                $data = str_getcsv($row);
                // $column1 เก็บ appoint_id
                $column1 = $conn->real_escape_string($data[0]);  // แก้ไขตามคอลัมน์ที่ต้องการ
                // $column2 เก็บ title
                $column2 = $conn->real_escape_string($data[1]); 
                // $column3 เก็บ description
                $column3 = $conn->real_escape_string($data[2]);
                // $column4 เก็บ group_id
                $column4 = $conn->real_escape_string($data[3]);
                
                
                echo  $column1." ".$column2." ".$column3." ".$column4;
                echo "-----<br>";
                echo  $data[0]." ".$data[1]." ".$data[2]." ".$data[3];
                echo "-----<br>";
                if((empty( $data[0] ))){
                    echo "<br>continue<br>";
                    continue;
                } 
                if(($data[0][0] != "a") || ($data[0][1] != "p")){
                    echo "<br>continue<br>";
                    continue;
                }    
                //เตรียมคำสั่ง SQL
                $sql = "INSERT INTO appoint(appoint_id, title, description, group_id)  VALUES ('$column1','$column2','$column3','$column4');";

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
