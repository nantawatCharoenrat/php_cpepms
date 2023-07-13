<?php
session_start();
// เชื่อมต่อกับฐานข้อมูล MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cpepms";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_FILES["file"])) {
        $targetDir =  "data/";
        $targetFile = $targetDir . basename($_FILES["file"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // เช็คประเภทไฟล์
        if ($fileType != "csv") {
            $uploadOk = 0;
            $_SESSION['error'] = "ขออภัย อนุญาตเฉพาะไฟล์ CSV เท่านั้น";
            header("Location: ../uploadCSV.php");
            exit();
        }

        // เช็คค่าตัวแปร $uploadOk ว่ายังคงเป็น 1 หรือไม่
        if ($uploadOk == 0) {
            $_SESSION['error'] = "ขออภัย ไฟล์ไม่ได้ถูกอัปโหลด";
            header("Location: ../uploadCSV.php");
            exit();
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                // อ่านข้อมูลจากไฟล์ CSV
                $csvData = file_get_contents($targetFile);
                $rows = explode("\n", $csvData);
                $rows = preg_replace("/\r\n|\r|\n/", ' ', $rows);

                // วนลูปเพื่อบันทึกข้อมูลลงในฐานข้อมูล
                $i = 0;
                foreach ($rows as $row) {
                    $i++;
                    if ($i >= 2) {
                        $data = str_getcsv($row);
                        // $column1 เก็บ new_id
                        $column1 = $conn->quote($data[0]); // แก้ไขตามคอลัมน์ที่ต้องการ     
                        if (isset($data[1])) {
                            // $column2 เก็บ new_head
                            $column2 = $conn->quote($data[1]);
                        } else {
                            // การจัดการกรณีที่ไม่มีดัชนีที่ 1
                            // อาจจะกำหนดค่าเริ่มต้นหรือทำการสร้างข้อความว่างเปล่า
                            $column2 = "";
                        }
                        
                        if (isset($data[2])) {
                            // $column3 เก็บ new_text
                            $column3 = $conn->quote($data[2]);
                        } else {
                            // การจัดการกรณีที่ไม่มีดัชนีที่ 2
                            // อาจจะกำหนดค่าเริ่มต้นหรือทำการสร้างข้อความว่างเปล่า
                            $column3 = "";
                        }
                        

                        if (empty($data[0]) || $data[0][0] != "n" || strlen($data[0]) != 9) {
                            echo "<br>continue<br>";
                            continue;
                        }

                        // เตรียมคำสั่ง SQL
                        $sql = "INSERT INTO new (new_id, new_head, new_text, new_date) VALUES ($column1, $column2, $column3, CURRENT_TIMESTAMP);";

                        // เรียกใช้คำสั่ง SQL
                        if ($conn->exec($sql)) {
                            echo "บันทึกข้อมูลสำเร็จ<br>";
                        } else {
                            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
                            //header("Location: ../uploadCSV.php");
                            exit();
                        }
                    }
                }

                // ลบไฟล์ CSV หลังจากเสร็จสิ้นการอัปโหลด
                unlink($targetFile);
                $_SESSION['success'] = "บันทึกข้อมูลกำหนดการในรายวิชาสำเร็จ";
                //header("Location: ../uploadCSV.php");
                exit();
            }
        }
    }
} catch (PDOException $e) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage());
}
?>
