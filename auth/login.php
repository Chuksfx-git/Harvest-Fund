<?php
session_start();
include("../config/database.php");

$message = "";
$msg_type = "";

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role']     = $user['role'];
            if($user['role'] == "farmer")   { header("Location: ../farmer/dashboard.php");   exit(); }
            if($user['role'] == "investor") { header("Location: ../investor/dashboard.php"); exit(); }
        } else {
            $message = "Incorrect password. Please try again.";
            $msg_type = "error";
        }
    } else {
        $message = "No account found with that email address.";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log in — HarvestFund</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --forest:#1A3329; --forest2:#213D2F; --gold:#C5861F; --gold-lt:#F5E5C0;
      --cream:#F7F3EB; --paper:#fff; --ink:#1C1C1E; --muted:#5C6B5E;
      --line:#E4DDD0; --growth:#2E6B44; --rust:#B5402A; --rust-bg:#FAE5E0;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;color:var(--ink);background:var(--paper);min-height:100vh;display:flex;-webkit-font-smoothing:antialiased;}
    a{text-decoration:none;color:inherit;}

    /* ── SPLIT LAYOUT ── */
    .left-panel{
      width:42%; background:var(--forest);
      display:flex; flex-direction:column; justify-content:space-between;
      padding:48px 52px; position:relative; overflow:hidden;
    }
    .left-panel::before{
      content:'';position:absolute;inset:0;
      background:radial-gradient(ellipse at 20% 80%,rgba(197,134,31,.18),transparent 60%),
                 radial-gradient(ellipse at 80% 20%,rgba(46,107,68,.2),transparent 50%);
      pointer-events:none;
    }
    /* grain */
    .left-panel::after{
      content:'';position:absolute;inset:0;
      background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.035'/%3E%3C/svg%3E");
      pointer-events:none;
    }
    .left-inner{position:relative;z-index:1;display:flex;flex-direction:column;height:100%;justify-content:space-between;}

    .brand{display:flex;align-items:center;gap:10px;}
    .brand .mark{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--gold),#3f8b5c);flex-shrink:0;}
    .brand span{font-family:'Syne',sans-serif;font-weight:700;font-size:17px;color:#fff;}

    .left-body{flex:1;display:flex;flex-direction:column;justify-content:center;padding:48px 0;}
    .left-body h1{font-family:'Syne',sans-serif;font-size:clamp(28px,3.2vw,42px);font-weight:800;color:#fff;line-height:1.1;margin-bottom:18px;}
    .left-body h1 em{color:var(--gold);font-style:normal;}
    .left-body p{font-size:15px;color:rgba(255,255,255,.55);line-height:1.75;margin-bottom:40px;max-width:340px;}

    .stat-row{display:flex;gap:32px;flex-wrap:wrap;}
    .stat .n{font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:var(--gold);}
    .stat .l{font-size:12px;color:rgba(255,255,255,.45);margin-top:3px;}

    .left-footer{font-size:12px;color:rgba(255,255,255,.25);}

    /* ── RIGHT PANEL (FORM) ── */
    .right-panel{flex:1;display:flex;align-items:center;justify-content:center;padding:48px 6vw;background:var(--cream);}
    .form-box{width:100%;max-width:420px;}

    .form-box h2{font-family:'Syne',sans-serif;font-size:26px;font-weight:700;margin-bottom:6px;}
    .form-box .subtitle{font-size:14.5px;color:var(--muted);margin-bottom:32px;}
    .form-box .subtitle a{color:var(--growth);font-weight:600;}

    .alert{display:flex;align-items:flex-start;gap:10px;padding:13px 16px;border-radius:10px;font-size:13.5px;font-weight:500;margin-bottom:22px;line-height:1.5;}
    .alert-error{background:var(--rust-bg);color:var(--rust);border:1px solid rgba(181,64,42,.2);}
    .alert svg{flex-shrink:0;margin-top:1px;}

    .field{display:flex;flex-direction:column;gap:6px;margin-bottom:16px;}
    .field label{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);}
    .field input{
      background:var(--paper);border:1.5px solid var(--line);border-radius:10px;
      padding:13px 14px;font-size:15px;font-family:'Inter',sans-serif;color:var(--ink);
      transition:border-color .2s,box-shadow .2s;outline:none;width:100%;
    }
    .field input:focus{border-color:var(--growth);box-shadow:0 0 0 3px rgba(46,107,68,.1);}

    .field-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;}
    .field-row label{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);}
    .forgot{font-size:12.5px;color:var(--growth);font-weight:600;}

    .remember{display:flex;align-items:center;gap:8px;margin-bottom:24px;cursor:pointer;}
    .remember input{width:16px;height:16px;accent-color:var(--growth);cursor:pointer;}
    .remember span{font-size:13.5px;color:var(--muted);}

    .submit-btn{
      width:100%;background:var(--forest);color:#fff;border:none;border-radius:999px;
      padding:15px;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;
      cursor:pointer;transition:opacity .2s;letter-spacing:.01em;
    }
    .submit-btn:hover{opacity:.88;}
    .submit-btn:active{transform:scale(.99);}

    .divider{display:flex;align-items:center;gap:12px;margin:24px 0;}
    .divider span{font-size:12px;color:var(--muted);white-space:nowrap;}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--line);}

    .bottom-link{text-align:center;font-size:14px;color:var(--muted);margin-top:20px;}
    .bottom-link a{color:var(--growth);font-weight:600;}

    .form-footer{text-align:center;font-size:12px;color:var(--muted);margin-top:32px;padding-top:24px;border-top:1px solid var(--line);}

    @media(max-width:820px){
      body{flex-direction:column;}
      .left-panel{width:100%;padding:36px 24px;min-height:auto;}
      .left-body{padding:32px 0;}
      .right-panel{padding:40px 24px;}
    }
  </style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
  <div class="left-inner">
    <div class="brand">
      <div class="mark"></div>
      <span>HarvestFund</span>
    </div>

    <div class="left-body">
      <h1>Every harvest<br>starts with a <em>single seed</em></h1>
      <p>Log in to track your farm investments, monitor cycle progress, and collect your returns — all in one place.</p>
      <div class="stat-row">
        <div class="stat"><div class="n">1,000+</div><div class="l">Farmers to empower</div></div>
        <div class="stat"><div class="n">₦500M</div><div class="l">Funding goal</div></div>
      </div>
    </div>

    <div class="left-footer">© 2025 HarvestFund. All rights reserved.</div>
  </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
  <div class="form-box">
    <h2>Welcome back</h2>
    <p class="subtitle">Don't have an account? <a href="register.php">Create one →</a></p>

    <?php if($message !== ""): ?>
    <div class="alert alert-error">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="field">
        <label>Email address</label>
        <input type="email" name="email" placeholder="you@example.com" required autocomplete="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
      </div>

      <div class="field">
        <div class="field-row">
          <label>Password</label>
          <a href="forgot_password.php" class="forgot">Forgot password?</a>
        </div>
        <input type="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
      </div>

      <label class="remember">
        <input type="checkbox" name="remember-me">
        <span>Keep me logged in</span>
      </label>

      <button type="submit" name="login" class="submit-btn">Log in to HarvestFund →</button>
    </form>

    <div class="divider"><span>New to HarvestFund?</span></div>
    <div class="bottom-link"><a href="register.php">Create a free account</a></div>

    <div class="form-footer">
      By logging in you agree to our <a href="#" style="color:var(--growth);">Terms</a> and <a href="#" style="color:var(--growth);">Privacy Policy</a>
    </div>
  </div>
</div>

</body>
</html>