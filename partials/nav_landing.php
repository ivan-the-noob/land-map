<?php
    require 'db.php';
    $query = "SELECT * FROM cms LIMIT 1";
    $result = $conn->query($query);
    $cms = $result->fetch_assoc();
?>

<nav class="site-nav">
	<div class="container">
		<div class="site-navigation d-flex justify-content-between align-items-center">
			<a href="#" class="logo m-0">
				<img src="assets/images/cms/<?= htmlspecialchars($cms['logo'] ?? '') ?>" alt="LandMap Logo" class="logo-img"
					style="width: 4rem; border-radius: 50%; margin-right: 1rem">Land<span
					class="text-primary">Map</span>
			</a>

			<ul class="js-clone-nav d-none d-lg-inline-block text-left site-menu mx-auto site-auth-buttons d-none d-lg-block">
				<li class=""><a href="../landMapLatest">Home</a></li>
				<li class=""><a href="../landMapLatest/index_properties.php">Properties</a></li>
				<li class=""><a href="../landMap_V6/frontend/contact_us.php">Contact Us</a></li>
				<li class=""><a href="./frontend/sign_up.php" class="btn btn-primary signup-hover">Sign Up</a></li>
				<li class=""><a href="./frontend/sign_in.php" class="btn btn-light signin-hover">Sign In</a></li>
			</ul>

			<a href="#"
				class="burger ml-auto float-right site-menu-toggle js-menu-toggle d-inline-block d-lg-none light"
				data-toggle="collapse" data-target="#main-navbar">
				<span></span>
			</a>
		</div>
	</div>
</nav>