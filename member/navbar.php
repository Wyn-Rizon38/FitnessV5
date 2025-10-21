<!-- Black & Gold Navbar -->
<style>
/* Base styling */
.top-nav, .bottom-nav {
  background-color: #000;
  padding: 0.5rem 1rem;
}

/* Default link look */
.top-nav .nav-link,
.bottom-nav .nav-link {
  color: #FFD700 !important;  /* gold text/icons */
  text-decoration: none;
  padding: 0.5rem 0.8rem;
  transition: 0.2s;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* Hover effect */
.top-nav .nav-link:hover,
.bottom-nav .nav-link:hover {
  color: #fff !important;
}
.top-nav .nav-link:hover span {
  color: #fff !important;
}

/* Active link */
.top-nav .nav-link.active,
.bottom-nav .nav-link.active {
  background-color: #FFD700 !important; /* gold box */
  color: #000 !important;
  border-radius: 8px;
  font-weight: 600;
  box-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
}
.top-nav .nav-link.active span,
.bottom-nav .nav-link.active span {
  color: #000 !important; /* icons black inside gold */
}

/* === Logout special style === */
.top-nav .nav-link.logout,
.bottom-nav .nav-link.logout {
  color: #ff4d4d !important; /* red text/icons */
}
.top-nav .nav-link.logout:hover,
.bottom-nav .nav-link.logout:hover {
  color: #fff !important;
  background-color: #ff4d4d !important;
  box-shadow: 0 0 10px rgba(255, 77, 77, 0.6);
}
.top-nav .nav-link.logout span,
.bottom-nav .nav-link.logout span {
  color: inherit !important; /* match red */
}

/* === Mobile Bottom Nav === */
.bottom-nav {
  position: fixed;  /* stick to bottom */
  bottom: 0;
  left: 0;
  width: 100%;
  border-top: 1px solid rgba(255, 215, 0, 0.4);
  z-index: 1050;
}

.bottom-nav .nav-link {
  flex: 1;
  display: flex;
  flex-direction: column;  /* stack icon above text */
  align-items: center;
  justify-content: center;
  gap: 4px;
  font-size: 0.8rem;
}

.bottom-nav .nav-link span {
  font-size: 1.4rem; /* larger icon */
  line-height: 1;
}

/* Prevent content hidden behind bottom nav */
body {
  padding-bottom: 60px; /* adjust based on nav height */
}
</style>

<!-- Top Navbar for Desktop -->
<nav class="top-nav d-none d-sm-flex justify-content-center">
  <a href="dashboard.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php') echo 'active'; ?>">
    <span class="bi bi-house"></span> Home
  </a>
  <a href="schedule.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='schedule.php') echo 'active'; ?>">
    <span class="bi bi-calendar-event"></span> Schedule
  </a>
  <a href="program.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='program.php') echo 'active'; ?>">
    <span class="bi bi-activity"></span> Workout Programs
  </a>
  <a href="chatbot.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='chatbot.php') echo 'active'; ?>">
    <span class="bi bi-robot"></span> Chatbot
  </a>
  <a href="logout.php" class="nav-link logout">
    <span class="bi bi-box-arrow-right"></span> Logout
  </a>
</nav>

<!-- Bottom Navbar for Mobile -->
<nav class="bottom-nav d-flex d-sm-none justify-content-around">
  <a href="dashboard.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php') echo 'active'; ?>">
    <span class="bi bi-house"></span>
    Home
  </a>
  <a href="schedule.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='schedule.php') echo 'active'; ?>">
    <span class="bi bi-calendar-event"></span>
    Schedule
  </a>
  <a href="program.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='program.php') echo 'active'; ?>">
    <span class="bi bi-activity"></span>
    Workout
  </a>
  <a href="chatbot.php" class="nav-link <?php if(basename($_SERVER['PHP_SELF'])=='chatbot.php') echo 'active'; ?>">
    <span class="bi bi-robot"></span>
    Chatbot
  </a>
  <a href="logout.php" class="nav-link logout">
    <span class="bi bi-box-arrow-right"></span>
    Logout
  </a>
</nav>
