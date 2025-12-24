<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>ObpPay Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
:root {
    --indigo: #4f46e5;
    --indigo-dark: #4338ca;
    --gray-dark: #1f2937;
    --gray-medium: #374151;
}

/* GLOBAL LIGHT DEFAULT */
body {
    background-color: #f3f4f6;
    font-family: 'Inter', sans-serif;
}

/* DARK MODE */
body.dark-mode {
    background-color: #111827 !important;
    color: #f3f4f6 !important;
}

/* SIDEBAR */
.sidebar {
    width: 290px;
    height: 100vh;
    background-color: var(--indigo);
    padding: 25px;
    color: white;
    position: fixed;
    top: 0;
    left: 0;
}

body.dark-mode .sidebar {
    background-color: var(--gray-dark);
}

/* LINKS */
.sidebar a {
    display: block;
    padding: 12px 18px;
    border-radius: 12px;
    color: #e0e7ff;
    margin-bottom: 10px;
    font-size: 1.05rem;
    text-decoration: none;
    transition: background 0.2s;
}

.sidebar a.active {
    background-color: rgba(255,255,255,0.2);
    font-weight: bold;
}

.sidebar a:hover {
    background-color: rgba(255,255,255,0.15);
}

/* MAIN CONTENT */
.main-content {
    margin-left: 270px;
    padding: 35px;
}

/* ---- TABLE STYLING ---- */

/* Container */
.table-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 20px;
    border: 1px solid #e6e6e6;
}

/* Dark */
body.dark-mode .table-card {
    background: #1f2937;
    border: 1px solid rgba(255,255,255,0.05);
}

/* Table wrapper */
.table-modern {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 10px;
}

/* Table header */
.table-modern thead tr th {
    background: transparent;
    font-weight: 600;
    padding: 10px 15px;
    color: #555;
}

/* DARK HEADERS */
body.dark-mode .table-modern thead tr th {
    color: #9ca3af;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

/* Table rows */
.table-modern tbody tr {
    background: #f8f9fa;
    transition: 0.2s;
    border-radius: 12px;
}

.table-modern tbody tr:hover {
    background: #eef1f4;
}

/* DARK ROWS */
body.dark-mode .table-modern tbody tr {
    background: rgba(255,255,255,0.03);
}

body.dark-mode .table-modern tbody tr:hover {
    background: rgba(255,255,255,0.08);
}

/* Table cells */
.table-modern tbody td {
    padding: 16px;
    color: #333;
}

/* DARK CELL TEXT */
body.dark-mode .table-modern tbody td {
    color: #e5e7eb;
}

/* Badges */
.badge-modern {
    border-radius: 10px;
    font-size: 12px;
    padding: 6px 10px;
}

.badge-pending { background: #ffca2c; color: #332a00; }
.badge-none { background: #6c757d; }
.badge-approved { background: #198754; }

/* SEARCH BOX */
.search-box {
    background: #ffffff;
    border: 1px solid #ddd;
    color: #333;
}

body.dark-mode .search-box {
    background: #1f2937;
    border: 1px solid rgba(255,255,255,0.1);
    color: white;
}


/* ===== MODAL DARK MODE ===== */

body.dark-mode .modal-content {
    background-color: #1f2937 !important; /* gris foncé */
    color: #f3f4f6 !important; /* texte white-soft */
    border: 1px solid rgba(255, 255, 255, 0.1);
}

body.dark-mode .modal-header {
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

body.dark-mode .modal-footer {
    border-top: 1px solid rgba(255,255,255,0.1);
}

body.dark-mode .modal-title {
    color: #f3f4f6 !important;
}

body.dark-mode .btn-close {
    filter: invert(1) grayscale(100%); /* close button visible */
}

/* Text inside modal */
body.dark-mode #modalContent h4,
body.dark-mode #modalContent h5,
body.dark-mode #modalContent h6,
body.dark-mode #modalContent p,
body.dark-mode #modalContent strong {
    color: #e5e7eb !important;
}

/* Optional: image shadow more visible in dark mode */
body.dark-mode #modalContent img {
    box-shadow: 0 0 10px rgba(255,255,255,0.08);
}

</style>

<style>

/* STAT CARDS BASE */
.stat-card {
    border-radius: 20px;
    padding: 25px;
    border: none;
    transition: 0.2s;
    color: white;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

/* 🔵 USERS (BLUE / INDIGO) */
.stat-blue {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
}
body.dark-mode .stat-blue {
    background: linear-gradient(135deg, #3730a3, #4f46e5);
}

/* 🟡 KYC PENDING (YELLOW) */
.stat-yellow {
    background: linear-gradient(135deg, #facc15, #eab308);
}
body.dark-mode .stat-yellow {
    background: linear-gradient(135deg, #a16207, #ca8a04);
}

/* 🟢 APPROVED (GREEN) */
.stat-green {
    background: linear-gradient(135deg, #22c55e, #16a34a);
}
body.dark-mode .stat-green {
    background: linear-gradient(135deg, #15803d, #166534);
}

/* TEXT FIXES */
.stat-card h6 {
    opacity: 0.9;
    font-weight: 500;
}

.stat-card h2 {
    font-size: 2.6rem;
    font-weight: 800;
}


/* ===============================
   MOBILE SIDEBAR RESPONSIVE
================================ */

/* Mobile top bar */
.mobile-topbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    padding: 0 15px;
    z-index: 1200;
}

body.dark-mode .mobile-topbar {
    background: #111827;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

/* Sidebar mobile default (hidden) */


/* Desktop behavior unchanged */
/* ===============================
   MOBILE SIDEBAR – FINAL FIX
================================ */

@media (max-width: 768px) {

    /* Hide sidebar by default */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 290px;
        height: 100vh;
        background-color: var(--indigo);
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 1300;
    }

    /* Show sidebar when toggled */
    .sidebar.show {
        transform: translateX(0);
    }

    /* Main content fix */
    .main-content {
        margin-left: 0 !important;
        padding-top: 90px;
    }
}

/* Overlay */
.sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 1250;
    display: none;
}

/* Show overlay when sidebar open */
.sidebar.show ~ .sidebar-overlay {
    display: block;
}

/* Desktop behavior */
@media (min-width: 769px) {
    .sidebar {
        transform: translateX(0);
    }

    .sidebar-overlay {
        display: none !important;
    }
}




/* ===============================
   RESPONSIVE TABLES & CARDS
================================ */

/* Cards responsiveness */
@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 1rem;
        font-size: 0.9rem;
        padding: 15px;
    }

    .stat-card h2 {
        font-size: 2rem;
    }

    .stat-card h6 {
        font-size: 0.85rem;
    }

    /* Main content padding */
    .main-content {
        padding: 15px !important;
    }

    /* Table responsiveness */
    .table-card {
        overflow-x: auto;
    }

    .table-modern {
        min-width: 600px; /* permet le scroll horizontal sur mobile */
    }

    /* Search input full width */
    .search-container {
        width: 100% !important;
        margin-bottom: 0.75rem;
    }

    /* Action buttons below search on mobile */
    .d-flex.justify-content-between.align-items-center.mb-3 {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }

    .d-flex.justify-content-between.align-items-center.mb-3 button {
        width: 100%;
    }

    /* Dashboard small cards in column */
    .row.g-4 > .col-md-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    /* Cards spacing */
    .stat-card,
    .table-card {
        margin-bottom: 1rem; /* add space below each card */
    }
}


.sidebar {
    overflow-y: auto;      /* enable vertical scroll */
    height: 100vh;         /* full viewport height */
    -webkit-overflow-scrolling: touch; /* smooth scroll on mobile */
}

/* Small scrollbar for sidebar */
.sidebar::-webkit-scrollbar {
    width: 6px;              /* width of vertical scrollbar */
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;  /* track color */
}

.sidebar::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.3); /* thumb color */
    border-radius: 3px;       /* rounded edges */
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.5); /* on hover */
}

</style>



    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (localStorage.theme === "dark") {
                document.body.classList.add("dark-mode");
                toggleIcon();
            }
        });

        function toggleTheme() {
            document.body.classList.toggle("dark-mode");

            if (document.body.classList.contains("dark-mode")) {
                localStorage.theme = "dark";
            } else {
                localStorage.theme = "light";
            }

            toggleIcon();
        }

        function toggleIcon() {
            const icon = document.getElementById("themeIcon");
            if (document.body.classList.contains("dark-mode")) {
                icon.classList.replace("bi-moon", "bi-sun");
            } else {
                icon.classList.replace("bi-sun", "bi-moon");
            }
        }
    </script>



</head>

<body>

        <!-- Mobile top bar -->
    <div class="mobile-topbar d-md-none">
        <button class="btn btn-light" onclick="toggleSidebar()">
            <i class="bi bi-list fs-3"></i>
        </button>
        <span class="fw-bold ms-2">ObpPay Admin</span>
    </div>

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>



    <!-- Sidebar -->
    <div class="sidebar shadow-lg">
        <h3 class="fw-bold mb-4">ObpPay Admin</h3>

        <a href="{{ route('admin.dashboard') }}"
           class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-fill me-2"></i> Dashboard
        </a>
        @if(auth()->user()->role === 'superadmin')
    <a href="{{ route('admin.settings.admins') }}"
    class="{{ request()->routeIs('admin.settings.admins') ? 'active' : '' }}">
        <i class="bi bi-people-fill me-2"></i> Gestion admins
        </a>
         @endif
         <a href="{{ route('admin.users.index') }}"
           class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
            <i class="bi bi-people me-2"></i> Utilisateurs
        </a>

        {{-- crediter le compte user visible for admin and superadmin --}}
        <a href="{{ route('admin.wallets.credit') }}"
           class="{{ request()->routeIs('admin.wallets.credit') ? 'active' : '' }}">
            <i class="bi bi-plus-circle me-2"></i> Créditer un client
        </a>

        {{-- crediter le compte des admins visible for superadmin only --}}
        @if(auth()->user()->role === 'superadmin')
        <a href="{{ route('admin.wallets.credit.admin') }}"
           class="{{ request()->routeIs('admin.wallets.credit.admin') ? 'active' : '' }}">
            <i class="bi bi-plus-circle me-2"></i> Créditer un admin
        </a>
        @endif

        {{-- crediter mon compte: only for superadmin    --}}
        @if(auth()->user()->role === 'superadmin')
        <a href="{{ route('admin.wallets.credit.self') }}"
           class="{{ request()->routeIs('admin.wallets.credit.self') ? 'active' : '' }}">
            <i class="bi bi-plus-circle me-2"></i> Créditer mon compte
        </a>
        @endif  

        {{-- KYC VALIDATION --}}

        <a href="{{ route('admin.kyc.index') }}"
           class="{{ request()->routeIs('admin.kyc.index') ? 'active' : '' }}">
            <i class="bi bi-person-vcard me-2"></i> Validation KYC
        </a>
        <a href="{{ route('admin.transactions.index') }}"
        class="{{ request()->routeIs('admin.transactions.index') ? 'active' : '' }}">
            <i class="bi bi-cash-stack me-2"></i> Transactions
        </a>

        {{-- withdraw menu --}}

        <a href="{{ route('admin.withdrawals.index') }}"
           class="{{ request()->routeIs('admin.withdrawals.index') ? 'active' : '' }}">
            <i class="bi bi-wallet2 me-2"></i> Retraits Utilisateurs
        </a>

        <a href="{{ route('admin.loans.index') }}"
        class="{{ request()->routeIs('admin.loan.index') ? 'active' : '' }}">
            <i class="bi bi-hand-thumbs-up-fill me-2"></i> Aide / Prêts
        </a>

        {{-- user withdrawal, Marketplace, Settings can be added later --}}
{{--
        <a href="{{route('admin.withdrawals.index')}}"
              class="{{ request()->routeIs('admin.withdrawals.index') ? 'active' : '' }}">
                <i class="bi bi-wallet2 me-2"></i> Retraits Utilisateurs 
        </a>
            --}}
    
            {{-- Marketplace --}}
        <a href="{{ route('admin.marketplace.index') }}"
           class="{{ request()->routeIs('admin.marketplace.index') ? 'active' : '' }}">
            <i class="bi bi-shop me-2"></i> Marketplace
        </a>

         {{-- Product Marketplace --}}
        <a href="{{ route('admin.products.index') }}"
           class="{{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
            <i class="bi bi-box-seam me-2"></i> Produits
        </a>

        <a href="{{ route('admin.settings.admins.index') }}"
           class="{{ request()->routeIs('admin.settings.admins.index') ? 'active' : '' }}">
            <i class="bi bi-gear-fill me-2"></i> Paramètres
        </a>

        <hr class="border-light">

        <button onclick="toggleTheme()" class="btn btn-light w-100 mt-3 d-flex align-items-center justify-content-center gap-2">
            <i id="themeIcon" class="bi bi-moon"></i> Mode sombre
        </button>
    </div>

    <!-- Main content -->
    <div class="main-content">
        @yield('content')
    </div>
@yield('scripts')

    <script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
}

document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            document.querySelector('.sidebar').classList.remove('show');
        }
    });
});

window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        document.querySelector('.sidebar').classList.remove('show');
    }
});
</script>

<script>
const sidebar = document.querySelector('.sidebar');
const overlay = document.querySelector('.sidebar-overlay');
const hamburger = document.querySelector('.mobile-topbar button i');

function toggleSidebar() {
    sidebar.classList.toggle('show');
    overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
    toggleHamburgerIcon();
}

function toggleHamburgerIcon() {
    if (sidebar.classList.contains('show')) {
        hamburger.classList.replace('bi-list', 'bi-x');
    } else {
        hamburger.classList.replace('bi-x', 'bi-list');
    }
}

// Close sidebar when clicking overlay
overlay.addEventListener('click', () => {
    sidebar.classList.remove('show');
    overlay.style.display = 'none';
    toggleHamburgerIcon();
});

// Close sidebar when clicking a link (mobile)
document.querySelectorAll('.sidebar a').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('show');
            overlay.style.display = 'none';
            toggleHamburgerIcon();
        }
    });
});

// Optional: close sidebar if window resized > 768px
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        sidebar.classList.remove('show');
        overlay.style.display = 'none';
        hamburger.classList.replace('bi-x', 'bi-list');
    }
});
</script>



</body>
</html>
