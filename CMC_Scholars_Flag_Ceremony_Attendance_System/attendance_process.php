<?php
include('db_connect.php');
date_default_timezone_set('Asia/Manila');

// 🧩 Get student input (from form)
$student_input = isset($_POST['input_value']) ? trim($_POST['input_value']) : '';

if ($student_input === '') {
    header("Location: index.php?error=empty_id");
    exit;
}

// 🕒 Get current day and time
$current_day = date('l');          // Monday, Tuesday, etc.
$current_time = date('H:i:s');     // e.g. 16:25:45
$current_date = date('Y-m-d');     // e.g. 2025-10-31

// 🧠 Check if student exists
$sql = "SELECT * FROM students WHERE student_id = ? OR fullname LIKE ?";
$stmt = $conn->prepare($sql);
$like = "%$student_input%";
$stmt->bind_param("ss", $student_input, $like);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?error=notfound");
    exit;
}

$student = $result->fetch_assoc();
$student_id = $student['student_id'];
$fullname = $student['fullname'];

// 🏫 Determine attendance status (only for Monday or Friday)
$status = "Absent"; // default

if ($current_day === "Monday") {
    if ($current_time <= "07:30:59") {
        $status = "Present";
    } elseif ($current_time <= "07:46:59") {
        $status = "Late";
    } else {
        $status = "Absent";
    }
} elseif ($current_day === "Friday") {
    // Convert current time to 24-hour format for comparison
    $time_in_seconds = strtotime($current_time);
    $start_time = strtotime('16:30:00'); // 4:30 PM
    $late_time = strtotime('16:45:59');  // 4:45:59 PM
    
    if ($time_in_seconds < $start_time) {
        // If logged before 4:30 PM
        $status = "Absent";
        $message = "❌ Too early for Flag Retreat. Please log in between 4:30 PM - 4:45 PM.";
    } elseif ($time_in_seconds <= $late_time) {
        // If logged between 4:30 PM - 4:45:59 PM
        $status = "Present";
    } else {
        // If logged after 4:45:59 PM
        $status = "Absent";
    }
} else {
    header("Location: index.php?error=notflagday");
    exit;
}

// 🧾 Check if student already logged attendance today
$check = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? AND date = ?");
$check->bind_param("ss", $student_id, $current_date);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows > 0) {
    // Already recorded
    $message = "🕓 You have already logged your attendance for today.";
    header("Location: success.php?name=" . urlencode($fullname) . "&status=Already Logged" . "&msg=" . urlencode($message));
    exit;
}

// 📝 Insert new attendance record
$insert = $conn->prepare("INSERT INTO attendance (student_id, date, day, time_in, status) VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("sssss", $student_id, $current_date, $current_day, $current_time, $status);
$insert->execute();

// 💬 Message for success page
if ($status === "Present") {
    $message = "✅ Attendance recorded successfully. You are marked as Present.";
} elseif ($status === "Late") {
    $message = "⚠️ You are marked as Late.";
} else {
    $message = "❌ You have logged in after the allowed time and are marked as Absent.";
}

// 🚀 Redirect to success page
header("Location: success.php?name=" . urlencode($fullname) . "&status=" . urlencode($status) . "&msg=" . urlencode($message));
exit;
?>