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
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-hover" id="publicationTable">
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
                                                <a href="<?= base_url('pages/viewFile/' . $file['id']); ?>" class="text-dark">
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
                                <?php foreach ($categories as $category): ?>
                                    <li class="mb-2">
                                        <a href="#" onclick="filterSingleCategory('category-<?= $category['id'] ?>')" class="d-flex justify-content-between align-items-center">
                                            <span><?= esc($category['name']) ?></span>
                                            <span class="badge bg-primary">(<?= count($filesByCategory[$category['id']] ?? []) ?>)</span>
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

<script>
    function filterSingleCategory(categoryClass) {
        let rows = document.querySelectorAll('.category-row');
        rows.forEach(row => {
            row.style.display = row.classList.contains(categoryClass) ? '' : 'none';
        });
    }
</script>


<?php echo view('template/footer'); ?>