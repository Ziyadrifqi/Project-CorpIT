<?php echo view('template/header'); ?>
<!--<< Breadcrumb Section Start >>-->
<div class="breadcrumb-wrapper bg-cover" style="background-image: url('<?php echo base_url('template/img/news.png') ?>');">
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

<!-- News Standard Section Start -->
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

                <div class="news-standard-wrapper">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-section" id="category-<?= $category['id'] ?>">
                            <h3 class="mb-4"><?= esc($category['name']); ?></h3>
                            <?php if (!empty($articlesByCategory[$category['id']])): ?>
                                <?php foreach ($articlesByCategory[$category['id']] as $article): ?>
                                    <div class="news-standard-items">
                                        <div class="news-thumb">
                                            <?php if (!empty($article['image'])): ?>
                                                <img src="<?= base_url('img/articles/' . $article['image']) ?>" alt="<?= esc($article['title']) ?>">
                                            <?php else: ?>
                                                <img src="<?= base_url('img/default.png') ?>" alt="Default Image">
                                            <?php endif; ?>
                                            <div class="post-date">
                                                <h3>
                                                    <?= date('d', strtotime($article['created_at'])) ?> <br>
                                                    <span><?= date('M', strtotime($article['created_at'])) ?></span>
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="news-content">
                                            <ul>
                                                <li>
                                                    <i class="fa-regular fa-user"></i>
                                                    <?= esc($article['author'] ?? 'Admin') ?>
                                                </li>
                                                <li>
                                                    <i class="fa-solid fa-tag"></i>
                                                    <?= esc($category['name']) ?>
                                                </li>
                                            </ul>
                                            <h3>
                                                <a href="<?= base_url('pages/view/' . $article['id']); ?>"><?= esc($article['title']) ?></a>
                                            </h3>
                                            <p>
                                                <?= character_limiter(strip_tags($article['content']), 150); ?>
                                            </p>
                                            <a href="<?= base_url('pages/view/' . $article['id']); ?>" class="theme-btn mt-4">
                                                Read More
                                                <i class="fa-solid fa-arrow-right-long"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-center text-muted">There are no articles for this category.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Inside the category section, after article listings -->
            <?php if ($totalPagesByCategory[$category['id']] > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPagesByCategory[$category['id']]; $i++): ?>
                        <a href="<?= base_url('pages/article?page=' . $i . '#category-' . $category['id']) ?>"
                            class="page-link <?= ($currentPage == $i) ? 'active' : '' ?>"
                            onclick="filterSingleCategory('category-<?= $category['id'] ?>')">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
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
                                            <span>(<?= count($articlesByCategory[$category['id']]) ?>)</span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Posts Widget -->
                    <div class="single-sidebar-widget">
                        <div class="wid-title">
                            <h3>Recent Post</h3>
                        </div>
                        <div class="recent-post-area">
                            <?php
                            $recentPosts = [];
                            foreach ($categories as $category) {
                                foreach ($articlesByCategory[$category['id']] as $article) {
                                    $recentPosts[] = $article;
                                }
                            }
                            usort($recentPosts, function ($a, $b) {
                                return strtotime($b['created_at']) - strtotime($a['created_at']);
                            });
                            $recentPosts = array_slice($recentPosts, 0, 3);

                            foreach ($recentPosts as $post):
                            ?>
                                <div class="recent-items">
                                    <div class="recent-thumb">
                                        <?php if (!empty($post['image'])): ?>
                                            <img src="<?= base_url('img/articles/' . $post['image']) ?>" alt="<?= esc($post['title']) ?>">
                                        <?php else: ?>
                                            <img src="<?= base_url('img/default.png') ?>" alt="Default Image">
                                        <?php endif; ?>
                                    </div>
                                    <div class="recent-content">
                                        <ul>
                                            <li>
                                                <i class="fa-solid fa-calendar-days"></i>
                                                <?= date('d M, Y', strtotime($post['created_at'])) ?>
                                            </li>
                                        </ul>
                                        <h6>
                                            <a href="<?= base_url('pages/view/' . $post['id']); ?>">
                                                <?= character_limiter(esc($post['title']), 50) ?>
                                            </a>
                                        </h6>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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