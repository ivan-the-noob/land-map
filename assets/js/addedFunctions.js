maptilersdk.config.apiKey = "gLXa6ihZF9HF7keYdTHC";

const map = new maptilersdk.Map({
  container: "map",
  style: maptilersdk.MapStyle.HYBRID,
  geolocate: maptilersdk.GeolocationType.POINT,
  zoom: 10,
  maxZoom: 16.2,
});

let points = [];
let markers = [];
let polygonLayerId = "polygon-fill";

map.on("click", (event) => {
  points.push([event.lngLat.lng, event.lngLat.lat]);

  // DRAGGABLE MARKER CODE
  const marker = new maptilersdk.Marker({ draggable: true })
    .setLngLat(event.lngLat)
    .addTo(map);

  // EVENT LISTENER TO UPDATE WHERE THE USER POINT THE MARKER
  marker.on("dragend", () => {
    const lngLat = marker.getLngLat();
    points[markers.indexOf(marker)] = [lngLat.lng, lngLat.lat]; // UPDATES THE CORRESPONDING POINTS
    updatePolygon(); // THIS UPDATE THE POLYGON
  });

  markers.push(marker); // Store the marker
  drawPolygon(); // Draw or update the polygon with the new point

  // Show the button container when the first point is created
  document.querySelector("#undo-last").style.display = "flex";
  document.querySelector("#clear-all").style.display = "flex";
});

function drawPolygon() {
  const polygon = {
    type: "Feature",
    geometry: {
      type: "Polygon",
      coordinates: [[...points, points[0]]], // Close the polygon
    },
  };

  // Add polygon source only once
  if (!map.getSource("custom-polygon")) {
    map.addSource("custom-polygon", {
      type: "geojson",
      data: polygon,
    });

    map.addLayer({
      id: polygonLayerId,
      type: "fill",
      source: "custom-polygon",
      layout: {},
      paint: {
        "fill-color": "#dc3545", // Change to desired color
        "fill-opacity": 0.6,
      },
    });
  } else {
    // Update the existing polygon data
    map.getSource("custom-polygon").setData(polygon);
  }
}

function updatePolygon() {
  const polygon = {
    type: "Feature",
    geometry: {
      type: "Polygon",
      coordinates: [[...points, points[0]]], // Close the polygon
    },
  };

  map.getSource("custom-polygon").setData(polygon); // Update the polygon data
}

// Undo last point
document.getElementById("undo-last").addEventListener("click", () => {
  if (points.length > 0) {
    points.pop(); // Remove the last point
    const lastMarker = markers.pop(); // Remove the last marker
    lastMarker.remove(); // Remove the marker from the map
    updatePolygon(); // Update the polygon

    // Hide the button container if no points are left
    if (points.length === 0) {
      document.querySelector("#undo-last").style.display = "none";
      document.querySelector("#clear-all").style.display = "none";
    }
  }
});

// Clear all points
document.getElementById("clear-all").addEventListener("click", () => {
  points = [];
  markers.forEach((marker) => marker.remove()); // Remove all markers
  markers = []; // Clear the markers array
  if (map.getSource("custom-polygon")) {
    map.removeLayer(polygonLayerId); // Remove the fill layer
    map.removeSource("custom-polygon"); // Remove the polygon source
  }

  // Hide the button container
  document.querySelector("#undo-last").style.display = "none";
  document.querySelector("#clear-all").style.display = "none";
});

// Update hidden input before form submission
document.querySelector("form").addEventListener("submit", function () {
  document.getElementById("coordinates").value = JSON.stringify(points);
});

document.getElementById("mapstyles").addEventListener("change", (e) => {
  const style_code = e.target.value.split(".");
  style_code.length === 2
    ? map.setStyle(maptilersdk.MapStyle[style_code[0]][style_code[1]])
    : map.setStyle(maptilersdk.MapStyle[style_code[0]]);
});
