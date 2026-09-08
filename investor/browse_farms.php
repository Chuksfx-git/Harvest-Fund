<?php
session_start();
include("../config/database.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "investor") {
    header("Location: ../auth/login.php");
    exit();
}

/* ── FILTERS from GET params ── */
$where   = ["f.status = 'active'"];
$params  = [];
$types   = "";

if (!empty($_GET['crop'])) {
    $where[]  = "f.crop_type = ?";
    $params[] = $_GET['crop'];
    $types   .= "s";
}

if (!empty($_GET['roi'])) {
    switch ($_GET['roi']) {
        case 'low':    $where[] = "f.roi < 15";  break;
        case 'mid':    $where[] = "f.roi BETWEEN 15 AND 25"; break;
        case 'high':   $where[] = "f.roi > 25";  break;
    }
}

if (!empty($_GET['progress'])) {
    switch ($_GET['progress']) {
        case 'open':
            $where[] = "(f.funded_amount / f.target_amount) * 100 < 80"; break;
        case 'almost':
            $where[] = "(f.funded_amount / f.target_amount) * 100 >= 80"; break;
    }
}

$sql = "
    SELECT f.*, u.fullname AS farmer_name
    FROM farms f
    LEFT JOIN users u ON f.farmer_id = u.id
    WHERE " . implode(" AND ", $where) . "
    ORDER BY f.created_at DESC
";

if ($params) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

/* ── UNIQUE CROP TYPES for filter chips ── */
$crop_result = $conn->query("SELECT DISTINCT crop_type FROM farms WHERE status = 'active' ORDER BY crop_type");
$crop_types  = [];
while ($row = $crop_result->fetch_assoc()) {
    $crop_types[] = $row['crop_type'];
}

$farm_count = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Browse Farms — HarvestFund</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --forest:#1A3329; --forest-mid:#274D3A; --gold:#C5861F; --gold-lt:#F5E5C0;
      --cream:#F7F3EB; --paper:#fff; --ink:#1C1C1E; --muted:#6B7A6E;
      --line:#E8E2D5; --growth:#2E6B44; --growth-bg:#E2EFE7;
      --amber:#A0660A; --amber-bg:#FDF0D5; --rust:#B5402A; --rust-bg:#FAE5E0;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    a{text-decoration:none;color:inherit;}
    body{background:var(--cream);color:var(--ink);font-family:'Inter',sans-serif;font-size:15px;-webkit-font-smoothing:antialiased;padding-bottom:90px;}

    /* ── TOP BAR ── */
    .topbar{background:var(--forest);padding:18px 20px 20px;position:sticky;top:0;z-index:100;}
    .topbar-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;}
    .back-btn{display:flex;align-items:center;gap:6px;color:rgba(255,255,255,.65);font-size:13px;font-weight:500;transition:color .15s;}
    .back-btn:hover{color:#fff;}
    .topbar h1{font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#fff;}
    .topbar-sub{font-size:13px;color:rgba(255,255,255,.5);}

    /* ── FILTER BAR ── */
    .filter-bar{padding:14px 16px;background:var(--paper);border-bottom:1px solid var(--line);overflow-x:auto;white-space:nowrap;-webkit-overflow-scrolling:touch;}
    .filter-bar::-webkit-scrollbar{display:none;}
    .filter-section{display:inline-flex;align-items:center;gap:6px;margin-right:16px;}
    .filter-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);}
    .chip{display:inline-flex;align-items:center;gap:5px;padding:6px 13px;border-radius:999px;font-size:12.5px;font-weight:600;border:1.5px solid var(--line);background:transparent;color:var(--muted);cursor:pointer;transition:all .15s;white-space:nowrap;text-decoration:none;}
    .chip:hover{border-color:var(--ink);color:var(--ink);}
    .chip.active{background:var(--forest);border-color:var(--forest);color:#fff;}
    .chip-clear{background:var(--rust-bg);border-color:transparent;color:var(--rust);}
    .chip-clear:hover{background:#f0cdc6;}

    /* ── RESULTS SUMMARY ── */
    .results-bar{padding:14px 16px 0;display:flex;align-items:center;justify-content:space-between;}
    .results-count{font-size:13px;color:var(--muted);font-weight:500;}
    .results-count strong{color:var(--ink);}
    .sort-select{font-size:12.5px;color:var(--muted);border:1.5px solid var(--line);border-radius:8px;padding:5px 10px;background:var(--paper);font-family:'Inter',sans-serif;outline:none;cursor:pointer;}

    /* ── FARM GRID ── */
    .farm-grid{display:grid;grid-template-columns:1fr;gap:14px;padding:14px 16px;}

    /* ── FARM CARD ── */
    .farm-card{background:var(--paper);border:1px solid var(--line);border-radius:18px;overflow:hidden;transition:box-shadow .2s,transform .15s;}
    .farm-card:hover{box-shadow:0 8px 28px rgba(0,0,0,.09);transform:translateY(-2px);}

    .card-image{height:140px;position:relative;overflow:hidden;background:linear-gradient(135deg,#C8DFC0,#E5D9B0);}
    .card-image img{width:100%;height:100%;object-fit:cover;}
    .card-image-placeholder{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:42px;opacity:.4;}

    /* status pill overlaid on image */
    .card-status{position:absolute;top:10px;left:10px;}
    .card-roi{position:absolute;top:10px;right:10px;background:rgba(26,51,41,.85);color:var(--gold-lt);font-family:'IBM Plex Mono',monospace;font-size:13px;font-weight:600;padding:4px 10px;border-radius:999px;backdrop-filter:blur(4px);}
    .s-pill{font-size:11px;font-weight:700;padding:4px 10px;border-radius:999px;}
    .s-open    {background:var(--growth-bg);color:var(--growth);}
    .s-almost  {background:var(--gold-lt);color:var(--amber);}
    .s-full    {background:var(--rust-bg);color:var(--rust);}

    .card-body{padding:16px;}

    .card-top{display:flex;justify-content:space-between;align-items:flex-start;gap:8px;margin-bottom:4px;}
    .card-name{font-family:'Syne',sans-serif;font-size:17px;font-weight:700;line-height:1.2;}
    .verified-badge{display:flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:var(--growth);background:var(--growth-bg);padding:3px 8px;border-radius:999px;white-space:nowrap;flex-shrink:0;}

    .card-meta{display:flex;flex-wrap:wrap;gap:6px;margin:8px 0 12px;}
    .meta-tag{display:flex;align-items:center;gap:4px;font-size:12px;color:var(--muted);background:var(--cream);padding:4px 9px;border-radius:6px;}
    .meta-tag svg{flex-shrink:0;}

    /* funding progress */
    .fund-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;}
    .fund-label{font-size:12px;color:var(--muted);font-weight:500;}
    .fund-pct{font-family:'IBM Plex Mono',monospace;font-size:12.5px;font-weight:600;color:var(--growth);}
    .fund-pct.high{color:var(--amber);}
    .progress-bar{height:6px;background:var(--line);border-radius:999px;overflow:hidden;margin-bottom:10px;}
    .progress-fill{height:100%;border-radius:999px;background:var(--growth);transition:width .4s ease;}
    .progress-fill.high{background:var(--gold);}

    /* amounts row */
    .amounts-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line);margin-bottom:12px;}
    .amount-item{text-align:center;}
    .amount-item .lbl{font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);font-weight:600;margin-bottom:3px;}
    .amount-item .val{font-family:'IBM Plex Mono',monospace;font-size:13.5px;font-weight:600;color:var(--ink);}
    .amount-divider{width:1px;height:32px;background:var(--line);}

    /* insurance note */
    .insurance-note{display:flex;align-items:center;gap:6px;font-size:11.5px;color:var(--amber);background:var(--amber-bg);padding:7px 10px;border-radius:8px;margin-bottom:12px;line-height:1.4;}
    .insurance-note svg{flex-shrink:0;}

    /* farmer track record */
    .farmer-row{display:flex;align-items:center;gap:10px;margin-bottom:14px;}
    .farmer-avatar{width:30px;height:30px;border-radius:50%;background:var(--forest-mid);color:#fff;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-weight:700;font-size:11px;flex-shrink:0;}
    .farmer-info .farmer-name{font-size:13px;font-weight:600;color:var(--ink);}
    .farmer-info .farmer-track{font-size:11.5px;color:var(--muted);}

    .invest-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;background:var(--forest);color:#fff;border:none;border-radius:999px;padding:13px;font-size:14.5px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:opacity .2s;text-decoration:none;}
    .invest-btn:hover{opacity:.88;}
    .invest-btn:active{transform:scale(.98);}

    /* ── EMPTY STATE ── */
    .empty-wrap{padding:48px 20px;text-align:center;}
    .empty-icon{font-size:52px;margin-bottom:16px;}
    .empty-title{font-family:'Syne',sans-serif;font-size:20px;font-weight:700;margin-bottom:8px;}
    .empty-sub{font-size:14.5px;color:var(--muted);line-height:1.65;max-width:280px;margin:0 auto 24px;}
    .notify-form{display:flex;gap:8px;max-width:320px;margin:0 auto;flex-wrap:wrap;justify-content:center;}
    .notify-input{flex:1;min-width:180px;border:1.5px solid var(--line);border-radius:10px;padding:11px 14px;font-size:14px;font-family:'Inter',sans-serif;outline:none;background:var(--paper);}
    .notify-input:focus{border-color:var(--growth);}
    .notify-btn{background:var(--gold);color:var(--forest);border:none;border-radius:999px;padding:11px 20px;font-size:14px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:opacity .2s;white-space:nowrap;}
    .notify-btn:hover{opacity:.88;}

    /* ── BOTTOM NAV ── */
    .bottom-nav{position:fixed;bottom:0;left:0;right:0;background:var(--paper);border-top:1px solid var(--line);display:flex;justify-content:space-around;padding:8px 0 max(8px,env(safe-area-inset-bottom));z-index:200;}
    .nav-item{display:flex;flex-direction:column;align-items:center;gap:4px;color:var(--muted);font-size:11px;font-weight:600;padding:4px 10px;border-radius:10px;transition:color .15s;}
    .nav-item.active,
    .nav-item:hover{color:var(--forest);}

    @media(min-width:600px){
      .farm-grid{grid-template-columns:repeat(2,1fr);max-width:780px;margin:0 auto;}
      .topbar{padding:20px 32px 22px;}
      .filter-bar,.results-bar{padding-left:32px;padding-right:32px;}
    }
    @media(min-width:900px){
      .farm-grid{grid-template-columns:repeat(3,1fr);max-width:1100px;}
    }
  </style>
</head>
<body>

<!-- ── TOP BAR ── -->
<div class="topbar">
  <div class="topbar-row">
    <a href="dashboard.php" class="back-btn">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
      Dashboard
    </a>
    <svg width="20" height="20" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
  </div>
  <h1>Browse farms</h1>
  <p class="topbar-sub">
    <?php echo $farm_count; ?> project<?php echo $farm_count !== 1 ? 's' : ''; ?> open for funding
  </p>
</div>

<!-- ── FILTERS ── -->
<form method="GET" action="browse_farms.php">
<div class="filter-bar">

  <!-- Crop type -->
  <div class="filter-section">
    <span class="filter-label">Crop</span>
    <a href="browse_farms.php" class="chip <?php echo empty($_GET['crop']) ? 'active' : ''; ?>">All</a>
    <?php foreach($crop_types as $ct): ?>
      <a href="?crop=<?php echo urlencode($ct); ?><?php echo !empty($_GET['roi']) ? '&roi='.urlencode($_GET['roi']) : ''; ?><?php echo !empty($_GET['progress']) ? '&progress='.urlencode($_GET['progress']) : ''; ?>"
         class="chip <?php echo (isset($_GET['crop']) && $_GET['crop'] === $ct) ? 'active' : ''; ?>">
        <?php echo htmlspecialchars($ct); ?>
      </a>
    <?php endforeach; ?>
  </div>

  <!-- ROI -->
  <div class="filter-section">
    <span class="filter-label">ROI</span>
    <?php
    $roi_opts = ['low'=>'Under 15%','mid'=>'15–25%','high'=>'25%+'];
    foreach($roi_opts as $val => $lbl):
    ?>
    <a href="?<?php echo !empty($_GET['crop']) ? 'crop='.urlencode($_GET['crop']).'&' : ''; ?>roi=<?php echo $val; ?><?php echo !empty($_GET['progress']) ? '&progress='.urlencode($_GET['progress']) : ''; ?>"
       class="chip <?php echo (isset($_GET['roi']) && $_GET['roi'] === $val) ? 'active' : ''; ?>">
      <?php echo $lbl; ?>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Funding progress -->
  <div class="filter-section">
    <span class="filter-label">Status</span>
    <a href="?<?php echo !empty($_GET['crop']) ? 'crop='.urlencode($_GET['crop']).'&' : ''; ?><?php echo !empty($_GET['roi']) ? 'roi='.urlencode($_GET['roi']).'&' : ''; ?>progress=open"
       class="chip <?php echo (isset($_GET['progress']) && $_GET['progress']==='open') ? 'active' : ''; ?>">Open</a>
    <a href="?<?php echo !empty($_GET['crop']) ? 'crop='.urlencode($_GET['crop']).'&' : ''; ?><?php echo !empty($_GET['roi']) ? 'roi='.urlencode($_GET['roi']).'&' : ''; ?>progress=almost"
       class="chip <?php echo (isset($_GET['progress']) && $_GET['progress']==='almost') ? 'active' : ''; ?>">Almost funded</a>
  </div>

  <?php if(!empty($_GET['crop']) || !empty($_GET['roi']) || !empty($_GET['progress'])): ?>
    <a href="browse_farms.php" class="chip chip-clear">✕ Clear filters</a>
  <?php endif; ?>

</div>
</form>

<!-- ── RESULTS BAR ── -->
<div class="results-bar">
  <p class="results-count">Showing <strong><?php echo $farm_count; ?></strong> farm<?php echo $farm_count !== 1 ? 's' : ''; ?></p>
  <select class="sort-select" onchange="window.location='browse_farms.php?sort='+this.value">
    <option value="newest">Newest first</option>
    <option value="roi">Highest ROI</option>
    <option value="progress">Almost funded</option>
  </select>
</div>

<!-- ── FARM GRID ── -->
<div class="farm-grid">

<?php if($farm_count === 0): ?>

  <!-- EMPTY STATE -->
  <div class="empty-wrap" style="grid-column:1/-1;">
    <div class="empty-icon">🌱</div>
    <h2 class="empty-title">No farms available yet</h2>
    <p class="empty-sub">We're onboarding our first farmers. Leave your email and we'll notify you the moment the first project goes live.</p>
    <div class="notify-form">
      <input type="email" class="notify-input" placeholder="your@email.com">
      <button class="notify-btn">Notify me</button>
    </div>
  </div>

<?php else: ?>

  <?php while($farm = $result->fetch_assoc()): ?>
    <?php
      /* Progress calc — your original logic */
      $progress = 0;
      if($farm['target_amount'] > 0){
        $progress = ($farm['funded_amount'] / $farm['target_amount']) * 100;
      }
      if($progress > 100) $progress = 100;
      $progress_r = round($progress);

      /* Status tag */
      if($progress_r >= 100)     { $status = 'full';   $status_label = 'Fully funded'; }
      elseif($progress_r >= 80)  { $status = 'almost'; $status_label = 'Almost funded'; }
      else                       { $status = 'open';   $status_label = 'Open'; }

      /* Farmer initials */
      $initials = '';
      if(!empty($farm['farmer_name'])){
        $parts = explode(' ', $farm['farmer_name']);
        foreach(array_slice($parts,0,2) as $p) $initials .= strtoupper(substr($p,0,1));
      }
    ?>

    <div class="farm-card">

      <!-- IMAGE -->
      <div class="card-image">
        <?php if(!empty($farm['image']) && file_exists("../assets/images/farms/".$farm['image'])): ?>
          <img src="../assets/images/farms/<?php echo htmlspecialchars($farm['image']); ?>"
               alt="<?php echo htmlspecialchars($farm['farm_name']); ?>"
               loading="lazy">
        <?php else: ?>
          <div class="card-image-placeholder">🌾</div>
        <?php endif; ?>
        <div class="card-status">
          <span class="s-pill s-<?php echo $status; ?>"><?php echo $status_label; ?></span>
        </div>
        <div class="card-roi">+<?php echo htmlspecialchars($farm['roi']); ?>% ROI</div>
      </div>

      <!-- BODY -->
      <div class="card-body">

        <div class="card-top">
          <h2 class="card-name"><?php echo htmlspecialchars($farm['farm_name']); ?></h2>
          <span class="verified-badge">
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            Verified
          </span>
        </div>

        <!-- META TAGS -->
        <div class="card-meta">
          <span class="meta-tag">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?php echo htmlspecialchars($farm['location']); ?>
          </span>
          <span class="meta-tag">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a9 9 0 0 1 9 9c0 4.17-2.84 7.67-6.73 8.66L12 22l-2.27-2.34C5.84 18.67 3 15.17 3 11a9 9 0 0 1 9-9z"/></svg>
            <?php echo htmlspecialchars($farm['crop_type']); ?>
          </span>
          <span class="meta-tag">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <?php echo htmlspecialchars($farm['duration']); ?>
          </span>
        </div>

        <!-- FUNDING PROGRESS -->
        <div class="fund-row">
          <span class="fund-label">Funding progress</span>
          <span class="fund-pct <?php echo $progress_r >= 80 ? 'high' : ''; ?>"><?php echo $progress_r; ?>%</span>
        </div>
        <div class="progress-bar">
          <div class="progress-fill <?php echo $progress_r >= 80 ? 'high' : ''; ?>"
               style="width:<?php echo $progress_r; ?>%"></div>
        </div>

        <!-- AMOUNTS -->
        <div class="amounts-row">
          <div class="amount-item">
            <div class="lbl">Target</div>
            <div class="val">₦<?php echo number_format($farm['target_amount']); ?></div>
          </div>
          <div class="amount-divider"></div>
          <div class="amount-item">
            <div class="lbl">Funded</div>
            <div class="val">₦<?php echo number_format($farm['funded_amount']); ?></div>
          </div>
          <div class="amount-divider"></div>
          <div class="amount-item">
            <div class="lbl">Remaining</div>
            <div class="val">₦<?php echo number_format(max(0, $farm['target_amount'] - $farm['funded_amount'])); ?></div>
          </div>
        </div>

        <!-- INSURANCE NOTE -->
        <div class="insurance-note">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          Insured: farming activity only — not capital
        </div>

        <!-- FARMER ROW -->
        <?php if(!empty($farm['farmer_name'])): ?>
        <div class="farmer-row">
          <div class="farmer-avatar"><?php echo $initials; ?></div>
          <div class="farmer-info">
            <div class="farmer-name"><?php echo htmlspecialchars($farm['farmer_name']); ?></div>
            <div class="farmer-track">Track record: — cycles</div>
          </div>
        </div>
        <?php endif; ?>

        <!-- INVEST BUTTON -->
        <?php if($status !== 'full'): ?>
          <a href="invest.php?id=<?php echo $farm['id']; ?>" class="invest-btn">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Invest in this farm
          </a>
        <?php else: ?>
          <div class="invest-btn" style="background:var(--line);color:var(--muted);cursor:not-allowed;opacity:.7;">Fully funded</div>
        <?php endif; ?>

      </div><!-- /.card-body -->
    </div><!-- /.farm-card -->

  <?php endwhile; ?>

<?php endif; ?>

</div><!-- /.farm-grid -->

<!-- ── BOTTOM NAV ── -->
<nav class="bottom-nav">
  <a href="dashboard.php" class="nav-item">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
    <span>Home</span>
  </a>
  <a href="browse_farms.php" class="nav-item active">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a9 9 0 0 1 9 9c0 4.17-2.84 7.67-6.73 8.66L12 22l-2.27-2.34C5.84 18.67 3 15.17 3 11a9 9 0 0 1 9-9z"/></svg>
    <span>Products</span>
  </a>
  <a href="my_investments.php" class="nav-item">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    <span>Community</span>
  </a>
  <a href="recharge.php" class="nav-item">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    <span>Recharge</span>
  </a>
  <a href="profile.php" class="nav-item">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    <span>Profile</span>
  </a>
</nav>

</body>
</html>