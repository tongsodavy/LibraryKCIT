<?php
// បើកដំណើរការ Session ប្រសិនបើមិនទាន់បានបើក
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db.php';
require_once '../includes/auth.php';

// ប្រសិនបើធ្លាប់បាន Login រួចហើយ (ទោះជា Admin ឬ Member) ឱ្យរុញទៅ Dashboard ភ្លាម
if (isset($_SESSION['admin_id']) || isset($_SESSION['member_id'])) { 
    header('Location: dashboard.php'); 
    exit; 
}

$error = '';
$username_val = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = function_exists('sanitize') ? sanitize($_POST['username'] ?? '') : htmlspecialchars(trim($_POST['username'] ?? ''));
    $username_val = $_POST['username'] ?? ''; 
    $password = $_POST['password'] ?? '';
    
    // =======================================================
    // ជំហានទី១៖ ពិនិត្យមើលនៅក្នុងតារាង `admin` មុនគេ
    // =======================================================
    $stmt_admin = $conn->prepare("SELECT id, password, full_name FROM admin WHERE username = ?");
    $stmt_admin->bind_param("s", $username);
    $stmt_admin->execute();
    $admin_result = $stmt_admin->get_result()->fetch_assoc();
    
    if ($admin_result) {
        // ផ្ទៀងផ្ទាត់លេខសម្ងាត់សម្រាប់ Admin (Plain Text យោងតាមកូដចាស់របស់បង)
        if ($password === $admin_result['password']) {
            $_SESSION['admin_id'] = $admin_result['id'];
            $_SESSION['admin_name'] = $admin_result['full_name'];
            $_SESSION['user_type'] = 'admin'; // សម្គាល់ប្រភេទអ្នកប្រើប្រាស់
            
            // ធ្វើបច្ចុប្បន្នភាពកាលបរិច្ឆេទចូលប្រើចុងក្រោយរបស់ Admin
            $conn->query("UPDATE admin SET last_login = NOW() WHERE id = {$admin_result['id']}");
            
            header('Location: ../admin/dashboard.php'); // ឬរុញទៅកាន់ ../admin/dashboard.php តាមរចនាសម្ព័ន្ធបង
            exit;
        } else {
            $error = 'ឈ្មោះអ្នកប្រើ ឬ លេខសម្ងាត់មិនត្រឹមត្រូវ!';
        }
    } else {
        // =======================================================
        // ជំហានទី២៖ បើរកមិនឃើញក្នុងតារាង Admin ទេ មកស្វែងរកក្នុងតារាង `members` ម្តង
        // =======================================================
        $stmt_member = $conn->prepare("SELECT id, password, name, member_code, role FROM members WHERE username = ?");
        $stmt_member->bind_param("s", $username);
        $stmt_member->execute();
        $member_result = $stmt_member->get_result()->fetch_assoc();
        
        if ($member_result && password_verify($password, $member_result['password'])) {
            $_SESSION['member_id'] = $member_result['id'];
            $_SESSION['member_name'] = $member_result['name'];
            $_SESSION['member_code'] = $member_result['id_student']; 
            $_SESSION['member_role'] = $member_result['role']; // 'student' ឬ 'teacher'      
            $_SESSION['user_type'] = 'member';
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'ឈ្មោះអ្នកប្រើ ឬ លេខសម្ងាត់មិនត្រឹមត្រូវ!';
        }
        $stmt_member->close();
    }
    $stmt_admin->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="km">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - KCIT Library System</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;600;700&family=Siemreap&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Moul&display=swap" rel="stylesheet">

<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{ 
    font-family: 'Siemreap', 'Segoe UI', sans-serif; min-height:100vh;
    background:linear-gradient(135deg,#84ADFA 0%,#F0F2EB 50%,#B0AF94 100%);
    display:flex;align-items:center;justify-content:center
  }
  .login-card{
    background:rgba(254, 254, 254, 0.65);backdrop-filter:blur(20px);
    border:1px solid rgba(255, 255, 255, 0.5);border-radius:24px;padding:48px 40px;
    width:420px;box-shadow:0 25px 50px rgba(0,0,0,0.15);
    animation:fadeIn .5s ease
  }
  @keyframes fadeIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
  
  .logo{text-align:center;margin-bottom:32px}
  .logo-icon{
    width:72px;height:72px;background:linear-gradient(135deg,#27ae60,#1e8449);
    border-radius:20px;display:inline-flex;align-items:center;justify-content:center;font-size:32px;
    color:white;margin-bottom:16px;box-shadow:0 8px 24px rgba(39,174,96,0.2)
  }
  .logo h1{color:blue;font-size:22px;font-family: 'Moul', cursive; }
  .logo p{color:rgba(245, 3, 3, 0.82);font-size:14px;margin-top:4px}
  
  .form-group{margin-bottom:20px}
  label{display:block;color:rgba(7, 4, 213, 0.91);font-size:13px;margin-bottom:8px;font-weight:bold}
  
  .input-wrap{position:relative}
  .input-wrap i.ic{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:rgba(6, 15, 194, 0.73);font-size:15px}

  input[type=text],input[type=password]{
    width:100%;padding:13px 42px;
    background:rgb(252, 252, 252);border:2px solid rgba(19, 14, 14, 0.12);
    border-radius:12px;color:black;font-size:14px;transition:all .3s;font-weight:bold;
  }
  input:focus{outline:none;border-color:#27ae60;background:rgba(238, 239, 223, 0.69)}
  
  .toggle-pwd{position:absolute;right:14px;top:50%;transform:translateY(-50%);cursor:pointer;color:rgba(6, 15, 194, 0.73)}
  
  .btn-login{
    width:100%;padding:14px;background:linear-gradient(135deg,#7de2a7,#74dfa1);
    font-family: 'Siemreap', 'Segoe UI', sans-serif;
    border:none;border-radius:12px;color:black;font-size:15px;
    font-weight:600;cursor:pointer;transition:all .3s;margin-top:8px
  }
  .btn-login:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(39,174,96,0.4)}
  
  .error{
    background:rgba(231,76,60,0.15);border:1px solid rgba(231,76,60,0.4);color:#e74c3c;
    padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px
  }
  
  .footer-links{text-align:center;margin-top:24px;color:rgba(0, 0, 0, 0.95);font-size:13px;display:flex;flex-direction:column;gap:10px}
  .footer-links a{color:#27ae60;text-decoration:none;font-weight:600}
  .footer-links a:hover{text-decoration:underline}
  .divider{height:1px;background:rgba(0, 0, 0, 0.08);margin:10px 0}
</style>
</head>
<body>

<div class="login-card">
  <div class="logo">
    <div class="logo-icon"><i class="fas fa-book-open"></i></div>
    <h1>ប្រព័ន្ធបណ្ណាល័យ-KCIT</h1>
    <p>Library Management System</p>
  </div>
  
  <?php if($error): ?>
  <div class="error"><i class="fas fa-exclamation-circle"></i><?= $error ?></div>
  <?php endif; ?>
  
  <form method="POST">
    <div class="form-group">
      <label>ឈ្មោះអ្នកប្រើ</label>
      <div class="input-wrap">
        <i class="fas fa-user ic"></i>
        <input type="text" name="username" placeholder="Username" 
        value="<?= htmlspecialchars($username_val) ?>" required>
      </div>
    </div>
    <div class="form-group">
      <label>លេខសម្ងាត់</label>
      <div class="input-wrap">
        <i class="fas fa-lock ic"></i>
        <input type="password" name="password" id="pwd" placeholder="••••••••" required>
        <i class="fas fa-eye toggle-pwd" onclick="togglePwd()"></i>
      </div>
    </div>
    <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> ចូលប្រព័ន្ធ</button>
  </form>

  <div class="footer-links">
    <div>សម្រាប់និស្សិត/គ្រូ៖ <a href="register_member.php">ចុះឈ្មោះនៅទីនេះ</a></div>
  </div>
</div>

<script>
function togglePwd(){
  const p=document.getElementById('pwd'),
  i=document.querySelector('.toggle-pwd');
  p.type=p.type==='password'?'text':'password';
  i.className='fas fa-eye'+(p.type==='text'?'-slash':'')+' toggle-pwd';
}
</script>
</body>
</html>