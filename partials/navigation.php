<nav class="primary-navigation" id="navigation">
    <div class="row-fixed align-center nav-header">
        <button id="admin-toggle-btn" onclick="toggleAdminSidebar()" aria-label="Toggle sidebar">
            <i class="fa-solid fa-bars fa-lg"></i>
        </button>

        <h1 class="sub-header bold">Smart<span class="accent-400">HR</span></h1>
    </div>

    <button class="tableBtn outlineBtn"
            data-trigger="modal"
            data-title="Confirm Logout"
            data-url="ajax/prompt.php?msg=Are you sure you want to log out?&action=logout&type=danger">
        <i class="fa-solid fa-right-from-bracket fa-lg"></i>
    </button>
</nav>