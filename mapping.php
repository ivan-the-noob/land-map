<?php

class Index_map{
    private $username = 'root';
    private $password = '';
    public $pdo = NULL;

    public function __construct($crime = null, $date = null, $location = null, $latitude = null, $longitude = null){
        $this->crime = $crime;
        $this->date = $date;
        $this->location = $location;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function con(){
        try{
            $this->pdo = new PDO('mysql:host=localhost;dbname=chart_palaciodb', $this->username, $this->password);
        }
        catch(PDOException $e){
            die($e->getMessage());
        }
        return $this->pdo;
    }

    public function getMarkers(){
        $con = $this->con();
        $sql = "SELECT crime, location, latitude, longitude FROM piechart_tbl";
        $data = $con->prepare($sql);
        $data->execute();

        return $data->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertCrime(){
        $con = $this->con();
        $sql = "INSERT INTO `piechart_tbl` (`crime`, `date`, `location`, `latitude`, `longitude`) VALUES (?, ?, ?, ?, ?)";
        $data = $con->prepare($sql);
        $data->execute([$this->crime, $this->date, $this->location, $this->latitude, $this->longitude]);

        return $data->rowCount() > 0;
    }

    public function getCrimes(){
        $con = $this->con();
        $sql = "SELECT crime_name FROM crimes";
        $data = $con->prepare($sql);
        $data->execute();
        $result = $data->fetchAll(PDO::FETCH_ASSOC);
        if(count($result) > 0){
            foreach($result as $row){
                echo "<option>".$row["crime_name"]."</option>";
            }
        }
        else{
            echo "<option value=''> No Item Found </option>";
        }
        return $data;
    }

    public function getCity(){
        $con = $this->con();
        $sql = "SELECT city FROM tbl_city";
        $data = $con->prepare($sql);
        $data->execute();
        $result = $data->fetchAll(PDO::FETCH_ASSOC);

        if(count($result) > 0){
            foreach($result as $row){
                echo "<option>".$row["city"]."</option>";
            }
        }
        else{
            echo "<option value=''> No City Found </option>";
        }
        return $data;
    }

    public function viewChart(){
        $con = $this->con();
        $sql = "SELECT crime, COUNT(*) AS CrimeCount FROM piechart_tbl GROUP BY crime";
        $data = $con->prepare($sql);
        $data->execute();
        $data_array = array();
        $total_count = 0;
        while($row = $data->fetch(PDO::FETCH_ASSOC)){
            $total_count += $row["CrimeCount"];
        }
        $data->execute();

        while($row = $data->fetch(PDO::FETCH_ASSOC)){
            $percentage = ($row["CrimeCount"] / $total_count) * 100;
            $data_array[] = array($row["crime"], round($percentage,2));
        }
        $data_json = json_encode($data_array);
        return $data_json;
    }

    public function insertCrimes(){
        if(!empty($_GET['crime']) && !empty($_GET['date']) && !empty($_GET['location']) && !empty($_GET['lat']) && !empty($_GET['lng'])){
            $this->crime = $_GET['crime'];
            $this->date = $_GET['date'];
            $this->location = $_GET['location'];
            $this->latitude = $_GET['lat'];
            $this->longitude = $_GET['lng'];
            if($this->insertCrime()){
                echo "Save Successfully";
            }
            else{
                echo "Failed";
            }
            header("Location: index.php");
            exit();
        }
    }

    public function viewCharts(){
        return $this->viewChart();
    }

    public function getCrimeDropdown(){
        return $this->getCrimes();
    }

    public function getCityDropdown(){
        return $this->getCity();
    }
}

$index = new Index_map();

if($_SERVER['REQUEST_METHOD'] === 'GET'){
    $index->insertCrimes();
}
$data_json = $index->viewCharts();
$markers = $index->getMarkers();

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>Crime | Cavite Map</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.maptiler.com/maptiler-sdk-js/v2.0.3/maptiler-sdk.umd.js"></script>
    <link href="https://cdn.maptiler.com/maptiler-sdk-js/v2.0.3/maptiler-sdk.css" rel="stylesheet" />
    <script src="https://cdn.maptiler.com/leaflet-maptilersdk/v2.0.0/leaflet-maptilersdk.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data_array = <?php echo isset($data_json) ? $data_json : '[]'; ?>;
            var data = google.visualization.arrayToDataTable([
                ['Crimes', 'Count'],
                <?php
                    if (isset($data_json)){
                        $data_array = json_decode($data_json,true);
                        foreach ($data_array as $crime) {
                            echo "['" .$crime[0] ."',".$crime[1]."],";
                        }
                    }
                ?>
            ]);

            var options = {
                title: 'Crime Reports'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));
            chart.draw(data, options);
        }
    </script>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['bar']});
        google.charts.setOnLoadCallback(drawStuff);

        function drawStuff() {
            var data = new google.visualization.arrayToDataTable([
                ['Crimes', 'Percentage'],
                <?php
                    if (isset($data_json)) {
                        $data_array = json_decode($data_json, true);
                        foreach($data_array as $crime){
                            echo "['" . $crime[0] ."', " . $crime[1] . "],";
                        }
                    }
                ?>
            ]);

            var options = {
                title: 'Crime Reports',
                width: 900,
                legend: {position: 'none'},
                chart: {title: 'Bar Graph Crime Report',
                        subtitle: 'Crime By Percentage'},
                bars: 'horizontal',
                axes: {
                    x: {
                        0: { side:'top', label:'percentage'}
                    }
                },
                bar: {groupwidth: '90%'}
            };

            var chart = new google.charts.Bar(document.getElementById('top_x_div'));
            chart.draw(data, options);
        };
    </script>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark shadow">
        <span class="navbar-brand mb-0 h1">Cavite | Chart And Graph</span>
    </nav>
    <div class="container mt-3">
        <div class="row">
            <div class="col-sm-3">
                <form action="" method="GET">
                    <div class="form-group">
                        <label for="task">Insert Crime</label>
                        <select name="crime" id="crime" class="form-control mt-0">
                            <?php $index->getCrimeDropdown(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Insert Date</label>
                        <input type="date" name="date" class="form-control mt-0" placeholder="Insert Date" required>
                    </div>
                    <div class="form-group">
                        <label for="task">Insert Location</label>
                        <select name="location" id="location" class="form-control mt-0">
                            <?php $index->getCityDropdown(); ?>
                        </select>
                        <label for="task">Latitude</label>
                        <input type="text" name="lat" id="lat" class="form-control mt-0">
                        <label for="task">Longitude</label>
                        <input type="text" name="lng" id="lng" class="form-control mt-0">
                    </div>
                    <input class="btn btn-success form-control mt-2" type="submit" value="Add Task" >
                </form>
            </div>
            <div class="col-md-6">
                <div id="map" class="form-control" style="height: 500px; width: 100%;"></div>
            </div>
            <div class="col-md-6">
                <div id="piechart" class="form-control mt-2" style="width: 100%; height: 570px;"></div>
                <div id="top_x_div" class="form-control mt-2" style="width: 100%; height: 570px;"></div>
            </div>
        </div>
    </div>

    <script>
        const key = 'ABhwglGTsyHdHuulaG2t';
        const map = L.map('map').setView([14.2456, 120.8786], 10);
        const mtLayer = L.maptilerLayer({
            apiKey: key,
            style: "basic-v2",
        }).addTo(map);

        <?php foreach($markers as $marker): ?>
            L.marker([<?= $marker['latitude'] ?>, <?= $marker['longitude'] ?>])
              .addTo(map)
              .bindPopup("<div style='text-align: center;'><b><?= $marker['crime'] ?></b><br><?= $marker['location'] ?>, Cavite</div>");
        <?php endforeach; ?>
    </script>

    <script type="text/javascript">
        async function getCoordinates(address){
            const apiKey = "ABhwglGTsyHdHuulaG2t";
            const url = `https://api.maptiler.com/geocoding/${encodeURIComponent(address + ",cavite")}.json?key=${apiKey}`;

            try {
                const response = await fetch(url);
                if(!response.ok){
                    if(response.status === 401){
                        throw new Error('Invalid API key');
                    } else {
                        throw new Error(`Error: ${response.status} ${response.statusText}`);
                    }
                }

                const data = await response.json();
                console.log('API response:', data);

                if (data.features && data.features.length > 0){
                    const [longitude, latitude] = data.features[0].geometry.coordinates;
                    return { latitude, longitude };
                } else {
                    throw new Error('No Results found');
                }
            } catch (error) {
                console.error(error);
                alert(error.message);
                return null;
            }
        }

        document.getElementById('location').addEventListener('change', async (event) => {
            const address = event.target.value;

            if(address){
                const coords = await getCoordinates(address);
                if (coords) {
                    document.getElementById('lat').value = coords.latitude;
                    document.getElementById('lng').value = coords.longitude;
                } else {
                    document.getElementById('lat').value = 'N/A';
                    document.getElementById('lng').value = 'N/A';
                }
            } else {
                document.getElementById('lat').value = '';
                document.getElementById('lng').value = '';
            }
        });
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
