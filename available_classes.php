<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];


$checkUser = $pdo->prepare("SELECT id FROM users WHERE id = ? AND is_deleted = 0");
$checkUser->execute([$user_id]);

if (!$checkUser->fetch()) {
    session_destroy();
    header("Location: index.php?account_deleted=1");
    exit();
}


function safeLog($pdo, $user_id, $action) {
    $check = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $check->execute([$user_id]);

    if (!$check->fetch()) return;

    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $action, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
}

$successMessage = '';
$errorMessage = '';
$enrolled_class_info = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $class_id = intval($_POST['class_id']);
    $method = $_POST['payment_method'] ?? '';
    $errors = [];

    if (!$method) $errors[] = "Please select a payment method.";

    if ($method === 'gcash') {
        $gcash_number = trim($_POST['gcash_number'] ?? '');
        $gcash_name = trim($_POST['gcash_name'] ?? '');

        if (!$gcash_number || !preg_match('/^\d{10,}$/', $gcash_number))
            $errors[] = "GCash number must be at least 10 digits.";

        if (!$gcash_name || strlen($gcash_name) < 3)
            $errors[] = "GCash name is too short.";
    }

    if ($method === 'maya') {
        $maya_number = trim($_POST['maya_number'] ?? '');
        $maya_name = trim($_POST['maya_name'] ?? '');

        if (!$maya_number || !preg_match('/^\d{10,}$/', $maya_number))
            $errors[] = "Maya number must be at least 10 digits.";

        if (!$maya_name || strlen($maya_name) < 3)
            $errors[] = "Maya name is too short.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO user_classes (user_id, class_id, enrolled_at) VALUES (?, ?, NOW())");
        if ($stmt->execute([$user_id, $class_id])) {

            $stmt_class = $pdo->prepare("
                SELECT c.class_name, c.schedule_datetime, c.duration_minutes, c.price, u.username AS tutor_name
                FROM classes c
                JOIN users u ON c.tutor_id = u.id
                WHERE c.class_id = ?
            ");
            $stmt_class->execute([$class_id]);
            $class = $stmt_class->fetch();

            $reference_number = 'ENR-' . date('Ymd') . '-' . rand(100, 999);

            safeLog($pdo, $user_id, "Enrolled in class: {$class['class_name']} (Ref: $reference_number)");

            $enrolled_class_info = [
                'class_name' => $class['class_name'],
                'tutor_name' => $class['tutor_name'],
                'schedule' => $class['schedule_datetime'],
                'duration' => $class['duration_minutes'],
                'price' => $class['price'],
                'reference' => $reference_number
            ];
        } else {
            $errorMessage = "❌ Something went wrong while enrolling. Please try again.";
        }
    } else {
        $errorMessage = implode("<br>", $errors);
    }
}


$stmt = $pdo->prepare("
    SELECT c.class_id, c.class_name, c.description, c.schedule_datetime,
           c.duration_minutes, c.price, u.username AS tutor_name
    FROM classes c
    JOIN users u ON c.tutor_id = u.id
    WHERE c.class_id NOT IN (SELECT class_id FROM user_classes WHERE user_id = ?)
    LIMIT 6
");
$stmt->execute([$user_id]);
$available_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Available Classes - Beyond Learning</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">

<style>
.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:1000; }
.modal-content { background:#fff; padding:2rem; border-radius:12px; width:350px; box-shadow:0 6px 20px rgba(0,0,0,0.25); position:relative; }
.modal.active { display:flex; }
.modal-content input, .modal-content select { width:100%; padding:0.5rem; margin:0.3rem 0; border:1px solid #ccc; border-radius:6px; }
.modal-content button { padding:0.6rem 1.2rem; border:none; border-radius:6px; cursor:pointer; }
.btn-primary { background:#ff6600; color:#fff; }
.btn-secondary { background:#ccc; color:#000; }
.print-btn { margin-top:0.5rem; background:#ff6600; color:#fff; border:none; padding:0.5rem 1rem; border-radius:6px; cursor:pointer; }
</style>
</head>

<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><span class="highlight">BEYOND</span></h2>
            <div class="welcome-user">Welcome, <?= htmlspecialchars($username) ?></div>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="my_classes.php">📚 My Classes</a></li>
            <li><a href="available_classes.php" class="active">🎓 Available Classes</a></li>
            <li><a href="archived_classes.php">📦 Archived Classes</a></li>
            <li><a href="activity_logs.php">📜 Activity Logs</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

<main class="main-content">

    <h1>Request class</h1>
    <a href="request_class.php?" class="btn-secondary">Request Class</a>

    <h1>Available Classes</h1>

    <?php if ($errorMessage): ?>
        <div class="error-box"><?= $errorMessage ?></div>
    <?php endif; ?>

    <?php if (count($available_classes) > 0): ?>
        <div class="classes-grid">
            <?php foreach ($available_classes as $class): ?>
                <div class="class-card">
                    <h3><?= htmlspecialchars($class['class_name']) ?></h3>
                    <div class="class-meta">
                        Tutor: <?= htmlspecialchars($class['tutor_name']) ?><br>
                        Schedule: <?= date('M d, Y H:i', strtotime($class['schedule_datetime'])) ?><br>
                        Duration: <?= htmlspecialchars($class['duration_minutes']) ?> mins<br>
                        Price: ₱<?= number_format($class['price'],2) ?>
                    </div>
                    <p><?= htmlspecialchars($class['description']) ?></p>
                    <button class="btn-secondary enroll-btn" data-class-id="<?= $class['class_id'] ?>">Enroll Now</button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><h3>No available classes right now</h3></div>
    <?php endif; ?>
</main>
</div>

<div class="modal" id="paymentModal">
    <div class="modal-content">
        <h3 style="color:#ff6600; text-align:center;">Complete Enrollment</h3>
        <form method="POST" id="paymentForm">
            <input type="hidden" name="class_id" id="payment_class_id">

            <label>Payment Method</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">-- Select --</option>
                <option value="gcash">GCash</option>
                <option value="maya">Maya</option>
            </select>

            <div id="gcash_fields" style="display:none;">
                <input type="text" name="gcash_number" placeholder="GCash Number">
                <input type="text" name="gcash_name" placeholder="Account Name">
            </div>

            <div id="maya_fields" style="display:none;">
                <input type="text" name="maya_number" placeholder="Maya Number">
                <input type="text" name="maya_name" placeholder="Account Name">
            </div>

            <div style="margin-top:1rem; display:flex; justify-content:space-between;">
                <button type="submit" class="btn-primary" name="confirm_payment">Pay & Enroll</button>
                <button type="button" id="closeModal" class="btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php if($enrolled_class_info): ?>
<div class="modal active" id="successModal">
    <div class="modal-content" id="receiptContent" style="max-width:400px;">
        <h3 style="color:#ff6600; text-align:center;">🎉 Enrollment Successful!</h3>
        <p style="text-align:center; font-weight:bold; margin-bottom:1rem;">
            Reference: <?= $enrolled_class_info['reference'] ?>
        </p>

        <table style="width:100%; border-collapse:collapse; margin-bottom:1rem;">
            <tr style="background:#ff6600; color:#fff;">
                <th style="padding:0.5rem;">Item</th>
                <th style="padding:0.5rem; text-align:right;">Details</th>
            </tr>
            <tr>
                <td style="padding:0.5rem;">Class</td>
                <td style="padding:0.5rem; text-align:right;"><?= htmlspecialchars($enrolled_class_info['class_name']) ?></td>
            </tr>
            <tr style="background:#f5f5f5;">
                <td style="padding:0.5rem;">Tutor</td>
                <td style="padding:0.5rem; text-align:right;"><?= htmlspecialchars($enrolled_class_info['tutor_name']) ?></td>
            </tr>
            <tr>
                <td style="padding:0.5rem;">Schedule</td>
                <td style="padding:0.5rem; text-align:right;"><?= date('M d, Y H:i', strtotime($enrolled_class_info['schedule'])) ?></td>
            </tr>
            <tr style="background:#f5f5f5;">
                <td style="padding:0.5rem;">Duration</td>
                <td style="padding:0.5rem; text-align:right;"><?= $enrolled_class_info['duration'] ?> mins</td>
            </tr>
            <tr>
                <td style="padding:0.5rem; font-weight:bold;">Price</td>
                <td style="padding:0.5rem; text-align:right; font-weight:bold;">₱<?= number_format($enrolled_class_info['price'],2) ?></td>
            </tr>
        </table>

        <div style="text-align:center; margin-bottom:1rem; color:#155724; font-weight:bold;">
            Congratulations, you are now enrolled!
        </div>

        <div style="display:flex; justify-content:center; gap:0.5rem;">
            <button onclick="window.print()" class="print-btn">Print</button>
            <button onclick="document.getElementById('successModal').style.display='none';" class="btn-secondary">Close</button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('paymentModal');
    const closeModal = document.getElementById('closeModal');
    const paymentMethod = document.getElementById('payment_method');
    const fields = {
        gcash: document.getElementById('gcash_fields'),
        maya: document.getElementById('maya_fields')
    };
    const paymentClassId = document.getElementById('payment_class_id');

    document.querySelectorAll('.enroll-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            paymentClassId.value = btn.dataset.classId;
            modal.classList.add('active');
        });
    });

    closeModal.addEventListener('click', () => modal.classList.remove('active'));

    paymentMethod.addEventListener('change', () => {
        fields.gcash.style.display = "none";
        fields.maya.style.display = "none";
        if (fields[paymentMethod.value]) {
            fields[paymentMethod.value].style.display = "block";
        }
    });

    window.addEventListener('click', e => {
        if (e.target === modal) modal.classList.remove('active');
    });
});
</script>

</body>
</html>
