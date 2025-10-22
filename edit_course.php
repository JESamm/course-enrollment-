<?php
// edit_course.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "online_courses";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

// Fetch the course details
$course = $conn->query("SELECT * FROM courses WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['course_code'];
    $title = $_POST['course_title'];
    $instructor = $_POST['instructor'];

    $stmt = $conn->prepare("UPDATE courses SET course_code=?, course_title=?, instructor=? WHERE id=?");
    $stmt->bind_param("sssi", $code, $title, $instructor, $id);

    if ($stmt->execute()) {
        header("Location: admin_manage_courses.php?updated=1");
        exit;
    } else {
        echo "<p style='color:red;'>Error updating course!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 50px;
            background-color: #f8fafc;
        }
        form {
            width: 400px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #0078d7;
        }
        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        button {
            background-color: #0078d7;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #005fa3;
        }
    </style>
</head>
<body>

<h2>Edit Course</h2>

<form method="POST">
    <label>Course Code:</label>
    <input type="text" name="course_code" value="<?= htmlspecialchars($course['course_code']) ?>" required>

    <label>Course Title:</label>
    <input type="text" name="course_title" value="<?= htmlspecialchars($course['course_title']) ?>" required>

    <label>Instructor:</label>
    <input type="text" name="instructor" value="<?= htmlspecialchars($course['instructor']) ?>" required>

    <button type="submit">Update Course</button>
</form>

</body>
</html>
