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
                if(true){
                $data = str_getcsv($row);
                // $column1 เก็บ project_id
                $column1 = mysqli_real_escape_string($conn, $data[0]);  // แก้ไขตามคอลัมน์ที่ต้องการ
                // $column2 เก็บ term
                $column2 = mysqli_real_escape_string($conn, $data[2]);  
                // $column3 เก็บ year
                $column3 = mysqli_real_escape_string($conn, $data[3]);  
                // $column4 เก็บชื่อภาษาไทย
                $column4 = mysqli_real_escape_string($conn, $data[4]);   
                // $column4 = str_replace('-','', $column4);
                // $column4 เก็บชื่อภาษาอังกฤษ
                $column5 = mysqli_real_escape_string($conn, $data[5]);    
                // $column6 เก็บ student_id1
                $column6 = mysqli_real_escape_string($conn, $data[6]);   
                $column6 = str_replace('-','', $column6);
                // $column7 เก็บ student_id2
                $column7 = mysqli_real_escape_string($conn, $data[9]);  
                $column7 = str_replace('-','', $column7);
                // $column8 เก็บ student_id3
                $column8 = mysqli_real_escape_string($conn, $data[12]);   
                $column8 = str_replace('-','', $column8); 
                // $column9 เก็บ teacher_id1
                $column9 = mysqli_real_escape_string($conn, $data[15]);  
                // $column10 เก็บ teacher_id2
                $column10 = mysqli_real_escape_string($conn, $data[16]);  
                // $column10 เก็บ referee_id
                $column11 = mysqli_real_escape_string($conn, $data[18]);  
                // $column10 เก็บ referee_id1
                $column12 = mysqli_real_escape_string($conn, $data[19]);  
                // $column10 เก็บ referee_id2
                $column13 = mysqli_real_escape_string($conn, $data[20]);  

                echo  $data[0]." ".$data[2]." ".$data[3]." ".$data[4]." ".$data[5]." ".$data[6]." ".$data[9]." ".$data[12]." ".$data[15]." ".$data[16]." ".$data[18]." ".$data[19]." ".$data[20];
                echo "-----<br>";
                if((empty( $data[0] ))){
                    echo "<br>continue<br>";
                    continue;
                } 
                if(($data[0][0] != "2") || ($data[0][1] != "5")){
                    echo "<br>continue<br>";
                    continue;
                }    
                //เตรียมคำสั่ง SQL
                if(empty( $data[9] )){
                    // project กลุ่ม1คน
                    if(empty( $data[16] )){
                        //ไม่มีที่ปรึกษาร่วม
                        $sql = "INSERT INTO project(project_id, project_nameTH, project_nameENG, student_id1, teacher_id1, referee_id, referee_id1, referee_id2, year, term)  VALUES ('$column1','$column4','$column5','$column6','$column9','$column11','$column12','$column13','$column3','$column2');";
                    }else{
                        //มีที่ปรึกษาร่วม
                        $sql = "INSERT INTO project(project_id, project_nameTH, project_nameENG, student_id1, teacher_id1, teacher_id2, referee_id, referee_id1, referee_id2, year, term)  VALUES ('$column1','$column4','$column5','$column6','$column9','$column10','$column11','$column12','$column13','$column3','$column2');";
                    }
                }else if(empty( $data[12] )){
                    //project กลุ่ม2คน
                    if(empty( $data[16] )){
                        //ไม่มีที่ปรึกษาร่วม
                        $sql = "INSERT INTO project(project_id, project_nameTH, project_nameENG, student_id1, student_id2, teacher_id1, referee_id, referee_id1, referee_id2, year, term)  VALUES ('$column1','$column4','$column5','$column6','$column7','$column9','$column11','$column12','$column13','$column3','$column2');";
                    }else{
                        //มีที่ปรึกษาร่วม
                        $sql = "INSERT INTO project(project_id, project_nameTH, project_nameENG, student_id1, student_id2, teacher_id1, teacher_id2, referee_id, referee_id1, referee_id2, year, term)  VALUES ('$column1','$column4','$column5','$column6','$column7','$column9','$column10','$column11','$column12','$column13','$column3','$column2');";
                    }
                }else {
                    //project กลุ่ม3คน
                    if(empty( $data[16] )){
                        //ไม่มีที่ปรึกษาร่วม
                        $sql = "INSERT INTO project(project_id, project_nameTH, project_nameENG, student_id1, student_id2, student_id3, teacher_id1, referee_id, referee_id1, referee_id2, year, term)  VALUES ('$column1','$column4','$column5','$column6','$column7','$column8','$column9','$column11','$column12','$column13','$column3','$column2');";
                    }else{
                        //มีที่ปรึกษาร่วม
                        $sql = "INSERT INTO project(project_id, project_nameTH, project_nameENG, student_id1, student_id2, student_id3, teacher_id1, teacher_id2, referee_id, referee_id1, referee_id2, year, term)  VALUES ('$column1','$column4','$column5','$column6','$column7','$column8','$column9','$column10','$column11','$column12','$column13','$column3','$column2');";
                    }
                }

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
