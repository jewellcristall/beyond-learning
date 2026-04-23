<?php
session_start();

$flash_error = $_SESSION['flash_error'] ?? null;
$flash_success = $_SESSION['flash_success'] ?? null;
$open_modal = $_SESSION['open_modal'] ?? null;

unset($_SESSION['flash_error'], $_SESSION['flash_success'], $_SESSION['open_modal']);

$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BEYOND</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    html {
  scroll-behavior: smooth;
}
    .flash {
      margin: 10px 0;
      padding: 10px;
      border-radius: 6px;
      font-size: 14px;
      text-align: center;
    }
    .flash.error {
      background: #fdd;
      color: #a00;
      border: 1px solid #a00;
    }
    .flash.success {
      background: #dfd;
      color: #060;
      border: 1px solid #060;
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <div class="logo">
      <div class="logo-circle">B</div>
      <span class="brand"><span class="highlight">BEYOND</span></span>
    </div>
    
    <div class="nav-right">
      <?php if (!$user): ?>
        <a href="#" id="openLogin" class="login-link">Login</a>
        <a href="#" id="openSignup" class="signup-btn">Sign Up</a>
      <?php else: ?>
        <div class="profile">
          <span class="username"><?=htmlspecialchars($user['username'])?></span>
          <a href="logout.php" class="logout-link">Logout</a>
        </div>
      <?php endif; ?>
    </div>
  </nav>

  <section class="hero" id="home">
    <h1>Learning <span class="highlight">BEYOND</span> Boundaries</h1>
    <p class="subtitle">
      An inclusive, event-driven online tutoring platform providing personalized education
      for professionals, students, and individuals with special learning needs.
    </p>
    <div class="hero-buttons">
      <a href="#" class="btn primary" id="getStarted">Get Started Today</a>
      <a href="#about" class="btn outline">Learn More</a>
    </div>
    <div class="hero-cards">
      <div class="feature-card">
        <div class="icon">📖</div>
        <h4>Personalized Learning</h4>
        <p>Tailored education experiences designed for your unique learning style and goals.</p>
      </div>
      <div class="feature-card">
        <div class="icon">👥</div>
        <h4>Expert Tutors</h4>
        <p>Learn from experienced educators passionate about inclusive education.</p>
      </div>
      <div class="feature-card">
        <div class="icon">❤️</div>
        <h4>Inclusive Access</h4>
        <p>Supporting learners with special needs through accessible design and methods.</p>
      </div>
    </div>
  </section>

  <section class="about" id="about">
    <h2>About <span class="highlight">BEYOND</span></h2>
    <p class="about-intro">BEYOND is an innovative, event-driven online tutoring platform committed to breaking down 
        barriers in education. We believe that learning should be accessible, engaging, and tailored to every individual's 
        unique needs and abilities.</p>
    <div class="mission-card">
      <h3>Our Mission</h3>
      <p>To democratize education by providing inclusive, personalized, and accessible learning experiences that empower 
        every individual—regardless of their background, abilities, or learning style—to achieve their full potential and 
        go BEYOND their boundaries.</p>
    </div>
    <div class="objectives-grid">
      <div class="obj-card">
        <div class="obj-icon">🎯</div>
        <h4>Personalized Goals</h4>
        <p>Helping learners achieve individual success paths.</p>
      </div>
      <div class="obj-card">
        <div class="obj-icon">📖</div>
        <h4>Inclusive Education</h4>
        <p>Provide accessible learning opportunities for all, including learners with special needs and diverse backgrounds.</p>
      </div>
      <div class="obj-card">
        <div class="obj-icon">💡</div>
        <h4>Innovation in Learning</h4>
        <p>Leverage technology and modern teaching methods to create engaging, interactive learning experiences.</p>
      </div>
    </div>
  </section>

  <section class="hero" id="home">
    <h2>Our Values</h2>
    <div class="hero-cards">
      <div class="feature-card">
        <br>
        <center><h4 color="orange">Accessibility</h4>
        <p>Making education available to everyone, regardless of abilities or circumstances.</p></center>
      </div>
      <div class="feature-card">
        <br>
        <center><h4>Empathy</h4>
        <p>Understanding and responding to each learner's unique needs and challenges.</p></center>
      </div>
      <div class="feature-card">
        <br>
        <center><h4>Excellence</h4>
        <p>Maintaining the highest standards in education quality and user experience.</p></center>
      </div>
    </div>
  </section>

  <?php if ($user): ?>
  <section id="classes" class="classes visible">
    <h2>Welcome, <?=htmlspecialchars($user['username'])?> — Your Classes</h2>
    <p class="classes-sub">Here are the classes available to you.</p>
  </section>
  <?php endif; ?>

  <footer class="footer">
    <p>© 2025 BEYOND. All rights reserved.</p>
  </footer>

  <div id="loginModal" class="modal" <?php if ($open_modal === 'login') echo 'style="display:flex;"'; ?>>
    <div class="modal-content">
      <button class="close">&times;</button>
      <h2>Welcome Back</h2>

      <?php if ($flash_success && $open_modal === 'login'): ?>
        <div class="flash success"><?=htmlspecialchars($flash_success)?></div>
      <?php endif; ?>

      <?php if ($flash_error && $open_modal === 'login'): ?>
        <div class="flash error"><?=htmlspecialchars($flash_error)?></div>
      <?php endif; ?>

      <form method="post" action="login.php" class="form">
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn primary full">Sign In</button>
      </form>
      <p class="switch-text">Don’t have an account? <a href="#" id="switchToSignup">Sign up here</a></p>
    </div>
  </div>

<div id="signupModal" class="modal" <?php if ($open_modal === 'signup') echo 'style="display:flex;"'; ?>>
  <div class="modal-content">
    <button class="close">&times;</button>
    <h2>Join BEYOND</h2>

    <?php if ($flash_error && $open_modal === 'signup'): ?>
      <div class="flash error"><?=htmlspecialchars($flash_error)?></div>
    <?php endif; ?>

    <form method="post" action="signup.php" class="form" id="signupForm">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter your username" required>
      </div>

      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <div id="email-hint" class="hint"></div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" required>
        <div id="password-hint" class="hint"></div>
      </div>

      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
        <div id="confirm-hint" class="hint"></div>
      </div>

      <div class="form-group terms-group">
        <label>
          <input type="checkbox" name="terms" id="terms" required>
          I agree to the <a href="#" id="showTerms">Terms of Service</a> and <a href="#" id="showPrivacy">Privacy Policy</a>.
        </label>
      </div>

      <button type="submit" class="btn primary full" id="signupBtn">Create Account</button>
    </form>

    <p class="switch-text">Already have an account? <a href="#" id="switchToLogin">Sign in here</a></p>
  </div>
</div>

<!-- TERMS & PRIVACY MODALS -->
<div id="termsModal" class="modal hidden">
  <div class="modal-content small">
    <button class="close" data-close="termsModal">&times;</button>
    <h2>Terms of Service</h2>
    <div class="modal-body">
      <p>By creating an account, you agree to use Beyond Learning responsibly, respect intellectual property, 
      and comply with our community guidelines. We may suspend accounts for violations.</p>
    </div>
  </div>
</div>

<div id="privacyModal" class="modal hidden">
  <div class="modal-content small">
    <button class="close" data-close="privacyModal">&times;</button>
    <h2>Privacy Policy</h2>
    <div class="modal-body">
      <p>Beyond Learning values your privacy. We collect and use personal data only for registration, 
      class tracking, and support purposes. Your data will not be sold or shared externally.</p>
    </div>
  </div>
</div>

<script src="assets/script.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const email = document.getElementById("email");
  const password = document.getElementById("password");
  const confirm = document.getElementById("confirm_password");

  const emailHint = document.getElementById("email-hint");
  const passwordHint = document.getElementById("password-hint");
  const confirmHint = document.getElementById("confirm-hint");

  const form = document.getElementById("signupForm");
  const signupBtn = document.getElementById("signupBtn");


  email.addEventListener("input", () => {
    if (email.value.trim() === "") {
      emailHint.textContent = "";
    } else if (!email.value.includes("@")) {
      emailHint.textContent = "⚠️ Email must include '@'";
      emailHint.style.color = "red";
    } else {
      emailHint.textContent = "✅ Looks good";
      emailHint.style.color = "green";
    }
  });

  password.addEventListener("input", () => {
    if (password.value.length < 6) {
      passwordHint.textContent = "⚠️ Password must be at least 6 characters";
      passwordHint.style.color = "red";
    } else {
      passwordHint.textContent = "✅ Strong password";
      passwordHint.style.color = "green";
    }
    if (confirm.value) confirm.dispatchEvent(new Event("input"));
  });

  confirm.addEventListener("input", () => {
    if (confirm.value !== password.value) {
      confirmHint.textContent = "⚠️ Passwords do not match";
      confirmHint.style.color = "red";
    } else {
      confirmHint.textContent = "✅ Passwords match";
      confirmHint.style.color = "green";
    }
  });


  const showTerms = document.getElementById("showTerms");
  const showPrivacy = document.getElementById("showPrivacy");

  showTerms.addEventListener("click", (e) => {
    e.preventDefault();
    document.getElementById("termsModal").style.display = "flex";
  });
  showPrivacy.addEventListener("click", (e) => {
    e.preventDefault();
    document.getElementById("privacyModal").style.display = "flex";
  });


  document.querySelectorAll(".close").forEach(btn => {
    btn.addEventListener("click", (e) => {
      const target = e.target.getAttribute("data-close");
      if (target) document.getElementById(target).style.display = "none";
      else e.target.closest(".modal").style.display = "none";
    });
  });
  window.addEventListener("click", (e) => {
    if (e.target.classList.contains("modal")) e.target.style.display = "none";
  });

  form.addEventListener("submit", function(e) {
    let valid = true;
    if (!email.value.includes("@")) valid = false;
    if (password.value.length < 6) valid = false;
    if (confirm.value !== password.value) valid = false;
    if (!document.getElementById("terms").checked) valid = false;

    if (!valid) {
      e.preventDefault();
      alert("Please fix the highlighted issues and accept the terms.");
      return;
    }

    signupBtn.disabled = true;
    signupBtn.textContent = "⏳ Validating...";
  });
});
</script>

<style>
.hint {
  font-size: 0.85rem;
  margin-top: 0.2rem;
}
.modal.hidden { display: none; }
.modal-content.small {
  max-width: 500px;
  padding: 20px;
}
.modal-body {
  max-height: 250px;
  overflow-y: auto;
  margin-top: 10px;
  font-size: 0.9rem;
  color: #444;
}
</style>

</body>
</html>
