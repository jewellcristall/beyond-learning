<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$class_id = $_GET['class_id'] ?? 0;


$stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id=? AND tutor_id=?");
$stmt->execute([$class_id, $admin_id]);
$class = $stmt->fetch();

if (!$class) {
    die("Class not found.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['class_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $schedule = trim($_POST['schedule_datetime'] ?? '');
    $duration = trim($_POST['duration_minutes'] ?? '');

    // VALIDATION
    if (!$name) {
        $error = "Class name cannot be empty.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Price must be a valid positive number.";
    } elseif (!$schedule) {
        $error = "Please select a schedule.";
    } elseif (!strtotime($schedule)) {
        $error = "Invalid date/time format.";
    } elseif (!is_numeric($duration) || $duration <= 0) {
        $error = "Duration must be greater than 0 minutes.";
    }

    if (!$error) {
        $stmt = $pdo->prepare("
            UPDATE classes 
            SET class_name=?, description=?, price=?, schedule_datetime=?, duration_minutes=?
            WHERE class_id=? AND tutor_id=?
        ");

        if ($stmt->execute([$name, $desc, $price, $schedule, $duration, $class_id, $admin_id])) {
            log_activity($admin_id, "Edited class: {$class['class_name']} → $name");
        }

        header("Location: admin_classes.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class - BEYOND</title>

    <style>
        body {
            margin:0;
            font-family:'Segoe UI', sans-serif;
            background:#f5f5f5;
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
        }

        .edit-container {
            background:#fff;
            padding:2rem;
            border-radius:12px;
            box-shadow:0 6px 20px rgba(0,0,0,0.1);
            width:100%;
            max-width:500px;
        }

        .edit-container h2 {
            color:#ff6600;
            margin-bottom:1.5rem;
            text-align:center;
        }

        .edit-container form input,
        .edit-container form textarea {
            width:100%;
            padding:0.75rem 1rem;
            margin-bottom:1rem;
            border-radius:10px;
            border:1px solid #ccc;
            font-size:1rem;
            transition:0.3s;
        }

        .edit-container form input:focus,
        .edit-container form textarea:focus {
            border-color:#ff6600;
            outline:none;
            box-shadow:0 0 5px rgba(255,102,0,0.5);
        }

        .edit-container form button {
            width:100%;
            padding:0.75rem;
            border:none;
            border-radius:10px;
            background:#ff6600;
            color:#fff;
            font-weight:bold;
            font-size:1rem;
            cursor:pointer;
            transition:0.3s;
        }

        .edit-container form button:hover {
            background:#e65500;
        }

        .edit-container form a {
            display:block;
            margin-top:0.5rem;
            text-align:center;
            text-decoration:none;
            color:#ff6600;
            font-size:0.95rem;
            transition:0.3s;
        }

        .edit-container form a:hover {
            text-decoration:underline;
        }

        .error-msg {
            background:#f8d7da;
            color:#721c24;
            padding:0.75rem;
            border-radius:8px;
            margin-bottom:1rem;
            text-align:center;
            font-size:0.95rem;
        }
    </style>
</head>
<body>
    
<div class="edit-container">
    <h2>Edit Class</h2>
    <?php if($error): ?><div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST">
        <label>Class Name</label>
        <input type="text" name="class_name" value="<?= htmlspecialchars($class['class_name']) ?>" required>
        <label>Description</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($class['description']) ?></textarea>

        <label>Price</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($class['price']) ?>" min="0.01" required>

        <label>Schedule</label>
        <input type="datetime-local" name="schedule_datetime"
               value="<?= date('Y-m-d\TH:i', strtotime($class['schedule_datetime'])) ?>" required>

        <label>Duration (minutes)</label>
        <input type="number" name="duration_minutes" min="1"
               value="<?= htmlspecialchars($class['duration_minutes']) ?>" required>

        <button type="submit">Save Changes</button>
        <a href="admin_classes.php">Cancel</a>
    </form>
</div>

</body>
</html>
