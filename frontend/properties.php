<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Map | Properties</title>
    <link rel="icon" href="../assets/images/logo.png" type="image/x-icon">

    <!--LINKS-->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Source+Serif+Pro:wght@400;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" href="../assets/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="../assets/fonts/icomoon/style.css">
    <link rel="stylesheet" href="../assets/fonts/flaticon/font/flaticon.css">
    <link rel="stylesheet" href="../assets/css/aos.css">
    <link rel="stylesheet" href="../assets/css/style.css">

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

    <?php require "../partials/nav_landing.php" ?>

    <div class="hero hero-inner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mx-auto text-center">
                    <div class="intro-wrap">
                        <h1 class="mb-0">Properties</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <?php
            require "config/dbconn.php";
            $propertiesSnapshot = $database->getReference('properties')->getSnapshot();
            $properties = $propertiesSnapshot->getValue();
            ?>
            <?php if (isset($properties) && !empty($properties)): ?>
                <div class="property-list">
                    <?php foreach ($properties as $property): ?>
                        <div class="container mt-4 mb-4">
                            <div class="property-card">
                                <div class="property-image">
                                    <?php if (!empty($property['images'])): ?>
                                        <img alt="Property image" height="300"
                                            src="assets/uploads/<?php echo $property['images'][0]; ?>" width="500" />
                                    <?php endif; ?>
                                    <div class="badge-new">NEW</div>
                                    <div class="image-overlay">
                                        <i class="fas fa-camera"></i> <?php echo count($property['images']); ?>
                                    </div>
                                </div>
                                <div class="property-details">
                                    <h3><?php echo htmlspecialchars($property['name']); ?></h3>
                                    <p><i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($property['location']); ?></p>
                                    <p class="description">
                                        <?php
                                        $maxLength = 250;
                                        $description = htmlspecialchars($property['description']);
                                        echo strlen($description) > $maxLength ? substr($description, 0, $maxLength) . '...' : $description;
                                        ?>
                                    </p>
                                    <div class="property-price">₱<?php echo number_format($property['price'], 2); ?>
                                    </div>
                                    <div class="property-meta">
                                        <span><i class="fas fa-bed"></i>
                                            <?php echo isset($property['amenities']['beds']) ? $property['amenities']['beds'] : 0; ?></span>
                                        <span><i class="fas fa-ruler-combined"></i>
                                            <?php echo htmlspecialchars($property['landArea']); ?> m²</span>
                                        <span><i class="fas fa-expand-arrows-alt"></i>
                                            <?php echo htmlspecialchars($property['floorArea']); ?> m²</span>
                                    </div>
                                    <div class="property-buttons">
                                        <a href="/viewproperty"><button class="btn btn-primary">View</button></a>

                                        <button class="btn btn-success">Chat</button>
                                        <button class="btn btn-warning">Contact info</button>
                                        <div class="property-agent">
                                            <img alt="Agent profile picture" height="40"
                                                src="https://storage.googleapis.com/a1aa/image/AHDaplfiAQyyPaSgBI50lHFgYCfOFjze4fvfS7oNbwfeniv2JA.jpg"
                                                width="40" />
                                            <div class="agent-info">
                                                <span>Agent Name</span>
                                                <span class="badge-semi-verified">SEMI VERIFIED</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No properties available.</p>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <img src="assets/images/img_1.jpg" class="card-img-top" alt="Property 2">
                <div class="card-body">
                    <h5 class="card-title">Modern Apartment</h5>
                    <p class="card-text">2 beds, 2 baths, close to downtown.</p>
                    <p class="card-text"><strong>Price: $250,000</strong></p>
                    <a href="#" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <img src="assets/images/img_1.jpg" class="card-img-top" alt="Property 2">
                <div class="card-body">
                    <h5 class="card-title">Modern Apartment</h5>
                    <p class="card-text">2 beds, 2 baths, close to downtown.</p>
                    <p class="card-text"><strong>Price: $250,000</strong></p>
                    <a href="#" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <img src="assets/images/img_1.jpg" class="card-img-top" alt="Property 2">
                <div class="card-body">
                    <h5 class="card-title">Modern Apartment</h5>
                    <p class="card-text">2 beds, 2 baths, close to downtown.</p>
                    <p class="card-text"><strong>Price: $250,000</strong></p>
                    <a href="#" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <img src="assets/images/img_1.jpg" class="card-img-top" alt="Property 2">
                <div class="card-body">
                    <h5 class="card-title">Modern Apartment</h5>
                    <p class="card-text">2 beds, 2 baths, close to downtown.</p>
                    <p class="card-text"><strong>Price: $250,000</strong></p>
                    <a href="#" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <img src="assets/images/img_1.jpg" class="card-img-top" alt="Property 2">
                <div class="card-body">
                    <h5 class="card-title">Modern Apartment</h5>
                    <p class="card-text">2 beds, 2 baths, close to downtown.</p>
                    <p class="card-text"><strong>Price: $250,000</strong></p>
                    <a href="#" class="btn btn-primary">View Details</a>
                </div>
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
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis nec nunc sit amet sapien
                                ornare
                                maximus nec eget neque. In at mauris at augue finibus iaculis. Donec interdum nisi ut ex
                                scelerisque eleifend. Morbi id aliquet arcu.</p>
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
                                <li class="email"><a href="#">company@example.com</a></li>
                                <li class="phone"><a href="#">+63 936 7876</a></li>
                                <li class="address"><a href="#">Makati, Philippines</a></li>
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

    <script src="../assets/js/jquery-3.4.1.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/jquery.animateNumber.min.js"></script>
    <script src="../assets/js/jquery.waypoints.min.js"></script>
    <script src="../assets/js/jquery.fancybox.min.js"></script>
    <script src="../assets/js/aos.js"></script>
    <script src="../assets/js/moment.min.js"></script>

    <script src="../assets/js/typed.js"></script>
    <script>
        $(function () {
            var slides = $('.slides'),
                images = slides.find('img');

            images.each(function (i) {
                $(this).attr('data-id', i + 1);
            })

            var typed = new Typed('.typed-words', {
                strings: ["John", " Doe", " Smith", " Jack", " Camelot"],
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

    <script src="../assets/js/custom.js"></script>

</body>

</html>