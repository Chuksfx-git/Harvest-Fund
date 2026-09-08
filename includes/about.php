<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About — HarvestFund</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/about.css">
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
    <div class="eyebrow">Our story</div>
    <h1>Built to bridge the gap<br><em>between capital and soil</em></h1>
    <p>Agriculture feeds Africa. HarvestFund funds the farmers who make that possible — while delivering real returns to the people who believe in them.</p>
  </div>
</section>

<!-- STORY -->
<section class="section">
  <div class="section-label">Our story</div>
  <div class="story">
    <div class="story-text">
      <h2 class="section-title">Why we built HarvestFund</h2>
      <p>Agriculture remains the backbone of Africa's economy, yet hardworking farmers consistently struggle to access the funding they need to grow. Traditional banks demand collateral most smallholder farmers don't have. Investors want opportunities that are real, transparent, and traceable.</p>
      <p>HarvestFund was built to solve both sides of that problem at once — by creating a platform where verified farmers get capital, and investors get visibility into exactly where their money goes and what it produces.</p>
      <p>Every cycle funded, every harvest collected, every payout made is a small proof that this model works.</p>
    </div>
    <div class="story-visual">
      <div class="story-visual-inner">
        <i class="fas fa-seedling"></i>
        <p>Farming the future</p>
      </div>
    </div>
  </div>
</section>

<!-- MISSION / VISION / VALUES -->
<section class="section" style="background:var(--cream); padding-top:0;">
  <div class="section-label">What drives us</div>
  <h2 class="section-title">Vision, mission & values</h2>
  <div class="mvv">
    <div class="mvv-card">
      <span class="card-icon">🌍</span>
      <h3>Our Vision</h3>
      <p>To become Africa's most trusted agricultural investment platform — creating lasting prosperity for both farmers and investors.</p>
    </div>
    <div class="mvv-card">
      <span class="card-icon">🚀</span>
      <h3>Our Mission</h3>
      <p>To connect investors with verified farming opportunities while giving farmers the capital and support they need to build sustainable businesses.</p>
    </div>
    <div class="mvv-card">
      <span class="card-icon">🤝</span>
      <h3>Our Values</h3>
      <p>Transparency, integrity, innovation, sustainability, and community impact guide every feature we build and every decision we make.</p>
    </div>
  </div>
</section>

<!-- WHY CHOOSE US -->
<section class="dark-section">
  <div class="section-label">Why choose us</div>
  <h2 class="section-title">What makes HarvestFund different</h2>
  <p class="section-sub">Platforms come and go. Here's what we built that others haven't.</p>
  <div class="choose-grid">
    <div class="choose-item"><div class="choose-icon"><i class="fas fa-check-double"></i></div><div><h3>Verified farms only</h3><p>Every project is screened before it reaches the marketplace. Investors see only vetted opportunities.</p></div></div>
    <div class="choose-item"><div class="choose-icon"><i class="fas fa-calendar-check"></i></div><div><h3>Exact payout dates</h3><p>No vague "after harvest" timelines. Every investment shows a real, committed payout date from day one.</p></div></div>
    <div class="choose-item"><div class="choose-icon"><i class="fas fa-photo-film"></i></div><div><h3>Photo & video updates</h3><p>Farmers submit visual progress at every milestone stage — investors see the farm grow in real time.</p></div></div>
    <div class="choose-item"><div class="choose-icon"><i class="fas fa-file-shield"></i></div><div><h3>Full document access</h3><p>Contracts, insurance certificates, and farmer ID verification — all available in your investor vault.</p></div></div>
  </div>
</section>

<!-- IMPACT -->
<section class="section">
  <div class="section-label">Impact goals</div>
  <h2 class="section-title">What we're building toward</h2>
  <p class="section-sub">These are our targets. Every investment gets us closer.</p>
  <div class="impact-grid">
    <div class="impact-box"><h3>1,000+</h3><p>Farmers empowered</p></div>
    <div class="impact-box"><h3>5,000+</h3><p>Investors connected</p></div>
    <div class="impact-box"><h3>₦500M+</h3><p>Funding facilitated</p></div>
    <div class="impact-box"><h3>36</h3><p>States targeted</p></div>
  </div>
</section>

<div class="cta-band">
  <h2>Whether you're farming or funding — HarvestFund is for you.</h2>
  <a href="../auth/register.php" class="btn-forest">Get started today →</a>
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
PHPEOF
echo "about done"