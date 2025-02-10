<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-90680653-2"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-90680653-2');
    </script>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Land Map | <?php echo "$title"?></title> 
    <link rel="icon" href="../assets/images/logo.png" type="image/x-icon">

    <link rel="icon" href="../../assets/images/logo.png" type="image/x-icon">

    <!-- vendor css -->
    <link href="../../assets/lib/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="../../assets/lib/ionicons/css/ionicons.min.css" rel="stylesheet">
    <link href="../../assets/lib/typicons.font/typicons.css" rel="stylesheet">
    <link href="../../assets/lib/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">

    <!-- Mapping Links -->
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.3.0/maptiler-sdk.css" rel="stylesheet" />

    <!-- azia CSS -->
    <link rel="stylesheet" href="../../assets/css/azia.css">

</head>
<body>

<div class="az-header">
    <?php require "../../partials/nav_home.php" ?>
</div>

<div class="container-unique">
    <div class="profile-header-unique">
    <img alt="Profile picture real estate agent" height="100" src="" width="100" />
        <h1>
            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </h1>
        <p>
            Senior Real Estate Agent
        </p>
        <div class="rating-unique">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <span>
                4.5/5
            </span>
        </div>
        <a class="edit-profile-unique" href="/editprofile">
            Edit Profile
        </a>
        <div class="bio-unique">
            <p>
                John Doe is a seasoned real estate agent with over 15 years of experience in the industry. He specializes in residential properties and is known for his exceptional customer service and market knowledge. John has helped hundreds of clients find their dream homes and is dedicated to making the buying and selling process as smooth as possible.
            </p>
        </div>
    </div>
    <div class="contact-info-unique">
        <div>
            <i class="fas fa-phone-alt">
            </i>
            <p>
                <?php echo isset($_SESSION['phone_number']) && $_SESSION['phone_number'] ? $_SESSION['phone_number'] : 'N/A'; ?>
            </p>
        </div>
        <div>
            <i class="fas fa-envelope">
            </i>
            <p>
                <?php echo htmlspecialchars($_SESSION['email']); ?>
            </p>
        </div>
        <div>
            <i class="fas fa-map-marker-alt">
            </i>
            <p>
                123 Main St, Anytown, USA
            </p>
        </div>
    </div>
    <div class="listed-properties-unique">
        <h2>
            Listed Properties
        </h2>
        <div class="property-category-unique">
            <select>
                <option value="all">
                    All Categories
                </option>
                <option value="house">
                    House
                </option>
                <option value="apartment">
                    Apartment
                </option>
                <option value="villa">
                    Villa
                </option>
                <option value="condo">
                    Condo
                </option>
                <option value="cabin">
                    Cabin
                </option>
            </select>
        </div>
        <div class="listings-unique">
            <div class="listing-unique">
                <img alt="Image of a modern house with a large garden" height="200" src="https://storage.googleapis.com/a1aa/image/VODOf5UWx1UqfkAKHj8aHC89iczxKwfYxGA2J7WG4TBsJdFoA.jpg" width="300" />
                <div class="listing-details-unique">
                    <h3>
                        Modern House
                    </h3>
                    <p>
                        3 Beds, 2 Baths, 2000 sqft
                    </p>
                </div>
            </div>
            <div class="listing-unique">
                <img alt="Image of a cozy apartment with a city view" height="200" src="https://storage.googleapis.com/a1aa/image/0OjQtQQ0nqJPDV79PmMEdjJCiRfFmNjcwvJnrGyu65QgSXBKA.jpg" width="300" />
                <div class="listing-details-unique">
                    <h3>
                        Cozy Apartment
                    </h3>
                    <p>
                        2 Beds, 1 Bath, 1200 sqft
                    </p>
                </div>
            </div>
            <div class="listing-unique">
                <img alt="Image of a luxurious villa with a swimming pool" height="200" src="https://storage.googleapis.com/a1aa/image/H68aefq4vltQ3EASR2S4mPEtTfKhMf7FRYfyfdFt8zMJNprAF.jpg" width="300" />
                <div class="listing-details-unique">
                    <h3>
                        Luxurious Villa
                    </h3>
                    <p>
                        5 Beds, 4 Baths, 3500 sqft
                    </p>
                </div>
            </div>
            <div class="listing-unique">
                <img alt="Image of a suburban house with a white picket fence" height="200" src="https://storage.googleapis.com/a1aa/image/w1GkDVaZ5dpXMtQeZfjQQomZeVKfeKPrO6MmEQy988TTn0VgC.jpg" width="300" />
                <div class="listing-details-unique">
                    <h3>
                        Suburban House
                    </h3>
                    <p>
                        4 Beds, 3 Baths, 2500 sqft
                    </p>
                </div>
            </div>
            <div class="listing-unique">
                <img alt="Image of a downtown condo with modern amenities" height="200" src="https://storage.googleapis.com/a1aa/image/fZixjnFKarWnHqGFpdzE4WnT3aNeV2uGw8meuUgRyLm8JdFoA.jpg" width="300" />
                <div class="listing-details-unique">
                    <h3>
                        Downtown Condo
                    </h3>
                    <p>
                        1 Bed, 1 Bath, 900 sqft
                    </p>
                </div>
            </div>
            <div class="listing-unique">
                <img alt="Image of a rustic cabin in the woods" height="200" src="https://storage.googleapis.com/a1aa/image/gWIVzI6rVxIACp0f2yOfoyCgnqAMba1t2rU8CLbhjTR8kuCUA.jpg" width="300" />
                <div class="listing-details-unique">
                    <h3>
                        Rustic Cabin
                    </h3>
                    <p>
                        2 Beds, 1 Bath, 1500 sqft
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

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