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

// enrolled class
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM user_classes
    WHERE user_id = ?
      AND archived = 0
");
$stmt->execute([$user_id]);
$enrolled_classes = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT class_id
    FROM user_classes
    WHERE user_id = ?
      AND archived = 0
");
$stmt->execute([$user_id]);
$active_classes = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($active_classes)) {
    $completed_lessons = 0;
    $progress = 0;
} else {

    $placeholders = implode(',', array_fill(0, count($active_classes), '?'));
    $params = array_merge([$user_id], $active_classes);


    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM completed_lessons
        WHERE user_id = ?
          AND class_id IN ($placeholders)
    ");
    $stmt->execute($params);
    $completed_lessons = (int)$stmt->fetchColumn();


    $total_lessons = max($completed_lessons, 1);

    $progress = round(($completed_lessons / $total_lessons) * 100);
    if ($progress > 100) $progress = 100;
}


// FOR NOTES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_note'])) {
    $note_text = trim($_POST['new_note']);
    $note_type = $_POST['note_type'] ?? 'plain';
    if ($note_text !== '') {
        $colors = ['#FFF9C4', '#C8E6C9', '#BBDEFB', '#FFE0B2', '#F8BBD0', '#E1BEE7'];
        $color = $colors[array_rand($colors)];
        $stmt = $pdo->prepare("INSERT INTO user_notes (user_id, content, color, type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $note_text, $color, $note_type]);
    }
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['delete_note'])) {
    $note_id = intval($_GET['delete_note']);
    $stmt = $pdo->prepare("DELETE FROM user_notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$note_id, $user_id]);
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_note_id'])) {
    $note_id = intval($_POST['edit_note_id']);
    $new_content = trim($_POST['edited_content']);
    if ($new_content !== '') {
        $stmt = $pdo->prepare("UPDATE user_notes SET content = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$new_content, $note_id, $user_id]);
    }
    header("Location: dashboard.php");
    exit();
}


$programming_tips = [
    "Write code as if the person maintaining it is a psychopath who knows where you live.",
    "Comment why, not what.",
    "Refactor often — your future self will thank you.",
    "Keep functions small and focused.",
    "Use meaningful variable names — future you will love this.",
    "Test early, test often.",
    "Avoid premature optimization — make it work first.",
    "Read code like a detective — understand before you change."
];
$random_tip = $programming_tips[array_rand($programming_tips)];


// fetch notes
$stmt = $pdo->prepare("SELECT id, content, color, type FROM user_notes WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// chart data
$today = new DateTime();
$period_days = intval($_GET['days'] ?? 7);
if ($period_days <= 0) $period_days = 7;

$chart_labels = [];
$chart_data = [];

for ($i = $period_days - 1; $i >= 0; $i--) {
    $day = (clone $today)->modify("-$i days");
    $chart_labels[] = $day->format('M d');

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM activity_logs 
                       WHERE user_id = ? AND action LIKE 'Logged in%' 
                       AND DATE(created_at) = ?");
$stmt->execute([$user_id, $day->format('Y-m-d')]);
$chart_data[] = (int)$stmt->fetchColumn(); // number of logins


}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - Beyond Learning</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="assets/style2.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body { overflow: hidden; }
.main-content { padding: 1.5rem; }


.dashboard-header {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr;
    gap: 1rem;
    margin-bottom: 1.2rem;
}
.header-widget {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 0.8rem;
    text-align: center;
}
.header-widget h4 { margin:0; font-size: 0.95rem; }
.header-widget p { margin: 0; font-size: 0.9rem; font-weight: bold; }
.progress-bar-bg { background:#eee; border-radius:6px; height:6px; margin-top:4px; }
.progress-bar-fill { background:#007bff; height:6px; border-radius:6px; }


.dashboard-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 1.2rem;
    height: calc(100vh - 250px);
}
.chart-box, .notes-box {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 1.2rem 1.5rem;
    overflow: hidden;
}
.notes-box { display: flex; flex-direction: column; }
.notes-header { margin-bottom: .8rem; }
.new-note-form { display: flex; flex-direction: column; gap: .4rem; }
.new-note-input {
    width: 100%; padding: .6rem; border-radius: 8px;
    border: 1px solid #ccc; font-size: .9rem; resize: none;
}


.notepad-container {
  display: flex;
  flex-direction: column;
  background: #fbfbfb;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  height: 100%;
  min-height: 360px;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  color: #222;
}


.menu-row {
  display: flex;
  gap: 10px;
  padding: 8px 10px;
  border-bottom: 1px solid #e9e9e9;
  background: linear-gradient(#ffffff, #fbfbfb);
  align-items: center;
  user-select: none;
  font-size: 14px;
}

/* each menu item (File/Edit/View/Help) */
.menu-item {
  position: relative;
  padding: 4px 8px;
  border-radius: 4px;
  cursor: pointer;
  color: #333;
}

/* dropdown panel */
.menu-dropdown {
  display: none;
  position: absolute;
  top: 36px;
  left: 0;
  min-width: 160px;
  background: #fff;
  border: 1px solid #ddd;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  z-index: 40;
  border-radius: 6px;
  padding: 6px 6px;
}


.menu-action {
  display: block;
  width: 100%;
  text-align: left;
  background: none;
  border: none;
  padding: 8px 10px;
  font-size: 13px;
  cursor: pointer;
  color: #333;
}


.menu-action:hover {
  background: #f3f3f3;
}


.menu-sep {
  height: 1px;
  background: #eee;
  margin: 6px 0;
}


#notepadArea {
  flex: 1;
  padding: 14px;
  border: none;
  outline: none;
  resize: none;
  font-family: Consolas, "Courier New", monospace;
  font-size: 14px;
  color: #1a1a1a;
  background: linear-gradient(#fff,#fff);
  line-height: 1.45;
  overflow: auto;
}


.footer-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 10px;
  border-top: 1px solid #eee;
  font-size: 12px;
  color: #676767;
  background: #fafafa;
}

.footer-right {
  font-style: normal;
  color: #666;
}
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
            <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
            <li><a href="my_classes.php">📚 My Classes</a></li>
            <li><a href="available_classes.php">🎓 Available Classes</a></li>
            <li><a href="archived_classes.php">📦 Archived Classes</a></li>
            <li><a href="activity_logs.php">📜 Activity Logs</a></li>
            <li><a href="settings.php">⚙️ Settings</a></li>
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="dashboard-header" style="display:flex; justify-content:space-between; align-items:center; background:#fff; border-radius:15px; padding:1.5rem 2rem; box-shadow:0 4px 15px rgba(0,0,0,0.1); margin-bottom:1.5rem; min-height:130px;">

    <!-- API section -->
    <div class="weather-section" style="flex:3; display:flex; align-items:center; gap:1rem;">
        <div id="weather-loading" style="display:flex; align-items:center; gap:0.5rem;">
            <div class="loader" style="border:4px solid rgba(0,0,0,0.1); border-top:4px solid #007bff; border-radius:50%; width:28px; height:28px; animation:spin 1s linear infinite;"></div>
            <span style="font-size:1rem; color:#555;">Loading weather...</span>
        </div>
        <div id="weather-info" style="display:none; align-items:center; gap:1rem;">
            <img id="weather-icon" src="" alt="icon" style="width:50px; height:50px;">
            <div>
                <div id="weather-temp" style="font-weight:bold; font-size:1.5rem; color:#007bff;"></div>
                <div id="weather-desc" style="text-transform:capitalize; font-size:0.95rem; color:#555;"></div>
                <div id="weather-location" style="font-size:0.85rem; color:#777;"></div>
            </div>
        </div>
    </div>


    <div class="stats-section" style="flex:1; display:flex; flex-direction:column; justify-content:center; align-items:flex-end; gap:0.5rem;">
        <div style="font-size:0.95rem; color:#333;">Enrolled Classes</div>
        <div style="font-weight:bold; font-size:1.3rem; color:#007bff;"><?= $enrolled_classes ?></div>

        

    <div style="background:#f0f9ff; padding:0.6rem 0.8rem; border-radius:8px; font-size:0.9rem; color:#28a745; line-height:1.3; max-width:250px;">
        💡 <?= htmlspecialchars($random_tip) ?>
    </div>
    </div>
</div>

<style>
@keyframes spin { 0% { transform:rotate(0deg); } 100% { transform:rotate(360deg); } }
</style>

<script>
async function fetchWeather() {
    const apiKey = '265a31cf7c8c541bbaa460d81f986b55';
    const loadingEl = document.getElementById('weather-loading');
    const infoEl = document.getElementById('weather-info');
    const iconEl = document.getElementById('weather-icon');
    const tempEl = document.getElementById('weather-temp');
    const descEl = document.getElementById('weather-desc');
    const locEl = document.getElementById('weather-location');

    if (!navigator.geolocation) {
        showError('Geolocation not supported');
        return;
    }

    navigator.geolocation.getCurrentPosition(async (position) => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;

        try {
            const res = await fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}`);
            if (!res.ok) throw new Error('Weather fetch failed');
            const data = await res.json();

            iconEl.src = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;
            tempEl.textContent = `${Math.round(data.main.temp)}°C`;
            descEl.textContent = data.weather[0].description;
            locEl.textContent = data.name;

            loadingEl.style.display = 'none';
            infoEl.style.display = 'flex';
        } catch(err) {
            console.error(err);
            showError('Unable to load weather');
        }
    }, () => {
        showError('Location access denied');
    });

    function showError(msg) {
        loadingEl.style.display = 'none';
        infoEl.style.display = 'flex';
        tempEl.textContent = 'N/A';
        descEl.textContent = msg;
        locEl.textContent = '';
        iconEl.style.display = 'none';
    }
}

fetchWeather();
</script>


        <div class="dashboard-grid">
            <!-- Chart -->
            <div class="chart-box">
                <h3>Study Activity</h3>
                <div style="display:flex; justify-content:flex-end; align-items:center; margin-bottom:0.5rem; gap:6px;">
    <label for="periodSelect" style="font-size:0.85rem; color:#555;">Show:</label>
    <form method="GET" id="periodForm">
        <select name="days" id="periodSelect" onchange="document.getElementById('periodForm').submit()" style="padding:3px 6px; border-radius:6px; border:1px solid #ccc; font-size:0.85rem;">
            <option value="7" <?= ($period_days==7)?'selected':'' ?>>Last 7 days</option>
            <option value="30" <?= ($period_days==30)?'selected':'' ?>>Last 30 days</option>
        </select>
    </form>
</div>

                <canvas id="activityChart" height="160"></canvas>
            </div>

            <!-- ===== Notepad (replace existing notes-box section) ===== -->
<div class="notes-box">
  <div class="notepad-container" id="notepadContainer">
    <!-- Top Menu -->
    <div class="menu-row">
      <div class="menu-item" id="fileMenuBtn">File ▾
        <div class="menu-dropdown" id="fileMenu">
          <button type="button" class="menu-action" id="newFileBtn">New</button>
          <button type="button" class="menu-action" id="openFileBtn">Open...</button>
          <button type="button" class="menu-action" id="saveFileBtn">Save</button>
          <button type="button" class="menu-action" id="saveAsFileBtn">Save As...</button>
          <div class="menu-sep"></div>
          <button type="button" class="menu-action" id="exitBtn">Exit</button>
        </div>
      </div>

      <div class="menu-item" id="editMenuBtn">Edit ▾
        <div class="menu-dropdown" id="editMenu">
          <button type="button" class="menu-action" id="undoBtn">Undo</button>
          <button type="button" class="menu-action" id="redoBtn">Redo</button>
          <div class="menu-sep"></div>
          <button type="button" class="menu-action" id="cutBtn">Cut</button>
          <button type="button" class="menu-action" id="copyBtn">Copy</button>
          <button type="button" class="menu-action" id="pasteBtn">Paste</button>
          <div class="menu-sep"></div>
          <button type="button" class="menu-action" id="selectAllBtn">Select All</button>
        </div>
      </div>

      <div class="menu-item" id="viewMenuBtn">View ▾
        <div class="menu-dropdown" id="viewMenu">
          <button type="button" class="menu-action" id="wordWrapBtn">Toggle Word Wrap</button>
          <div class="menu-sep"></div>
          <button type="button" class="menu-action" id="zoomInBtn">Zoom In</button>
          <button type="button" class="menu-action" id="zoomOutBtn">Zoom Out</button>
          <button type="button" class="menu-action" id="resetZoomBtn">Reset Zoom</button>
        </div>
      </div>

      <div class="menu-item" id="helpMenuBtn">Help ▾
        <div class="menu-dropdown" id="helpMenu">
          <button type="button" class="menu-action" id="aboutBtn">About</button>
          <button type="button" class="menu-action" id="shortcutsBtn">Shortcuts</button>
        </div>
      </div>
    </div>

    <!-- Hidden file input fallback (for Open when FS API unavailable) -->
    <input type="file" id="fileInputFallback" accept=".txt" style="display:none">

    <!-- Text area -->
    <textarea id="notepadArea" spellcheck="false" placeholder="// Multiline TextBox"></textarea>

    <!-- Footer: edited time appears only after open/save -->
    <div class="footer-row">
      <div class="footer-left"></div>
      <div class="footer-right" id="editedStamp" style="visibility:hidden">Edited @ <span id="editedTime">--:--</span></div>
    </div>
  </div>
</div>

<script>
const ctx = document.getElementById('activityChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [{
            label: 'Active Hours',
            data: <?= json_encode($chart_data) ?>,
            borderColor: 'rgba(54,162,235,1)',
            backgroundColor: 'rgba(54,162,235,0.1)',
            tension: 0.3,
            fill: true,
            borderWidth: 2
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

(function() {
  // Elements
  const fileMenuBtn = document.getElementById('fileMenuBtn');
  const editMenuBtn = document.getElementById('editMenuBtn');
  const viewMenuBtn = document.getElementById('viewMenuBtn');
  const helpMenuBtn = document.getElementById('helpMenuBtn');

  const fileMenu = document.getElementById('fileMenu');
  const editMenu = document.getElementById('editMenu');
  const viewMenu = document.getElementById('viewMenu');
  const helpMenu = document.getElementById('helpMenu');

  const newFileBtn = document.getElementById('newFileBtn');
  const openFileBtn = document.getElementById('openFileBtn');
  const saveFileBtn = document.getElementById('saveFileBtn');
  const saveAsFileBtn = document.getElementById('saveAsFileBtn');
  const exitBtn = document.getElementById('exitBtn');

  const undoBtn = document.getElementById('undoBtn');
  const redoBtn = document.getElementById('redoBtn');
  const cutBtn = document.getElementById('cutBtn');
  const copyBtn = document.getElementById('copyBtn');
  const pasteBtn = document.getElementById('pasteBtn');
  const selectAllBtn = document.getElementById('selectAllBtn');

  const wordWrapBtn = document.getElementById('wordWrapBtn');
  const zoomInBtn = document.getElementById('zoomInBtn');
  const zoomOutBtn = document.getElementById('zoomOutBtn');
  const resetZoomBtn = document.getElementById('resetZoomBtn');

  const aboutBtn = document.getElementById('aboutBtn');
  const shortcutsBtn = document.getElementById('shortcutsBtn');

  const fileInputFallback = document.getElementById('fileInputFallback');
  const area = document.getElementById('notepadArea');

  const editedStamp = document.getElementById('editedStamp');
  const editedTime = document.getElementById('editedTime');

  // State
  let fileHandle = null;
  let fileName = 'untitled.txt';
  let lastSavedOrOpened = null;
  let fontSize = 14;
  let wordWrapOn = true;

  // Utility to toggle dropdowns
  function closeAllDropdowns() {
    fileMenu.style.display = 'none';
    editMenu.style.display = 'none';
    viewMenu.style.display = 'none';
    helpMenu.style.display = 'none';
  }

  function toggleDropdown(btn, panel) {
    const isOpen = panel.style.display === 'block';
    closeAllDropdowns();
    panel.style.display = isOpen ? 'none' : 'block';
  }


  document.addEventListener('click', (e) => {
    const path = e.composedPath ? e.composedPath() : (e.path || []);
    const clickedInMenu = path.includes(fileMenu) || path.includes(editMenu) || path.includes(viewMenu) || path.includes(helpMenu)
                          || path.includes(fileMenuBtn) || path.includes(editMenuBtn) || path.includes(viewMenuBtn) || path.includes(helpMenuBtn);
    if (!clickedInMenu) closeAllDropdowns();
  });

  fileMenuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleDropdown(fileMenuBtn, fileMenu); });
  editMenuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleDropdown(editMenuBtn, editMenu); });
  viewMenuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleDropdown(viewMenuBtn, viewMenu); });
  helpMenuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleDropdown(helpMenuBtn, helpMenu); });


  newFileBtn.addEventListener('click', () => {
    closeAllDropdowns();
    if (area.value.trim() !== '') {
      if (!confirm('Start a new file? Unsaved changes will be lost.')) return;
    }
    fileHandle = null;
    fileName = 'untitled.txt';
    area.value = '';
    editedStamp.style.visibility = 'hidden';
  });

  openFileBtn.addEventListener('click', async () => {
    closeAllDropdowns();
   
    try {
      if ('showOpenFilePicker' in window) {
        const [handle] = await window.showOpenFilePicker({
          types: [{ description: 'Text Files', accept: { 'text/plain': ['.txt'] } }],
          multiple: false
        });
        if (!handle) return;
        const file = await handle.getFile();
        const text = await file.text();
        fileHandle = handle;
        fileName = file.name || 'untitled.txt';
        area.value = text;
        stampNow();
      } else {
        
        fileInputFallback.click();
      }
    } catch (err) {
   
      console.error(err);
    }
  });

  fileInputFallback.addEventListener('change', (ev) => {
    const f = ev.target.files && ev.target.files[0];
    if (!f) return;
    if (!f.name.toLowerCase().endsWith('.txt')) {
      alert('Only .txt files are allowed');
      fileInputFallback.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = function(e) {
      area.value = e.target.result;
      fileName = f.name;
      fileHandle = null;
      stampNow();
      fileInputFallback.value = '';
    };
    reader.readAsText(f);
  });


  saveFileBtn.addEventListener('click', async () => {
    closeAllDropdowns();
    await saveToFile(false);
  });

  saveAsFileBtn.addEventListener('click', async () => {
    closeAllDropdowns();
    await saveToFile(true);
  });

  async function saveToFile(forceSaveAs = false) {
    try {
      if ('showSaveFilePicker' in window && (forceSaveAs || !fileHandle)) {
        
        const handle = await window.showSaveFilePicker({
          suggestedName: fileName || 'untitled.txt',
          types: [{ description: 'Text Files', accept: { 'text/plain': ['.txt'] } }],
        });
        if (!handle) return;
        fileHandle = handle;
      }

      if (fileHandle && !forceSaveAs && 'createWritable' in fileHandle) {
       
        const writable = await fileHandle.createWritable();
        await writable.write(area.value);
        await writable.close();
        fileName = (fileHandle.name || fileName);
        stampNow();
      } else {
     
        if (!area.value.trim()) {
          alert('Cannot save an empty file');
          return;
        }
        const blob = new Blob([area.value], { type: 'text/plain' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = fileName || 'note.txt';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(a.href);
        stampNow();
      }
    } catch (err) {
      console.error(err);
    }
  }

  exitBtn.addEventListener('click', () => {
    closeAllDropdowns();

    if (confirm('Exit notepad? Unsaved changes will be lost.')) {
      area.value = '';
      fileHandle = null;
      fileName = 'untitled.txt';
      editedStamp.style.visibility = 'hidden';
    }
  });


  undoBtn.addEventListener('click', () => { document.execCommand('undo'); closeAllDropdowns(); });
  redoBtn.addEventListener('click', () => { document.execCommand('redo'); closeAllDropdowns(); });

  cutBtn.addEventListener('click', async () => {
    closeAllDropdowns();
    if (area.selectionStart === area.selectionEnd) return;
    try {
      const sel = area.value.slice(area.selectionStart, area.selectionEnd);
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(sel);
      } else {
        document.execCommand('cut');
      }

      const before = area.value.slice(0, area.selectionStart);
      const after = area.value.slice(area.selectionEnd);
      area.value = before + after;
    } catch (e) {
      document.execCommand('cut');
    }
  });

  copyBtn.addEventListener('click', async () => {
    closeAllDropdowns();
    if (area.selectionStart === area.selectionEnd) return;
    const sel = area.value.slice(area.selectionStart, area.selectionEnd);
    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(sel);
      } else {
        document.execCommand('copy');
      }
    } catch (e) {
      document.execCommand('copy');
    }
  });

  pasteBtn.addEventListener('click', async () => {
    closeAllDropdowns();
    try {
      if (navigator.clipboard && navigator.clipboard.readText) {
        const text = await navigator.clipboard.readText();
        insertAtCaret(text);
      } else {
        document.execCommand('paste');
      }
    } catch (e) {
      document.execCommand('paste');
    }
  });

  selectAllBtn.addEventListener('click', () => {
    closeAllDropdowns();
    area.select();
  });

  function insertAtCaret(text) {
    const start = area.selectionStart;
    const end = area.selectionEnd;
    area.value = area.value.slice(0, start) + text + area.value.slice(end);

    const newPos = start + text.length;
    area.selectionStart = area.selectionEnd = newPos;
    area.focus();
  }


  wordWrapBtn.addEventListener('click', () => {
    closeAllDropdowns();
    wordWrapOn = !wordWrapOn;
    area.style.whiteSpace = wordWrapOn ? 'pre-wrap' : 'pre';
    area.wrap = wordWrapOn ? 'soft' : 'off';
  });

  zoomInBtn.addEventListener('click', () => {
    closeAllDropdowns();
    fontSize = Math.min(28, fontSize + 1);
    area.style.fontSize = fontSize + 'px';
  });

  zoomOutBtn.addEventListener('click', () => {
    closeAllDropdowns();
    fontSize = Math.max(10, fontSize - 1);
    area.style.fontSize = fontSize + 'px';
  });

  resetZoomBtn.addEventListener('click', () => {
    closeAllDropdowns();
    fontSize = 14;
    area.style.fontSize = fontSize + 'px';
  });


  aboutBtn.addEventListener('click', () => {
    closeAllDropdowns();
    alert('Beyond Learning Notepad\nSimple notepad with local open/save support.\nOnly .txt files are supported.');
  });

  shortcutsBtn.addEventListener('click', () => {
    closeAllDropdowns();
    alert('Shortcuts:\nCtrl+S Save\nCtrl+O Open\nCtrl+A Select All\nCtrl+Z Undo\nCtrl+Y Redo');
  });


  function stampNow() {
    const now = new Date();
    const hh = String(now.getHours()).padStart(2,'0');
    const mm = String(now.getMinutes()).padStart(2,'0');
    editedTime.textContent = `${hh}:${mm}`;
    editedStamp.style.visibility = 'visible';
    lastSavedOrOpened = now;
  }


  area.addEventListener('input', () => {
    if (lastSavedOrOpened) {
      const now = new Date();
      const hh = String(now.getHours()).padStart(2,'0');
      const mm = String(now.getMinutes()).padStart(2,'0');
      editedTime.textContent = `${hh}:${mm}`;
    }
  });


  document.addEventListener('keydown', async (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
      e.preventDefault();
      await saveToFile(false);
    } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'o') {
      e.preventDefault();
      openFileBtn.click();
    } else if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'a') {
      
      if (document.activeElement !== area) {
        e.preventDefault();
        area.select();
      }
    }
  });


  area.style.fontSize = fontSize + 'px';
  area.style.whiteSpace = 'pre-wrap';
  area.wrap = 'soft';


  document.getElementById('notepadContainer').addEventListener('dblclick', () => area.focus());

})();
</script>
</body>
</html>
