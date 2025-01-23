<?php echo view('template/header'); ?>
<!--<< Breadcrumb Section Start >>-->
<div class="breadcrumb-wrapper bg-cover" style="background-image: url('<?php echo base_url('template/img/details.png') ?>');">
    <div class="border-shape">
        <img src="<?php echo base_url('template/img/element.png') ?>" alt="shape-img">
    </div>
    <div class="line-shape">
        <img src="<?php echo base_url('template/img/line-element.png') ?>" alt="shape-img">
    </div>
    <div class="container">
        <div class="page-heading">
            <h1 class="wow fadeInUp" data-wow-delay=".3s"><?= esc($article['title']) ?></h1>
            <ul class="breadcrumb-items wow fadeInUp" data-wow-delay=".5s">
                <li>
                    <a href="<?= base_url('/') ?>">
                        Home
                    </a>
                </li>
                <li>
                    <i class="fas fa-chevron-right"></i>
                </li>
                <li>
                    Article Details
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- News Standard Section Start -->
<section class="news-standard fix section-padding">
    <div class="container">
        <div class="news-details-area">
            <div class="row g-5">
                <div class="col-12 col-lg-8">
                    <div class="blog-post-details">
                        <div class="single-blog-post">
                            <?php if (!empty($article['image'])): ?>
                                <div class="post-featured-thumb bg-cover"
                                    style="background-image: url('<?= base_url('img/articles/' . $article['image']) ?>');"></div>
                            <?php endif; ?>
                            <div class="post-content">
                                <ul class="post-list d-flex align-items-center">
                                    <li>
                                        <i class="fa-regular fa-user"></i>
                                        <?= esc($article['author']) ?>
                                    </li>
                                    <li>
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <?= date('d M, Y', strtotime($article['created_at'])) ?>
                                    </li>
                                    <?php if (!empty($article['category_name'])): ?>
                                        <li>
                                            <i class="fa-solid fa-tag"></i>
                                            <?= esc($article['category_name']) ?>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                                <h3><?= esc($article['title']) ?></h3>
                                <div class="article-content">
                                    <?= $article['content'] ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tags Section -->
                        <?php if (!empty($article['tags'])): ?>
                            <div class="row tag-share-wrap mt-4 mb-5">
                                <div class="col-lg-8 col-12">
                                    <div class="tagcloud">
                                        <?php
                                        $tags = explode(',', $article['tags']);
                                        foreach ($tags as $tag):
                                        ?>
                                            <a href="#"><?= trim(esc($tag)) ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-12 mt-3 mt-lg-0 text-lg-end">
                                    <div class="social-share">
                                        <span class="me-3">Share:</span>
                                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-12 col-lg-4">
                    <div class="main-sidebar">

                        <!-- Categories Widget -->
                        <?php if (!empty($categories)): ?>
                            <div class="single-sidebar-widget">
                                <div class="wid-title">
                                    <h3>Categories</h3>
                                </div>
                                <div class="news-widget-categories">
                                    <ul>
                                        <?php foreach ($categories as $category): ?>
                                            <li>
                                                <a href="<?= base_url('pages/article?category=' . $category['id']) ?>">
                                                    <?= esc($category['name']) ?>
                                                    <span>(<?= $category['article_count'] ?>)</span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Recent Posts Widget -->
                        <?php if (!empty($recent_posts)): ?>
                            <div class="single-sidebar-widget">
                                <div class="wid-title">
                                    <h3>Recent Post</h3>
                                </div>
                                <div class="recent-post-area">
                                    <?php foreach ($recent_posts as $post): ?>
                                        <div class="recent-items">
                                            <?php if (!empty($post['image'])): ?>
                                                <div class="recent-thumb">
                                                    <img src="<?= base_url('img/articles/' . $post['image']) ?>" alt="<?= esc($post['title']) ?>">
                                                </div>
                                            <?php endif; ?>
                                            <div class="recent-content">
                                                <ul>
                                                    <li>
                                                        <i class="fa-solid fa-calendar-days"></i>
                                                        <?= date('d M, Y', strtotime($post['created_at'])) ?>
                                                    </li>
                                                </ul>
                                                <h6>
                                                    <a href="<?= base_url('pages/view/' . $post['id']) ?>">
                                                        <?= character_limiter(esc($post['title']), 50) ?>
                                                    </a>
                                                </h6>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo view('template/footer'); ?>