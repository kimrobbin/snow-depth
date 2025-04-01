<?php
include 'connect.php';

$sted = [["Hamar",284003, 6745503], ["Bodø", 475400, 7461774], ["Nordkapp", 885695, 7924393]];
$antall = count($sted);

$directory = './csv/';
if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}

for ($x = 0; $x < $antall; $x++) {
    for ($i = 0; $i < 7; $i++) {
        $dato = date('d-m-y', strtotime("-$i days"));
        $url = "https://gts.nve.no/api/GridTimeSeries/" . $sted[$x][1] . "/" . $sted[$x][2] . "/$dato/$dato/sd.csv";
        $file_name = $directory . "dybde_" . $sted[$x][0] . "_" . $dato . ".csv";

        $file_content = file_get_contents($url);
        if ($file_content !== false) {
            if (file_put_contents($file_name, $file_content) === false) {
                error_log("Failed to write file: $file_name");
            }
        } else {
            error_log("Failed to download file for " . $sted[$x][0] . " on " . $dato . ".");
        }
    }
}

$files = glob($directory . 'dybde_*.csv');

foreach ($files as $file) {
    $innhold = fopen($file, "r") or die("Unable to open file!");

    
    fgets($innhold);

    while (($line = fgets($innhold)) !== false) {
        $data = explode(';', $line);

        // Fix date conversion
        $dateObj = DateTime::createFromFormat('d.m.Y H:i:s', trim($data[0]));
        if ($dateObj) {
            $date = $dateObj->format('Y-m-d H:i:s');
        } else {
            continue; 
        }

        $depth = floatval($data[1]);
        $location = explode('_', basename($file))[1];

        $sql = "INSERT INTO snow_depth (location, date, depth) VALUES (:location, :date, :depth)
                ON DUPLICATE KEY UPDATE depth = :depth";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':depth', $depth);

        $stmt->execute();
    }

    fclose($innhold);
}

$conn = null;






header("Location: index.php");
exit();
?>