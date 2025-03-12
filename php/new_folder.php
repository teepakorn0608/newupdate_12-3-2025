<?php
include '../config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the folder name from the form
    $folderName = $_POST["folder"];
    $user = $_SESSION['user_id'];
    $redirect = $_SERVER['HTTP_REFERER'];

    // Define the directory where you want to create the folder (organized by user)
    $directory = "../folders_list/" . $user . "/";
    $folderPath = $directory . $folderName;

    // Create user directory if not exists
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    // Create the folder without checking for duplicates
    if (mkdir($folderPath, 0777, true)) {
        // Folder created successfully, now insert into the database
        $sql = "INSERT INTO folders (folder_user, folder_name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user, $folderName);

        if ($stmt->execute()) {
            $_SESSION['new_folder_created'] = "success";
            header("location: $redirect");
        } else {
            echo "Error creating folder in database: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error creating folder on server.";
    }

    $conn->close();
}
?>
