<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle Add Course
if (isset($_POST['add_course'])) {
    $code = strtoupper(trim($_POST['course_code']));
    $title = trim($_POST['course_title']);
    $instructor = trim($_POST['instructor']);

    if ($code && $title && $instructor) {
        $stmt = $conn->prepare("INSERT INTO courses (course_code, course_title, instructor) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $code, $title, $instructor);
        if ($stmt->execute()) {
            $msg = "<p style='color:green;'>Course added successfully!</p>";
        } else {
            $msg = "<p style='color:red;'>Error: Course code already exists!</p>";
        }
    } else {
        $msg = "<p style='color:red;'>All fields are required!</p>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM courses WHERE id=$id");
    header("Location: manage_courses.php");
    exit();
}

// Handle Edit
if (isset($_POST['update_course'])) {
    $id = intval($_POST['course_id']);
    $code = strtoupper(trim($_POST['course_code']));
    $title = trim($_POST['course_title']);
    $instructor = trim($_POST['instructor']);

    $stmt = $conn->prepare("UPDATE courses SET course_code=?, course_title=?, instructor=? WHERE id=?");
    $stmt->bind_param("sssi", $code, $title, $instructor, $id);
    $stmt->execute();
    header("Location: manage_courses.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Courses</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: white;
        padding: 20px;
    }
    h1 {
        text-align: center;
        color: #333;
    }
    form {
        background: white;
        padding: 20px;
        border-radius: 10px;
        max-width: 500px;
        margin: 30px auto;
        box-shadow: 0 12px 12px rgba(0,0,0,0.1);
    }
    label { display: block; margin: 10px 0 5px; font-weight: bold; }
    input {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        margin-bottom: 15px;
    }
    button {
        background: #007BFF;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
    }
    button:hover { background: #0056b3; }
    table {
        width: 90%;
        margin: 20px auto;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    th {
        background: #007BFF;
        color: white;
    }
    tr:nth-child(even) { background: #f2f2f2; }
    .actions a {
        margin-right: 8px;
        text-decoration: none;
        color: #007BFF;
    }
    .actions a:hover { text-decoration: underline; }
    .nav {
        text-align: center;
        margin-bottom: 20px;
    }
    .nav a {
        text-decoration: none;
        color: #007BFF;
        margin: 0 10px;
        font-weight: bold;
    }
    .nav a:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="header" style="text-align:center;">
    <img src="images/logo.png" alt="Institution Logo" style="width:200px;height:auto;margin-bottom:10px;">
    <h1>Admin - Manage Courses</h1>
</div>

<div class="nav">
   
    <a href="view_enrollments.php">
    <button class="btn-view">View Enrollments</button>
    </a>

    <a href="logout.php">
        <button class="btn-view">Logout</a>
</div>

<?php if (!empty($msg)) echo "<div style='text-align:center;'>$msg</div>"; ?>

<!-- Add or Edit Form -->
<?php
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM courses WHERE id=$id");
    $course = $result->fetch_assoc();
    ?>
    <form method="post">
        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
        <label>Course Code:</label>
        <input type="text" name="course_code" value="<?php echo $course['course_code']; ?>" required>

        <label>Course Title:</label>
        <input type="text" name="course_title" value="<?php echo $course['course_title']; ?>" required>

        <label>Instructor:</label>
        <input type="text" name="instructor" value="<?php echo $course['instructor']; ?>" required>

        <button type="submit" name="update_course">Update Course</button>
    </form>
    <?php
} else {
?>
<form method="post">
    <h3 style="text-align:center;">Add New Course</h3>
    <label>Course Code:</label>
    <input type="text" name="course_code" placeholder="e.g., IT102" required>

    <label>Course Title:</label>
    <input type="text" name="course_title" placeholder="e.g., Web Development Basics" required>

    <label>Instructor:</label>
    <input type="text" name="instructor" placeholder="e.g., Mr. Otieno" required>

    <button type="submit" name="add_course">Add Course</button>
</form>
<?php } ?>

<!-- List of Courses -->
<h3 style="text-align:center;">Available Courses</h3>
<table>
    <tr>
        <th>ID</th>
        <th>Course Code</th>
        <th>Course Title</th>
        <th>Instructor</th>
        <th>Actions</th>
    </tr>
    <?php
    $result = $conn->query("SELECT * FROM courses ORDER BY course_code ASC");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['course_code']}</td>
                    <td>{$row['course_title']}</td>
                    <td>{$row['instructor']}</td>
                    <td class='actions'>
                        <a href='edit_course.php?edit={$row['id']}'>Edit</a>
                        <a href='manage_courses.php?delete={$row['id']}' onclick='return confirm(\"Delete this course?\")'>Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='5' style='text-align:center;'>No courses available</td></tr>";
    }
    ?>
</table>

</body>
</html>
