<?php
// Include your database connection code here
include('../config.php');
session_start();

// Maximum file size in bytes (100MB)
$maxFileSize = 100 * 1024 * 1024;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user = $_SESSION['user_id'];
    $redirect = $_SERVER['HTTP_REFERER'];
    $documentName = $_POST['name'];
    $folderID = $_POST['folder'];
    $documentDesc = $_POST['desc'];

    // Find folder info
    $sql1 = "SELECT * FROM folders WHERE folder_id = ? AND folder_user = ?";
    $stmt = $conn->prepare($sql1);
    $stmt->bind_param("ii", $folderID, $user);
    $stmt->execute();
    $result1 = $stmt->get_result();

    if ($result1->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $folderName = $row1['folder_name'];

        // Set upload path based on user ID
        $uploadDir = '../folders_list/' . $user . '/' . $folderName . '/';
        
        // Create folder if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadFile = $uploadDir . basename($_FILES['file']['name']);

        // Check file size
        if ($_FILES['file']['size'] > $maxFileSize) {
            echo "File is too large. Max size is 100MB.";
        } else {
            // Move file to the correct folder
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                // Save file info to database
                $sql2 = "INSERT INTO documents (doc_user, doc_name, doc_folder, doc_desc, doc_path, doc_size) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("isissi", $user, $documentName, $folderID, $documentDesc, $uploadFile, $_FILES['file']['size']);

                if ($stmt2->execute()) {
                    $_SESSION['doc_upload'] = "success";
                    header("location: $redirect");
                    exit();
                } else {
                    echo "Error inserting document in database: " . $stmt2->error;
                }

                $stmt2->close();
            } else {
                echo "Error uploading file.";
            }
        }
    } else {
        echo "Folder not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
