<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Map Test</title>

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initAgentMap" async defer></script>

    <style>
        /* Floating Button */
        .floating-map-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(255, 255, 255, 0.9);
            color: #666;
            border: 1px solid #ddd;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        /* Map Panel */
        #agentMapPanel {
            display: none;
            position: fixed;
            top: 0;
            right: -100%;
            width: 50%;
            height: 100vh;
            background: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
            z-index: 999;
        }

        #agentMapPanel.active {
            display: block;
            right: 0;
        }

        /* Map Controls */
        .map-controls {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            gap: 10px;
            z-index: 1002;
        }

        .map-control-btn {
            background: white;
            border: none;
            border-radius: 4px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease-in-out;
        }

        .map-control-btn:hover {
            background: #f5f5f5;
            transform: translateY(-2px);
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>

    <!-- Floating Button -->
    <button id="agentMapButton" class="floating-map-btn">
        <i class="fas fa-map-marker-alt"></i>
    </button>

    <!-- Map Panel -->
    <div id="agentMapPanel">
        <div class="map-controls">
            <button class="map-control-btn" onclick="toggleAgentMapFullscreen()">
                <i class="fas fa-expand"></i>
            </button>
            <button class="map-control-btn" onclick="toggleAgentMap()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="agentMapContainer" style="height: 100vh;"></div>
    </div>

    <script>
        window.agentMap = null;

        function initAgentMap() {
            console.log("Initializing Map...");

            const caviteCenter = { lat: 14.2794, lng: 120.8786 };

            window.agentMap = new google.maps.Map(document.getElementById("agentMapContainer"), { 
                center: caviteCenter,
                zoom: 12
            });

            console.log("Map Initialized!");
        }

        // Toggle Map Panel
        function toggleAgentMap() {
            console.log("Toggling map panel...");
            const agentMapPanel = document.getElementById('agentMapPanel');
            
            if (!agentMapPanel) {
                console.error("Error: agentMapPanel not found!");
                return;
            }

            agentMapPanel.classList.toggle('active');
            console.log("New class list:", agentMapPanel.classList);

            if (window.agentMap) {
                setTimeout(() => {
                    google.maps.event.trigger(window.agentMap, 'resize');
                }, 300);
            }
        }

        // Toggle Fullscreen
        function toggleAgentMapFullscreen() {
            const agentMapPanel = document.getElementById('agentMapPanel');
            if (agentMapPanel) {
                agentMapPanel.classList.toggle('fullscreen');
            }
        }

        // Add event listener to button
        document.addEventListener('DOMContentLoaded', function () {
            console.log("DOM Loaded.");
            document.getElementById('agentMapButton').addEventListener('click', toggleAgentMap);
        });
    </script>

</body>
</html>
