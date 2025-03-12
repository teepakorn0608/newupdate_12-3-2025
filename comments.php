<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "comment_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// =============================
// เพิ่มคอมเมนต์
// =============================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
    $username = $_POST['username'] ?: 'Anonymous';
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
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
        echo "<p style='color: red;'>Please enter a comment!</p>";
    }
}

// =============================
// ลบคอมเมนต์
// =============================
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
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Comment System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { margin-bottom: 20px; }
        textarea { width: 100%; height: 80px; }
        .comment { padding: 10px; border: 1px solid #ccc; margin-bottom: 10px; }
        .delete-btn { color: red; border: none; background: none; cursor: pointer; }
    </style>
</head>
<body>

<h1>Comment System</h1>

<!-- ฟอร์มเพิ่มคอมเมนต์ -->
<form action="" method="post">
    <input type="text" name="username" placeholder="Your name (optional)">
    <textarea name="comment" placeholder="Write your comment here..." required></textarea>
    <button type="submit" name="submit_comment">Submit Comment</button>
</form>

<hr>

<h2>Comments:</h2>

<?php
// =============================
// ดึงคอมเมนต์จากฐานข้อมูล
// =============================
$sql = "SELECT * FROM comments ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='comment'>";
        echo "<h3>" . htmlspecialchars($row['username']) . "</h3>";
        echo "<p>" . nl2br(htmlspecialchars($row['comment'])) . "</p>";
        echo "<small>Posted on " . $row['created_at'] . "</small>";

        // ปุ่มลบคอมเมนต์
        echo "<form action='' method='post' style='margin-top:10px;'>";
        echo "<input type='hidden' name='comment_id' value='" . $row['id'] . "'>";
        echo "<button type='submit' name='delete_comment' class='delete-btn' onclick='return confirm(\"Are you sure?\")'>Delete</button>";
        echo "</form>";

        echo "</div>";
    }
} else {
    echo "<p>No comments yet. Be the first to comment!</p>";
}

$conn->close();
?>

</body>
</html>
