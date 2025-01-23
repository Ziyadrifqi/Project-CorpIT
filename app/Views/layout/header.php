<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? $title : 'Home'; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('AdminLTE/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?php echo base_url('AdminLTE/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo base_url('AdminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?php echo base_url('AdminLTE/plugins/jqvmap/jqvmap.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url('AdminLTE/dist/css/adminlte.min.css') ?>">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?php echo base_url('AdminLTE/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?php echo base_url('AdminLTE/plugins/daterangepicker/daterangepicker.css') ?>">
    <!-- summernote -->
    <link rel="stylesheet" href="<?php echo base_url('AdminLTE/plugins/summernote/summernote-bs4.min.css') ?>">
</head>

<body class="hold-transition sidebar-mini layout-fixed  text-sm">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- User Account Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="modal" href="#" data-target="#logoutModal">
                        <i class="far fa-user"></i> <?= user()->username; ?>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="<?php echo base_url('AdminLTE/dist/img/lintasarta.png') ?>" alt="AdminLTELogo" height="70" width="auto">
        </div>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="<?= base_url('img/' . (user()->user_image ?? 'default.png')); ?>" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="<?= base_url('user/') ?>" class="d-block"><?= user()->username; ?></a>
                    </div>
                </div>

                <!-- SidebarSearch Form -->
                <div class="form-inline">
                    <div class="input-group" data-widget="sidebar-search">
                        <input class="form-control form-control-sidebar form-control-sm" type="search" placeholder="Search" aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-sidebar btn-sm">
                                <i class="fas fa-search fa-fw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <?php
                        $menuModel = new \App\Models\MenuModel();
                        $roleId = $menuModel->getCurrentUserRole();

                        if ($roleId) {
                            $menus = $menuModel->getMenusByRole($roleId);

                            foreach ($menus as $menu):
                                if (isset($menu['is_standalone'])): ?>
                                    <!-- Standalone Menu Item -->
                                    <li class="nav-item">
                                        <a href="<?= base_url($menu['url']) ?>" class="nav-link">
                                            <i class="<?= esc($menu['icon']) ?>"></i>
                                            <p><?= esc($menu['name']) ?></p>
                                        </a>
                                    </li>
                                <?php else: ?>
                                    <!-- Header Menu -->
                                    <?php if (!empty($menu['menus'])): ?>
                                        <li class="nav-header"><?= esc($menu['name']) ?></li>

                                        <?php foreach ($menu['menus'] as $submenu): ?>
                                            <?php if ($submenu['is_dropdown']): ?>
                                                <!-- Dropdown Menu -->
                                                <li class="nav-item">
                                                    <a href="#" class="nav-link">
                                                        <i class="<?= esc($submenu['icon']) ?>"></i>
                                                        <p>
                                                            <?= esc($submenu['name']) ?>
                                                            <i class="fas fa-angle-left right"></i>
                                                        </p>
                                                    </a>
                                                    <ul class="nav nav-treeview">
                                                        <?php foreach ($submenu['children'] as $child): ?>
                                                            <?php if ($child['has_access']): ?>
                                                                <li class="nav-item">
                                                                    <a href="<?= base_url($child['url']) ?>" class="nav-link">
                                                                        <i class="<?= esc($child['icon']) ?>"></i>
                                                                        <p><?= esc($child['name']) ?></p>
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </li>
                                            <?php else: ?>
                                                <!-- Regular Menu Item -->
                                                <?php if ($submenu['has_access']): ?>
                                                    <li class="nav-item">
                                                        <a href="<?= base_url($submenu['url']) ?>" class="nav-link">
                                                            <i class="<?= esc($submenu['icon']) ?>"></i>
                                                            <p><?= esc($submenu['name']) ?></p>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                        <?php endforeach;
                        }
                        ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>
        <!-- Logout Modal-->
        <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-primary" href="<?= base_url('logout') ?>">Logout</a>
                    </div>
                </div>
            </div>
        </div>