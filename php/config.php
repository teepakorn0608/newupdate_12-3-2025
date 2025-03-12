<?php
// =======================
// เชื่อมต่อฐานข้อมูล
// =======================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comment_system";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// =======================
// เพิ่มคอมเมนต์
// =======================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    $username = trim($_POST['username']);
    $comment = trim($_POST['comment']);

    if (!empty($username) && !empty($comment)) {
        $sql = "INSERT INTO comments (username, comment) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $comment);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Please fill out all fields!";
    }
}

// =======================
// ลบคอมเมนต์
// =======================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_comment'])) {
    $comment_id = $_POST['comment_id'];

    if (!empty($comment_id)) {
        $sql = "DELETE FROM comments WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $comment_id);

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error deleting comment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Invalid comment ID!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Comments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { margin-bottom: 20px; }
        textarea { width: 100%; height: 100px; }
        .comment { padding: 10px; border: 1px solid #ccc; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>User Comments</h1>

    <!-- ฟอร์มเพิ่มคอมเมนต์ -->
    <form action="" method="post">
        <input type="text" name="username" placeholder="Your name" required>
        <textarea name="comment" placeholder="Write your comment..." required></textarea>
        <button type="submit" name="submit_comment">Submit Comment</button>
    </form>

    <hr>

    <h2>Comments:</h2>

    <?php
    // =======================
    // ดึงคอมเมนต์มาแสดง
    // =======================
    $sql = "SELECT * FROM comments ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='comment'>";
            echo "<h3>" . htmlspecialchars($row['username']) . "</h3>";
            echo "<p>" . nl2br(htmlspecialchars($row['comment'])) . "</p>";
            echo "<small>Posted on " . $row['created_at'] . "</small>";

            // ฟอร์มลบคอมเมนต์
            echo "<form action='' method='post' style='margin-top:10px;'>";
            echo "<input type='hidden' name='comment_id' value='" . $row['id'] . "'>";
            echo "<button type='submit' name='delete_comment' onclick='return confirm(\"Are you sure?\")'>Delete Comment</button>";
            echo "</form>";

            echo "</div>";
        }
    } else {
        echo "<p>No comments yet. Be the first!</p>";
    }

    $conn->close();
    ?>
</body>
</html>
