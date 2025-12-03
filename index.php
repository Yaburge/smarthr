<base href="/smarthr/">

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="icon" type="image/x-icon" href="assets/media/images/ACLC logo.png">

  <!-- <link rel="stylesheet" href="assets/fontawesome/css/all.css"> -->

  <title>SmartHR</title>
</head>
<body>
  <div id="sidebar-backdrop"></div>
  <header id="navigation">
    <?php include 'partials/navigation.php'; ?>
  </header>

  <main id="app">
    <aside id="side-nav">
      <?php include 'partials/sidebar.php'; ?>
    </aside>
    <div id="display"></div>
  </main>

  <div id="modal"></div>

  <footer id="footer-nav"></footer>

  <script type="module" src="assets/js/app.js" defer></script>
  <script src="https://kit.fontawesome.com/fd08063381.js" crossorigin="anonymous" defer></script>
</body>
</html>