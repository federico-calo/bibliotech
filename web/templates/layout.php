<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>bibliotech</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/font/fontawesome/css/all.min.css">
      <link rel="stylesheet" href="/assets/swagger/swagger-ui.css" />
  </head>
  <body>
    <header class="banner bg-custom-primary text-white text-center">
      <div class="container">
        <a class="logo" href="/">
          <h1>bibliotech</h1>
        </a>
      </div>
    </header>
    <?php if (!empty($messageText)) : ?>
      <div class="container alert alert-<?php echo $messageType ?? '' ?> text-center mt-4 mx-100"
           role="alert">
          <?php echo $messageText ?>
      </div>
    <?php endif; ?>
    <div class="container search mt-4 px-0">
      <form action="/" class="search-form">
        <input type="text" name="search" placeholder="Rechercher...">
        <button type="submit"><i class="fas fa-search"></i></button>
      </form>
    </div>
    <div class="container main-content my-4 pb-4">
        <?php echo !empty($content) ? $content : ''; ?>
    </div>
    <footer class="text-white text-center">
      <div class="footer-top text-white text-center py-3">
        <a target="_blank" class="footer-link" href="https://federico-calo.net">
          Une application proposée par <strong>Federico CALO</strong>
        </a>
      </div>
    </footer>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
  </body>
</html>