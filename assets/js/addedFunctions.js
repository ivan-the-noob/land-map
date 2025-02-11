maptilersdk.config.apiKey = "gLXa6ihZF9HF7keYdTHC";

const map = new maptilersdk.Map({
  container: "map",
  style: maptilersdk.MapStyle.HYBRID,
  geolocate: maptilersdk.GeolocationType.POINT,
  zoom: 10,
  maxZoom: 16.2,
});

let currentMarker = null; // Store a single marker

map.on("click", (event) => {
  const lngLat = event.lngLat;

  // If a marker already exists, just update its position
  if (currentMarker) {
    currentMarker.setLngLat(lngLat);
  } else {
    // Create a draggable marker
    currentMarker = new maptilersdk.Marker({ draggable: true })
      .setLngLat(lngLat)
      .addTo(map);

    // Update position when dragged
    currentMarker.on("dragend", () => {
      const newLngLat = currentMarker.getLngLat();
      updateHiddenInput(newLngLat);
    });

    // Show the buttons
    document.querySelector("#clear-marker").style.display = "flex";
  }

  updateHiddenInput(lngLat);
});

// Clear the marker
document.getElementById("clear-marker").addEventListener("click", () => {
  if (currentMarker) {
    currentMarker.remove();
    currentMarker = null;
  }

  // Hide the button
  document.querySelector("#clear-marker").style.display = "none";

  // Clear hidden input
  document.getElementById("coordinates").value = "";
});

// Update hidden input before form submission
function updateHiddenInput(lngLat) {
  document.getElementById("coordinates").value = JSON.stringify([lngLat.lng, lngLat.lat]);
}

// Change map style
document.getElementById("mapstyles").addEventListener("change", (e) => {
  const style_code = e.target.value.split(".");
  style_code.length === 2
    ? map.setStyle(maptilersdk.MapStyle[style_code[0]][style_code[1]])
    : map.setStyle(maptilersdk.MapStyle[style_code[0]]);
});
