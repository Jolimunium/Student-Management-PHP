<?php
include 'connect.php';

// ตัวแปรสำหรับ search
$searchQuery = "";
$conditions = [];
$params = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $names = $_POST['names'];
        $gender = $_POST['gender'];
        $class_year = (int)$_POST['class_year'];
        $gpa = (float)$_POST['gpa'];

        $stmt = $conn->prepare("INSERT INTO students (names, gender, class_year, gpa) VALUES (:names,:gender,:class_year,:gpa)");
        $stmt->bindParam(':names', $names);
        $stmt->bindParam(':gender', $gender);      
        $stmt->bindParam(':class_year', $class_year);
        $stmt->bindParam(':gpa', $gpa);      
        if ($stmt->execute()) {
            echo "เพิ่มข้อมูลสำเร็จ";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "เกิดข้อผิดพลาด: " . $errorInfo[2]; 
        }
    }
    if(isset($_POST['edit_form'])){
        $id=$_POST['id'];

        $stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if($stmt->execute()){
            $editRow = $stmt->fetch(PDO::FETCH_ASSOC);
            $Edit_id = $editRow['id'];
            $Edit_names = $editRow['names'];
            $Edit_gender = $editRow['gender'];
            $Edit_class_year = $editRow['class_year'];
            $Edit_gpa = $editRow['gpa'];
        }else{
            $errorInfo = $stmt->errorInfo();
            echo "เกิดข้อผิดพลาด: " . $errorInfo[2]; 
        }
    }
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $names = $_POST['names'];
        $gender = $_POST['gender'];
        $class_year = (int)$_POST['class_year'];
        $gpa = (float)$_POST['gpa'];

        $stmt = $conn->prepare("UPDATE students SET names = :names, gender = :gender, class_year = :class_year, gpa = :gpa WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':names', $names);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':class_year', $class_year);
        $stmt->bindParam(':gpa', $gpa);
        if ($stmt->execute()) {
            echo "แก้ไขข้อมูลสำเร็จ";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "เกิดข้อผิดพลาด: " . $errorInfo[2]; 
        }
    }
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM students WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            echo "ลบข้ออมูลสำเร็จ";
        } else {
            $errorInfo = $stmt->errorInfo();
            echo "เกิดข้อผิดพลาด: " . $errorInfo[2];  
        }
    }

    // ค้นหานักเรียน
    if (isset($_POST['search'])) {
        $names = $_POST['names'];
        $gender = $_POST['gender'];
        $class_year = $_POST['class_year'];
        $gpa = $_POST['gpa'];
        
        if (!empty($names)) {
            $conditions[] = "names LIKE ?";
            $params[] = "%$names%";
        }
    
        if (!empty($gender)) {
            $conditions[] = "gender = ?";
            $params[] = $gender;
        }
    
        if (!empty($class_year)) {
            $conditions[] = "class_year = ?";
            $params[] = (int)$class_year;
        }
    
        if (!empty($gpa)) {
            if($gpa == '3'){
                $conditions[] = "gpa >= ? AND gpa < ?";
                $params[] = (float)$gpa;
                $params[] = (float)4;
            }else if($gpa == '2'){
                $conditions[] = "gpa >= ? AND gpa < ?";
                $params[] = (float)$gpa;
                $params[] = (float)3;
            }else if($gpa == '1'){
                $conditions[] = "gpa >= ? AND gpa < ?";
                $params[] = (float)$gpa;
                $params[] = (float)2;
            }else if($gpa == '4'){
                $conditions[] = "gpa = ?";
                $params[] = (float)4;
            }
        }
    
        if (count($conditions) > 0) {
            $searchQuery = "WHERE " . implode(" AND ", $conditions);
        }
    }
}

$sql = "SELECT * FROM students $searchQuery";
$stmt = $conn->prepare($sql);  

if(count($params)>0){
    $stmt->execute($params); // ดึงข้อมูลพร้อมค้นหา
} else {
    $stmt->execute(); // ดึงข้อมูลเฉยๆ ไม่มีการค้นหา
}

// เอาไว้ตรวจสอบคำสั่ง sql
// var_dump($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        
        <!-- ฟอร์มเพิ่มข้อมูล และ แก้ไขข้อมูล-->
        <h2 class="mb-4">Add Data Student</h2>
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col">
                    <input type="text" name="names" class="form-control" placeholder="Name" value="<?= isset($Edit_names) ? $Edit_names : '' ?>">
                </div>
                <div class="col">
                    <select name="gender" class="form-control">
                        <option value="">Gender</option>
                        <option value="Male" <?= isset($Edit_gender) && $Edit_gender == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= isset($Edit_gender) && $Edit_gender == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="col">
                    <select name="class_year" class="form-control">
                        <option value="">Class Year</option>
                        <option value="4" <?= isset($Edit_class_year) && $Edit_class_year == 4 ? 'selected' : '' ?>>Year 4</option>
                        <option value="3" <?= isset($Edit_class_year) && $Edit_class_year == 3 ? 'selected' : '' ?>>Year 3</option>
                        <option value="2" <?= isset($Edit_class_year) && $Edit_class_year == 2 ? 'selected' : '' ?>>Year 2</option>
                        <option value="1" <?= isset($Edit_class_year) && $Edit_class_year == 1 ? 'selected' : '' ?>>Year 1</option>
                    </select>
                </div>
                <div class="col">
                    <input type="text" name="gpa" class="form-control" placeholder="GPA" value="<?= isset($Edit_gpa) ? $Edit_gpa : '' ?>">
                </div>

                <div class="col">
                    <?php
                        if (!empty($Edit_id)) {
                            echo "<input type='hidden' name='id' value='" . $Edit_id . "'>"; 
                            echo "<button type='submit' name='edit' class='btn btn-primary px-4'>Save</button>";
                        } else {
                            echo "<button type='submit' name='add' class='btn btn-success px-4'>Add</button>";
                        }
                    ?>
                </div>
            </div>
        </form>

        <!-- ฟอร์มค้นหาข้อมูล -->
        <h2 class="mb-4">Search Data Student</h2>
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col">
                    <input type="text" name="names" class="form-control" placeholder="Name" value="<?= isset($names) ? $names : '' ?>">
                </div>
                <div class="col">
                    <select name="gender" class="form-control">
                        <option value="">Gender</option>
                        <option value="Male" <?= isset($gender) && $gender == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= isset($gender) && $gender == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="col">
                    <select name="class_year" class="form-control">
                        <option value="">Class Year</option>
                        <option value="4" <?= isset($class_year) && $class_year == 4 ? 'selected' : '' ?>>Year 4</option>
                        <option value="3" <?= isset($class_year) && $class_year == 3 ? 'selected' : '' ?>>Year 3</option>
                        <option value="2" <?= isset($class_year) && $class_year == 2 ? 'selected' : '' ?>>Year 2</option>
                        <option value="1" <?= isset($class_year) && $class_year == 1 ? 'selected' : '' ?>>Year 1</option>
                    </select>
                </div>
                <div class="col">
                    <select name="gpa" class="form-control">
                        <option value="">GPA</option>
                        <option value="4" <?= isset($gpa) && $gpa == '4' ? 'selected' : '' ?>>GPA = 4</option>
                        <option value="3" <?= isset($gpa) && $gpa == '3' ? 'selected' : '' ?>>GPA >= 3 And GPA &lt;4 </option>
                        <option value="2" <?= isset($gpa) && $gpa == '2' ? 'selected' : '' ?>>GPA >= 2 And GPA &lt;3 </option>
                        <option value="1" <?= isset($gpa) && $gpa == '1' ? 'selected' : '' ?>>GPA >= 1 And GPA &lt;2 </option>
                    </select>
                </div>

                <div class="col">
                    <button type="submit" name="search" class="btn btn-primary px-4">Search</button>
                </div>
            </div>
        </form>

        <!-- ตารางแสดงข้อมูลนักเรียน -->
        <h2 class="mt-5">Student Data</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th data-column="id" onclick="sortTable('id')" style="cursor: pointer;">ID</th>
                    <th data-column="names" onclick="sortTable('names')" style="cursor: pointer;">Name</th>
                    <th data-column="gender" onclick="sortTable('gender')" style="cursor: pointer;">Gender</th>
                    <th data-column="class_year" onclick="sortTable('class_year')" style="cursor: pointer;">Class Year</th>
                    <th data-column="gpa" onclick="sortTable('gpa')" style="cursor: pointer;">GPA</th>
                    <th data-column="created_at" onclick="sortTable('created_at')" style="cursor: pointer;">Created At</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($stmt->rowCount() > 0) { // ตรวจสอบว่ามีข้อมูลไหม
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>
                        <td>" . $row['id'] . "</td>
                        <td>" . $row['names'] . "</td>
                        <td>" . $row['gender'] . "</td>
                        <td>" . $row['class_year'] . "</td>
                        <td>" . $row['gpa'] . "</td>
                        <td>" . $row['created_at'] . "</td>
                        <td>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='".$row['id']."'>
                                <button type='sumbit' name='edit_form' class='btn btn-primary w-100'>Edit</button>
                            </form>
                        </td>
                        <td>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='id' value='".$row['id']."'>
                                <button type='sumbit' name='delete' class='btn btn-danger w-100'>Delete</button>
                            </form>
                        </td>
                    </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No results found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        function sortTable(column) {
            let table = document.querySelector("table");
            let tbody = table.querySelector("tbody");
            // ดึงมาทั้งหมดแล้วแปลงเป็น array 
            let rows = Array.from(tbody.querySelectorAll("tr"));
            let header = document.querySelector(`th[data-column="${column}"]`);
            let order = header.getAttribute("data-order") === "asc" ? "desc" : "asc";
            
            rows.sort((a, b) => {
                let valA = a.querySelector(`td:nth-child(${header.cellIndex + 1})`).innerText.trim();
                let valB = b.querySelector(`td:nth-child(${header.cellIndex + 1})`).innerText.trim();

                // ตรวจสอบว่าค่าเป็นตัวเลขไหม
                if (!isNaN(valA) && !isNaN(valB)) {
                    valA = parseFloat(valA);
                    valB = parseFloat(valB);
                }

                return (order === "asc") ? (valA > valB ? 1 : -1) : (valA < valB ? 1 : -1);
            });

            tbody.innerHTML = "";
            rows.forEach(row => tbody.appendChild(row));

            // อัปเดตสถานะการเรียง
            document.querySelectorAll("th").forEach(th => th.removeAttribute("data-order"));
            header.setAttribute("data-order", order);
        }
    </script>

            
</body>

</html>
