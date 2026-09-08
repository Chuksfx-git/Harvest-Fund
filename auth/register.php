<?php
session_start();

$conn = new mysqli("localhost", "root", "", "harvestfund");
if($conn->connect_error){ die("Connection failed: " . $conn->connect_error); }

$message = "";
$old = [];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $fullname = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $role     = $_POST['role'];
    $old      = ['username'=>$fullname,'email'=>$email,'role'=>$role];

    if($password !== $confirm){
        $message = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){
            $message = "An account with that email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $hashed, $role);
            if($stmt->execute()){
                header("Location: login.php?registered=1");
                exit();
            } else {
                $message = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
        $check->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create account — HarvestFund</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --forest:#1A3329;--gold:#C5861F;--gold-lt:#F5E5C0;
      --cream:#F7F3EB;--paper:#fff;--ink:#1C1C1E;--muted:#5C6B5E;
      --line:#E4DDD0;--growth:#2E6B44;--rust:#B5402A;--rust-bg:#FAE5E0;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;color:var(--ink);min-height:100vh;display:flex;-webkit-font-smoothing:antialiased;}
    a{text-decoration:none;color:inherit;}

    /* SPLIT */
    .left-panel{
      width:40%;background:var(--forest);display:flex;flex-direction:column;
      justify-content:space-between;padding:48px 52px;position:relative;overflow:hidden;
    }
    .left-panel::before{
      content:'';position:absolute;inset:0;
      background:radial-gradient(ellipse at 80% 80%,rgba(197,134,31,.18),transparent 55%),
                 radial-gradient(ellipse at 10% 20%,rgba(46,107,68,.2),transparent 50%);
    }
    .left-inner{position:relative;z-index:1;display:flex;flex-direction:column;height:100%;}

    .brand{display:flex;align-items:center;gap:10px;}
    .brand .mark{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--gold),#3f8b5c);}
    .brand span{font-family:'Syne',sans-serif;font-weight:700;font-size:17px;color:#fff;}

    .left-body{flex:1;display:flex;flex-direction:column;justify-content:center;padding:48px 0;}
    .left-body h1{font-family:'Syne',sans-serif;font-size:clamp(26px,3vw,40px);font-weight:800;color:#fff;line-height:1.1;margin-bottom:18px;}
    .left-body h1 em{color:var(--gold);font-style:normal;}
    .left-body p{font-size:15px;color:rgba(255,255,255,.55);line-height:1.75;margin-bottom:36px;max-width:320px;}

    /* Role preview cards on left panel */
    .role-preview{display:flex;flex-direction:column;gap:10px;}
    .rp-card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;}
    .rp-icon{width:36px;height:36px;border-radius:8px;background:rgba(197,134,31,.15);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
    .rp-card h4{font-size:13.5px;color:#fff;font-weight:600;margin-bottom:2px;}
    .rp-card p{font-size:12px;color:rgba(255,255,255,.45);}

    .left-footer{font-size:12px;color:rgba(255,255,255,.25);margin-top:auto;padding-top:24px;}

    /* RIGHT */
    .right-panel{flex:1;display:flex;align-items:center;justify-content:center;padding:48px 5vw;background:var(--cream);overflow-y:auto;}
    .form-box{width:100%;max-width:440px;}

    .form-box h2{font-family:'Syne',sans-serif;font-size:26px;font-weight:700;margin-bottom:6px;}
    .subtitle{font-size:14.5px;color:var(--muted);margin-bottom:30px;}
    .subtitle a{color:var(--growth);font-weight:600;}

    .alert{display:flex;align-items:flex-start;gap:10px;padding:13px 16px;border-radius:10px;font-size:13.5px;font-weight:500;margin-bottom:22px;line-height:1.5;}
    .alert-error{background:var(--rust-bg);color:var(--rust);border:1px solid rgba(181,64,42,.2);}
    .alert svg{flex-shrink:0;margin-top:1px;}

    .field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px;}
    .field label{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);}
    .field input{
      background:var(--paper);border:1.5px solid var(--line);border-radius:10px;
      padding:13px 14px;font-size:15px;font-family:'Inter',sans-serif;color:var(--ink);
      transition:border-color .2s,box-shadow .2s;outline:none;width:100%;
    }
    .field input:focus{border-color:var(--growth);box-shadow:0 0 0 3px rgba(46,107,68,.1);}

    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}

    /* ROLE SELECTOR — card toggle instead of a dropdown */
    .role-label{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:8px;display:block;}
    .role-toggle{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px;}
    .role-option{position:relative;}
    .role-option input{position:absolute;opacity:0;width:0;height:0;}
    .role-card{
      display:flex;flex-direction:column;align-items:center;gap:8px;
      padding:18px 12px;background:var(--paper);border:1.5px solid var(--line);
      border-radius:12px;cursor:pointer;transition:border-color .2s,background .2s;
      text-align:center;
    }
    .role-card .rc-icon{font-size:26px;}
    .role-card .rc-title{font-weight:700;font-size:14px;color:var(--ink);}
    .role-card .rc-sub{font-size:11.5px;color:var(--muted);line-height:1.4;}
    .role-option input:checked + .role-card{
      border-color:var(--growth);background:rgba(46,107,68,.05);
      box-shadow:0 0 0 3px rgba(46,107,68,.1);
    }
    .role-option input:checked + .role-card .rc-title{color:var(--growth);}

    .submit-btn{
      width:100%;background:var(--forest);color:#fff;border:none;border-radius:999px;
      padding:15px;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;
      cursor:pointer;transition:opacity .2s;margin-top:6px;
    }
    .submit-btn:hover{opacity:.88;}

    .bottom-link{text-align:center;font-size:14px;color:var(--muted);margin-top:20px;}
    .bottom-link a{color:var(--growth);font-weight:600;}

    .form-footer{text-align:center;font-size:12px;color:var(--muted);margin-top:28px;padding-top:22px;border-top:1px solid var(--line);}
    .form-footer a{color:var(--growth);}

    @media(max-width:860px){
      body{flex-direction:column;}
      .left-panel{width:100%;padding:36px 24px;}
      .left-body{padding:28px 0;}
      .right-panel{padding:40px 20px;}
    }
    @media(max-width:480px){.form-row{grid-template-columns:1fr;}}
  </style>
</head>
<body>

<!-- LEFT -->
<div class="left-panel">
  <div class="left-inner">
    <div class="brand"><div class="mark"></div><span>HarvestFund</span></div>

    <div class="left-body">
      <h1>Join the platform<br>that's <em>growing Nigeria</em></h1>
      <p>Whether you're a farmer who needs capital or an investor seeking returns — there's a place for you here.</p>
      <div class="role-preview">
        <div class="rp-card">
          <div class="rp-icon">🌾</div>
          <div><h4>I'm a Farmer</h4><p>Get funded, receive inputs, grow your farm.</p></div>
        </div>
        <div class="rp-card">
          <div class="rp-icon">📈</div>
          <div><h4>I'm an Investor</h4><p>Fund verified farms and earn harvest returns.</p></div>
        </div>
      </div>
    </div>

    <div class="left-footer">© 2025 HarvestFund. All rights reserved.</div>
  </div>
</div>

<!-- RIGHT -->
<div class="right-panel">
  <div class="form-box">
    <h2>Create your account</h2>
    <p class="subtitle">Already have an account? <a href="login.php">Log in →</a></p>

    <?php if($message !== ""): ?>
    <div class="alert alert-error">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="register.php">

      <div class="form-row">
        <div class="field">
          <label>Full name</label>
          <input type="text" name="username" placeholder="Adaeze Obi" required value="<?php echo isset($old['username']) ? htmlspecialchars($old['username']) : ''; ?>">
        </div>
        <div class="field">
          <label>Email address</label>
          <input type="email" name="email" placeholder="you@example.com" required value="<?php echo isset($old['email']) ? htmlspecialchars($old['email']) : ''; ?>">
        </div>
      </div>

      <div class="form-row">
        <div class="field">
          <label>Password</label>
          <input type="password" name="password" placeholder="Create a password" required autocomplete="new-password">
        </div>
        <div class="field">
          <label>Confirm password</label>
          <input type="password" name="confirm_password" placeholder="Repeat password" required autocomplete="new-password">
        </div>
      </div>

      <!-- ROLE TOGGLE — visual card selector replacing the plain dropdown -->
      <span class="role-label">I am joining as a</span>
      <div class="role-toggle">
        <label class="role-option">
          <input type="radio" name="role" value="farmer" <?php echo (isset($old['role']) && $old['role']==='farmer') ? 'checked' : ''; ?> required>
          <div class="role-card">
            <span class="rc-icon">🌾</span>
            <span class="rc-title">Farmer</span>
            <span class="rc-sub">I need funding for my farm</span>
          </div>
        </label>
        <label class="role-option">
          <input type="radio" name="role" value="investor" <?php echo (isset($old['role']) && $old['role']==='investor') ? 'checked' : ''; ?>>
          <div class="role-card">
            <span class="rc-icon">📈</span>
            <span class="rc-title">Investor</span>
            <span class="rc-sub">I want to fund farms and earn returns</span>
          </div>
        </label>
      </div>

      <button type="submit" name="register" class="submit-btn">Create my account →</button>
    </form>

    <div class="bottom-link">Already have an account? <a href="login.php">Log in</a></div>
    <div class="form-footer">By registering you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></div>
  </div>
</div>

</body>
</html>