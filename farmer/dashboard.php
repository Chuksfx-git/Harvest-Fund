<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "farmer") {
    header("Location: ../auth/login.php");
    exit();
}

include("../config/database.php");

$farmer_id = $_SESSION['user_id'];

$result = $conn->query("SELECT COUNT(*) AS total FROM farms WHERE farmer_id = $farmer_id");
$total_farms = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) AS active FROM farms WHERE farmer_id = $farmer_id AND status = 'active'");
$active_farms = $result->fetch_assoc()['active'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmer Dashboard — HarvestFund</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    /* === TOKENS === */
    :root {
      --forest:     #1A3329;
      --forest-mid: #274D3A;
      --gold:       #C5861F;
      --gold-light: #F5E5C0;
      --cream:      #F7F3EB;
      --paper:      #FFFFFF;
      --ink:        #1C1C1E;
      --muted:      #6B7A6E;
      --line:       #E8E2D5;
      --growth:     #2E6B44;
      --growth-bg:  #E2EFE7;
      --amber:      #A0660A;
      --amber-bg:   #FDF0D5;
      --rust:       #B5402A;
      --rust-bg:    #FAE5E0;
      --sidebar-w:  240px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    a { text-decoration: none; color: inherit; }
    button { cursor: pointer; font-family: inherit; }

    body {
      display: flex;
      min-height: 100vh;
      background: var(--cream);
      color: var(--ink);
      font-family: 'Inter', system-ui, sans-serif;
      font-size: 15px;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
    }

    /* === SIDEBAR === */
    .sidebar {
      width: var(--sidebar-w);
      min-width: var(--sidebar-w);
      background: var(--forest);
      display: flex;
      flex-direction: column;
      padding: 24px 0 20px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
    }

    .sidebar-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 0 20px 24px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      margin-bottom: 12px;
    }

    .brand-mark {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--gold), #3F8B5C);
      flex-shrink: 0;
    }

    .brand-name {
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      font-size: 16px;
      color: #fff;
    }

    .sidebar-nav {
      display: flex;
      flex-direction: column;
      gap: 2px;
      padding: 0 10px;
      flex: 1;
    }

    .sidebar-nav a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 12px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 500;
      color: rgba(255,255,255,0.65);
      text-decoration: none;
      transition: background .15s, color .15s;
    }

    .sidebar-nav a:hover,
    .sidebar-nav a.active {
      background: rgba(255,255,255,0.1);
      color: #fff;
    }

    .sidebar-nav a svg { flex-shrink: 0; }

    .sidebar-logout {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 16px 22px 0;
      margin-top: 8px;
      border-top: 1px solid rgba(255,255,255,0.08);
      font-size: 13.5px;
      color: rgba(255,255,255,0.4);
      text-decoration: none;
      transition: color .15s;
    }

    .sidebar-logout:hover { color: rgba(255,255,255,0.75); }

    /* === MAIN === */
    .main-content {
      margin-left: var(--sidebar-w);
      flex: 1;
      padding: 32px 36px 48px;
      overflow-x: hidden;
    }

    /* === TOPBAR === */
    .topbar {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-bottom: 28px;
      flex-wrap: wrap;
      gap: 16px;
    }

    .topbar-eyebrow {
      font-size: 12px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      font-weight: 600;
      margin-bottom: 4px;
    }

    .topbar-title {
      font-family: 'Syne', sans-serif;
      font-size: 28px;
      font-weight: 700;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 11px 20px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      text-decoration: none;
      transition: opacity .15s;
    }

    .btn-primary { background: var(--gold); color: var(--forest); }
    .btn-primary:hover { opacity: 0.88; }

    /* === STAT GRID === */
    .stat-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
      margin-bottom: 28px;
    }

    .stat-card {
      background: var(--paper);
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 18px 16px;
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .stat-icon {
      width: 42px;
      height: 42px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .stat-label {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--muted);
      font-weight: 600;
      margin-bottom: 4px;
    }

    .stat-value {
      font-family: 'IBM Plex Mono', monospace;
      font-size: 22px;
      font-weight: 600;
      color: var(--ink);
      line-height: 1;
    }

    .stat-sub {
      font-family: 'Inter', sans-serif;
      font-size: 11px;
      color: var(--muted);
      font-weight: 400;
    }

    /* === TWO COL === */
    .two-col {
      display: grid;
      grid-template-columns: 1.4fr 1fr;
      gap: 20px;
      align-items: start;
    }

    /* === PANELS === */
    .panel {
      background: var(--paper);
      border: 1px solid var(--line);
      border-radius: 16px;
      padding: 20px;
    }

    .panel + .panel { margin-top: 16px; }

    .panel-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
    }

    .panel-title {
      font-family: 'Syne', sans-serif;
      font-size: 16px;
      font-weight: 700;
    }

    .panel-sub {
      font-size: 13px;
      color: var(--muted);
      margin: 4px 0 14px;
    }

    .text-link { font-size: 13px; font-weight: 600; color: var(--growth); }

    /* === CYCLE CARD === */
    .cycle-card {
      border: 1px solid var(--line);
      border-radius: 12px;
      padding: 16px;
    }

    .cycle-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 6px;
    }

    .crop-tag {
      background: var(--forest);
      color: #fff;
      font-size: 11.5px;
      font-weight: 600;
      padding: 4px 11px;
      border-radius: 999px;
    }

    .cycle-name {
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 14px;
    }

    .cycle-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 10px;
      border-top: 1px dashed var(--line);
      flex-wrap: wrap;
      gap: 6px;
    }

    .payout-date { font-size: 12.5px; color: var(--muted); font-family: 'IBM Plex Mono', monospace; }
    .payout-date strong { color: var(--ink); font-weight: 600; }
    .update-due { font-size: 12px; color: var(--amber); font-weight: 500; }
    .update-due strong { font-weight: 700; }

    /* === STAGE TRACKER === */
    .stage-tracker {
      display: flex;
      align-items: flex-start;
      margin-bottom: 14px;
    }

    .stage {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    .stage-bar {
      position: absolute;
      top: 6px;
      left: -50%;
      width: 100%;
      height: 2px;
      background: var(--line);
      z-index: 1;
    }

    .stage:first-child .stage-bar { display: none; }

    .stage-dot {
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: var(--line);
      border: 2px solid #fff;
      z-index: 2;
      position: relative;
    }

    .stage-label {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--muted);
      margin-top: 5px;
      font-weight: 600;
      text-align: center;
    }

    .stage.done .stage-bar   { background: var(--growth); }
    .stage.done .stage-dot   { background: var(--growth); border-color: #fff; }
    .stage.done .stage-label { color: var(--growth); }

    .stage.current .stage-bar { background: var(--growth); }
    .stage.current .stage-dot { background: var(--gold); border-color: #fff; box-shadow: 0 0 0 4px var(--gold-light); }
    .stage.current .stage-label { color: var(--ink); font-weight: 700; }

    /* === STATUS PILLS === */
    .pill { font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 999px; }
    .pill-green  { background: var(--growth-bg); color: var(--growth); }
    .pill-amber  { background: var(--amber-bg);  color: var(--amber); }
    .pill-red    { background: var(--rust-bg);   color: var(--rust); }

    /* === ACTION GRID === */
    .action-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      margin-bottom: 14px;
    }

    .action-tile {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      padding: 14px 8px;
      background: var(--cream);
      border: 1px solid var(--line);
      border-radius: 12px;
      color: var(--ink);
      text-decoration: none;
      transition: border-color .15s;
    }

    .action-tile:hover { border-color: var(--forest); background: #fff; }
    .action-tile-label { font-size: 12px; font-weight: 600; text-align: center; }
    .action-tile svg { color: var(--forest); }

    /* === REPORT BUTTON === */
    .report-btn {
      width: 100%;
      background: var(--rust-bg);
      border: 1.5px dashed var(--rust);
      border-radius: 10px;
      padding: 13px 16px;
      color: var(--rust);
      font-size: 13.5px;
      font-weight: 600;
      text-align: left;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      gap: 3px;
      transition: background .15s;
    }

    .report-btn:hover { background: #f0cdc6; }
    .report-sub { font-size: 11.5px; font-weight: 400; opacity: 0.8; }

    /* === VERIFY LIST === */
    .verify-list { display: flex; flex-direction: column; gap: 10px; }

    .verify-item {
      display: flex;
      align-items: center;
      gap: 9px;
      padding: 10px 12px;
      border-radius: 9px;
      font-size: 13.5px;
      font-weight: 500;
    }

    .verify-done    { background: var(--growth-bg); color: var(--growth); }
    .verify-pending { background: var(--amber-bg);  color: var(--amber); }
    .verify-item svg { flex-shrink: 0; }

    /* === MILESTONES === */
    .milestone {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 11px 0;
      border-bottom: 1px solid var(--line);
      font-size: 13.5px;
    }

    .milestone:last-child { border-bottom: none; }
    .m-name { color: var(--muted); }
    .m-amount { font-family: 'IBM Plex Mono', monospace; font-size: 13px; font-weight: 600; }
    .m-done .m-name   { color: var(--growth); text-decoration: line-through; opacity: 0.7; }
    .m-done .m-amount { color: var(--growth); }
    .m-pending .m-amount { color: var(--amber); }
    .m-due .m-name    { color: var(--ink); font-weight: 600; }
    .m-due .m-amount  { color: var(--rust); }

    /* === OFFICER CARD === */
    .officer-card {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-top: 10px;
    }

    .officer-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: var(--forest-mid);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Syne', sans-serif;
      font-weight: 700;
      font-size: 14px;
      flex-shrink: 0;
    }

    .officer-name { font-weight: 600; font-size: 14px; }
    .officer-role { font-size: 12px; color: var(--muted); }

    .officer-call {
      margin-left: auto;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: var(--growth-bg);
      color: var(--growth);
      display: flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      flex-shrink: 0;
    }

    .officer-call:hover { background: #c4ddc8; }

    /* === EMPTY STATE === */
    .empty-state {
      border: 1.5px dashed var(--line);
      border-radius: 12px;
      padding: 32px 20px;
      text-align: center;
    }

    .empty-icon  { font-size: 36px; margin-bottom: 10px; }
    .empty-title { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 700; margin-bottom: 6px; }
    .empty-sub   { font-size: 13.5px; color: var(--muted); line-height: 1.5; }

    /* === RESPONSIVE === */
    @media (max-width: 1024px) {
      .stat-grid { grid-template-columns: repeat(2, 1fr); }
      .two-col   { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
      .sidebar { display: none; }
      .main-content { margin-left: 0; padding: 20px 16px 40px; }
      .action-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { transition: none !important; }
    }
  </style>
</head>
<body>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-mark"></div>
    <span class="brand-name">HarvestFund</span>
  </div>

  <nav class="sidebar-nav">
    <a href="dashboard.php" class="active">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Dashboard
    </a>
    <a href="add_farm.php">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
      Add farm
    </a>
    <a href="my_farms.php">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a9 9 0 0 1 9 9c0 4.17-2.84 7.67-6.73 8.66L12 22l-2.27-2.34C5.84 18.67 3 15.17 3 11a9 9 0 0 1 9-9z"/></svg>
      My farms
    </a>
    <a href="investors.php">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Investors
    </a>
    <a href="withdrawals.php">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
      Withdrawals
    </a>
    <a href="profile.php">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Profile
    </a>
  </nav>

  <a href="../auth/login.php" class="sidebar-logout">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    Log out
  </a>
</aside>

<!-- ═══ MAIN ═══ -->
<div class="main-content">

  <!-- Topbar -->
  <div class="topbar">
    <div>
      <p class="topbar-eyebrow">Farmer dashboard</p>
      <h1 class="topbar-title">Welcome back, <?php echo htmlspecialchars($_SESSION['fullname']); ?></h1>
    </div>
    <a href="add_farm.php" class="btn btn-primary">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
      List a farm
    </a>
  </div>

  <!-- Stat cards -->
  <div class="stat-grid">
    <div class="stat-card">
      <div class="stat-icon" style="background:#E2EFE7;">
        <svg width="20" height="20" fill="none" stroke="#2E6B44" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a9 9 0 0 1 9 9c0 4.17-2.84 7.67-6.73 8.66L12 22l-2.27-2.34C5.84 18.67 3 15.17 3 11a9 9 0 0 1 9-9z"/></svg>
      </div>
      <div>
        <p class="stat-label">Total farms</p>
        <p class="stat-value"><?php echo $total_farms; ?></p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background:#FDF0D5;">
        <svg width="20" height="20" fill="none" stroke="#A0660A" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      </div>
      <div>
        <p class="stat-label">Active farms</p>
        <p class="stat-value"><?php echo $active_farms; ?></p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background:#E2EFE7;">
        <svg width="20" height="20" fill="none" stroke="#2E6B44" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
      </div>
      <div>
        <p class="stat-label">Earnings</p>
        <p class="stat-value">₦0.00</p>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" style="background:#E2EFE7;">
        <svg width="20" height="20" fill="none" stroke="#2E6B44" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <div>
        <p class="stat-label">Cycles completed</p>
        <p class="stat-value">0 <span class="stat-sub">track record</span></p>
      </div>
    </div>
  </div>

  <!-- Two column -->
  <div class="two-col">

    <!-- LEFT -->
    <div>
      <!-- Active cycle -->
      <div class="panel">
        <div class="panel-head">
          <h2 class="panel-title">Active farm cycle</h2>
          <a href="my_farms.php" class="text-link">See all farms →</a>
        </div>

        <?php if ($active_farms > 0): ?>
        <div class="cycle-card">
          <div class="cycle-head">
            <span class="crop-tag">Maize · Kaduna</span>
            <span class="pill pill-green">On track</span>
          </div>
          <p class="cycle-name">2 acre maize — funded ₦400,000</p>

          <div class="stage-tracker">
            <div class="stage done">
              <div class="stage-bar"></div><div class="stage-dot"></div>
              <span class="stage-label">Seed</span>
            </div>
            <div class="stage done">
              <div class="stage-bar"></div><div class="stage-dot"></div>
              <span class="stage-label">Sprout</span>
            </div>
            <div class="stage current">
              <div class="stage-bar"></div><div class="stage-dot"></div>
              <span class="stage-label">Grow</span>
            </div>
            <div class="stage">
              <div class="stage-bar"></div><div class="stage-dot"></div>
              <span class="stage-label">Harvest</span>
            </div>
            <div class="stage">
              <div class="stage-bar"></div><div class="stage-dot"></div>
              <span class="stage-label">Paid</span>
            </div>
          </div>

          <div class="cycle-footer">
            <span class="payout-date">Repayment: <strong>Sep 2, 2026</strong></span>
            <span class="update-due">Update due in <strong>3 days</strong></span>
          </div>
        </div>
        <?php else: ?>
        <div class="empty-state">
          <div class="empty-icon">🌱</div>
          <p class="empty-title">No active farm cycle</p>
          <p class="empty-sub">List your first farm to start receiving funding from investors.</p>
          <a href="add_farm.php" class="btn btn-primary" style="margin-top:14px; display:inline-flex;">List a farm</a>
        </div>
        <?php endif; ?>
      </div>

      <!-- Actions -->
      <div class="panel" style="margin-top:16px;">
        <h2 class="panel-title" style="margin-bottom:14px;">Quick actions</h2>
        <div class="action-grid">
          <a href="add_farm.php" class="action-tile">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
            <span class="action-tile-label">List a farm</span>
          </a>
          <a href="updates.php" class="action-tile">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <span class="action-tile-label">Post update</span>
          </a>
          <a href="investors.php" class="action-tile">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <span class="action-tile-label">My investors</span>
          </a>
          <a href="withdrawals.php" class="action-tile">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
            <span class="action-tile-label">Withdraw</span>
          </a>
        </div>

        <button class="report-btn" onclick="window.location.href='report_issue.php'">
          <span>⚠ Report a problem with your farm</span>
          <span class="report-sub">Flooding, pest, delay — notify investors immediately</span>
        </button>
      </div>
    </div>

    <!-- RIGHT -->
    <div>
      <!-- Verification -->
      <div class="panel">
        <h2 class="panel-title">Verification status</h2>
        <p class="panel-sub">Verified farmers get funded faster.</p>
        <div class="verify-list">
          <div class="verify-item verify-done">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            ID verified
          </div>
          <div class="verify-item verify-pending">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Land lease — <a href="profile.php" style="color:var(--amber);font-weight:600;margin-left:4px;">upload doc</a>
          </div>
          <div class="verify-item verify-pending">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Co-op membership — <a href="profile.php" style="color:var(--amber);font-weight:600;margin-left:4px;">upload doc</a>
          </div>
        </div>
      </div>

      <!-- Repayment schedule -->
      <div class="panel" style="margin-top:16px;">
        <h2 class="panel-title">Repayment schedule</h2>
        <?php if ($active_farms > 0): ?>
        <div style="margin-top:8px;">
          <div class="milestone m-done">
            <span class="m-name">Seed tranche released</span>
            <span class="m-amount">₦160,000</span>
          </div>
          <div class="milestone m-done">
            <span class="m-name">Mid-cycle tranche</span>
            <span class="m-amount">₦120,000</span>
          </div>
          <div class="milestone m-pending">
            <span class="m-name">Harvest tranche</span>
            <span class="m-amount">₦120,000</span>
          </div>
          <div class="milestone m-due">
            <span class="m-name">Investor repayment</span>
            <span class="m-amount">₦472,000 · Sep 2</span>
          </div>
        </div>
        <?php else: ?>
        <p class="panel-sub" style="margin-top:8px;">Schedule appears here once a farm is funded.</p>
        <?php endif; ?>
      </div>

      <!-- Field officer -->
      <div class="panel" style="margin-top:16px;">
        <h2 class="panel-title">Your field officer</h2>
        <div class="officer-card">
          <div class="officer-avatar">IS</div>
          <div>
            <p class="officer-name">Ibrahim Sule</p>
            <p class="officer-role">Field agronomist</p>
          </div>
          <a href="tel:+2348000000000" class="officer-call" aria-label="Call field officer">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

</body>
</html>