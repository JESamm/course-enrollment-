<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Course Enrollment Portal</title>
<style>
    body {
        
        background-image: url("background.jpg")
        font-family: Arial, sans-serif;
        background: white;
        margin: 0;
        padding: 20px;
    }
    h1, h2 { text-align: center; color: #333; }
    form {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        max-width: 600px;
        margin: 20px auto;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    label { display: block; margin: 10px 0 5px; font-weight: bold; }
    input, select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }
    button {
        background: #007BFF;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
    }
    button:hover { background: #0056b3; }
    .message { text-align: center; font-weight: bold; }
    .count-box {
        background: #fff;
        padding: 15px;
        margin: 30px auto;
        max-width: 300px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        font-size: 18px;
    }
    .header {
    text-align: center;
    margin-bottom: 20px;
}
.header {
    text-align: center;
    margin-bottom: 20px;
}
.logo {
    width: 200px;
    height: auto;
    margin-bottom: 10px;
}

</style>
</head>
<body>
<div class="header">
    <img src="logo.png" alt="Institution Logo" class="logo">
    <h1>Online Course Enrollment</h1>
</div>
<h2>Enroll in a Course</h2>




<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name       = trim($_POST['student_name']);
    $email      = trim($_POST['email']);
    $code       = trim($_POST['course_code']);
    $title      = trim($_POST['course_title']);
    $instructor = trim($_POST['instructor']);
    $semester   = trim($_POST['semester']);

    // Check for duplicate registration
    $check = $conn->prepare("SELECT * FROM enrollments WHERE email=? AND course_code=?");
    $check->bind_param("ss", $email, $code);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<p class='message' style='color:red;'>You are already enrolled in this course.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO enrollments (student_name, email, course_code, course_title, instructor, semester) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $code, $title, $instructor, $semester);
        if ($stmt->execute()) {
            echo "<p class='message' style='color:green;'>Enrollment successful!</p>";
        } else {
            echo "<p class='message' style='color:red;'>Error: " . $conn->error . "</p>";
        }
    }
}
?>

<form method="post" action="">
    <label>Student Name:</label>
    <input type="text" name="student_name" required>

    <label>Email:</label>
    <input type="email" name="email" required>
    <label>Available Courses:</label>
<select name="course_code" required>
    <option value="">-- Select a Course --</option>
    <?php
    $courses = $conn->query("SELECT * FROM courses ORDER BY course_code ASC");
    while ($course = $courses->fetch_assoc()) {
        echo "<option value='{$course['course_code']}' data-title='{$course['course_title']}' data-instructor='{$course['instructor']}'>
                {$course['course_code']} - {$course['course_title']} ({$course['instructor']})
              </option>";
    }
    ?>
</select>

<!-- Hidden fields to store course title and instructor -->
<input type="hidden" name="course_title" id="course_title">
<input type="hidden" name="instructor" id="instructor">

<script>
document.querySelector("select[name='course_code']").addEventListener("change", function() {
    let selected = this.options[this.selectedIndex];
    document.getElementById("course_title").value = selected.getAttribute("data-title");
    document.getElementById("instructor").value = selected.getAttribute("data-instructor");
});
</script>


    <label>Semester:</label>
    <select name="semester" required>
        <option value="">-- Select Semester --</option>
        <option>Semester 1</option>
        <option>Semester 2</option>

    </select>

    <button type="submit">Enroll</button>
</form>

<?php
$countResult = $conn->query("SELECT COUNT(*) AS total FROM enrollments");
$row = $countResult->fetch_assoc();
$total = $row['total'];
?>
<div class="count-box">
    Total Registered Students: <strong><?php echo $total; ?></strong>
</div>

<p style="text-align:center;">Admin? <a href="admin_login.php">Login here</a></p>

</body>
</html>


