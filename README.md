#Student Management System (PHP + MySQL)
---------------------------------------

🚀 ฟีเจอร์หลัก 
- เพิ่มข้อมูลนักเรียน 
- แก้ไขข้อมูลนักเรียน 
- ลบข้อมูลนักเรียน 
- ค้นหานักเรียนจาก ID, ชื่อ, เพศ, ปีที่เรียน, GPA 
- เรียงลำดับข้อมูล จาก น้อย → มาก หรือ มาก → น้อย 
- ใช้ Bootstrap 5 ในการจัดแต่ง UI 
- ใช้การเชื่อมต่อแบบ PDO ป้องกันการ SQL Injection 

🛠️ เทคโนโลยีที่ใช้ 
- PHP (สำหรับ Back-end)
- MySQL (สำหรับฐานข้อมูล)
- Bootstrap 5 (ออกแบบ UI)

สร้าง DB
CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `names` varchar(255) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `class_year` int(11) NOT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
