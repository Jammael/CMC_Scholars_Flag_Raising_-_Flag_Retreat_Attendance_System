<?php
include('db_connect.php');
date_default_timezone_set('Asia/Manila');
$dayNum = date('N'); // 1 (Mon) .. 7 (Sun)
$isFlagDay = in_array($dayNum, [1, 5]);
$todayName = date('l');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>CMC Scholars — Flag Raising & Flag Retreat Attendance</title>
  <link rel="stylesheet" href="bootstrap-5.3.8-dist/css/bootstrap.min.css">
  <link rel="icon" type="image/jpg" href="images/favicon.jpg"/>
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      overflow-y: hidden;
      background-image: url(images/background_image.jpg);
      background-repeat: no-repeat;
      background-position: center;
      background-size: cover;
      background-attachment: fixed;
      font-family: "Poppins", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color: #fff;
    }

    .container, .card.custom-shadow, .banner { position: relative; z-index:2; }

    .banner { background: linear-gradient(90deg,#0b63d6,#075ad5); color:#fff; padding:18px; border-radius:.5rem; margin-bottom:1rem; }

    .transparent-card {
      background: rgba(46, 57, 77, 0.7) !important; /* More transparent background */
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    /* floating label color override for dark background */
    .form-label, .form-text { color: #dceff0; }
    .form-control {
      background: rgba(240, 241, 243, 0.9);
      color: #050a21;
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(5px);
    }
    .form-select {
      background-color: rgba(240, 241, 243, 0.9);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .btn-primary { background: linear-gradient(90deg,#6ec1ff,#7b61ff); border: none; color:#061022; }
    .btn-outline-primary {
      color: #fff;
      border-color: rgba(255, 255, 255, 0.4);
      background: rgba(109, 193, 255, 0.1);
    }
    .btn-outline-primary:hover {
      background: rgba(13, 110, 253, 0.9) !important; /* Bootstrap primary blue with opacity */
      border-color: rgba(13, 110, 253, 0.9) !important;
      color: #fff !important;
    }
    .small-muted {
      color: rgba(255, 255, 255, 0.8);
      font-size: 0.875rem;
    }

    /* Add a subtle gradient overlay to the body */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(
          45deg,
          rgba(0, 0, 0, 0.4),
          rgba(0, 0, 0, 0.2)
      );
      z-index: 1;
    }

    /* Ensure container stays above the overlay */
    .container {
      position: relative;
      z-index: 2;
      max-width: 450px !important;
      margin: auto !important; /* Center vertically */
      padding: 1.5rem !important;
      height: auto;
    }

    /* Update card styles for better proportions */
    .card.transparent-card {
      border-radius: 15px;
      margin: 0;
    }

    /* Adjust padding of container */
    .container.pt-3.pb-3 {
      padding-top: 0 !important;
      padding-bottom: 0 !important;
    }

    /* Adjust card body padding */
    .card-body.p-4.p-md-5 {
      padding: 1.5rem !important;
    }

    /* Make logo slightly smaller */
    .card-body img {
      width: 80px !important;  /* Increased from 60px */
      height: 80px !important; /* Increased from 60px */
      margin-bottom: 1rem !important;
    }

    /* Adjust title text */
    .card-body h4 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
    }

    /* Form group spacing */
    .mb-3 {
      margin-bottom: 0.75rem !important;
    }

    /* Form controls sizing */
    .form-control, .form-select {
      height: 38px;
    }

    /* Update the button styling */
    .btn-lg {
      height: 38px;
      line-height: 1;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Quick info section */
    .mt-4 {
      margin-top: 0.75rem !important;
    }

    /* Footer adjustments */
    .card-footer {
      padding: 1rem;
      font-size: 0.875rem;
    }

    /* Update column sizes */
    @media (min-width: 992px) {
      .col-lg-5 {
        flex: 0 0 auto;
        width: 450px;
      }
    }
  </style>
</head>
<body>


  <div class="container pt-3 pb-3">
      <div class="row justify-content-center">
        <div class="col-11 col-sm-10 col-md-8 col-lg-5"> <!-- Updated column sizes -->
          <div class="card transparent-card shadow-sm">
            <div class="card-body p-4 p-md-5">
              <div class="text-center mb-3">
                <img src="images/CMC_Logo.jpg" alt="CMC Logo" class="rounded-circle mb-2 mt-0" style="width: 120px;height: 120px;border-radius:0;">
                <h4 class="mb-1 fw-bold text-white">CMC Scholars — Flag Raising & Flag Retreat Attendance</h4>
                <p class="small-muted mb-0 fw-bold text-white">Competence Meets Character.</p>
              </div>

              <form id="loginForm" action="admin_login.php" method="POST" autocomplete="off" novalidate>
                <div class="mb-3">
                  <label for="loginType" class="form-label">I am logging in as</label>
                  <select id="loginType" name="login_type" class="form-select" required>
                    <option value="admin">Admin / Coordinator</option>
                    <option value="student">Student</option>
                  </select>
                  <div class="form-text small-muted">Choose Admin to access the dashboard, or Student to log attendance.</div>
                </div>

                <div id="adminFields" class="mb-3">
                  <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input id="username" name="username" type="text" class="form-control" required>
                  </div>

                  <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password" class="form-control" required>
                  </div>
                </div>

                <div id="studentFields" class="mb-3 d-none">
                  <div class="mb-2">
                    <label for="student_input" class="form-label">School ID or Full Name</label>
                    <input id="student_input" name="input_value" type="text" class="form-control" placeholder="">
                  </div>
                  <div class="form-text small-muted">Students may only log attendance on Flag Ceremony (Monday) & Flag Retreat (Friday).</div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label small-muted" for="remember">Remember me</label>
                  </div>
                  <a href="forgot_password.php" class="small-muted ">Forgot password?</a>
                </div>

                <div class="col d-grid gap-2 mt-4 mb-2">
                  <button id="primaryBtn" type="submit" class="btn btn-outline-primary btn-lg">Admin Login</button>
                </div>

                <!-- <div class="col d-flex gap-2 mt-3">
                  <button type="button" class="btn btn-outline-primary flex-fill" id="btnDemo">Demo</button>
                  <button type="button" class="btn btn-outline-primary flex-fill" onclick="location.reload();">Reset</button>
                </div> -->

                <div class="text-center mt-4">
                  <div class="fw-bold text-white">Quick Info</div>
                  <div class="small-muted mt-1">Today: <strong><?php echo htmlspecialchars($todayName); ?></strong>
                    <?php if (!$isFlagDay): ?>
                      &nbsp;•&nbsp;<span class="text-warning fw-semibold">Attendance closed</span>
                    <?php else: ?>
                      &nbsp;•&nbsp;<span class="text-success fw-semibold pt-2">Flag Day — attendance open</span>
                    <?php endif; ?>
                  </div>
                </div>
                 <div class="text-center small-muted pt-2 mt-4">
              © <span id="yr">2025</span> CMC Scholars. All rights reserved.
            </div>
              </form>

            </div>
            <!-- <div class="card-footer text-center small-muted">
              © <span id="yr"></span> CMC Scholars. All rights reserved.
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </main>
  <script>
    (function(){
      const loginType = document.getElementById('loginType');
      const adminFields = document.getElementById('adminFields');
      const studentFields = document.getElementById('studentFields');
      const form = document.getElementById('loginForm');
      const primaryBtn = document.getElementById('primaryBtn');
      const studentInput = document.getElementById('student_input');
      const username = document.getElementById('username');
      const password = document.getElementById('password');
      const btnDemo = document.getElementById('btnDemo');

      const isFlagDay = <?php echo $isFlagDay ? 'true' : 'false'; ?>;

      function updateMode(){
        const mode = loginType.value;
        if(mode === 'admin'){
          adminFields.classList.remove('d-none');
          studentFields.classList.add('d-none');
          form.action = 'admin_login.php';
          primaryBtn.textContent = 'Admin Login';
          primaryBtn.disabled = false;
        } else {
          adminFields.classList.add('d-none');
          studentFields.classList.remove('d-none');
          form.action = 'attendance_process.php';
          primaryBtn.textContent = 'Log Attendance';
          primaryBtn.disabled = !isFlagDay;
        }
      }

      // Update mode on change
      loginType.addEventListener('change', updateMode);

      // Demo button handler
      btnDemo.addEventListener('click', function() {
        loginType.value = 'admin';
        username.value = 'demo';
        password.value = 'demo123';
        updateMode();
      });

      // Set current year in footer
      document.getElementById('yr').textContent = new Date().getFullYear();

      // Initialize form state
      updateMode();
    })();
  </script>
</body>
</html>