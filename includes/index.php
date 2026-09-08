<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HarvestFund — Invest in Farms, Harvest Wealth</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar" id="navbar">
  <div class="nav-logo">
    <div class="mark"></div>
    <span>HarvestFund</span>
  </div>
  <ul class="nav-links" id="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="contact.php">Contact</a></li>
    <li><a href="../auth/register.php">Register</a></li>
    <li><a href="../auth/login.php" class="nav-btn">Log in</a></li>
    
  </ul>
  <div class="hamburger" id="hamburger">
    <span></span><span></span><span></span>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <div class="hero-eyebrow">🌾 Nigeria's Agricultural Investment Platform</div>
    <h1>Invest in Farms,<br><em>Harvest Wealth</em></h1>
    <p>HarvestFund connects verified farmers with investors. Fund real farm cycles, track progress, and earn returns when the harvest comes in.</p>
    <div class="hero-ctas">
      <a href="../auth/register.php" class="btn-gold">Start investing →</a>
      <a href="about.php" class="btn-ghost">Learn how it works</a>
    </div>
    <div class="hero-stats">
      <div class="hero-stat"><div class="num">1,000+</div><div class="lbl">Farmers to empower</div></div>
      <div class="hero-stat"><div class="num">₦500M</div><div class="lbl">Funding goal</div></div>
      <div class="hero-stat"><div class="num">36</div><div class="lbl">States target</div></div>
    </div>
  </div>
  <div class="scroll-cue">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
    scroll
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section">
  <div class="section-label">The process</div>
  <h2 class="section-title">Four steps to your first harvest</h2>
  <p class="section-sub">We make it simple for anyone to invest directly in farming projects and earn real returns.</p>
  <div class="steps">
    <div class="step"><div class="step-accent"></div><div class="step-num">01</div><h3>Sign up</h3><p>Create your HarvestFund account in minutes. Choose your role — investor or farmer.</p></div>
    <div class="step"><div class="step-accent"></div><div class="step-num">02</div><h3>Invest</h3><p>Browse verified farm projects, review the details, and fund the ones that fit your goals.</p></div>
    <div class="step"><div class="step-accent"></div><div class="step-num">03</div><h3>Monitor</h3><p>Track your farms in real time — photo updates, milestone stages, and live payout dates.</p></div>
    <div class="step"><div class="step-accent"></div><div class="step-num">04</div><h3>Collect</h3><p>Receive your returns directly to your wallet once the harvest cycle completes.</p></div>
  </div>
</section>

<!-- WHY INVEST -->
<section class="why">
  <div class="section-label">Why farmland</div>
  <h2 class="section-title">Built on Africa's most resilient asset</h2>
  <p class="section-sub">Farmland has outperformed most asset classes over the last decade. Here's why it matters in Nigeria.</p>
  <div class="reasons">
    <div class="reason"><div class="reason-icon"><i class="fas fa-seedling"></i></div><h3>Rising food demand</h3><p>Population growth drives consistent demand for agricultural output year over year.</p></div>
    <div class="reason"><div class="reason-icon"><i class="fas fa-map"></i></div><h3>Abundant arable land</h3><p>Nigeria holds some of Africa's most fertile, diverse farmland — from north to south.</p></div>
    <div class="reason"><div class="reason-icon"><i class="fas fa-leaf"></i></div><h3>Government backing</h3><p>Agricultural policies and incentives actively support investors and smallholder farmers.</p></div>
    <div class="reason"><div class="reason-icon"><i class="fas fa-chart-line"></i></div><h3>Long-term returns</h3><p>Farmland generates stable, compounding returns across crop cycles and market conditions.</p></div>
    <div class="reason"><div class="reason-icon"><i class="fas fa-shield-alt"></i></div><h3>Inflation hedge</h3><p>Agricultural assets hold value and often appreciate as the cost of goods rises.</p></div>
    <div class="reason"><div class="reason-icon"><i class="fas fa-people-carry"></i></div><h3>Community impact</h3><p>Every investment creates jobs, supports families, and strengthens local food supply.</p></div>
  </div>
</section>

<!-- CTA BAND -->
<div class="cta-band">
  <h2>Ready to put your money where the soil is?</h2>
  <a href="../auth/register.php" class="btn-forest">Create your account →</a>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="mark"></div>
      <h3>HarvestFund</h3>
      <p>Connecting investors with verified farmers to build a stronger agricultural future for Nigeria.</p>
    </div>
    <div>
      <h4>Navigate</h4>
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
    </div>
    <div>
      <h4>Account</h4>
      <a href="../auth/register.php">Register</a>
      <a href="../auth/login.php">Log in</a>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2025 HarvestFund. All rights reserved.</p>
    <p>Lagos, Nigeria</p>
  </div>
</footer>

<script>
  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 40));
  document.getElementById('hamburger').addEventListener('click', () => document.getElementById('nav-links').classList.toggle('open'));
</script>
</body>
</html>
