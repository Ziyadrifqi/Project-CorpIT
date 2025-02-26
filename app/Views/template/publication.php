<?php echo view('template/header'); ?>
<!--<< Breadcrumb Section Start >>-->
<div class="breadcrumb-wrapper bg-cover" style="background-image: url('<?php echo base_url('template/img/uploads.png') ?>');">
    <div class="border-shape">
        <img src="<?php echo base_url('template/img/element.png') ?>" alt="shape-img">
    </div>
    <div class="line-shape">
        <img src="<?php echo base_url('template/img/line-element.png') ?>" alt="shape-img">
    </div>
    <div class="container">
        <div class="page-heading">
            <h1 class="wow fadeInUp" data-wow-delay=".3s"><?= esc($title) ?></h1>
            <ul class="breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                <li>
                    <a href="<?= base_url() ?>">
                        Home
                    </a>
                </li>
                <li>
                    <i class="fas fa-chevron-right"></i>
                </li>
                <li>
                    <?= esc($title) ?>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Publication Section Start -->
<section class="news-standard fix section-padding">
    <div class="container">
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-12 col-lg-8">
                <!-- Removed card wrapper as requested -->
                <div class="table-responsive">
                    <table id="publicationTable" class="table table-sm table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Category</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            foreach ($categories as $category):
                                if (!empty($filesByCategory[$category['id']])):
                                    foreach ($filesByCategory[$category['id']] as $file): ?>
                                        <tr class="category-row category-<?= $category['id'] ?>">
                                            <td class="text-center align-middle"> <?= $no++; ?> </td>
                                            <td class="align-middle"> <?= esc($category['name']); ?> </td>
                                            <td class="align-middle">
                                                <a class="text-dark">
                                                    <?= esc($file['title']) ?>
                                                </a>
                                            </td>
                                            <td class="align-middle"> <?= esc($file['author']); ?> </td>
                                            <td class="align-middle"> <?= character_limiter(esc($file['description']), 100); ?> </td>
                                            <td class="text-center align-middle">
                                                <?php if (!empty($file['full_path'])): ?>
                                                    <a href="/<?= $file['file_path']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                                        <i class="fa-solid fa-eye me-1"></i> Preview
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                            <?php endforeach;
                                endif;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Sidebar -->
            <div class="col-12 col-lg-4">
                <div class="main-sidebar">
                    <!-- Categories Widget -->
                    <div class="single-sidebar-widget">
                        <div class="wid-title">
                            <h3>Categories</h3>
                        </div>
                        <div class="news-widget-categories">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="javascript:void(0)" onclick="filterByCategory('all')" class="d-flex justify-content-between align-items-center">
                                        <span>All Categories</span>
                                        <span id="count-all">(<?= array_sum(array_map(function ($cat) {
                                                                    return count($cat);
                                                                }, $filesByCategory)) ?>)</span>
                                    </a>
                                </li>
                                <?php foreach ($categories as $category): ?>
                                    <li class="mb-2">
                                        <a href="javascript:void(0)" onclick="filterByCategory('<?= $category['id'] ?>')" class="d-flex justify-content-between align-items-center">
                                            <span><?= esc($category['name']) ?></span>
                                            <span>(<?= count($filesByCategory[$category['id']] ?? []) ?>)</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

<!-- Updated Custom CSS for DataTables controls -->
<style>
    /* Fix for select elements */
    div.dataTables_length select {
        width: 60px;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        height: calc(1.5em + 0.5rem + 2px);
        position: relative;
        z-index: 1;
        pointer-events: auto !important;
        opacity: 1 !important;
        display: inline-block !important;
        visibility: visible !important;
    }

    /* Fix for search input */
    div.dataTables_filter input {
        width: 120px;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        height: calc(1.5em + 0.5rem + 2px);
    }

    /* Fix pagination buttons */
    ul.pagination li a {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>

<!-- Improved Script Section -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Try to load scripts in sequence with better error handling
        loadScriptsSequentially([
            'https://code.jquery.com/jquery-3.6.0.min.js',
            'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js',
            'https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js'
        ], initializeDataTable);
    });

    function loadScriptsSequentially(urls, callback, index = 0) {
        if (index >= urls.length) {
            callback();
            return;
        }

        var script = document.createElement('script');
        script.src = urls[index];

        script.onload = function() {
            loadScriptsSequentially(urls, callback, index + 1);
        };

        script.onerror = function() {
            console.error('Failed to load script:', urls[index]);
            // Continue with next script even if one fails
            loadScriptsSequentially(urls, callback, index + 1);
        };

        document.head.appendChild(script);
    }

    function initializeDataTable() {
        try {
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.DataTable !== 'undefined') {
                var table = jQuery('#publicationTable').DataTable({
                    responsive: true,
                    ordering: true,
                    searching: true,
                    paging: true,
                    lengthMenu: [
                        [5, 10, 25, -1],
                        [5, 10, 25, "All"]
                    ],
                    language: {
                        lengthMenu: "Show _MENU_",
                        search: "Search:",
                        searchPlaceholder: "Keywords..."
                    },
                    dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-5'i><'col-sm-7'p>>"
                });

                // Force DataTables to redraw & recalculate column widths
                setTimeout(function() {
                    table.columns.adjust().draw();

                    // Additional fix for select elements
                    jQuery('div.dataTables_length select').css({
                        'visibility': 'visible',
                        'opacity': '1',
                        'display': 'inline-block',
                        'pointer-events': 'auto'
                    });
                }, 500);

                // Setup category filter function
                window.filterByCategory = function(categoryId) {
                    if (categoryId === 'all') {
                        jQuery('.category-row').show();
                    } else {
                        jQuery('.category-row').hide();
                        jQuery('.category-' + categoryId).show();
                    }

                    // Redraw the DataTable
                    table.draw();
                };
            } else {
                console.error('DataTables not loaded properly');
            }
        } catch (e) {
            console.error('DataTable initialization error:', e);
        }
    }
</script>

<?php echo view('template/footer'); ?>