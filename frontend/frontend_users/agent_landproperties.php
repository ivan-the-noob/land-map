<?php
session_start();

// Initialize a variable to store error message for modal
$show_modal = false;
$error_message = '';

// Check if the user is logged in
if (!isset($_SESSION['role_type']) || !isset($_SESSION['user_id'])) {
    // If not logged in, set flag and message for modal
    $show_modal = true;
    $error_message = 'You must be logged in to access this page.';
}

// Check if the user is an actual 'user' (not admin, agent, etc.)
elseif ($_SESSION['role_type'] !== 'agent') {
    // If not user, set flag and message for modal
    $show_modal = true;
    $error_message = 'You do not have the necessary permissions to access this page.';
}

// Add the session user_id if it is missing
if (!isset($_SESSION['user_id']) && isset($user['user_id'])) {
    $_SESSION['user_id'] = $user['user_id'];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-90680653-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-90680653-2');
    </script>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Land Map | Land Properties</title>
    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <!-- vendor css -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

    <!-- Mapping Links -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmgygVeipMUsrtGeZPZ9UzXRmcVdheIqw&libraries=places"></script>
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />

    <!-- azia CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css">
    <style>
        .land-card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }

        .land-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }

        .map-container {
            height: 200px;
            margin: 10px 0;
        }

        .client-messages {
            max-height: 300px;
            overflow-y: auto;
        }
    </style>

    <style>
        .land-card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }

        .land-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }

        .map-container {
            height: 200px;
            margin: 10px 0;
        }

        .client-messages {
            max-height: 300px;
            overflow-y: auto;
        }

        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        .features-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 10px 0;
        }

        .feature-tag {
            background: #e9ecef;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
        }
    </style>

    <style>
        .new-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff4757;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }

        .property-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .property-timestamp {
            font-size: 0.9rem;
            color: #666;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 5px;
        }
                        
        .property-timestamp i {
            color: #999;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="az-header">
        <?php require '../../partials/nav_agent.php' ?>
    </div>

    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">View land Properties</h2>
                        <p class="az-dashboard-text">Find your dream land property</p>
                    </div>
                    <!-- Time and Date -->
                    <div class="az-content-header-right">
                        <div class="media">
                            <div class="media-body">
                                <label>Current Date</label>
                                <h6 id="current-date"></h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>Current Time</label>
                                <h6 id="current-time"></h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>Time Zone</label>
                                <h6>Philippine Time (PHT)</h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                    </div>
                    <script>
                        function updateDateTime() {
                            const now = new Date();
                            const dateOptions = { year: 'numeric', month: 'short', day: 'numeric' };
                            const timeOptions = { 
                                hour: '2-digit', 
                                minute: '2-digit', 
                                second: '2-digit', 
                                hour12: true,
                                timeZone: 'Asia/Manila'
                            };
                            
                            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', dateOptions);
                            document.getElementById('current-time').textContent = now.toLocaleTimeString('en-US', timeOptions);
                        }

                        updateDateTime();
                        setInterval(updateDateTime, 1000);
                    </script>
                    <!-- Time and Date footer -->
                </div>
                <div class="az-content az-content-dashboard">
                    <div class="container">
                        <div class="az-content-body">
            
                <!-- Add this before the Filters Section -->
                

                <!-- Filters Section -->
                <div class="filter-section">
                <div class="search-section mb-4">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search land properties by Name, Land Type, Location..." 
                            oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1)">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" onclick="handleSearch()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <h5>Filters</h5>
                    <div class="filter-row">
                        <?php
                        $selectedSaleType = isset($_GET['saleTypeFilter']) ? $_GET['saleTypeFilter'] : 'all';
                        $selectedLandType = isset($_GET['landTypeFilter']) ? $_GET['landTypeFilter'] : 'all';
                        $selectedLocation = isset($_GET['propertyLocation']) ? $_GET['propertyLocation'] : 'all';
                        $minPrice = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
                        $maxPrice = isset($_GET['max_price']) ? intval($_GET['max_price']) : 1000000000;
                        ?>

                        <div class="filter-item">
                            <label for="saleTypeFilter">Sale Type:</label>
                            <select id="saleTypeFilter" class="form-control">
                                <option value="all" <?= ($selectedSaleType == 'all') ? 'selected' : '' ?>>All Types</option>
                                <option value="For Sale" <?= ($selectedSaleType == 'For Sale') ? 'selected' : '' ?>>For Sale</option>
                                <option value="For Lease" <?= ($selectedSaleType == 'For Lease') ? 'selected' : '' ?>>For Lease</option>
                            </select>
                        </div>
                        <div class="filter-item lease-options" style="display:none;">
                            <label for="leaseTermFilter">Lease Term:</label>
                            <select id="leaseTermFilter" class="form-control">
                                <option value="all">All Terms</option>
                                <option value="Short Term">Short Term (Less than 1 year)</option>
                                <option value="Long Term">Long Term (More than 1 year)</option>
                            </select>
                        </div>
                        <div class="filter-item lease-options" style="display:none;">
                            <label for="monthlyRental">Monthly Rental Cost:</label>
                            <div class="input-group">
                            <input type="number" id="monthlyRentalMin" class="form-control" placeholder="Min">
                                <div class="input-group-text">to</div>
                                <input type="number" id="monthlyRentalMax" class="form-control" placeholder="Max">
                            </div>
                        </div>
                        <div class="filter-item sale-options" style="display:none;">
                            <label for="landCondition">Land Condition:</label>
                            <select id="landCondition" class="form-control">
                                <option value="all">All Terms</option>
                                <option value="resale">Resale</option>
                                <option value="foreClose">Foreclose/Acquired Assets</option>
                                <option value="pasalo">Pasalo/Assumed Balance</option>
                            </select>
                        </div>
                        <div class="filter-item sale-options" style="display:none;">
                            <label for="landContract">Land Contract Price:</label>
                            <div class="input-group">
                                <input type="number" id="minPrice" class="form-control" placeholder="Min">
                                <div class="input-group-text">to</div>
                                <input type="number" id="maxPrice" class="form-control" placeholder="Max">
                            </div>
                        </div>
                        <script>
                            document.getElementById('saleTypeFilter').addEventListener('change', function() {
                                const saleOptions = document.querySelectorAll('.sale-options');
                                const leaseOptions = document.querySelectorAll('.lease-options');

                                if (this.value === 'For Sale') {
                                    saleOptions.forEach(option => option.style.display = 'block');
                                    leaseOptions.forEach(option => option.style.display = 'none');
                                } else if (this.value === 'For Lease') {
                                    leaseOptions.forEach(option => option.style.display = 'block');
                                    saleOptions.forEach(option => option.style.display = 'none');
                                } else {
                                    saleOptions.forEach(option => option.style.display = 'none');
                                    leaseOptions.forEach(option => option.style.display = 'none');
                                }
                            });
                        </script>


                     
                        <div class="filter-item">
                            <label for="landTypeFilter">Land Type:</label>
                            <select id="landTypeFilter" class="form-control">
                                <option value="all" <?= ($selectedLandType == 'all') ? 'selected' : '' ?>>All Types</option>
                                <option value="House and Lot" <?= ($selectedLandType == 'House and Lot') ? 'selected' : '' ?>>House and Lot</option>
                                <option value="Agricultural Farm" <?= ($selectedLandType == 'Agricultural Farm') ? 'selected' : '' ?>>Agricultural Farm</option>
                                <option value="Commercial Lot" <?= ($selectedLandType == 'Commercial Lot') ? 'selected' : '' ?>>Commercial Lot</option>
                                <option value="Raw Land" <?= ($selectedLandType == 'Raw Land') ? 'selected' : '' ?>>Raw Land</option>
                                <option value="Residential Land" <?= ($selectedLandType == 'Residential Land') ? 'selected' : '' ?>>Residential Land</option>
                                <option value="Residential Farm" <?= ($selectedLandType == 'Residential Farm') ? 'selected' : '' ?>>Residential Farm</option>
                                <option value="Memorial Lot" <?= ($selectedLandType == 'Memorial Lot') ? 'selected' : '' ?>>Memorial Lot</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label for="propertyLocation">Location:</label>
                            <select name="propertyLocation" class="form-control" id="propertyLocation">
                                <?php include '../../backend/filter_place.php'; ?>  
                                <option value="Bacoor, Cavite" <?= ($selectedLocation == 'Bacoor, Cavite') ? 'selected' : '' ?>>Bacoor</option>            
                            </select>
                        </div>
                       
                      
                        
                    </div>
                    <div class="filter-row">
                    <div class="filter-item">
                            <label for="areaRange">Area (sqm):</label>
                            <div class="input-group">
                                <input type="number" id="minArea" class="form-control" placeholder="Min">
                                <div class="input-group-text">to</div>
                                <input type="number" id="maxArea" class="form-control" placeholder="Max">
                            </div>
                        </div>
                        <div class="filter-item">
                            <label>Additional Info:</label>
                            <div class="features-list">
                                <label class="feature-tag"><input type="checkbox" name="additionalInfo" value="cleanTitle"> Clean Title</label>
                                <label class="feature-tag"><input type="checkbox" name="additionalInfo" value="DisPromo"> Discounted/Promo</label>
                                <label class="feature-tag"><input type="checkbox" name="additionalInfo" value="pagibig"> Pag-IBIG Accredited</label>
                                <label class="feature-tag"><input type="checkbox" name="additionalInfo" value="fsbo"> For Sale by Owner</label>
                                <small class="text-muted">Additional Info will be displayed in the land details</small>
                            </div>
                        </div>
                       
                    </div>
                    <button id="applyFilters" class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                    <button id="resetFilters" class="btn btn-secondary" onclick="resetFilters()">Reset</button>
                </div>

                <!-- Land Listings Grid -->
                <div class="row" id="landListings">
                    <!-- Content will be dynamically populated -->
                </div>
            </div>
        </div>
    </div>
   
    <script>
   function applyFilters() {
        console.log("Filters applied!");
    }

    function resetFilters() {
        // Reset all filters
        document.getElementById("saleTypeFilter").value = "all";
        document.getElementById("leaseTermFilter").value = "all";
        document.getElementById("monthlyRentalMin").value = "";
        document.getElementById("monthlyRentalMax").value = "";
        document.getElementById("landTypeFilter").value = "all";
        document.getElementById("propertyLocation").value = "all";
        document.getElementById("landCondition").value = "all";
        document.getElementById("minPrice").value = "";
        document.getElementById("maxPrice").value = "";
        document.getElementById("minArea").value = "";
        document.getElementById("maxArea").value = "";

        // Uncheck all additional info checkboxes
        document.querySelectorAll("input[name='additionalInfo']").forEach(cb => cb.checked = false);

        // Show all property cards
        document.querySelectorAll(".property-card").forEach(card => card.style.display = "block");

        // Remove URL parameters without reloading
        const newUrl = window.location.pathname;
        history.replaceState(null, "", newUrl);
    }

    document.getElementById("resetFilters").addEventListener("click", resetFilters);

    document.addEventListener("DOMContentLoaded", function () {
        const params = new URLSearchParams(window.location.search);

        if (params.has("saleTypeFilter")) {
            document.getElementById("saleTypeFilter").value = params.get("saleTypeFilter");
        }

        if (params.has("landTypeFilter")) {
            document.getElementById("landTypeFilter").value = params.get("landTypeFilter");
        }

        if (params.has("propertyLocation")) {
            document.getElementById("propertyLocation").value = params.get("propertyLocation");
        }

        if (params.has("min_price")) {
            document.getElementById("minPrice").value = params.get("min_price");
        }

        if (params.has("max_price")) {
            document.getElementById("maxPrice").value = params.get("max_price");
        }

        if (params.has("monthly_rental_min")) {
            document.getElementById("monthlyRentalMin").value = params.get("monthly_rental_min");
        }

        if (params.has("monthly_rental_max")) {
            document.getElementById("monthlyRentalMax").value = params.get("monthly_rental_max");
        }

        if (
            params.has("saleTypeFilter") || 
            params.has("landTypeFilter") || 
            params.has("propertyLocation") || 
            params.has("min_price") || 
            params.has("max_price") ||
            params.has("monthly_rental_min") ||
            params.has("monthly_rental_max")
        ) {
            document.getElementById("applyFilters").click();
        }
    });

    function applyFilters() {
        let selectedSaleType = document.getElementById("saleTypeFilter").value;
        let selectedLeaseTerm = document.getElementById("leaseTermFilter").value;
        let minMonthlyRent = document.getElementById("monthlyRentalMin").value.trim();
        let maxMonthlyRent = document.getElementById("monthlyRentalMax").value.trim();
        let selectedLandType = document.getElementById("landTypeFilter").value;
        let selectedLocation = document.getElementById("propertyLocation").value;
        let selectedLandCondition = document.getElementById("landCondition").value;
        let minPrice = document.getElementById("minPrice").value.trim();
        let maxPrice = document.getElementById("maxPrice").value.trim();
        let minArea = document.getElementById("minArea").value.trim();
        let maxArea = document.getElementById("maxArea").value.trim();

        let selectedAdditionalInfo = Array.from(document.querySelectorAll("input[name='additionalInfo']:checked")).map(cb => cb.value);

        let cards = document.querySelectorAll(".property-card");

        cards.forEach(card => {
            let saleType = card.getAttribute("data-sale-type");
            let leaseTerm = card.getAttribute("data-lease-term");
            let monthlyRent = parseFloat(card.getAttribute("data-monthly-rent")) || 0;
            let landType = card.getAttribute("data-land-type");
            let propertyLocation = card.getAttribute("data-location");
            let landCondition = card.getAttribute("data-land-condition");
            let salePrice = parseFloat(card.getAttribute("data-sale-price")) || 0;
            let landArea = parseFloat(card.getAttribute("data-land-area")) || 0;
            let additionalInfo = card.getAttribute("data-another-info") ? card.getAttribute("data-another-info").split(",") : [];

            card.style.display = "none";

            if (
                selectedSaleType === "all" && 
                selectedLeaseTerm === "all" && 
                minMonthlyRent === "" && maxMonthlyRent === "" &&
                selectedLandType === "all" && 
                selectedLocation === "all" && 
                selectedLandCondition === "all" &&
                minPrice === "" && maxPrice === "" &&
                minArea === "" && maxArea === "" &&
                selectedAdditionalInfo.length === 0
            ) {
                card.style.display = "block";
                return;
            }

            if (selectedSaleType !== "all" && saleType !== selectedSaleType) {
                return;
            }

            if (selectedSaleType === "For Lease" && selectedLeaseTerm !== "all" && leaseTerm !== selectedLeaseTerm) {
                return;
            }

            if (minMonthlyRent !== "" && monthlyRent < parseFloat(minMonthlyRent)) {
                return;
            }

            if (maxMonthlyRent !== "" && monthlyRent > parseFloat(maxMonthlyRent)) {
                return;
            }

            if (selectedLandType !== "all" && landType !== selectedLandType) {
                return;
            }

            if (selectedLocation !== "all" && propertyLocation !== selectedLocation) {
                return;
            }

            if (selectedSaleType === "For Sale" && selectedLandCondition !== "all" && landCondition !== selectedLandCondition) {
                return;
            }

            if (selectedSaleType === "For Sale") {
                if (minPrice !== "" && salePrice < parseFloat(minPrice)) {
                    return;
                }
                if (maxPrice !== "" && salePrice > parseFloat(maxPrice)) {
                    return;
                }
            } else if (selectedSaleType === "For Lease") {
                if (minPrice !== "" && monthlyRent < parseFloat(minPrice)) {
                    return;
                }
                if (maxPrice !== "" && monthlyRent > parseFloat(maxPrice)) {
                    return;
                }
            }

            if (minArea !== "" && landArea < parseFloat(minArea)) {
                return;
            }

            if (maxArea !== "" && landArea > parseFloat(maxArea)) {
                return;
            }

            if (selectedAdditionalInfo.length > 0) {
                let matches = selectedAdditionalInfo.some(info => additionalInfo.includes(info));
                if (!matches) {
                    return;
                }
            }

            card.style.display = "block";
        });
    }

    document.getElementById("applyFilters").addEventListener("click", applyFilters);
</script>


    
    

                <div class="tab-content mt-4">
                    <div id="dashboard" class="tab-pane active">
                        <div id="dashboard" class="tab-pane">
                            <!-- Post new land property -->
                            <h3 class="mb-1 mr-5" id="land_property">All Land Properties</h3>
                            <div class="property-list">
                                
    <?php
    require '../../db.php';

    $sql = "SELECT p.*, 
            u.fname, u.lname,
            ui.image_name as user_image,
            (SELECT image_name FROM property_images WHERE property_id = p.property_id LIMIT 1) AS property_image,
            DATE_FORMAT(p.created_at, '%M %d, %Y') as formatted_date,
            DATE_FORMAT(p.created_at, '%h:%i %p') as formatted_time,
            DATEDIFF(CURRENT_DATE, p.created_at) as days_since_added
            FROM properties p 
            LEFT JOIN users u ON p.user_id = u.user_id
            LEFT JOIN user_img ui ON u.user_id = ui.user_id
            WHERE p.is_archive = 0
            ORDER BY p.property_id DESC";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagePath = $row['property_image'] ? "../../assets/property_images/" . $row['property_image'] : "../../assets/images/default-property.jpg";
            $agentName = $row['fname'] . ' ' . $row['lname'];
            
            $createdDate = strtotime($row['created_at']);
            $currentDate = strtotime('now');
            $daysDifference = floor(($currentDate - $createdDate) / (60 * 60 * 24));
            $isNew = ($row['days_since_added'] <= 7);
    ?>
          <div class="property-card" 
        data-sale-type="<?php echo ($row['sale_or_lease'] == 'sale') ? 'For Sale' : 'For Lease'; ?>" 
        data-lease-term="<?php echo ($row['lease_duration'] == 'short_term') ? 'Short Term' : 'Long Term'; ?>"
        data-monthly-rent="<?php echo $row['monthly_rent']; ?>"
        data-land-type="<?php echo htmlspecialchars($row['property_type']); ?>" 
        data-location="<?php echo htmlspecialchars($row['property_location']); ?>"
        data-sale-price="<?php echo htmlspecialchars($row['sale_price']); ?>"
        data-land-condition="<?php echo htmlspecialchars($row['land_condition']); ?>"
        data-land-area="<?php echo htmlspecialchars($row['land_area']); ?>"
        data-another-info="<?php echo htmlspecialchars($row['another_info']); ?>">






                <div class="property-image">
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['property_name']); ?>">
                    <div class="sale-badge">
                    <?php echo $row['sale_or_lease'] == 'sale' ? 'FOR SALE' : 'FOR LEASE'; ?>
                    </div>
                    <?php if ($isNew) { ?>
                        <div class="new-badge" data-created="<?php echo $row['created_at']; ?>">NEW</div>
                    <?php } ?>
                    <div class="location-badge">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['property_location']); ?>
                    </div>
                </div>

                <div class="property-content">
                    <div class="property-header">
                        <h3 class="property-title">Property Name: <?php echo htmlspecialchars($row['property_name']); ?></h3>
                    </div>
                    
                    <?php if ($row['sale_or_lease'] == 'sale' && $row['sale_price'] > 0) { ?>
                        <div class="property-price">₱<?php echo number_format($row['sale_price'], 2); ?>/contract price</div>
                    <?php } elseif ($row['sale_or_lease'] == 'lease' && $row['monthly_rent'] > 0) { ?>
                        <div class="property-price">₱<?php echo number_format($row['monthly_rent'], 2); ?>/monthly cost</div>
                    <?php } ?>

                    <div class="property-details">
                        <?php if ($row['land_area']) { ?>
                            <span><i class="fas fa-ruler-combined"> Land Area:</i> <?php echo number_format($row['land_area']); ?> sqm</span>
                        <?php } ?>
                        <?php if ($row['property_type']) { ?>
                            <span><i class="fas fa-home"> Land Type:</i> <?php echo htmlspecialchars($row['property_type']); ?></span>
                        <?php } ?>
                        <?php if (!empty($row['sale_or_lease'])) { ?>
                            <span><i class="fas fa-tag">Lease Type </i>
                                <?php 
                                    echo $row['sale_or_lease'] === 'sale' ? 'For Sale' : ($row['sale_or_lease'] === 'lease' ? 'For Lease' : htmlspecialchars($row['sale_or_lease'])); 
                                ?>
                            </span>
                        <?php } ?>
                        <?php if ($row['sale_or_lease'] === 'lease' && !empty($row['lease_duration'])) { 
                            $lease_label = ($row['lease_duration'] === 'short_term') ? 'Short Term' : 'Long Term';
                        ?>
                            <span><i class="fas fa-file-contract">Lease Term: </i> <?php echo $lease_label; ?></span>
                        <?php } ?>

                        <?php if ($row['sale_or_lease'] === 'lease' && !empty($row['monthly_rent'])) { ?>
                            <span class="d-none"><i class="fas fa-money-bill-wave"></i> Monthly Rent: <?php echo $row['monthly_rent']; ?></span>
                        <?php } ?>
                        <?php if ($row['sale_or_lease'] === 'sale' && !empty($row['land_condition'])) { ?>
                            <span><i class="fas fa-check-circle">Land Condition</i> <?php echo $row['land_condition']; ?></span>
                        <?php } ?>
                        <?php if (!empty($row['property_type'])) { ?>
                            <span class="d-none"><i class="fas fa-map-marker-alt">Property Location</i> <?php echo $row['property_type']; ?></span>
                        <?php } ?>
                        <?php if (!empty($row['property_location'])) { ?>
                            <span class="d-none"><i class="fas fa-map-marker-alt">Property Location</i> <?php echo $row['property_location']; ?></span>
                        <?php } ?>

                       
                    </div>

                    <?php if ($row['property_description']) { ?>
                        <div class="property-description"><i class="fas fa-land"> Land Description:</i>
                            <?php echo substr(htmlspecialchars($row['property_description']), 0, 100) . '...'; ?>
                        </div>
                    <?php } ?>

                    <?php if ($row['another_info']) { ?><i class="fas fa-land"> Another Information:</i>
                        <div class="promo-badge">
                            <?php echo ucfirst($row['another_info']); ?>
                        </div>
                    <?php } ?>

                    <div class="property-actions">
                    <?php if ($row['formatted_date'] && $row['formatted_time']) { ?>
                        <div class="property-timestamp">
                            <i class="fas fa-clock"></i>
                            <span>Added on <?php echo htmlspecialchars($row['formatted_date']); ?> at <?php echo htmlspecialchars($row['formatted_time']); ?></span>
                        </div>
                    <?php } ?>
                    </div>

                    <div class="property-actions">
                        <button class="btn btn-primary btn-sm w-75 d-flex mx-auto justify-content-center align-items-center" onclick="viewDetails(<?php echo $row['property_id']; ?>)">
                            <i class="fas fa-eye"></i> View
                        </button>
                        
                       


                        
                         

                        <!-- Add to List Modal -->
                        <div class="modal fade" id="addToListModal" tabindex="-1" aria-labelledby="addToListModalLabel">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addToListModalLabel">Confirm Listing</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to list this property?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                        <button type="button" class="btn btn-success" id="confirmAddToListBtn">Yes, List</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script>
                            let selectedPropertyId = null;

                            function openAddToListModal(propertyId) {
                                selectedPropertyId = propertyId;
                                $('#addToListModal').modal('show');
                            }

                            $(document).ready(function () {
                                $('#confirmAddToListBtn').on('click', function () {
                                    if (selectedPropertyId) {
                                        $.ajax({
                                            url: '../../backend/update_add_list.php',
                                            type: 'POST',
                                            data: { property_id: selectedPropertyId },
                                            success: function (response) {
                                                console.log("Server Response:", response); 
                                                if (response.trim() === "success") {
                                                    alert("Property listed successfully!");
                                                    location.reload(); 
                                                } else {
                                                    alert("Failed to list property: " + response); 
                                                }
                                            },

                                            error: function () {
                                                alert("An error occurred.");
                                            }
                                        });
                                        $('#addToListModal').modal('hide');
                                    }
                                });
                            });
                        </script>


                    </div>
                    <hr>
                    <strong>Agent:</strong> <?php echo htmlspecialchars($agentName); ?>
                       

                  
                </div>
            </div>
    <?php
        }
    } else {
        echo '<div class="no-properties">
                <i class="fas fa-home"></i>
                <p>No properties found</p>
              </div>';
    }
    ?>
</div>

<style>
.property-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
    gap: 20px;
    padding: 20px;
}

.property-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.property-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.property-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sale-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #4CAF50;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.location-badge {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
}

.property-content {
    padding: 15px;
}

.property-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.property-price {
    font-size: 20px;
    font-weight: bold;
    color: #4CAF50;
    margin-bottom: 10px;
}

.property-details {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
    color: black;
    font-size: 14px;
}

.property-details span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.property-description {
    font-size: 14px;
    color: black;
    margin-bottom: 15px;
}

.property-condition {
    display: inline-block;
    background: #FFC107;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    margin-bottom: 15px;
}

.promo-badge {
    display: inline-block;
    background: #FFC107;
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    margin-bottom: 15px;
}

.property-actions, .admin-actions {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.admin-actions {
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.btn-view, .btn-contact, .btn-submit, .btn-update, .btn-delete {
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.btn-view {
    background: #4CAF50;
    color: white;
}

.btn-contact {
    background: white;
    color: #4CAF50;
    border: 1px solid #4CAF50;
}

.btn-submit {
    background: #2196F3;
    color: white;
}

.btn-update {
    background: #FFC107;
    color: white;
}

.btn-delete {
    background: #f44336;
    color: white;
}

.btn-view:hover, .btn-submit:hover, .btn-update:hover, .btn-delete:hover {
    opacity: 0.9;
}

.btn-contact:hover {
    background: #4CAF50;
    color: white;
}

.agent-info {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #666;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.agent-info img {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}

@media (max-width: 768px) {
    .property-list {
        grid-template-columns: 1fr;
    }
    
    .property-card {
        margin: 10px;
    }

    .property-actions, .admin-actions {
        flex-direction: column;
    }
}

/* Add these styles to your existing CSS */
.restrict-controls {
    display: flex;
    gap: 10px;
    align-items: center;
}

.restrict-days {
    width: 80px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn-archive {
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 5px;
    background: #6c757d;
    color: white;
    transition: all 0.3s ease;
}

.btn-archive:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.floating-message {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #28a745;
    color: white;
    padding: 12px 20px;
    border-radius: 5px;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.3s ease, transform 0.3s ease;
    z-index: 1000;
}
.floating-message.show {
    opacity: 1;
    transform: translateY(0);
}

</style>

<script>

let propertyIdToInquire = null;

function openInquireModal(propertyId) {
    propertyIdToInquire = propertyId;
    $('#inquireModal').modal('show'); 
}

document.getElementById("confirmInquireBtn").addEventListener("click", function() {
    if (propertyIdToInquire) {
        $('#inquireModal').modal('hide'); 

        fetch("../../backend/inquire.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "property_id=" + propertyIdToInquire
        })
        .then(response => response.json())
        .then(data => {
            showFloatingMessage(data.message, data.status === 'success' ? 'success' : 'error');
        })
        .catch(error => console.error("Error:", error));
    }
});

function showFloatingMessage(message, type = 'success') {
    let floatingMessage = document.getElementById('floatingMessage');
    
    floatingMessage.innerText = message;
    floatingMessage.style.backgroundColor = (type === 'error') ? '#dc3545' : '#28a745';

    floatingMessage.classList.add('show');

    setTimeout(() => {
        floatingMessage.classList.remove('show');
    }, 3000);
}



function submitProperty(propertyId) {
    if(confirm('Are you sure you want to submit this property?')) {
        // Add your submit logic here
        console.log('Submitting property:', propertyId);
    }
}

function updateProperty(propertyId) {
    window.location.href = 'edit_property.php?id=' + propertyId;
}

function deleteProperty(propertyId) {
    if(confirm('Are you sure you want to delete this property?')) {
        // Add your delete logic here
        console.log('Deleting property:', propertyId);
    }
}

function viewDetails(propertyId) {
    fetch(`../../backend/get_property_details.php?property_id=${propertyId}`)
        .then(response => response.json())
        .then(data => {
            // Populate modal with property details
            document.getElementById('modalPropertyName').textContent = data.property_name || '';
            document.getElementById('modalPropertyType').textContent = data.property_type || '';
            document.getElementById('modalSaleType').textContent = data.sale_or_lease ? data.sale_or_lease.toUpperCase() : '';
            document.getElementById('modalLocation').textContent = data.property_location || '';
            document.getElementById('modalLandArea').textContent = data.land_area || '';
            document.getElementById('modalPrice').textContent = data.price || '';
            document.getElementById('modalLandCondition').textContent = data.land_condition || '';
            document.getElementById('modalLeaseDuration').textContent = data.lease_duration || '';
            document.getElementById('modalMonthlyRent').textContent = data.monthly_rent || '';
            document.getElementById('modalAgentName').textContent = (data.agent_fname ? data.agent_fname + ' ' + data.agent_lname : '');
            document.getElementById('modalDescription').textContent = data.property_description || '';

            


            // Set price based on sale or lease type
            const price = data.sale_or_lease === 'sale' 
                ? `₱${Number(data.sale_price).toLocaleString()}`
                : `₱${Number(data.monthly_rent).toLocaleString()}/month`;
            document.getElementById('modalPrice').textContent = price;

            // Populate features
            const featuresContainer = document.getElementById('modalFeatures');
            featuresContainer.innerHTML = '';
            if (data.features) {
                const features = data.features.split(',');
                features.forEach(feature => {
                    const featureElement = document.createElement('div');
                    featureElement.className = 'feature-item';
                    featureElement.innerHTML = `<i class="fas fa-check"></i> ${feature.trim()}`;
                    featuresContainer.appendChild(featureElement);
                });
            }

            // Populate image carousel
            const carouselInner = document.querySelector('.carousel-inner');
            carouselInner.innerHTML = '';
            if (data.images && data.images.length > 0) {
                data.images.forEach((image, index) => {
                    const carouselItem = document.createElement('div');
                    carouselItem.className = `carousel-item ${index === 0 ? 'active' : ''}`;
                    carouselItem.innerHTML = `<img src="../../assets/property_images/${image}" class="d-block w-100" alt="Property Image">`;
                    carouselInner.appendChild(carouselItem);
                });
            }

            // **Initialize Google Map**
            if (data.latitude && data.longitude) {
                initGoogleMap(data.latitude, data.longitude);
            }

            // Set agent information
            document.getElementById('modalAgentName').textContent = 
                data.agent_fname && data.agent_lname ? `${data.agent_fname} ${data.agent_lname}` : 'Unknown Agent';

                console.log("Agent Image:", data.agent_image);
                document.getElementById('modalAgentImage').src = data.agent_image 
                    ? `../../assets/profile_images/${data.agent_image}`
                    : '../../assets/images/default-profile.jpg';

            // Get the actions container
            const actionsContainer = document.getElementById('agentActions');
            actionsContainer.innerHTML = '';

            // Check if the property belongs to the logged-in user
            if (data.user_id == <?php echo $_SESSION['user_id']; ?>) {
                actionsContainer.innerHTML = `
                    <button class="btn btn-warning btn-sm mr-2" onclick="editProperty(${propertyId})">
                        <i class="fas fa-edit"></i> Edit Property
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="archiveProperty(${propertyId})">
                        <i class="fas fa-archive"></i> Archive Property
                    </button>
                `;
            } else {
                actionsContainer.innerHTML = `
                    
                `;
            }

            // Show the modal
            $('#propertyDetailsModal').modal('show');
        })
        .catch(error => {
            console.error('Error fetching property details:', error);
            alert('Error loading property details. Please try again.');
        });
}


// **Google Maps Initialization**
function initGoogleMap(lat, lng) {
    const map = new google.maps.Map(document.getElementById('modalMap'), {
        center: { lat: parseFloat(lat), lng: parseFloat(lng) },
       zoom: 15,
        mapTypeId: google.maps.MapTypeId.SATELLITE,
        mapTypeControl: true, // Ensures map type options are visible
        zoomControl: true, // Ensures zoom buttons appear
        streetViewControl: true, // Ensures the walk icon is available
        fullscreenControl: true, // Ensures fullscreen button appears
        
    });

    new google.maps.Marker({
        position: { lat: parseFloat(lat), lng: parseFloat(lng) },
        map: map,
        title: 'Property Location',
        icon: {
            url: '../../assets/images/land.png', // Replace with your custom marker image path
            scaledSize: new google.maps.Size(32, 32), // Adjust size of the marker
            origin: new google.maps.Point(0, 0), // Origin point of the image
            anchor: new google.maps.Point(16, 32) // Anchor point for the marker
        }
    });
}



function contactAgent(userId) {
    // Add your contact agent logic here
    console.log('Contacting agent:', userId);
}

// Function to update the current time every second
function updateTime() {
    const timeElement = document.getElementById('current-time');
    const now = new Date().toLocaleString("en-US", {
        timeZone: "Asia/Manila",
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    timeElement.textContent = now;
}

// Update time immediately and then every second
updateTime();
setInterval(updateTime, 1000);
</script>



                    <!-- Add the floating button -->
                    <button id="mapButton" class="floating-map-btn" onclick="toggleMap()">
                        <i class="fas fa-map-marker-alt"></i> <!-- Map button icon -->
                    </button>

                    <!-- Add the map panel -->
                    <div id="mapPanel" class="map-panel" style="margin-top: 65px;">
                        <div class="map-controls" style="margin-top: 50px;">
                        <button class="map-control-btn" onclick="toggleFullscreen()">
                            <i class="fas fa-expand"></i> 
                            
                            <button class="map-control-btn" onclick="toggleMap()">
                                <i class="fas fa-times"></i> <!-- Close map button icon -->
                            </button>
                        </div>
                        <div id="agentPropertyMaps" style="height: 100vh;"></div> <!-- Map container -->
                    </div>

                    <style>
                        .floating-map-btn {
                            position: fixed; /* Fixed position */
                            bottom: 30px; /* Bottom position */
                            right: 30px; /* Right position */
                            background: rgba(255, 255, 255, 0.9); /* Background color */
                            color: #666; /* Text color */
                            border: 1px solid #ddd; /* Border */
                            border-radius: 50%; /* Circular button */
                            width: 45px; /* Button width */
                            height: 45px; /* Button height */
                            display: flex; /* Flex layout */
                            align-items: center; /* Align items to center */
                            justify-content: center; /* Center content */
                            cursor: pointer; /* Pointer cursor */
                            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Shadow effect */
                            z-index: 1000; /* Z-index */
                            transition: all 0.3s ease; /* Transition effect */
                        }

                        .map-panel {
                            position: fixed; /* Fixed position */
                            top: 0; /* Top position */
                            right: -50%; /* Off-screen initially */
                            width: 50%; /* Width */
                            height: 100vh; /* Full height */
                            background: white; /* Background color */
                            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1); /* Shadow effect */
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* Transition effect */
                            z-index: 999; /* Z-index */
                        }

                        .map-panel.active {
                            right: 0; /* Slide in */
                        }

                        .map-panel.fullscreen {
                            width: 100% !important; /* Fullscreen width */
                            height: 100vh !important; /* Fullscreen height */
                            right: 0; /* Slide in */
                            top: 0; /* Top position */
                            z-index: 1001; /* Z-index */
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* Transition effect */
                        }

                        .map-controls {
                            position: absolute; /* Absolute position */
                            top: 10px; /* Top position */
                            left: 10px; /* Left position */
                            display: flex; /* Flex layout */
                            gap: 10px; /* Gap between items */
                            z-index: 1002; /* Z-index */
                        }

                        .map-control-btn {
                            background: white; /* Background color */
                            border: none; /* No border */
                            border-radius: 4px; /* Rounded corners */
                            width: 32px; /* Button width */
                            height: 32px; /* Button height */
                            display: flex; /* Flex layout */
                            align-items: center; /* Align items to center */
                            justify-content: center; /* Center content */
                            cursor: pointer; /* Pointer cursor */
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Shadow effect */
                            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); /* Transition effect */
                        }

                        .map-control-btn:hover {
                            background: #f5f5f5; /* Background color on hover */
                            transform: translateY(-2px); /* Hover effect */
                            box-shadow: 0 3px 6px rgba(0,0,0,0.15); /* Shadow effect on hover */
                        }

                        .map-control-btn i {
                            transition: transform 0.3s ease; /* Transition effect for icon */
                        }

                        .map-control-btn:active i {
                            transform: scale(0.9); /* Scale down on click */
                        }

                        /* Add animation for fullscreen icon */
                        .fa-expand, .fa-compress {
                            transition: transform 0.3s ease;
                        }

                        .fullscreen .fa-expand {
                            transform: rotate(180deg); 
                        }

                        .property-list {
                            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                            width: 100%; 
                        }

                        .property-list.map-active {
                            width: 50%; 
                        }

                        .property-list.fullscreen-active {
                            opacity: 0; 
                            transform: scale(0.95);
                            display: none; 
                            transition: opacity 0.3s ease, transform 0.3s ease;
                        }

                        @media (max-width: 768px) {
                            .map-panel {
                                width: 100%;
                                right: -100%; 
                            }

                            .property-list.map-active {
                                width: 0; 
                                overflow: hidden; 
                            }
                        }

                        /* Hides the InfoWindow close button */
                        .gm-ui-hover-effect {
                            display: none !important;
                        }

                    </style>


                    <script>
                       window.map = null;
                        window.allowedBounds = null;
                        let infoWindows = [];
                        let showInfo = true;

                        function initMap() {
    const caviteCenter = { lat: 14.2794, lng: 120.8786 };

    window.allowedBounds = new google.maps.LatLngBounds(
        { lat: 14.1325, lng: 120.6750 },
        { lat: 14.5050, lng: 121.0000 }
    );

    window.map = new google.maps.Map(document.getElementById("agentPropertyMaps"), { 
        center: caviteCenter,
        zoom: 10,
        mapTypeId: google.maps.MapTypeId.SATELLITE,
        restriction: {
            latLngBounds: window.allowedBounds,
            strictBounds: true
        },
        mapTypeControl: true
    });

    const streetView = window.map.getStreetView();

    fetch('../../backend/get_properties.php')
    .then(response => response.json())
    .then(properties => {
        if (!Array.isArray(properties)) {
            console.error("Invalid data format:", properties);
            return;
        }

        properties.forEach(property => {
            const { latitude, longitude, property_name, property_type, sale_price, sale_or_lease, image_name, property_location, land_area } = property;

            if (!latitude || !longitude || isNaN(latitude) || isNaN(longitude)) {
                console.warn(`Skipping property: ${property_name} (Invalid coordinates)`);
                return;
            }

            const propertyLocation = new google.maps.LatLng(parseFloat(latitude), parseFloat(longitude));

            if (!window.allowedBounds.contains(propertyLocation)) {
                console.warn(`Skipping property: ${property_name} (Out of Cavite bounds)`);
                return;
            }

            let statusText = sale_or_lease ? (sale_or_lease.toLowerCase() === 'lease' ? 'For Lease' :
                                            sale_or_lease.toLowerCase() === 'sale' ? 'For Sale' : 'N/A') : 'N/A';

            let imageUrl = image_name ? `../../assets/property_images/${image_name}` : 'https://via.placeholder.com/150';

            const homeIcon = {
                url: "../../assets/images/land.png", 
                scaledSize: new google.maps.Size(40, 40), 
                origin: new google.maps.Point(0, 0), 
                anchor: new google.maps.Point(20, 40) 
            };

            const marker = new google.maps.Marker({
                position: propertyLocation,
                map: window.map,
                title: property_name,
                icon: homeIcon
            });

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="white-space: nowrap; text-align: center;">
                        <img src="${imageUrl}" alt="${property_name}" style="width: 100%; height: 100px; object-fit: cover; border-radius: 5px;"><br>
                        <strong>${property_name}</strong><br>
                        <b>Location:</b> ${property_location || 'N/A'}<br>
                        <b>Type:</b> ${property_type || 'N/A'}<br>
                        <b>Status:</b> ${statusText}<br>
                        <div class="d-flex justify-content-center mx-auto align-items-center">
                        <b>Price:</b> ₱${sale_price ? parseInt(sale_price).toLocaleString("en-PH") : 'N/A'}
                        | ${land_area ? land_area + " sqm" : 'N/A'}</div>
                    </div>`,
                disableAutoPan: true
            });

            infoWindows.push({ marker, infoWindow });

            marker.addListener("click", () => {
                infoWindows.forEach(({ infoWindow }) => infoWindow.close()); 
                infoWindow.open(window.map, marker);

                // Move to Street View and place marker inside it
                streetView.setPosition(propertyLocation);
                streetView.setVisible(true);
            });
        });
    })
    .catch(error => console.error("Error fetching properties:", error));

    const showInfoControl = document.createElement("button");
    showInfoControl.textContent = "Show all info";
    showInfoControl.classList.add("show-info-btn");

    showInfoControl.style.fontSize = "14px"; 
    showInfoControl.style.fontWeight = "bold";
    showInfoControl.style.margin = "8px"; 
    showInfoControl.style.padding = "12px 20px"; 
    showInfoControl.style.background = "#fff";
    showInfoControl.style.border = "1px solid #ccc"; 
    showInfoControl.style.cursor = "pointer";
    showInfoControl.style.borderRadius = "5px";
    showInfoControl.style.boxShadow = "0 2px 4px rgba(0,0,0,0.2)"; 

    showInfoControl.addEventListener("click", () => {
        showInfo = !showInfo;
        infoWindows.forEach(({ marker, infoWindow }) => {
            if (showInfo) {
                infoWindow.open(window.map, marker);
            } else {
                infoWindow.close();
            }
        });
    });

    window.map.controls[google.maps.ControlPosition.TOP_LEFT].push(showInfoControl);
}

google.maps.event.addDomListener(window, 'load', initMap);


                        google.maps.event.addDomListener(window, 'load', initMap);

                        window.toggleMap = function() {
                            const mapPanel = document.getElementById('mapPanel');
                            if (mapPanel) {
                                mapPanel.classList.toggle('active'); 
                            }

                            if (window.map) {
                                setTimeout(() => {
                                    google.maps.event.trigger(window.map, 'resize');
                                }, 300);
                            }
                        };

                        window.toggleFullscreen = function() {
                            const mapPanel = document.getElementById('mapPanel');
                            const fullscreenIcon = document.querySelector('.map-control-btn i.fa-expand, .map-control-btn i.fa-compress');

                            if (mapPanel) {
                                mapPanel.classList.toggle('fullscreen'); 

                                if (fullscreenIcon) {
                                    if (mapPanel.classList.contains('fullscreen')) {
                                        fullscreenIcon.classList.remove('fa-expand');
                                        fullscreenIcon.classList.add('fa-compress');
                                    } else {
                                        fullscreenIcon.classList.remove('fa-compress');
                                        fullscreenIcon.classList.add('fa-expand');
                                    }
                                }

                                if (window.map) {
                                    setTimeout(() => {
                                        google.maps.event.trigger(window.map, 'resize');
                                    }, 300);
                                }
                            }
                        };

                    </script>

<!-- start of footer -->
<div class="modal-footer">
    </div>

    <div class="az-footer">
        <div class="container">
            <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
        </div>
    </div>
    <!-- End of Footer -->
                                   
    <!-- Unauthorized Access Modal -->
    <div class="modal fade" id="warningModal" tabindex="-1" role="dialog" aria-labelledby="warningModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="warning-modal-content">
                <div class="modal-body text-center">
                    <!-- Custom Warning Icon with Animation -->
                    <div class="warning-icon-wrapper">
                        <i class="fas fa-exclamation-circle warning-icon"></i>
                    </div>
                    <p class="warning-modal-message" id="warningMessage">You do not have permission to view this page.
                    </p>
                </div>
                <div class="warning-modal-footer justify-content-center">
                    <button type="button" class="btn warning-btn-danger" id="warningCloseButton">Sign In</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sign Out Confirmation Modal -->
    <div class="modal fade" id="signOutModal" tabindex="-1" role="dialog" aria-labelledby="signOutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content"> <!-- This is the white container -->
                <div class="modal-body text-center">
                    <!-- Custom Sign Out Icon with Animation -->
                    <div class="signout-icon-wrapper">
                        <i class="fas fa-sign-out-alt signout-icon"></i>
                    </div>
                    <p class="signout-modal-message">Are you sure you want to sign out?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmSignOutButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal create property -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content-popup">
                <div class="modal-body text-center">
                    <div class="checkmark-wrapper">
                        <i class="fas fa-check-circle checkmark-icon"></i>
                    </div>
                    <p class="modal-message">Property successfully added.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" id="closeModalBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Details Modal -->
    <div class="modal fade" id="propertyDetailsModal" tabindex="-1" role="dialog" aria-labelledby="propertyDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="propertyDetailsModalLabel">Property Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Image Carousel -->
                    <div id="propertyImageCarousel" class="carousel slide mb-4" data-ride="carousel">
                        <div class="carousel-inner">
                            <!-- Images will be dynamically added here -->
                        </div>
                        <a class="carousel-control-prev" href="#propertyImageCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#propertyImageCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>

                    <!-- Property Information -->
                    <div class="property-info">
                        <h3 id="modalPropertyName"></h3>
                        <div class="property-meta">
                            <span class="badge badge-primary" id="modalPropertyType"></span>
                            <span class="badge badge-info" id="modalSaleType"></span>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>Basic Information</h5>
                                <ul class="list-unstyled">

                                    <li><strong>Location:</strong> <span id="modalLocation"></span></li>
                                    <li><strong>Land Area:</strong> <span id="modalLandArea"></span> sqm</li>
                                    <li><strong>Price:</strong> <span id="modalPrice"></span></li>
                                    <li><strong>Land Condition:</strong> <span id="modalLandCondition"></span></li>
                                    <li><strong>Lease Duration:</strong> <span id="modalLeaseDuration"></span></li>
                                    <li><strong>Monthly Rent:</strong> <span id="modalMonthlyRent"></span></li>
                                    <li><strong>Property Description:</strong> <span id="modalDescription"></span></li>
                                </ul>
                                <script>
                                    
                                </script>

                            </div>
                            <div class="col-md-6 d-none">
                                <h5>Features</h5>
                                <div id="modalFeatures" class="features-list">
                                    <!-- Features will be dynamically added here -->
                                </div>
                            </div>
                        </div>

                        <div class="description-section mt-4 d-none">
                            <h5>Description</h5>
                            <p id="modalDescription"></p>
                        </div>

                        <!-- Property Location Map -->
                        <div class="mt-4">
                            <h5>Property Location</h5>
                            <div id="modalMap" style="height: 300px;"></div>
                        </div>

                        <!-- Agent Information -->
                        <div class="agent-info mt-4">
                            <h5>Agent Information</h5>
                            <div class="d-flex align-items-center">
                                <img id="modalAgentImage" src="" alt="Agent" class="rounded-circle mr-3" style="width: 60px; height: 60px; object-fit: cover;">
                                <div>
                                    <h6 id="modalAgentName"></h6>
                                    <div id="agentActions">
                                        <!-- Buttons will be dynamically populated based on ownership -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Restrict Property Modal -->
    <div class="modal fade" id="restrictModal" tabindex="-1" role="dialog" aria-labelledby="restrictModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restrictModalLabel">Restrict Property</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group" id="restrictDaysGroup">
                        <label for="restrictDays">Number of Days to Restrict:</label>
                        <input type="number" class="form-control" id="restrictDays" min="1" max="365" placeholder="Enter number of days">
                        <small class="text-muted">Property will be hidden from users for the specified number of days.</small>
                    </div>
                    <div id="unrestrictMessage" style="display: none;">
                        <p>Are you sure you want to remove the restriction from this property?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmRestrictBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/lib/jquery/jquery.min.js"></script>
    <script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/lib/ionicons/ionicons.js"></script>
    <script src="../../assets/js/azia.js"></script>

    <script src="../../assets/js/addedFunctions.js"></script>



    <script>
        // Update the label on file selection
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            const fileNames = Array.from(e.target.files).map(file => file.name);
            const label = e.target.nextElementSibling;
            label.classList.add('selected');
            label.innerHTML = fileNames.length > 2 ? `${fileNames[0]}, ${fileNames[1]}, +${fileNames.length - 2} more` : fileNames.join(', ');
        });
    </script>

    <script>
        document.getElementById('mapButton').addEventListener('click', function() {
            var mapContainer = document.getElementById('mapContainer');
            var propertyList = document.querySelector('.property-list');

            // Toggle the map container visibility
            mapContainer.classList.toggle('open');

            // Adjust the property list layout: switch to 1 column when the map is shown
            if (mapContainer.classList.contains('open')) {
                propertyList.classList.add('one-column');
            } else {
                propertyList.classList.remove('one-column');
            }
        });
    </script>

    <!--Label Changer in listing type, for sale or for lease-->
    <script>
        document.getElementById('saleOrLease').addEventListener('change', function() {
            const priceLabel = document.getElementById('priceLabel');
            const priceInput = document.getElementById('propertyPrice');

            if (this.value === 'lease') {
                priceLabel.textContent = 'Monthly Rate'; // Change label to "Monthly Rate"
                priceInput.placeholder = 'Enter monthly rate'; // Change placeholder
            } else if (this.value === 'sale') {
                priceLabel.textContent = 'Price'; // Change label back to "Price"
                priceInput.placeholder = 'Enter price'; // Change placeholder
            }
        });
    </script>


    <!--Image preview-->
    <script>
        let imageFiles = [];

        // Handle image preview
        document.getElementById('propertyImages').addEventListener('change', function(event) {
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');

            if (event.target.files.length > 0) {
                previewContainer.style.display = 'block'; // Show the preview box

                // Loop through selected files and add them to the preview section
                Array.from(event.target.files).forEach(file => {
                    imageFiles.push(file); // Add the file to the imageFiles array
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imgElement = document.createElement('img');
                        imgElement.src = e.target.result;
                        imgElement.classList.add('img-thumbnail', 'm-2');
                        imgElement.style.maxWidth = '150px';
                        imgElement.style.maxHeight = '150px';

                        // Create a delete button for the image
                        const deleteButton = document.createElement('button');
                        deleteButton.innerHTML = '&times;';
                        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm', 'position-absolute', 'top-0', 'end-0');
                        deleteButton.style.zIndex = '10';
                        deleteButton.style.width = '30px'; // Set width for round button
                        deleteButton.style.height = '30px'; // Set height for round button
                        deleteButton.style.borderRadius = '50%'; // Make it circular
                        deleteButton.style.fontSize = '18px'; // Adjust font size
                        deleteButton.style.padding = '0'; // Remove padding
                        deleteButton.style.lineHeight = '30px'; // Vertically center the 'X'
                        deleteButton.style.textAlign = 'center'; // Center the 'X'

                        deleteButton.onclick = function() {
                            // Remove the image from the array and the preview
                            const index = imageFiles.indexOf(file);
                            if (index > -1) {
                                imageFiles.splice(index, 1);
                            }
                            imgElement.remove();
                            deleteButton.remove();

                            // If no images left, hide the preview container
                            if (imageFiles.length === 0) {
                                previewContainer.style.display = 'none';
                            }
                        };

                        // Wrapper div for the image and delete button
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('position-relative');
                        wrapper.appendChild(imgElement);
                        wrapper.appendChild(deleteButton);

                        preview.appendChild(wrapper);
                    };

                    reader.readAsDataURL(file);
                });
            }
        });
    </script>

    <!--Unauthorized modal-->
    <script>
        $(document).ready(function() {
            var showModal = <?php echo $show_modal ? 'true' : 'false'; ?>;
            var errorMessage = <?php echo json_encode($error_message); ?>;

            if (showModal) {
                $('#warningMessage').text(errorMessage); // Set the error message dynamically
                $('#warningModal').modal({
                    backdrop: 'static', // Prevent closing when clicking outside
                    keyboard: false // Prevent closing when pressing the escape key
                });
                $('#warningModal').modal('show'); // Show the modal
            }

            // Close the modal and redirect to login when the "Sign In" button is clicked
            $('#warningCloseButton').click(function() {
                $('#warningModal').modal('hide');
                window.location.href = '../../index.php'; // Redirect to the login page
            });
        });
    </script>

    <!--Signout process--->
    <script>
        // Show the sign-out confirmation modal when the Sign Out button is clicked
        document.getElementById('signOutButton').addEventListener('click', function() {
            $('#signOutModal').modal('show'); // Show the modal
        });

        // Confirm sign out (destroy session and redirect to login page)
        document.getElementById('confirmSignOutButton').addEventListener('click', function() {
            // Make a request to sign_out.php to destroy the session
            fetch('../../backend/sign_out.php', {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // If sign out is successful, redirect to login page
                        window.location.href = '../../index.php'; // Adjust the login page URL as needed
                    } else {
                        alert('Error: Could not sign out.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>

    <!--Time Update-->
    <script>
        // Function to update the current time every second
        function updateTime() {
            const timeElement = document.getElementById('current-time');

            // Get the current time in Manila timezone
            const now = new Date().toLocaleString("en-US", {
                timeZone: "Asia/Manila"
            });

            // Format the time as hh:mm:ss AM/PM
            const options = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            };
            const timeString = new Date(now).toLocaleTimeString('en-US', options);

            // Update the time on the page
            timeElement.textContent = timeString;
        }

        // Update the time every 1000 milliseconds (1 second)
        setInterval(updateTime, 1000);
    </script>

    <!--Di ko alam kay palacio-->
    <script>
        // Initialize maps
        const map1 = new maptilersdk.Map({
            container: 'map1',
            style: maptilersdk.MapStyle.STREETS,
            center: [121.0537, 14.5489], // Manila coordinates
            zoom: 13
        });

        const map2 = new maptilersdk.Map({
            container: 'map2',
            style: maptilersdk.MapStyle.STREETS,
            center: [121.0537, 14.5489],
            zoom: 13
        });

        const mapInput = new maptilersdk.Map({
            container: 'mapInput',
            style: maptilersdk.MapStyle.STREETS,
            center: [121.0537, 14.5489],
            zoom: 13
        });
    </script>

    <!--create property form part-->
    <script>
        document.getElementById('saleOrLease').addEventListener('change', function() {
            var saleForm = document.getElementById('saleForm');
            var leaseForm = document.getElementById('leaseForm');
            var leaseDuration = document.getElementById('leaseDuration');
            var landCondition = document.getElementById('landCondition');
            var anotherInfo = document.getElementById('anotherInfo');

            // Hide both forms initially
            saleForm.style.display = 'none';
            leaseForm.style.display = 'none';

            // Reset the required attribute and visibility for fields
            landCondition.required = false;
            anotherInfo.required = false;
            landCondition.style.display = 'none';
            anotherInfo.style.display = 'none';

            // Show the relevant form based on selection
            if (this.value === 'sale') {
                saleForm.style.display = 'block';
                leaseDuration.required = false; // Disable required for lease duration if sale is selected

                // Show landCondition and anotherInfo for sale
                landCondition.style.display = 'block';
                landCondition.required = true; // Enable required for landCondition when 'For Sale' is selected
                anotherInfo.style.display = 'block';
                anotherInfo.required = true; // Enable required for anotherInfo when 'For Sale' is selected

            } else if (this.value === 'lease') {
                leaseForm.style.display = 'block';
                leaseDuration.required = true; // Enable required for lease duration if lease is selected

                // Hide landCondition and anotherInfo for lease
                landCondition.style.display = 'none';
                anotherInfo.style.display = 'none';
            }
        });

        // Trigger the change event on page load to initialize the form visibility
        document.getElementById('saleOrLease').dispatchEvent(new Event('change'));
    </script>

    <!-- modal if data is sent and modal appear -->
    <script>
        document.getElementById('propertyForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Prevent form from refreshing the page

            // Show loading spinner and disable button
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            submitBtn.disabled = true;
            btnText.textContent = "Submitting...";
            loadingSpinner.classList.remove("d-none");

            const formData = new FormData(this); // Get form data

            try {
                const response = await fetch('../../backend/add_property.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.status === "success") {
                    // Show success modal
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'), {
                        backdrop: 'static',
                        keyboard: false
                    });
                    successModal.show();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (error) {
                console.error("Submission error:", error);
                alert("Something went wrong!");
            } finally {
                // Reset button after submission
                submitBtn.disabled = false;
                btnText.textContent = "Submit";
                loadingSpinner.classList.add("d-none");
            }
        });

        // Redirect button
        document.getElementById('closeModalBtn').addEventListener('click', function() {
            window.location.href = "agent_listing.php"; // Change this to your actual landing page
        });
    </script>

    <!-- Function to check and remove expired new badges -->
    <script>
    function checkNewBadges() {
        const newBadges = document.querySelectorAll('.new-badge');
        const now = new Date();
        
        newBadges.forEach(badge => {
            const createdDate = new Date(badge.dataset.created);
            const daysDifference = Math.floor((now - createdDate) / (1000 * 60 * 60 * 24));
            
            if (daysDifference >= 7) {
                badge.remove();
            }
        });
    }

    // Check badges when page loads
    document.addEventListener('DOMContentLoaded', checkNewBadges);

    // Optionally check periodically (every minute) for real-time updates
    setInterval(checkNewBadges, 60000);
    </script>

    <!-- New functions for edit and archive -->
    <script>
    function editProperty(propertyId) {
        // Redirect to edit property page
        window.location.href = `edit_property.php?id=${propertyId}`;
    }

    function archiveProperty(propertyId) {
        if (confirm('Are you sure you want to archive this property? This action cannot be undone.')) {
            fetch('../../backend/archive_property.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    property_id: propertyId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to remove the archived property
                    location.reload();
                } else {
                    alert('Failed to archive property: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while archiving the property');
            });
        }
    }
    </script>

    <!-- Add this JavaScript before the closing body tag -->
    <script>
    let currentPropertyId = null;
    let currentStatus = null;

    function showRestrictModal(propertyId, status) {
        currentPropertyId = propertyId;
        currentStatus = status;
        
        const restrictDaysGroup = document.getElementById('restrictDaysGroup');
        const unrestrictMessage = document.getElementById('unrestrictMessage');
        const modalTitle = document.getElementById('restrictModalLabel');
        
        if (status === 'restricted') {
            restrictDaysGroup.style.display = 'none';
            unrestrictMessage.style.display = 'block';
            modalTitle.textContent = 'Unrestrict Property';
        } else {
            restrictDaysGroup.style.display = 'block';
            unrestrictMessage.style.display = 'none';
            modalTitle.textContent = 'Restrict Property';
        }
        
        $('#restrictModal').modal('show');
    }

    document.getElementById('confirmRestrictBtn').addEventListener('click', function() {
        if (currentStatus === 'restricted') {
            togglePropertyStatus(currentPropertyId, currentStatus);
        } else {
            const days = document.getElementById('restrictDays').value;
            if (!days || days < 1) {
                alert('Please enter a valid number of days');
                return;
            }
            togglePropertyStatus(currentPropertyId, currentStatus, days);
        }
        $('#restrictModal').modal('hide');
    });
    </script>

    <script>
    function togglePropertyStatus(propertyId, currentStatus, days = null) {
        const data = {
            property_id: propertyId,
            current_status: currentStatus
        };
        
        if (days) {
            data.days = days;
        }

        fetch('../../backend/toggle_property_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated status
                location.reload();
            } else {
                alert('Failed to update property status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the property status');
        });
    }
    </script>

    <!-- Add this JavaScript before the closing body tag -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const propertyCards = document.querySelectorAll('.property-card');
            
            propertyCards.forEach(card => {
                const propertyName = card.querySelector('.property-title').textContent.toLowerCase();
                const propertyLocation = card.querySelector('.location-badge').textContent.toLowerCase();
                const propertyType = card.querySelector('.property-details').textContent.toLowerCase();
                const propertyDescription = card.querySelector('.property-description') ? 
                                          card.querySelector('.property-description').textContent.toLowerCase() : '';
                
                // Check if any of the property details match the search term
                const matches = propertyName.includes(searchTerm) ||
                              propertyLocation.includes(searchTerm) ||
                              propertyType.includes(searchTerm) ||
                              propertyDescription.includes(searchTerm);
                
                // Show/hide the card based on search match
                card.style.display = matches ? 'block' : 'none';
            });
        });
    });
    </script>

    <style>
    /* Add these styles to your existing CSS */
    .search-section {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .search-section .input-group {
        max-width: 600px;
        margin: 0 auto;
    }

    .search-section input {
        border-right: none;
        padding-left: 15px;
    }

    .search-section .btn {
        border-left: none;
        background: white;
    }

    .search-section .btn:hover {
        background: #f8f9fa;
    }

    .search-section .fa-search {
        color: #6c757d;
    }

    /* Add animation for search results */
    .property-card {
        transition: all 0.3s ease-in-out;
    }

    .property-card:not([style*="display: none"]) {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>

    <!-- Add this JavaScript before the closing body tag -->
    <!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get filter elements
        const saleTypeFilter = document.getElementById('saleTypeFilter');
        const leaseTermFilter = document.getElementById('leaseTermFilter');
        const monthlyRental = document.getElementById('monthlyRental');
        const landCondition = document.getElementById('landCondition');
        const landContract = document.getElementById('landContract');
        const landTypeFilter = document.getElementById('landTypeFilter');
        const locationFilter = document.getElementById('locationFilter');
        const minPrice = document.getElementById('minPrice');
        const maxPrice = document.getElementById('maxPrice');
        const minArea = document.getElementById('minArea');
        const maxArea = document.getElementById('maxArea');
        const featureCheckboxes = document.querySelectorAll('input[name="features"]');
        const additionalInfoCheckboxes = document.querySelectorAll('input[name="additionalInfo"]');

        // Apply filters button click handler
        document.getElementById('applyFilters').addEventListener('click', function() {
            const propertyCards = document.querySelectorAll('.property-card');
            
            propertyCards.forEach(card => {
                let showCard = true;

                // Sale Type Filter
                            // Sale Type Filter
            // Sale Type Filter
if (saleTypeFilter.value !== 'all') {
    const saleType = card.querySelector('.sale-badge').textContent.trim().toLowerCase();
    
    if (saleTypeFilter.value === 'sale' && saleType !== 'For Sale') {
        showCard = false;
    }
    if (saleTypeFilter.value === 'lease' && saleType !== 'For Lease') {
        showCard = false;
    }
}



                // Lease Term Filter (if applicable)
                if (saleTypeFilter.value === 'lease' && leaseTermFilter.value !== 'all') {
                    const leaseTerm = card.querySelector('.lease-term')?.textContent.toLowerCase();
                    if (leaseTermFilter.value === 'short' && !leaseTerm?.includes('less than 1 year')) showCard = false;
                    if (leaseTermFilter.value === 'long' && !leaseTerm?.includes('more than 1 year')) showCard = false;
                }

                // Monthly Rental Filter (if applicable)
                if (saleTypeFilter.value === 'lease' && monthlyRental.value) {
                    const rental = parseFloat(card.querySelector('.property-price')?.textContent.replace(/[^0-9.]/g, '')) || 0;
                    const filterRental = parseFloat(monthlyRental.value.replace(/,/g, '')) || 0;
                    if (rental > filterRental) showCard = false;
                }

                // Land Condition Filter (if applicable)
                if (saleTypeFilter.value === 'sale' && landCondition.value !== 'all') {
                    const condition = card.querySelector('.property-conditon')?.textContent.toLowerCase();
                    if (!condition?.includes(landCondition.value.toLowerCase())) showCard = false;
                }

                // Land Contract Price Filter (if applicable)
                if (saleTypeFilter.value === 'sale' && landContract.value) {
                    const price = parseFloat(card.querySelector('.property-price')?.textContent.replace(/[^0-9.]/g, '')) || 0;
                    const filterPrice = parseFloat(landContract.value.replace(/,/g, '')) || 0;
                    if (price > filterPrice) showCard = false;
                }

                // Land Type Filter
                if (landTypeFilter.value !== 'all') {
                    const landType = card.querySelector('.property-details')?.textContent.toLowerCase();
                    if (!landType?.includes(landTypeFilter.value.toLowerCase())) showCard = false;
                }

                // Location Filter
                if (locationFilter.value !== 'all') {
                    const location = card.querySelector('.location-badge')?.textContent.toLowerCase();
                    if (!location?.includes(locationFilter.value.toLowerCase())) showCard = false;
                }

                // Price Range Filter
                const price = parseFloat(card.querySelector('.property-price')?.textContent.replace(/[^0-9.]/g, '')) || 0;
                if (minPrice.value && price < parseFloat(minPrice.value)) showCard = false;
                if (maxPrice.value && price > parseFloat(maxPrice.value)) showCard = false;

                // Area Range Filter
                const area = parseFloat(card.querySelector('.property-details')?.textContent.match(/\d+/)?.[0]) || 0;
                if (minArea.value && area < parseFloat(minArea.value)) showCard = false;
                if (maxArea.value && area > parseFloat(maxArea.value)) showCard = false;

                // Features Filter
                const selectedFeatures = Array.from(featureCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                if (selectedFeatures.length > 0) {
                    const features = card.querySelector('.features-list')?.textContent.toLowerCase() || '';
                    if (!selectedFeatures.every(feature => features.includes(feature.toLowerCase()))) {
                        showCard = false;
                    }
                }

                // Additional Info Filter
                const selectedInfo = Array.from(additionalInfoCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                if (selectedInfo.length > 0) {
                    const additionalInfo = card.querySelector('.promo-badge')?.textContent.toLowerCase() || '';
                    if (!selectedInfo.every(info => additionalInfo.includes(info.toLowerCase()))) {
                        showCard = false;
                    }
                }

                // Show/hide card based on all filters
                card.style.display = showCard ? 'block' : 'none';
            });
        });

        // Reset filters button click handler
        document.getElementById('resetFilters').addEventListener('click', function() {
            // Reset all filter values
            saleTypeFilter.value = 'all';
            leaseTermFilter.value = 'all';
            monthlyRental.value = '';
            landCondition.value = 'all';
            landContract.value = '';
            landTypeFilter.value = 'all';
            locationFilter.value = 'all';
            minPrice.value = '';
            maxPrice.value = '';
            minArea.value = '';
            maxArea.value = '';
            
            // Uncheck all checkboxes
            featureCheckboxes.forEach(cb => cb.checked = false);
            additionalInfoCheckboxes.forEach(cb => cb.checked = false);

            // Show all property cards
            document.querySelectorAll('.property-card').forEach(card => {
                card.style.display = 'block';
            });

            // Reset sale/lease specific options visibility
            document.querySelectorAll('.sale-options').forEach(option => {
                option.style.display = 'none';
            });
            document.querySelectorAll('.lease-options').forEach(option => {
                option.style.display = 'none';
            });
        });
    });
    </script>

s<script>
    function handleSearch() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const propertyCards = document.querySelectorAll('.property-card');

        propertyCards.forEach(card => {
            const propertyName = card.querySelector('.property-title').textContent.toLowerCase();
            const propertyType = card.querySelector('.property-details').textContent.toLowerCase();
            const propertyLocation = card.querySelector('.location-badge').textContent.toLowerCase();
            
            const matchesSearch = propertyName.includes(searchInput) || 
                                propertyType.includes(searchInput) || 
                                propertyLocation.includes(searchInput);
            
            card.style.display = matchesSearch ? 'block' : 'none';
        });
    }

    function applyFilters() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const saleType = document.getElementById('saleTypeFilter').value;
        const landType = document.getElementById('landTypeFilter').value;
        const location = document.getElementById('propertyLocation').value;
        const minPrice = document.getElementById('minPrice').value;
        const maxPrice = document.getElementById('maxPrice').value;
        const minArea = document.getElementById('minArea').value;
        const maxArea = document.getElementById('maxArea').value;
        
        const propertyCards = document.querySelectorAll('.property-card');

        propertyCards.forEach(card => {
            let showCard = true;
            
            // Search text filter
            const propertyName = card.querySelector('.property-title').textContent.toLowerCase();
            const propertyType = card.querySelector('.property-details').textContent.toLowerCase();
            const propertyLocation = card.querySelector('.location-badge').textContent.toLowerCase();
            
            if (searchInput && !propertyName.includes(searchInput) && 
                !propertyType.includes(searchInput) && 
                !propertyLocation.includes(searchInput)) {
                showCard = false;
            }

            // Sale type filter
            if (saleType !== 'all') {
                const cardSaleType = card.querySelector('.sale-badge').textContent.toLowerCase();
                if (!cardSaleType.includes(saleType)) {
                    showCard = false;
                }
            }

            // Land type filter
            if (landType !== 'all') {
                const cardLandType = card.querySelector('.property-details').textContent.toLowerCase();
                if (!cardLandType.includes(landType)) {
                    showCard = false;
                }
            }

            // Location filter
            if (location !== 'all') {
                const cardLocation = card.querySelector('.location-badge').textContent.toLowerCase();
                if (!cardLocation.includes(location.toLowerCase())) {
                    showCard = false;
                }
            }

            // Price range filter
            const priceText = card.querySelector('.property-price').textContent;
            const price = parseFloat(priceText.replace(/[^0-9.]/g, ''));
            if ((minPrice && price < minPrice) || (maxPrice && price > maxPrice)) {
                showCard = false;
            }

            // Area range filter
            const areaText = card.querySelector('.property-details').textContent;
            const areaMatch = areaText.match(/(\d+)\s*sqm/);
            if (areaMatch) {
                const area = parseFloat(areaMatch[1]);
                if ((minArea && area < minArea) || (maxArea && area > maxArea)) {
                    showCard = false;
                }
            }

            card.style.display = showCard ? 'block' : 'none';
        });
    }

    function resetFilters() {
        // Reset all filter inputs
        document.getElementById('searchInput').value = '';
        document.getElementById('saleTypeFilter').value = 'all';
        document.getElementById('landTypeFilter').value = 'all';
        document.getElementById('propertyLocation').value = 'all';
        document.getElementById('minPrice').value = '';
        document.getElementById('maxPrice').value = '';
        document.getElementById('minArea').value = '';
        document.getElementById('maxArea').value = '';
        
        // Reset checkboxes
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });

        // Show all property cards
        document.querySelectorAll('.property-card').forEach(card => {
            card.style.display = 'block';
        });
    }

    // Add event listener for search input
    document.getElementById('searchInput').addEventListener('keyup', handleSearch);
    </script>  -->

</body>

</html>
