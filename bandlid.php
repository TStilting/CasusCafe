<?php

include("connection.php");

// Get the bandlid ID from the URL parameter
if (isset($_GET['bandlid_id'])) {
  $bandlidId = $_GET['bandlid_id'];
} else {
  // If the bandlid ID is not provided, redirect to the events page
  header("location: index.php");
  exit;
}

// Retrieve the bandlid details
$stmt = $pdo->prepare("SELECT Lineup.naam, Lineup.tussenvoegsel, Lineup.achternaam, CONCAT('<a href=\"band.php?band_id=', Band.bandId, '\">', Band.bandNaam, '</a>') as bandNaam, Lineup.omschrijving, Lineup.foto
FROM Lineup
JOIN Band ON Lineup.Band_bandId = Band.bandId
WHERE Lineup.bandlidId = ?");
$stmt->execute([$bandlidId]);
$bandlid = $stmt->fetch(PDO::FETCH_ASSOC);

// If the bandlid does not exist, redirect to the events page
if (!$bandlid) {
  header("location: index.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="casuscss.css">
  <title>Bandlid Details</title>
</head>
<header>
  <a href="index.php">Terug naar de event lijst</a>
</header>
<body>
<div class="container">
  <div class="display-details">
  <h1><?php echo $bandlid['naam']; ?><?php if (!empty($bandlid['tussenvoegsel'])) echo ' ' . $bandlid['tussenvoegsel']; ?><?php echo ' '.$bandlid['achternaam']; ?></h1>
  <p class="sub-detail"><b>Band:</b> <?php echo $bandlid['bandNaam']; ?></p>
  <p><?php echo $bandlid['omschrijving']; ?></p>
  <p class="center"><img src="uploads/<?php echo $bandlid['foto']; ?>" alt="Foto van <?php echo $bandlid['naam'].' '.$bandlid['tussenvoegsel'].' '.$bandlid['achternaam']; ?>"></p>
</body>
</html>