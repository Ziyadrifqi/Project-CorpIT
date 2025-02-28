<!-- Footer Area   S T A R T -->
<footer class="footer-area">
    <div class="widget-area style1  pt-100 pb-80">
        <div class="container">
            <div class="footer-layout style1">
                <div class="row">
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="widget footer-widget wow fadeInUp" data-wow-delay=".6s">
                            <div class="gt-widget-about">
                                <div class="about-logo">
                                    <a href="<?= base_url() ?>"><img src="<?php echo base_url('template/img/logo-footer.png') ?>" alt="extech"></a>
                                </div>
                                <p class="about-text"> Jakarta Pusat, Menara Thamrin 12th Floor Jl. M.H. Thamrin Kav.3 Jakarta 10250</p>
                                <div class="gt-social style2">
                                    <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                                    <a href="https://www.twitter.com/"><i class="fab fa-twitter"></i></a>
                                    <a href="https://www.youtube.com/"><i class="fab fa-youtube"></i></a>
                                    <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-6 col-12">
                        <div class="widget widget_nav_menu footer-widget wow fadeInUp" data-wow-delay="1s">
                            <h3 class="widget_title">Quick Links</h3>
                            <div class="menu-all-pages-container">
                                <ul class="menu">
                                    </li>
                                    <li><a href="<?= base_url('pages/article') ?>"><i class="fa-solid fa-chevrons-right"></i>Our
                                            Blogs</a>
                                    </li>
                                    <li><a href="<?= base_url('pages/publication') ?>"><i class="fa-solid fa-chevrons-right"></i>Work Instruction</a></li>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6 col-12">
                        <div class="widget footer-widget wow fadeInUp" data-wow-delay="1.3s">
                            <h3 class="widget_title">Recent Posts</h3>
                            <div class="recent-post-wrap">
                                <?php foreach ($recent_posts as $post): ?>
                                    <div class="recent-post">
                                        <div class="media-img">
                                            <a href="<?= base_url('pages/view/' . $post['id']); ?>">
                                                <?php if (!empty($post['image'])): ?>
                                                    <img src="<?= base_url('img/articles/' . $post['image']) ?>" alt="<?= esc($post['title']) ?>">
                                                <?php else: ?>
                                                    <img src="<?= base_url('img/default.png') ?>" alt="Default Image">
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <div class="recent-post-meta">
                                                <a href="<?= base_url('pages/view/' . $post['id']); ?>">
                                                    <img src="<?= base_url('template/img/icon/calendarIcon.svg') ?>" alt="icon">
                                                    <?= date('d M, Y', strtotime($post['created_at'])) ?>
                                                </a>
                                            </div>
                                            <h4 class="post-title">
                                                <a class="text-inherit" href="<?= base_url('pages/view/' . $post['id']); ?>">
                                                    <?= character_limiter(esc($post['title']), 50) ?>
                                                </a>
                                            </h4>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 col-12">
                        <div class="widget widget_nav_menu footer-widget wow fadeInUp" data-wow-delay="1.6s">
                            <h3 class="widget_title">Contact Us</h3>
                            <div class="checklist style2">
                                <ul class="ps-0">
                                    <li class="text-white"><i class="fa-solid fa-envelope"></i></li>
                                    <li class="text-white">info@lintasarta.co.id</li>
                                </ul>
                                <ul class="ps-0">
                                    <li class="text-white"><i class="fa-solid fa-phone"></i></li>
                                    <li class="text-white">+6221 230 2345</li>
                                </ul>
                                <div class="email-input-container">
                                    <input type="email" id="email" placeholder="Your email address" required="">
                                    <button type="submit" id="submitButton" disabled=""><i
                                            class="fa-regular fa-arrow-right-long"></i></button>
                                </div>
                                <form id="termsForm">
                                    <label class="custom-checkbox">
                                        <input type="checkbox" name="agree" id="agreeCheckbox">
                                        <span class="checkmark"></span>
                                        I agree to the <a class="text-underline" href="contact.html" target="_blank">Privacy
                                            Policy.</a>
                                    </label>
                                    <br>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-wrap bg-theme">
        <div class="container">
            <div class="copyright-layout">
                <div class="layout-text wow fadeInUp" data-wow-delay=".3s">
                    <p class="copyright">
                        <i class="fal fa-copyright"></i> Copyright &copy; Lintasarta <?= date('Y'); ?></a>
                    </p>
                </div>
                <div class="layout-link wow fadeInUp" data-wow-delay=".6s">
                    <div class="link-wrapper">
                        <a href="contact.html">Terms &amp; Condition </a>
                        <a href="contact.html">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!--<< All JS Plugins >>-->
<script src="<?php echo base_url('template/js/jquery-3.7.1.min.js') ?>"></script>
<!--<< Viewport Js >>-->
<script src="<?php echo base_url('template/js/viewport.jquery.js') ?>"></script>
<!--<< Bootstrap Js >>-->
<script src="<?php echo base_url('template/js/bootstrap.bundle.min.js') ?>"></script>
<!--<< Nice Select Js >>-->
<script src="<?php echo base_url('template/js/jquery.nice-select.min.js') ?>"></script>
<!--<< Waypoints Js >>-->
<script src="<?php echo base_url('template/js/jquery.waypoints.js') ?>"></script>
<!--<< Counterup Js >>-->
<script src="<?php echo base_url('template/js/jquery.counterup.min.js') ?>"></script>
<!--<< Swiper Slider Js >>-->
<script src="<?php echo base_url('template/js/swiper-bundle.min.js') ?>"></script>
<!--<< MeanMenu Js >>-->
<script src="<?php echo base_url('template/js/jquery.meanmenu.min.js') ?>"></script>
<!--<< Magnific Popup Js >>-->
<script src="<?php echo base_url('template/js/jquery.magnific-popup.min.js') ?>"></script>
<!--<< Wow Animation Js >>-->
<script src="<?php echo base_url('template/js/wow.min.js') ?>"></script>
<!--<< Main.js >>-->
<script src="<?php echo base_url('template/js/main.js') ?>"></script>
</body>

</html>