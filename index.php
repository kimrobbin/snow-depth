<?php
include 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snow Data</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Snow Data</h1>
    <form method="post" action="dybde.php">
        <button type="submit">Fetch New Data</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Location</th>
                <th>Date</th>
                <th>Depth (cm)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT location, date, depth FROM snow_depth ORDER BY location DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $row) {
                echo "<tr>";
                echo "<td>" . $row['location'] . "</td>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['depth'] . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
<?php
$conn = null;
?>