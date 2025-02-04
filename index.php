<?php
session_start();
include 'config.php'; // Assuming you have a database configuration file

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);

// If logged in, fetch user profile
$user_profile = null;
if ($user_logged_in) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_profile = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $target_dir = "uploads/";  // Directory to store uploaded images
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is an image
    if (getimagesize($_FILES["image"]["tmp_name"]) === false) {
        $uploadOk = 0;
        echo "File is not an image.";
    }

    // Check file size (optional)
    if ($_FILES["image"]["size"] > 500000) {
        $uploadOk = 0;
        echo "Sorry, your file is too large.";
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadOk = 0;
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    // Try to upload the file
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["image"]["name"]) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinterest Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .navbar {
            padding: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: #e60023;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: #111;
            padding: 8px 16px;
            border-radius: 20px;
        }

        .nav-links a:hover {
            background: #e9e9e9;
        }

        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .auth-buttons a {
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
        }

        .login {
            color: #111;
        }

        .signup {
            background: #e60023;
            color: white;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #111;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }

        .grid-container {
            column-count: 5;
            column-gap: 10px;
            padding: 20px;
        }

        .pin {
            break-inside: avoid;
            margin-bottom: 10px;
            border-radius: 16px;
            overflow: hidden;
        }

        .pin img {
            width: 100%;
            display: block;
        }

        .user-profile {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 10px;
            text-align: center;
        }

        .user-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        @media (max-width: 1200px) {
            .grid-container {
                column-count: 4;
            }
        }

        @media (max-width: 900px) {
            .grid-container {
                column-count: 3;
            }
        }

        @media (max-width: 600px) {
            .grid-container {
                column-count: 2;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">Pinterest Clone</a>
        <div class="nav-links">
            <a href="#" class="active">Home</a>
            <a href="#">Explore</a>
            <a href="#">Create</a>
        </div>
        <div class="auth-buttons">
            <?php if ($user_logged_in): ?>
                <a href="profile.php" class="profile-btn">
                    <img src="<?php echo $user_profile['profile_pic'] ?? 'default_avatar.png'; ?>" 
                         alt="Profile" class="profile-avatar">
                    <?php echo htmlspecialchars($user_profile['username'] ?? 'Profile'); ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="login">Log in</a>
                <a href="signup.php" class="signup">Sign up</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- User Profile Display (when logged in) -->
    <?php if ($user_logged_in && $user_profile): ?>
    <div class="user-profile">
        <img src="<?php echo $user_profile['profile_pic'] ?? 'default_avatar.png'; ?>" 
             alt="User Avatar" class="user-avatar">
        <h2><?php echo htmlspecialchars($user_profile['username']); ?></h2>
        <p><?php echo htmlspecialchars($user_profile['bio'] ?? 'No bio available'); ?></p>
    </div>
    <?php endif; ?>

    <!-- Image Upload Section -->
    <div class="image-upload">
        <h2>Upload Image</h2>
        <form action="index.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="image" required>
            <button type="submit" class="btn-primary">Upload</button>
        </form>
    </div>

    <!-- Display Uploaded Image -->
    <div class="uploaded-images">
        <h2>Uploaded Images</h2>
        <?php
        $dir = "uploads/";
        if (is_dir($dir)) {
            $images = scandir($dir);
            foreach ($images as $image) {
                if ($image != '.' && $image != '..') {
                    echo "<img src='$dir$image' alt='$image' style='max-width: 200px; margin: 10px;'>";
                }
            }
        }
        ?>
    </div>

    <div class="grid-container">
        <?php
        // Sample image array - in a real application, these would come from a database
        $images = [
            'https://picsum.photos/400/600',
            'https://picsum.photos/400/800',
            'https://picsum.photos/400/500',
            'https://picsum.photos/400/700',
            'https://picsum.photos/400/900',
            'https://picsum.photos/400/600',
            'https://picsum.photos/400/800',
            'https://picsum.photos/400/500',
        ];

        foreach ($images as $image) {
            echo '<div class="pin"><img src="' . $image . '" alt="Pin"></div>';
        }
        ?>
    </div>
</body>
</html>
