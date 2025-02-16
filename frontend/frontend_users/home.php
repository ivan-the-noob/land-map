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

  <title>Land Map | Home</title>
  <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

  <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

  <!-- vendor css -->
  <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
  <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
  <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

  <!-- Mapping Links -->
  <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
  <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />

  <!--di pa sure kung buburahin-->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>

  <!-- azia CSS -->
  <link rel="stylesheet" href="../../assets/css/azia.css">
  <link rel="stylesheet" href="../../assets/css/profile.css">


</head>

<body>

<!-- az-header-head -->

<div class="az-header">
    <?php require "../../partials/nav_home.php" ?>
</div>

<!-- az-header-tail -->

<div class="az-content az-content-dashboard">
    <div class="container">
        <div class="az-content-body">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Agent'): ?>
                <div class="az-dashboard-one-title">
                    <div>
                        <h2 class="az-dashboard-title">Agent: <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
                        <p class="az-dashboard-text">
                            <span class="star-rating">
                                <i class="fa fa-star" style="color: gold;"></i>
                                <i class="fa fa-star" style="color: gold;"></i>
                                <i class="fa fa-star" style="color: gold;"></i>
                                <i class="fa fa-star-half" style="color: gold;"></i>
                                <i class="fa fa-star-o" style="color: gold;"></i>
                            </span>
                        </p>
                    </div>
                    <div class="az-content-header-right">
                        <div class="media">
                            <div class="media-body">
                                <label>Start Date</label>
                                <h6>Oct 10, 2018</h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>End Date</label>
                                <h6>Oct 23, 2018</h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <div class="media">
                            <div class="media-body">
                                <label>Event Category</label>
                                <h6>All Categories</h6>
                            </div><!-- media-body -->
                        </div><!-- media -->
                        <a href="" class="btn btn-purple">Export</a>
                    </div>
                </div>

                <div class="az-dashboard-nav">
                    <nav class="nav">
                        <a class="nav-link active" data-toggle="tab" href="#property-list">Property List</a>
                        <a class="nav-link" data-toggle="tab" href="#create_property">Create New Listing</a>
                        <a class="nav-link" data-toggle="tab" href="#update-property">Update a Property</a>
                        <a class="nav-link" data-toggle="tab" href="#delete">Delete</a>
                    </nav>

                    <nav class="nav">
                        <a class="nav-link" href="#"><i class="far fa-save"></i> Save Report</a>
                        <a class="nav-link" href="#"><i class="far fa-file-pdf"></i> Export to PDF</a>
                        <a class="nav-link" href="#"><i class="far fa-envelope"></i>Send to Email</a>
                        <a class="nav-link" href="#"><i class="fas fa-ellipsis-h"></i></a>
                    </nav>
                </div>

                <div class="tab-content mt-4">
                    <div id="property-list" class="tab-pane active">
                        <?php if (isset($properties) && !empty($properties)): ?>
                            <div class="property-list">
                                <?php foreach ($properties as $property): ?>
                                    <div class="container mt-4">
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
                                                    <a href="/viewproperty?id=<?php echo $property['id']; ?>"><button class="btn btn-primary">View</button></a>
                                                    
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

                    <!-- PROPERTY CARD -->

                    <!-- CREATE PROPERTY LIST -->

                    <div id="create_property" class="tab-pane">
                        <div class="d-flex align-items-center mb-4">
                            <!-- Title -->
                            <h3 class="mb-1 mr-5">List New Land</h3>

                            <!-- Tab-like Button -->
                            <div class="btn-group" role="group" aria-label="Property Type">
                                <button type="button" class="btn btn-outline-primary active" id="landBtn">Vacant Lot</button>
                                <button type="button" class="btn btn-outline-primary"
                                    id="singleAttachedHouseBtn">Residential</button>
                                <button type="button" class="btn btn-outline-primary"
                                    id="singleDetachedHouseBtn">Commercial</button>
                                <button type="button" class="btn btn-outline-primary" id="rowhouseBtn">Industrial</button>
                                <button type="button" class="btn btn-outline-primary" id="apartmentBtn">Agricultural</button>
                                <button type="button" class="btn btn-outline-primary" id="villaBtn">Memorial</button>
                            </div>
                        </div>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 order-md-2">
                                    <div id="map" class="mb-3 position-relative">
                                        <label for="mapstyles" class="form-label">Select Map Style</label>
                                        <select name="mapstyles" id="mapstyles" class="form-select mapstyles-select">
                                            <optgroup label="Map Styles">
                                                <option value="STREETS">Streets</option>
                                                <option value="STREETS.DARK">Streets Dark</option>
                                                <option value="HYBRID" selected>Satellite</option>
                                            </optgroup>
                                        </select>

                                        <div class="custom-button-container d-flex justify-content-center">
                                            <button id="undo-last" class="custom-btn custom-btn-secondary mx-2">Undo
                                                Last</button>
                                            <button id="clear-all" class="custom-btn custom-btn-danger mx-2">Clear
                                                All</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 order-md-1">
                                    
                                    <!-- vacant lot -->
                                    <div id="landForm" class="property-form">
                                        <div class="form-group">
                                            <label for="propertyName">Property Name</label>
                                            <input name="propertyName" type="text" class="form-control" id="propertyName"
                                                placeholder="Enter property name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyLocation">Property Address</label>
                                            <input name="propertyLocation" type="text" class="form-control"
                                                id="propertyLocation" placeholder="Enter Property Address" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyPrice">Price</label>
                                            <input name="propertyPrice" type="number" class="form-control"
                                                id="propertyPrice" placeholder="Enter price" required>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="landArea">Land Area</label>
                                                <input name="landArea" type="number" class="form-control" id="landArea"
                                                    placeholder="Enter land area" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="floorArea">Floor Area</label>
                                                <input name="floorArea" type="number" class="form-control" id="floorArea"
                                                    placeholder="Enter floor area" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyDescription">Description</label>
                                            <textarea name="propertyDescription" class="form-control"
                                                id="propertyDescription" rows="3" placeholder="Enter description"
                                                required></textarea>
                                        </div>

                                        <!--
                                        <div class="form-group">
                                            <label for="amenities" class="font-weight-bold">Amenities</label>
                                            <div class="d-flex align-items-center mb-3">
                                                <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#amenityModal" style="white-space: nowrap;">Select Amenities</button>
                                                <div class="custom-file">
                                                    <input type="file" name="images[]" class="custom-file-input"
                                                        id="customFile" multiple>
                                                    <label class="custom-file-label" for="customFile">Choose images</label>
                                                </div>
                                            </div>
                                            <div id="selectedAmenities" class="mt-2"></div>
                                        </div>
                                        -->
                                        <div class="form-group">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="custom-file">
                                                    <input type="file" name="images[]" class="custom-file-input"
                                                        id="customFile" multiple>
                                                    <label class="custom-file-label" for="customFile">Choose images</label>
                                                </div>
                                            </div>
                                            <div id="selectedAmenities" class="mt-2"></div>
                                        </div>
                                    </div>

                                    <!-- SINGLE-ATTACHED FORM -->
                                    <div id="singleAttachedHouseForm" class="property-form" style="display: none;">
                                        <div class="form-group">
                                            <label for="propertyName">Property Nameee</label>
                                            <input name="propertyName" type="text" class="form-control" id="propertyName"
                                                placeholder="Enter property name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyLocation">Location</label>
                                            <input name="propertyLocation" type="text" class="form-control"
                                                id="propertyLocation" placeholder="Enter location" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyPrice">Price</label>
                                            <input name="propertyPrice" type="number" class="form-control"
                                                id="propertyPrice" placeholder="Enter price" required>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="landArea">Land Area</label>
                                                <input name="landArea" type="number" class="form-control" id="landArea"
                                                    placeholder="Enter land area" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="floorArea">Floor Area</label>
                                                <input name="floorArea" type="number" class="form-control" id="floorArea"
                                                    placeholder="Enter floor area" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyDescription">Description</label>
                                            <textarea name="propertyDescription" class="form-control"
                                                id="propertyDescription" rows="3" placeholder="Enter description"
                                                required></textarea>
                                        </div>
                                    </div>

                                    <!-- SINGLE-DETACHED FORM -->
                                    <div id="singleDetachedHouseForm" class="property-form" style="display: none;">
                                        <div class="form-group">
                                            <label for="propertyName">Property Name</label>
                                            <input name="propertyName" type="text" class="form-control" id="propertyName"
                                                placeholder="Enter property name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyLocation">Location</label>
                                            <input name="propertyLocation" type="text" class="form-control"
                                                id="propertyLocation" placeholder="Enter location" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyPrice">Price</label>
                                            <input name="propertyPrice" type="number" class="form-control"
                                                id="propertyPrice" placeholder="Enter price" required>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="landArea">Land Area</label>
                                                <input name="landArea" type="number" class="form-control" id="landArea"
                                                    placeholder="Enter land area" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="floorArea">Floor Area</label>
                                                <input name="floorArea" type="number" class="form-control" id="floorArea"
                                                    placeholder="Enter floor area" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyDescription">Description</label>
                                            <textarea name="propertyDescription" class="form-control"
                                                id="propertyDescription" rows="3" placeholder="Enter description"
                                                required></textarea>
                                        </div>
                                    </div>

                                    <!-- ROWHOUSE -->
                                    <div id="rowhouseForm" class="property-form" style="display: none;">
                                        <!-- Add fields specific to Rowhouse -->
                                        <div class="form-group">
                                            <label for="propertyName">Property Name</label>
                                            <input name="propertyName" type="text" class="form-control" id="propertyName"
                                                placeholder="Enter property name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyLocation">Location</label>
                                            <input name="propertyLocation" type="text" class="form-control"
                                                id="propertyLocation" placeholder="Enter location" required>
                                        </div>
                                        <!-- Add more unique fields for this property type -->
                                    </div>

                                    <!-- Apartment Form -->
                                    <div id="apartmentForm" class="property-form" style="display: none;">
                                        <!-- Add fields specific to Apartment -->
                                        <div class="form-group">
                                            <label for="propertyName">Property Name</label>
                                            <input name="propertyName" type="text" class="form-control" id="propertyName"
                                                placeholder="Enter property name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyLocation">Location</label>
                                            <input name="propertyLocation" type="text" class="form-control"
                                                id="propertyLocation" placeholder="Enter location" required>
                                        </div>
                                        <!-- Add more unique fields for this property type -->
                                    </div>

                                    <!-- Villa Form -->
                                    <div id="villaForm" class="property-form" style="display: none;">
                                        <!-- Add fields specific to Villa -->
                                        <div class="form-group">
                                            <label for="propertyName">Property Name</label>
                                            <input name="propertyName" type="text" class="form-control" id="propertyName"
                                                placeholder="Enter property name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="propertyLocation">Location</label>
                                            <input name="propertyLocation" type="text" class="form-control"
                                                id="propertyLocation" placeholder="Enter location" required>
                                        </div>
                                        <!-- Add more unique fields for this property type -->
                                    </div>


                                    <!-- Modal -->
                                    <div class="modal fade" id="amenityModal" tabindex="-1" role="dialog"
                                        aria-labelledby="amenityModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="amenityModalLabel">Select Amenities</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="amenities[]"
                                                            value="🏊 Swimming Pool" id="amenityPool">
                                                        <label class="form-check-label" for="amenityPool">🏊 Swimming
                                                            Pool</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="amenities[]"
                                                            value="🏋️‍♂️ Gym" id="amenityGym">
                                                        <label class="form-check-label" for="amenityGym">🏋️‍♂️
                                                            Gym</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="amenities[]"
                                                            value="📶 Wi-Fi" id="amenityWifi">
                                                        <label class="form-check-label" for="amenityWifi">📶
                                                            Wi-Fi</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="amenities[]"
                                                            value="🅿️ Parking" id="amenityParking">
                                                        <label class="form-check-label" for="amenityParking">🅿️
                                                            Parking</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="amenities[]"
                                                            value="❄️ Air Conditioning" id="amenityAc">
                                                        <label class="form-check-label" for="amenityAc">❄️ Air
                                                            Conditioning</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" id="saveAmenities">Save
                                                        Changes</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="coordinates" id="coordinates" value="">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                    <div id="update-property" class="tab-pane">
                        <h3>Update a Property</h3>
                        <!-- Content for Update a Property goes here -->
                    </div>

                    <div id="delete" class="tab-pane">
                        <h3>Delete</h3>
                        <!-- Content for Delete goes here -->
                    </div>
                </div>


                <div id="mapContainer" class="map-container">
                    <div id="agentPropertyMap"></div> <!-- Your map will go here -->
                </div>

                <button id="mapButton" class="btn">
                    <i class="fas fa-map-marker-alt"></i>
                </button>
            <?php endif; ?>

            <!-- USER DESIGN -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'User'): ?>
                <div id="property-list" class="tab-pane active">
                    <?php if (isset($properties) && !empty($properties)): ?>
                        <div class="property-list">
                            <?php foreach ($properties as $property): ?>
                                <div class="container mt-4">
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
                                            <div class="property-price">₱<?php echo number_format($property['price'], 2); ?></div>
                                            <div class="property-meta">
                                                <span><i class="fas fa-bed"></i>
                                                    <?php echo isset($property['amenities']['beds']) ? $property['amenities']['beds'] : 0; ?></span>
                                                <span><i class="fas fa-ruler-combined"></i>
                                                    <?php echo htmlspecialchars($property['landArea']); ?> m²</span>
                                                <span><i class="fas fa-expand-arrows-alt"></i>
                                                    <?php echo htmlspecialchars($property['floorArea']); ?> m²</span>
                                            </div>
                                            <div class="property-buttons">
                                                <button class="btn btn-primary">View</button>
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
                <div id="mapContainer" class="map-container">
                    <div id="userPropertyMap"></div> <!-- Your map will go here -->
                </div>

                <button id="mapButton" class="btn">
                    <i class="fas fa-map-marker-alt"></i>
                </button>
            <?php endif; ?>

            <!-- Map Container (initially hidden) -->

        </div><!-- az-content-body -->
    </div>
</div><!-- az-content -->

<div class="az-footer ht-40">
  <div class="container ht-100p pd-t-0-f">
    <span class="text-muted d-block text-center">Copyright ©LoremIpsum 2024</span>
  </div><!-- container -->
</div>


<script src="../../assets/lib/jquery/jquery.min.js"></script>
<script src="../../assets/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/lib/ionicons/ionicons.js"></script>
<script src="../../assets/lib/jquery.flot/jquery.flot.js"></script>
<script src="../../assets/lib/jquery.flot/jquery.flot.resize.js"></script>
<script src="../../assets/lib/chart.js/Chart.bundle.min.js"></script>
<script src="../../assets/lib/peity/jquery.peity.min.js"></script>

<script src="../../assets/js/azia.js"></script>
<script src="../../assets/js/chart.flot.sampledata.js"></script>
<script src="../../assets/js/dashboard.sampledata.js"></script>
<script src="../../assets/js/jquery.cookie.js" type="text/javascript"></script>


<script src="../../assets/js/addedFunctions.js"></script>

<script>
  $(function () {
    'use strict'

    var plot = $.plot('#flotChart', [{
      data: flotSampleData3,
      color: '#007bff',
      lines: {
        fillColor: { colors: [{ opacity: 0 }, { opacity: 0.2 }] }
      }
    }, {
      data: flotSampleData4,
      color: '#560bd0',
      lines: {
        fillColor: { colors: [{ opacity: 0 }, { opacity: 0.2 }] }
      }
    }], {
      series: {
        shadowSize: 0,
        lines: {
          show: true,
          lineWidth: 2,
          fill: true
        }
      },
      grid: {
        borderWidth: 0,
        labelMargin: 8
      },
      yaxis: {
        show: true,
        min: 0,
        max: 100,
        ticks: [[0, ''], [20, '20K'], [40, '40K'], [60, '60K'], [80, '80K']],
        tickColor: '#eee'
      },
      xaxis: {
        show: true,
        color: '#fff',
        ticks: [[25, 'OCT 21'], [75, 'OCT 22'], [100, 'OCT 23'], [125, 'OCT 24']],
      }
    });

    $.plot('#flotChart1', [{
      data: dashData2,
      color: '#00cccc'
    }], {
      series: {
        shadowSize: 0,
        lines: {
          show: true,
          lineWidth: 2,
          fill: true,
          fillColor: { colors: [{ opacity: 0.2 }, { opacity: 0.2 }] }
        }
      },
      grid: {
        borderWidth: 0,
        labelMargin: 0
      },
      yaxis: {
        show: false,
        min: 0,
        max: 35
      },
      xaxis: {
        show: false,
        max: 50
      }
    });

    $.plot('#flotChart2', [{
      data: dashData2,
      color: '#007bff'
    }], {
      series: {
        shadowSize: 0,
        bars: {
          show: true,
          lineWidth: 0,
          fill: 1,
          barWidth: .5
        }
      },
      grid: {
        borderWidth: 0,
        labelMargin: 0
      },
      yaxis: {
        show: false,
        min: 0,
        max: 35
      },
      xaxis: {
        show: false,
        max: 20
      }
    });


    //-------------------------------------------------------------//


    // Line chart
    $('.peity-line').peity('line');

    // Bar charts
    $('.peity-bar').peity('bar');

    // Bar charts
    $('.peity-donut').peity('donut');

    var ctx5 = document.getElementById('chartBar5').getContext('2d');
    new Chart(ctx5, {
      type: 'bar',
      data: {
        labels: [0, 1, 2, 3, 4, 5, 6, 7],
        datasets: [{
          data: [2, 4, 10, 20, 45, 40, 35, 18],
          backgroundColor: '#560bd0'
        }, {
          data: [3, 6, 15, 35, 50, 45, 35, 25],
          backgroundColor: '#cad0e8'
        }]
      },
      options: {
        maintainAspectRatio: false,
        tooltips: {
          enabled: false
        },
        legend: {
          display: false,
          labels: {
            display: false
          }
        },
        scales: {
          yAxes: [{
            display: false,
            ticks: {
              beginAtZero: true,
              fontSize: 11,
              max: 80
            }
          }],
          xAxes: [{
            barPercentage: 0.6,
            gridLines: {
              color: 'rgba(0,0,0,0.08)'
            },
            ticks: {
              beginAtZero: true,
              fontSize: 11,
              display: false
            }
          }]
        }
      }
    });

    // Donut Chart
    var datapie = {
      labels: ['Search', 'Email', 'Referral', 'Social', 'Other'],
      datasets: [{
        data: [25, 20, 30, 15, 10],
        backgroundColor: ['#6f42c1', '#007bff', '#17a2b8', '#00cccc', '#adb2bd']
      }]
    };

    var optionpie = {
      maintainAspectRatio: false,
      responsive: true,
      legend: {
        display: false,
      },
      animation: {
        animateScale: true,
        animateRotate: true
      }
    };

    // For a doughnut chart
    var ctxpie = document.getElementById('chartDonut');
    var myPieChart6 = new Chart(ctxpie, {
      type: 'doughnut',
      data: datapie,
      options: optionpie
    });

  });
</script>

<script>
  maptilersdk.config.apiKey = 'gLXa6ihZF9HF7keYdTHC';

  const userPropertyMap = new maptilersdk.Map({
    container: 'userPropertyMap',
    style: maptilersdk.MapStyle.HYBRID,
    geolocate: maptilersdk.GeolocationType.POINT,
    zoom: 10,
        mapTypeId: google.maps.MapTypeId.SATELLITE,
    maxZoom: 16.2
  });
</script>

<script>
  maptilersdk.config.apiKey = 'gLXa6ihZF9HF7keYdTHC';

  const agentPropertyMap = new maptilersdk.Map({
    container: 'agentPropertyMap',
    style: maptilersdk.MapStyle.HYBRID,
    geolocate: maptilersdk.GeolocationType.POINT,
    zoom: 10,
        mapTypeId: google.maps.MapTypeId.SATELLITE,
    maxZoom: 16.2
  });

  const properties = <?php echo json_encode($properties); ?>;

  properties.forEach(property => {
    // Extract coordinates (an array of [longitude, latitude] pairs for the polygon)
    const coordinates = property.coordinates;

    if (Array.isArray(coordinates)) {
      // Loop over the coordinates array to add a marker for each pair
      coordinates.forEach(coord => {
        if (Array.isArray(coord) && coord.length === 2) {
          const lng = coord[0];  // Longitude
          const lat = coord[1];  // Latitude

          // Create a marker at the property coordinates
          const marker = new maptilersdk.Marker()
            .setLngLat([lng, lat])  // Set the position of the marker
            .addTo(agentPropertyMap);  // Add marker to the map
        } else {
          console.warn("Invalid coordinate format:", coord);
        }
      });
    } else {
      console.warn("Coordinates are not an array:", coordinates);
    }
  });
</script>

<script>
  document.getElementById('saveAmenities').addEventListener('click', function () {
    const selectedAmenities = [];
    const checkboxes = document.querySelectorAll('.modal-body input[type="checkbox"]:checked');
    checkboxes.forEach(checkbox => {
      selectedAmenities.push(checkbox.value);
    });

    // Display selected amenities
    const selectedAmenitiesDiv = document.getElementById('selectedAmenities');
    selectedAmenitiesDiv.innerHTML = selectedAmenities.length > 0
      ? 'Selected Amenities: ' + selectedAmenities.join(', ')
      : 'No amenities selected';

    // Close the modal
    $('#amenityModal').modal('hide');
  });
</script>

<script>
  // Update the label on file selection
  document.querySelector('.custom-file-input').addEventListener('change', function (e) {
    const fileNames = Array.from(e.target.files).map(file => file.name);
    const label = e.target.nextElementSibling;
    label.classList.add('selected');
    label.innerHTML = fileNames.length > 2 ? `${fileNames[0]}, ${fileNames[1]}, +${fileNames.length - 2} more` : fileNames.join(', ');
  });
</script>

<script>
  function showTable(event) {
    event.preventDefault(); // Prevent default anchor behavior
    document.getElementById('agent-tables').style.display = 'block';
    document.getElementById('registration-form').style.display = 'none';
    document.getElementById('website-design').style.display = 'none';
    setActiveLink(event.currentTarget.id);
  }

  function showRegistration(event) {
    event.preventDefault(); // Prevent default anchor behavior
    document.getElementById('agent-tables').style.display = 'none';
    document.getElementById('registration-form').style.display = 'block';
    document.getElementById('website-design').style.display = 'none';
    setActiveLink(event.currentTarget.id);
  }

  function showWebsiteDesign(event) {
    event.preventDefault(); // Prevent default anchor behavior
    document.getElementById('agent-tables').style.display = 'none';
    document.getElementById('registration-form').style.display = 'none';
    document.getElementById('website-design').style.display = 'block';
    setActiveLink(event.currentTarget.id);
  }

  function setActiveLink(activeId) {
    const links = ['agentListLink', 'userListLink', 'agentRegistrationLink', 'websitedesignListLink'];
    links.forEach(link => {
      const element = document.getElementById(link);
      if (link === activeId) {
        element.classList.add('active');
      } else {
        element.classList.remove('active');
      }
    });
  }
</script>

<script>
  document.getElementById('mapButton').addEventListener('click', function () {
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



<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Get all the buttons
    const buttons = document.querySelectorAll('.btn-group .btn');

    // Form sections for different property types
    const landForm = document.getElementById('landForm');
    const singleAttachedHouseForm = document.getElementById('singleAttachedHouseForm');
    const singleDetachedHouseForm = document.getElementById('singleDetachedHouseForm');
    const rowhouseForm = document.getElementById('rowhouseForm');
    const apartmentForm = document.getElementById('apartmentForm');
    const villaForm = document.getElementById('villaForm');

    // Function to hide all forms and disable their fields
    function hideAllForms() {
      // Get all form sections
      const forms = [landForm, singleAttachedHouseForm, singleDetachedHouseForm, rowhouseForm, apartmentForm, villaForm];

      // Loop through all forms
      forms.forEach(form => {
        // Disable all input fields within the form
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
          input.disabled = true;
        });
        // Hide the form
        form.style.display = 'none';
      });
    }

    // Function to show the selected form and enable its fields
    function showForm(form) {
      // Hide all forms first and disable their fields
      hideAllForms();

      // Show the selected form and enable its fields
      form.style.display = 'block';
      const inputs = form.querySelectorAll('input, textarea, select');
      inputs.forEach(input => {
        input.disabled = false;  // Enable the fields of the currently visible form
      });
    }

    // Add event listeners to each button
    buttons.forEach(button => {
      button.addEventListener('click', function () {
        // Remove the "active" class from all buttons
        buttons.forEach(btn => btn.classList.remove('active'));

        // Add the "active" class to the clicked button
        this.classList.add('active');

        // Show the corresponding form based on the clicked button
        if (this.id === 'landBtn') {
          showForm(landForm);
        } else if (this.id === 'singleAttachedHouseBtn') {
          showForm(singleAttachedHouseForm);
        } else if (this.id === 'singleDetachedHouseBtn') {
          showForm(singleDetachedHouseForm);
        } else if (this.id === 'rowhouseBtn') {
          showForm(rowhouseForm);
        } else if (this.id === 'apartmentBtn') {
          showForm(apartmentForm);
        } else if (this.id === 'villaBtn') {
          showForm(villaForm);
        }
      });
    });

    // Initially, show the Land form
    showForm(landForm);
  });
</script>







<script>
  // Function to reload the iframe
  function reloadWebsite() {
    var iframe = document.getElementById('website-viewer');
    iframe.contentWindow.location.reload(); // Reload the page in the iframe
  }

  // Event listener for the reload button
  document.getElementById('reload-page-button').addEventListener('click', reloadWebsite);

  document.getElementById('website-viewer').style.transform = 'scale(0.8)';
  document.getElementById('website-viewer').style.transformOrigin = 'top left';
  document.getElementById('website-viewer').style.width = '125%';  // Increase width to compensate for zoom

  // Handle Form Submit for Appearance Customization
  document.getElementById('appearance-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const backgroundColor = document.getElementById('background-color').value;
    const primaryColor = document.getElementById('primary-color').value;
    const secondaryColor = document.getElementById('secondary-color').value;
    const fontFamily = document.getElementById('font-family').value;
    const fontSize = document.getElementById('font-size').value;
    const layoutWidth = document.getElementById('layout-width').value;
    const spacing = document.getElementById('spacing').value;

    const iframe = document.getElementById('website-viewer');
    const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;

    // Apply changes to iframe styles
    iframeDocument.body.style.backgroundColor = backgroundColor;
    iframeDocument.body.style.fontFamily = fontFamily;
    iframeDocument.body.style.fontSize = fontSize + 'px';
    iframeDocument.body.style.margin = spacing + 'px';

    // Apply layout width
    iframeDocument.documentElement.style.maxWidth = layoutWidth + 'px';

    // Apply primary and secondary colors (just for demo purposes)
    iframeDocument.querySelectorAll('a').forEach(link => {
      link.style.color = primaryColor;
    });

    // Apply additional styling (for example, change button background)
    iframeDocument.querySelectorAll('button').forEach(button => {
      button.style.backgroundColor = primaryColor;
    });

    alert("Changes applied to the iframe");
  });


</script>

</body>

</html>