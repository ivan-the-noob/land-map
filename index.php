<?php 
    require 'db.php';
    $query = "SELECT * FROM cms LIMIT 1";
    $result = $conn->query($query);
    $cms = $result->fetch_assoc();

    $fontStyle = htmlspecialchars($cms['font_style'] ?? 'normal');
    $fontFamily = htmlspecialchars($cms['font_family'] ?? 'Arial, sans-serif');
    $fontSize = htmlspecialchars($cms['font_size'] ?? '32'); 
    $backgroundColor = htmlspecialchars($cms['background_color'] ?? '#006D77');
    $imageFile = htmlspecialchars($cms['img'] ?? '');
    $backgroundImage = "assets/images/cms/{$imageFile}";

    if (is_numeric($fontSize)) {
        $fontSize .= 'px';
    }

    $style = "font-family: {$fontFamily}; font-size: {$fontSize}; ";
    $style .= ($fontStyle === 'bold') ? "font-weight: bold;" : "font-style: {$fontStyle};";


    $styleWithoutFontSize = "font-family: {$fontFamily}; ";
    $styleWithoutFontSize .= ($fontStyle === 'bold') ? "font-weight: bold;" : "font-style: {$fontStyle};";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Map | Home</title>
    <!-- Add a favicon -->
    <link rel="icon" href="../assets/images/logo.png" type="image/x-icon">

    <!--LINKS-->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Source+Serif+Pro:wght@400;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="assets/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="assets/fonts/icomoon/style.css">
    <link rel="stylesheet" href="assets/fonts/flaticon/font/flaticon.css">
    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>

    <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
            <div class="site-mobile-menu-close">
                <span class="icofont-close js-menu-toggle"></span>
            </div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>

    <!--Navigation bar head-->

    <?php require "partials/nav_landing.php" ?>

    <!--Navigation bar tail-->

    <div class="hero" style="background: <?= $backgroundColor ?>;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="intro-wrap">
                    <h1 class="mb-5">
                        <span class="d-block w-100"  style="<?= $style ?>"><?= nl2br(htmlspecialchars($cms['text'] ?? '')) ?></span>
                        <span class="typed-words"  style="<?= $style ?>"></span>
                    </h1>
                        </h1>

                        <!--Search bar head-->
                        <div class="row">
                            <div class="col-12">
                                <form class="form">
                                    <div class="row mb-2">
                                        <div class="col-sm-12 col-md-6 mb-3 mb-lg-0 col-lg-6">
                                            <select name="saleTypeFilter" id="saleTypeFilter" class="form-control">
                                                <option value="">Sale/Lease Type</option>
                                                <option value="sale">For Sale</option>
                                                <option value="lease">For Lease</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-12 col-md-6 mb-3 mb-lg-0 col-lg-6">
                                            <select name="landTypeFilter" id="landTypeFilter" class="form-control">
                                                <option value="all">Land Types</option>
                                                <option value="house and lot">House and Lot</option>
                                                <option value="agricultural farm">Agricultural Farm</option>
                                                <option value="commercial lot">Commercial Lot</option>
                                                <option value="raw land">Raw Land</option>
                                                <option value="residential land">Residential Land</option>
                                                <option value="residential farm">Residential Farm</option>
                                                <option value="memorial lot">Memorial Lot</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <!--Search location-->
                                        <div class="col-sm-12 col-md-6 mb-3 mb-lg-0 col-lg-6">
                                            <div class="position-relative">
                                                <input id="searchInput" placeholder="Barangay, Municipal, Province" data-cy="home-filter-search-keyword" data-test="textInput-field" maxlength="100" minlength="0" autocomplete="off" aria-label="Search lands" type="search" class="-input form-control w-100" aria-invalid="false" value="">
                                                <span class="icon-room position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%);"></span>
                                            </div>
                                        </div>
                                        <!--Search price range -->
                                        <div class="col-sm-12 col-md-6 mb-3 mb-lg-0 col-lg-6">
                                            <div class="d-flex align-items-center">
                                                <div class="input-group me-2">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="text"  min="0" step="1000" class="form-control" name="min_price" placeholder="Minimum price" oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value < 0) this.value = 0; this.value = Number(this.value).toLocaleString();">
                                                </div>
                                                <div class="input-group">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="text" min="0" max="1000000000" step="1000" class="form-control" name="max_price" placeholder="Maximum price" oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value < 0) this.value = 0; if(this.value > 1000000000) this.value = 1000000000; this.value = Number(this.value).toLocaleString();">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--Search button/ check box-->
                                    <div class="row align-items-center">
                                        <div class="col-sm-12 col-md-6 mb-3 mb-lg-0 col-lg-4">
                                            <button type="submit" class="btn btn-primary btn-block w-100" value="Search">
                                                <i class="icon-search"></i> Search</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!--Search bar tail-->

                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="slides">
                        <img src="assets/images/cms/<?= htmlspecialchars($cms['img'] ?? 'assets/images/hero-slider-1.jpg') ?>" alt="Image" class="img-fluid active">
                        <img src="assets/images/hero-slider-2.jpg" alt="Image" class="img-fluid">
                        <img src="assets/images/hero-slider-3.jpg" alt="Image" class="img-fluid">
                        <img src="assets/images/hero-slider-4.jpg" alt="Image" class="img-fluid">
                        <img src="assets/images/hero-slider-5.jpg" alt="Image" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="untree_co-section">
        <div class="container">
            <div class="row mb-5 justify-content-center">
            <div class="col-lg-6 text-center">
                    <h2 class="section-title text-center mb-3" style="<?= $styleWithoutFontSize ?>">LAND SERVICES</h2>
                    <p style="<?= $styleWithoutFontSize ?>"><?= nl2br(htmlspecialchars($cms['land_services']))?></p>
                </div>
            </div>
            <div class="row align-items-stretch">
                <div class="col-lg-4 order-lg-1">
                    <div class="h-100">
                        <div class="frame h-100">
                        <div class="feature-img-bg h-100" style="background-image: url('<?= $backgroundImage ?>');"></div>
                                
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-6 col-lg-4 feature-1-wrap d-md-flex flex-md-column order-lg-1">

                <div class="feature-1 d-md-flex">
                        <div class="align-self-center">
                            <span class="flaticon-house display-4 text-primary"></span>
                            <h3>Easy to Use</h3>
                            <p class="mb-0">Our user-friendly platform makes searching and managing land properties simple and efficient for cients, and agents.</p>
                        </div>
                    </div>

                    <div class="feature-1 ">
                        <div class="align-self-center">
                            <span class="flaticon-restaurant display-4 text-primary"></span>
                            <h3>Land Description</h3>
                            <p class="mb-0">Detailed property information including lot size, zoning, topography, aminities and other key features to help inform your land purchase decision.</p>
                        </div>
                    </div>

                </div>

                <div class="col-6 col-sm-6 col-lg-4 feature-1-wrap d-md-flex flex-md-column order-lg-3">

                    <div class="feature-1 d-md-flex">
                        <div class="align-self-center">
                            <span class="flaticon-mail display-4 text-primary"></span>
                            <h3>Easy Registration</h3>
                            <p class="mb-0">Quick and simple registration process for buyers, sellers and agents to access our land property services and listings.</p>
                        </div>
                    </div>

                    <div class="feature-1 d-md-flex">
                        <div class="align-self-center">
                            <span class="flaticon-phone-call display-4 text-primary"></span>
                            <h3>24/7 Support</h3>
                            <p class="mb-0">Our dedicated support team is available 24/7 to assist with any land property inquiries, technical issues, or urgent requests.</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>


    <div class="untree_co-section">
        <div class="container">
            <div class="row text-center justify-content-center mb-5">
                <div class="col-lg-7">
                    <h2 class="section-title text-center">LAND PROPERTIES FOR SALE</h2>
                </div>
            </div>

            <div class="owl-carousel owl-3-slider">

                <div class="item">
                    <a class="media-thumb" href="assets/images/hero-slider-1.jpg" data-fancybox="gallery" onclick="window.location.href='/properties'">
                        <div class="media-text">
                            <h3>Agricultural Land</h3>
                            <span class="icon-room"></span>
                            <span class="location">Cavite</span>
                            <div class="price ml-auto">
                                <span>₱520.00</span>
                            </div>
                        </div>
                        <img src="assets/images/hero-slider-1.jpg" alt="Image" class="img-fluid">
                    </a>
                </div>

                <div class="item">
                    <a class="media-thumb" href="assets/images/hero-slider-1.jpg" data-fancybox="gallery" onclick="window.location.href='/properties'">
                        <div class="media-text">
                            <h3>Commercial Land</h3>
                            <span class="icon-room"></span>
                            <span class="location">Cavite</span>
                            <div class="price ml-auto">
                                <span>₱520.00</span>
                            </div>
                        </div>
                        <img src="assets/images/hero-slider-1.jpg" alt="Image" class="img-fluid">
                    </a>
                </div>

                <div class="item">
                    <a class="media-thumb" href="assets/images/hero-slider-1.jpg" data-fancybox="gallery" onclick="window.location.href='/properties'">
                        <div class="media-text">
                            <h3>Residential Land</h3>
                            <span class="icon-room"></span>
                            <span class="location">Cavite</span>
                            <div class="price ml-auto">
                                <span>₱520.00</span>
                            </div>
                        </div>
                        <img src="assets/images/hero-slider-1.jpg" alt="Image" class="img-fluid">
                    </a>
                </div>

                <div class="item">
                    <a class="media-thumb" href="assets/images/hero-slider-1.jpg" data-fancybox="gallery" onclick="window.location.href='/properties'">
                        <div class="media-text">
                            <h3>Industrial Land</h3>
                            <span class="icon-room"></span>
                            <span class="location">Cavite</span>
                            <div class="price ml-auto">
                                <span>₱520.00</span>
                            </div>
                        </div>
                        <img src="assets/images/hero-slider-1.jpg" alt="Image" class="img-fluid">
                    </a>
                </div>

                <div class="item">
                    <a class="media-thumb" href="assets/images/hero-slider-1.jpg" data-fancybox="gallery" onclick="window.location.href='/properties'">
                        <div class="media-text">
                            <h3>Recreational Land</h3>
                            <span class="icon-room"></span>
                            <span class="location">Cavite</span>
                            <div class="price ml-auto">
                                <span>₱520.00</span>
                            </div>
                        </div>
                        <img src="assets/images/hero-slider-1.jpg" alt="Image" class="img-fluid">
                    </a>
                </div>

                <div class="item">
                    <a class="media-thumb" href="assets/images/hero-slider-1.jpg" data-fancybox="gallery" onclick="window.location.href='/properties'">
                        <div class="media-text">
                            <h3>Mixed-Use Land</h3>
                            <span class="icon-room"></span>
                            <span class="location">Cavite</span>
                            <div class="price ml-auto">
                                <span>₱520.00</span>
                            </div>
                        </div>
                        <img src="assets/images/hero-slider-1.jpg" alt="Image" class="img-fluid">
                    </a>
                </div>
            </div>
        </div>
    </div>


    <div class="untree_co-section">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-6">
                    <h2 class="section-title text-center mb-3">AFFORDABLE LAND PROPERTIES</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                    <div class="media-1">
                        <a href="#" class="d-block mb-3"><img src="assets/images/hero-slider-1.jpg" alt="Image"
                                class="img-fluid"></a>
                        <span class="d-flex align-items-center loc mb-2">
                            <span class="icon-room mr-3"></span>
                            <span>Cavite</span>
                        </span>
                        <div class="d-flex align-items-center">
                            <div>
                                <h3><a href="#">Agricultural Farm</a></h3>
                                <div class="price ml-auto">
                                    <span>₱520.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                    <div class="media-1">
                        <a href="#" class="d-block mb-3"><img src="assets/images/hero-slider-2.jpg" alt="Image"
                                class="img-fluid"></a>
                        <span class="d-flex align-items-center loc mb-2">
                            <span class="icon-room mr-3"></span>
                            <span>Laguna</span>
                        </span>
                        <div class="d-flex align-items-center">
                            <div>
                                <h3><a href="#">Commercial Lot</a></h3>
                                <div class="price ml-auto">
                                    <span>₱520.00</span>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                    <div class="media-1">
                        <a href="#" class="d-block mb-3"><img src="assets/images/hero-slider-3.jpg" alt="Image"
                                class="img-fluid"></a>
                        <span class="d-flex align-items-center loc mb-2">
                            <span class="icon-room mr-3"></span>
                            <span>Batangas</span>
                        </span>
                        <div class="d-flex align-items-center">
                            <div>
                                <h3><a href="#">Residential Land</a></h3>
                                <div class="price ml-auto">
                                    <span>₱750.00</span>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="col-6 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                    <div class="media-1">
                        <a href="#" class="d-block mb-3"><img src="assets/images/hero-slider-4.jpg" alt="Image"
                                class="img-fluid"></a>

                        <span class="d-flex align-items-center loc mb-2">
                            <span class="icon-room mr-3"></span>
                            <span>Quezon</span>
                        </span>

                        <div class="d-flex align-items-center">
                            <div>
                                <h3><a href="#">Memorial Lot</a></h3>
                                <div class="price ml-auto">
                                    <span>₱520.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <a href="#" class="btn btn-primary custom-hover">View More Lands</a>
                </div>
            </div>
        </div>
    </div>


    <div class="py-5 cta-section">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-12">
                    <h2 class="mb-2 text-white">Discover your ideal property with ease using our LandMap</h2>
                    <p class="mb-4 lead text-white text-white-opacity">Contact us today to start your land journey!</p>
                    <p class="mb-0"><a href="booking.html"
                            class="btn btn-outline-white text-white btn-md font-weight-bold">Get in touch</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="site-footer">
        <div class="inner first">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 col-lg-4">
                        <div class="widget">
                            <h3 class="heading">About Page</h3>
                            <p style="<?= $styleWithoutFontSize ?>"><?= nl2br(htmlspecialchars($cms['about_page'] ?? '')) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2 pl-lg-5">
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <div class="widget">
                            <h3 class="heading">Pages</h3>
                            <ul class="links list-unstyled">
                                <li><a href="/">Home</a></li>
                                <li><a href="/agents">Agents</a></li>
                                <li><a href="/properties">Properties</a></li>
                                <li><a href="/contact_us">Contact Us</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="widget">
                            <h3 class="heading">Contacts</h3>
                            <ul class="list-unstyled quick-info links">
                                <li class="email"><a href="#"  style="<?= $styleWithoutFontSize ?>"><?= nl2br(htmlspecialchars($cms['contact_email'] ?? '')) ?></a></li>
                                <li class="phone"><a href="#"  style="<?= $styleWithoutFontSize ?>"><?= nl2br(htmlspecialchars($cms['contact_number'] ?? '')) ?></a></li>
                                <li class="address"><a href="#"  style="<?= $styleWithoutFontSize ?>"><?= nl2br(htmlspecialchars($cms['contact_location'] ?? '')) ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="overlayer"></div>
    <div class="loader">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <script src="assets/js/jquery-3.4.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.animateNumber.min.js"></script>
    <script src="assets/js/jquery.waypoints.min.js"></script>
    <script src="assets/js/jquery.fancybox.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/moment.min.js"></script>

    <script src="assets/js/typed.js"></script>
    <script>
        $(function () {
            var slides = $('.slides'),
                images = slides.find('img');

            images.each(function (i) {
                $(this).attr('data-id', i + 1);
            })

            var typed = new Typed('.typed-words', {
                strings: [<?= json_encode($cms['animation_text'] ?? 'LandMap') ?>],
                typeSpeed: 80,
                backSpeed: 80,
                backDelay: 4000,
                startDelay: 1000,
                loop: true,
                showCursor: true,
                preStringTyped: (arrayPos, self) => {
                    arrayPos++;
                    console.log(arrayPos);
                    $('.slides img').removeClass('active');
                    $('.slides img[data-id="' + arrayPos + '"]').addClass('active');
                }

            });
        })
    </script>

    <script src="assets/js/custom.js"></script>

</body>
</html>