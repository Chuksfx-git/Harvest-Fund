<?php
$message = "";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if(isset($_POST['send_message'])){
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $subject = htmlspecialchars($_POST['subject']);
  $userMessage = htmlspecialchars($_POST['message']);
  $mail = new PHPMailer(true);
  try{
    $mail->isSMTP(); $mail->Host='smtp.gmail.com'; $mail->SMTPAuth=true;
    $mail->Username='harvestfundbusiness@gmail.com'; $mail->Password='llrw xoxj tjbz vtqn';
    $mail->SMTPSecure='tls'; $mail->Port=587;
    $mail->setFrom('harvestfundbusiness@gmail.com','HarvestFund');
    $mail->addAddress('harvestfundbusiness@gmail.com');
    $mail->addReplyTo($email,$name);
    $mail->isHTML(true);
    $mail->Subject="Contact Form: ".$subject;
    $mail->Body="<h3>New Contact Message</h3><p><strong>Name:</strong> {$name}</p><p><strong>Email:</strong> {$email}</p><p><strong>Subject:</strong> {$subject}</p><p><strong>Message:</strong><br>{$userMessage}</p>";
    $mail->send(); $message="success";
  }catch(Exception $e){ $message="error"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact — HarvestFund</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    :root{--forest:#1A3329;--gold:#C5861F;--gold-lt:#F5E5C0;--cream:#F7F3EB;--paper:#fff;--ink:#1C1C1E;--muted:#5C6B5E;--line:#E4DDD0;--growth:#2E6B44;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html{scroll-behavior:smooth;}
    body{font-family:'Inter',sans-serif;color:var(--ink);background:var(--paper);-webkit-font-smoothing:antialiased;}
    a{text-decoration:none;color:inherit;}

    .navbar{position:fixed;top:0;left:0;right:0;z-index:999;display:flex;align-items:center;justify-content:space-between;padding:18px 6vw;transition:background .3s,box-shadow .3s;}
    .navbar.scrolled{background:rgba(26,51,41,.97);box-shadow:0 2px 20px rgba(0,0,0,.3);}
    .nav-logo{display:flex;align-items:center;gap:10px;}
    .nav-logo .mark{width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--gold),#3f8b5c);}
    .nav-logo span{font-family:'Syne',sans-serif;font-weight:700;font-size:17px;color:#fff;}
    .nav-links{display:flex;align-items:center;gap:6px;list-style:none;}
    .nav-links a{color:rgba(255,255,255,.8);font-size:14px;font-weight:500;padding:6px 12px;border-radius:8px;transition:color .2s;}
    .nav-links a:hover{color:#fff;}
    .nav-btn{background:var(--gold);color:var(--forest) !important;font-weight:700 !important;padding:9px 20px !important;border-radius:999px !important;}
    .hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;}
    .hamburger span{display:block;width:22px;height:2px;background:#fff;border-radius:2px;}

    .hero{min-height:52vh;background:linear-gradient(160deg,#0d2118,#1a3329 60%,#1f4535);display:flex;align-items:flex-end;padding:120px 6vw 64px;position:relative;overflow:hidden;}
    .hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 80% 20%,rgba(197,134,31,.12),transparent 60%);pointer-events:none;}
    .hero-inner{position:relative;z-index:1;max-width:600px;}
    .eyebrow{display:inline-block;background:rgba(197,134,31,.15);border:1px solid rgba(197,134,31,.3);color:var(--gold-lt);font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;padding:5px 14px;border-radius:999px;margin-bottom:20px;}
    .hero h1{font-family:'Syne',sans-serif;font-size:clamp(34px,5vw,60px);font-weight:800;color:#fff;line-height:1.1;margin-bottom:16px;}
    .hero h1 em{color:var(--gold);font-style:normal;}
    .hero p{font-size:16px;color:rgba(255,255,255,.6);line-height:1.7;}

    /* CONTACT LAYOUT */
    .contact-wrap{display:grid;grid-template-columns:1fr 1.4fr;gap:0;min-height:600px;}

    /* INFO PANEL */
    .info-panel{background:var(--forest);padding:64px 48px;display:flex;flex-direction:column;justify-content:space-between;}
    .info-panel h2{font-family:'Syne',sans-serif;font-size:24px;color:#fff;margin-bottom:8px;}
    .info-panel > p{font-size:14.5px;color:rgba(255,255,255,.55);line-height:1.7;margin-bottom:48px;}
    .info-items{display:flex;flex-direction:column;gap:28px;}
    .info-item{display:flex;align-items:flex-start;gap:16px;}
    .info-item-icon{width:40px;height:40px;background:rgba(197,134,31,.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .info-item-icon i{color:var(--gold);font-size:15px;}
    .info-item h4{font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.4);font-weight:600;margin-bottom:4px;}
    .info-item p{font-size:14.5px;color:rgba(255,255,255,.75);}

    /* FORM PANEL */
    .form-panel{background:var(--cream);padding:64px 52px;}
    .form-panel h2{font-family:'Syne',sans-serif;font-size:22px;font-weight:700;margin-bottom:6px;}
    .form-panel > p{font-size:14px;color:var(--muted);margin-bottom:36px;}

    .alert{padding:13px 16px;border-radius:10px;font-size:14px;font-weight:500;margin-bottom:24px;}
    .alert-success{background:#e2efe7;color:var(--growth);border:1px solid #b8dcc4;}
    .alert-error{background:#fae5e0;color:#b5402a;border:1px solid #f0c0b4;}

    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
    .field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px;}
    .field label{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);}
    .field input,.field textarea{
      background:#fff;border:1.5px solid var(--line);border-radius:10px;
      padding:13px 14px;font-size:14.5px;font-family:'Inter',sans-serif;color:var(--ink);
      transition:border-color .2s,box-shadow .2s;outline:none;width:100%;
    }
    .field input:focus,.field textarea:focus{border-color:var(--growth);box-shadow:0 0 0 3px rgba(46,107,68,.1);}
    .field textarea{resize:vertical;min-height:130px;}
    .submit-btn{
      width:100%;background:var(--forest);color:#fff;border:none;border-radius:999px;
      padding:14px;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;
      cursor:pointer;transition:opacity .2s;margin-top:6px;
    }
    .submit-btn:hover{opacity:.88;}

    /* FOOTER */
    footer{background:#0d1f16;padding:56px 6vw 32px;}
    .footer-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr;gap:40px;margin-bottom:40px;}
    .footer-brand .mark{width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,var(--gold),#3f8b5c);margin-bottom:12px;}
    .footer-brand h3{font-family:'Syne',sans-serif;color:#fff;font-size:16px;margin-bottom:10px;}
    .footer-brand p{font-size:13px;color:rgba(255,255,255,.4);line-height:1.65;}
    footer h4{color:rgba(255,255,255,.4);font-size:11px;text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;}
    footer a{display:block;color:rgba(255,255,255,.6);font-size:14px;margin-bottom:8px;transition:color .2s;}
    footer a:hover{color:#fff;}
    .footer-bottom{border-top:1px solid rgba(255,255,255,.08);padding-top:24px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;}
    .footer-bottom p{font-size:13px;color:rgba(255,255,255,.3);}

    @media(max-width:900px){.contact-wrap{grid-template-columns:1fr;}.form-row{grid-template-columns:1fr;}.footer-grid{grid-template-columns:1fr 1fr;}}
    @media(max-width:640px){.nav-links{display:none;position:fixed;inset:0;background:var(--forest);flex-direction:column;justify-content:center;align-items:center;gap:20px;}.nav-links.open{display:flex;}.hamburger{display:flex;}.info-panel{padding:40px 24px;}.form-panel{padding:40px 24px;}.footer-grid{grid-template-columns:1fr;}}
  </style>
</head>
<body>

<nav class="navbar" id="navbar">
  <div class="nav-logo"><div class="mark"></div><span>HarvestFund</span></div>
  <ul class="nav-links" id="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="contact.php">Contact</a></li>
    <li><a href="../auth/register.php">Register</a></li>
    <li><a href="../auth/login.php" class="nav-btn">Log in</a></li>
  </ul>
  <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
</nav>

<section class="hero">
  <div class="hero-inner">
    <div class="eyebrow">Get in touch</div>
    <h1>We're here<br><em>to help you grow</em></h1>
    <p>Have questions about investing, farming, or the platform? Send us a message and we'll get back to you.</p>
  </div>
</section>

<div class="contact-wrap">
  <!-- INFO -->
  <div class="info-panel">
    <div>
      <h2>Contact information</h2>
      <p>Reach out any time. Our team responds within 24 hours on business days.</p>
      <div class="info-items">
        <div class="info-item">
          <div class="info-item-icon"><i class="fas fa-location-dot"></i></div>
          <div><h4>Address</h4><p>Lagos, Nigeria</p></div>
        </div>
        <div class="info-item">
          <div class="info-item-icon"><i class="fas fa-phone"></i></div>
          <div><h4>Phone</h4><p>+234 916 277 4838</p></div>
        </div>
        <div class="info-item">
          <div class="info-item-icon"><i class="fas fa-envelope"></i></div>
          <div><h4>Email</h4><p>harvestfundbusiness@gmail.com</p></div>
        </div>
      </div>
    </div>
  </div>

  <!-- FORM -->
  <div class="form-panel">
    <h2>Send us a message</h2>
    <p>We read every message. No chatbots, no automated replies.</p>

    <?php if($message === "success"): ?>
      <div class="alert alert-success">✓ Message sent successfully! We'll be in touch within 24 hours.</div>
    <?php elseif($message === "error"): ?>
      <div class="alert alert-error">⚠ Message could not be sent. Please try again or email us directly.</div>
    <?php endif; ?>

    <form method="POST" action="contact.php">
      <div class="form-row">
        <div class="field"><label>Your name</label><input type="text" name="name" placeholder="Adaeze Obi" required></div>
        <div class="field"><label>Email address</label><input type="email" name="email" placeholder="you@example.com" required></div>
      </div>
      <div class="field"><label>Subject</label><input type="text" name="subject" placeholder="What's this about?" required></div>
      <div class="field"><label>Message</label><textarea name="message" placeholder="Tell us how we can help..." required></textarea></div>
      <button type="submit" name="send_message" class="submit-btn">Send message →</button>
    </form>
  </div>
</div>

<footer>
  <div class="footer-grid">
    <div class="footer-brand"><div class="mark"></div><h3>HarvestFund</h3><p>Connecting investors and farmers to build a stronger agricultural future for Nigeria.</p></div>
    <div><h4>Navigate</h4><a href="index.php">Home</a><a href="about.php">About</a><a href="contact.php">Contact</a></div>
    <div><h4>Account</h4><a href="../auth/register.php">Register</a><a href="../auth/login.php">Log in</a></div>
  </div>
  <div class="footer-bottom"><p>© 2025 HarvestFund. All rights reserved.</p><p>Lagos, Nigeria</p></div>
</footer>

<script>
  const navbar=document.getElementById('navbar');
  window.addEventListener('scroll',()=>navbar.classList.toggle('scrolled',window.scrollY>40));
  document.getElementById('hamburger').addEventListener('click',()=>document.getElementById('nav-links').classList.toggle('open'));
</script>
</body>
</html>
