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
            <div class="col-12 col-lg-8">
                <?php if (!empty($categories)): ?>
                    <div class="mb-4">
                        <select class="form-select mb-3" id="categorySelect" onchange="filterCategory()">
                            <option value="all">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="category-<?= $category['id'] ?>"><?= esc($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="publication-standard-wrapper">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-section" id="category-<?= $category['id'] ?>">
                            <h3 class="mb-4"><?= esc($category['name']); ?></h3>
                            <?php if (!empty($filesByCategory[$category['id']])): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($filesByCategory[$category['id']] as $file): ?>
                                                <tr>
                                                    <td>
                                                        <a href="<?= base_url('pages/viewFile/' . $file['id']); ?>" class="text-dark">
                                                            <?= esc($file['title']) ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?= character_limiter(esc($file['description']), 100); ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($file['full_path'])): ?>
                                                            <a href="/<?= $file['file_path']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                                                <i class="fa-solid fa-eye me-1"></i>Preview
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-center text-muted">Tidak ada file dalam kategori ini.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
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
                            <ul>
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a href="#" onclick="filterSingleCategory('category-<?= $category['id'] ?>')">
                                            <?= esc($category['name']) ?>
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

<script>
    function filterCategory() {
        var selectedCategory = document.getElementById('categorySelect').value;
        filterSingleCategory(selectedCategory);
    }

    function filterSingleCategory(categoryId) {
        var sections = document.querySelectorAll('.category-section');
        sections.forEach(function(section) {
            if (categoryId === 'all' || section.id === categoryId) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });

        if (categoryId !== 'all') {
            document.getElementById('categorySelect').value = categoryId;
        }

        // Scroll to the selected category section
        var targetSection = document.getElementById(categoryId);
        if (targetSection) {
            targetSection.scrollIntoView({
                behavior: 'smooth'
            });
        }
    }
</script>

<?php echo view('template/footer'); ?>