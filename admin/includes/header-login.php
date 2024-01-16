<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Project</title>

  <link rel="stylesheet" href="<?= urlOf('plugins/fontawesome-free/css/all.min.css') ?>">

  <link rel="stylesheet" href="<?= urlOf('css/fonts.css') ?>">
  <link rel="stylesheet" href="<?= urlOf('css/adminlte.min.css') ?>">
  <link rel="stylesheet" href="<?= urlOf('css/style.css') ?>">

  <script src="<?= urlOf('plugins/jquery/jquery.min.js') ?>"></script>
  <script src="<?= urlOf('js/constants.js') ?>"></script>
  <script src="<?= urlOf('js/app.js') ?>"></script>
  <script src="<?= urlOf('js/auth.js') ?>"></script>
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
      </ul>
    </nav>
    <aside class="main-sidebar sidebar-light-primary elevation-4">
      <a href="#" class="brand-link">
        <img src="<?= urlOf('assets/images/AdminLTELogo.png') ?>" alt="Admin panel"
          class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Admin Panel</span>
      </a>
    </aside>