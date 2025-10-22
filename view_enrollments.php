<?php
// view_enrollments.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "online_courses";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM enrollments ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Enrollments</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 40px;
            background-color: #f4f7fa;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: center;
        }
        th {
            background-color: #0078d7;
            color: white;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn-back {
            display: block;
            margin: 20px auto;
            background: #0078d7;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-back:hover {
            background: #005fa3;
        }
    </style>
</head>
<body>

    <h2>All Enrolled Students</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Student Name</th>
            <th>Email</th>
            <th>Course Code</th>
            <th>Course Title</th>
            <th>Instructor</th>
            <th>Semester</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['course_code']) ?></td>
                    <td><?= htmlspecialchars($row['course_title']) ?></td>
                    <td><?= htmlspecialchars($row['instructor']) ?></td>
                    <td><?= htmlspecialchars($row['semester']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No enrollments found.</td></tr>
        <?php endif; ?>
    </table>

    <a href="admin_dashboard.php"><button class="btn-back">Back to Dashboard</button></a>

</body>
</html>
