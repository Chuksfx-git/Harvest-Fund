<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['role'] != "investor") {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investor Dashboard — HarvestFund</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/investor.css">
</head>

<body>

    <!-- ── TOP HEADER ── -->
    <header class="top-header">
        <div class="header-top">
            <div class="brand">
                <div class="brand-mark"></div>
                <span class="brand-name">HarvestFund</span>
            </div>
            <a href="notifications.php" class="notif-btn" aria-label="Notifications">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                <span class="notif-dot"></span>
            </a>
        </div>

        <p class="welcome-text">Welcome back,</p>
        <h1 class="welcome-name"><?php echo htmlspecialchars($_SESSION['fullname']); ?> 👋</h1>

        <!-- Portfolio balance — your original feature, redesigned -->
        <div class="balance-card">
            <span class="balance-label">Portfolio Balance</span>
            <div class="balance-amount">₦0.00</div>
            <div class="balance-sub">
                <span class="payout-badge">Next payout: <strong>—</strong></span>
            </div>
        </div>
    </header>

    <!-- ── SCROLLABLE CONTENT ── -->
    <main class="content">

        <!-- ── SUMMARY CARDS — your original 3 cards, upgraded ── -->
        <section class="summary">
            <div class="card stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-body">
                    <p class="stat-label">Active Investments</p>
                    <p class="stat-value">0</p>
                </div>
            </div>

            <div class="card stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-body">
                    <p class="stat-label">Total Earnings</p>
                    <p class="stat-value mono">₦0.00</p>
                </div>
            </div>

            <div class="card stat-card">
                <div class="stat-icon">👛</div>
                <div class="stat-body">
                    <p class="stat-label">Wallet Balance</p>
                    <p class="stat-value mono">₦0.00</p>
                </div>
            </div>
        </section>

        <!-- ── QUICK ACTIONS ── -->
        <section class="quick-actions">
            <a href="recharge.php" class="action-btn action-btn--primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Fund wallet
            </a>
            <a href="browse_farms.php" class="action-btn action-btn--outline">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                Browse farms
            </a>
            <a href="my_investments.php" class="action-btn action-btn--outline">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Community
            </a>
        </section>

        <!-- ── ACTIVE INVESTMENTS (with stage tracker + payout status) ── -->
        <section class="section">
            <div class="section-head">
                <h2 class="section-title">Your funded projects</h2>
                <a href="my_investments.php" class="section-link">See all →</a>
            </div>

            <!-- Empty state — shown when investor has no investments yet -->
            <div class="empty-state">
                <div class="empty-icon">🌱</div>
                <p class="empty-title">No active investments yet</p>
                <p class="empty-sub">Browse farms to fund your first project and start earning.</p>
                <a href="browse_farms.php" class="action-btn action-btn--primary" style="margin-top:14px; display:inline-flex;">Browse farms</a>
            </div>

            <!--
                When investments exist, replace empty-state with project cards like this:

            <div class="project-card">
                <div class="project-photo">
                    <span class="project-tag">Maize · Kaduna</span>
                </div>
                <div class="project-body">
                    <div class="project-title-row">
                        <h3 class="project-title">Musa Ibrahim — 2 acre maize</h3>
                        <span class="project-roi">+18% ROI</span>
                    </div>
                    <p class="project-meta">Funded ₦400,000 · 5-month cycle</p>

                    <div class="stage-tracker">
                        <div class="stage done">
                            <div class="stage-bar"></div><div class="stage-dot"></div>
                            <label class="stage-label">Seed</label>
                        </div>
                        <div class="stage done">
                            <div class="stage-bar"></div><div class="stage-dot"></div>
                            <label class="stage-label">Grow</label>
                        </div>
                        <div class="stage current">
                            <div class="stage-bar"></div><div class="stage-dot"></div>
                            <label class="stage-label">Mid</label>
                        </div>
                        <div class="stage">
                            <div class="stage-bar"></div><div class="stage-dot"></div>
                            <label class="stage-label">Harvest</label>
                        </div>
                        <div class="stage">
                            <div class="stage-bar"></div><div class="stage-dot"></div>
                            <label class="stage-label">Paid</label>
                        </div>
                    </div>

                    <div class="payout-line">
                        <span class="payout-date mono">Payout: Sep 2, 2026</span>
                        <span class="status-pill status-pill--ontrack">On track</span>
                    </div>
                </div>
            </div>

            <div class="project-card">
                ... same structure, change status-pill--ontrack to
                    status-pill--delayed or status-pill--atrisk as needed
            </div>
            -->
        </section>

        <!-- ── OPEN FARMS (Browse marketplace preview) ── -->
        <section class="section">
            <div class="section-head">
                <h2 class="section-title">Open for funding</h2>
                <a href="browse_farms.php" class="section-link">See marketplace →</a>
            </div>

            <?php
            /*
             * TODO: Replace this empty state with a PHP loop once you have farms in the DB.
             *
             * Example query (run this from browse_farms.php or a shared db file):
             *   $farms = $pdo->query("SELECT * FROM farms WHERE status = 'open' LIMIT 3")->fetchAll();
             *
             * Then loop:
             *   if (count($farms) > 0):
             *     foreach ($farms as $farm): ?>
             *       <div class="farm-preview-card"> ... </div>
             *     <?php endforeach;
             *   else: // show the empty state below
             *   endif;
             */
            ?>

            <div class="empty-state">
                <div class="empty-icon">🌾</div>
                <p class="empty-title">No farms listed yet</p>
                <p class="empty-sub">We're onboarding our first farmers. Check back soon — you'll be notified the moment a project goes live.</p>
            </div>
        </section>

        <!-- ── DOCUMENT VAULT ── -->
        <section class="section">
            <div class="section-head">
                <h2 class="section-title">Your documents</h2>
            </div>
            <div class="vault-list">
                <a href="documents.php" class="vault-item">
                    <span class="vault-icon">📄</span>
                    <span class="vault-label">Investment contracts</span>
                    <span class="vault-arrow">›</span>
                </a>
                <a href="documents.php" class="vault-item">
                    <span class="vault-icon">🛡️</span>
                    <span class="vault-label">Insurance certificates</span>
                    <span class="vault-arrow">›</span>
                </a>
                <a href="profile.php" class="vault-item">
                    <span class="vault-icon">🪪</span>
                    <span class="vault-label">ID verification</span>
                    <span class="vault-arrow">›</span>
                </a>
            </div>
        </section>

        <!-- spacer so content clears bottom nav -->
        <div style="height: 90px;"></div>
    </main>

    <!-- ── BOTTOM NAV — your original links, kept exactly ── -->
    <nav class="bottom-nav">
        <a href="dashboard.php" class="nav-item active">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Home</span>
        </a>
        <a href="browse_farms.php" class="nav-item">
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