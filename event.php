<?php

include("connection.php");

// Get the event ID from the URL parameter
if (isset($_GET['event_id'])) {
  $eventId = $_GET['event_id'];
} else {
  // If the event ID is not provided, redirect to the events page
  header("location: index.php");
  exit;
}

// Retrieve the event details
$stmt = $pdo->prepare("SELECT Event.eventId, Event.naam, DATE_FORMAT(Event.beginstijd, '%d-%m-%Y %H:%i') AS beginstijd, DATE_FORMAT(Event.eindtijd, '%d-%m-%Y %H:%i') AS eindtijd,
Event.omschrijving, GROUP_CONCAT(CONCAT('<a href=\"band.php?band_id=', Band.bandId, '\">', Band.bandNaam, '</a>') SEPARATOR ', ') AS bandNamen,
entreePrijs
FROM Event
JOIN Act ON Event.eventId = Act.Event_eventId
JOIN Band ON Act.Bands_bandId = Band.bandId
WHERE Event.eventId = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);


// If the event does not exist, redirect to the events page
if (!$event) {
  header("location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="casuscss.css">
  <title>Event Details</title>
</head>
<header>
  <a href="index.php">Bekijk meer events</a>
</header>

<body>
  <div class="container">
    <div class="display-details">
      <h1><?php echo $event['naam']; ?></h1>
      <p class="sub-detail"><b>Van:</b> <?php echo $event['beginstijd']; ?> <b>Tot:</b> <?php echo $event['eindtijd']; ?></p>
      <p class="sub-detail"><b>Bands:</b> <?php echo $event['bandNamen']; ?></p>
      <p class="omschrijving"><?php echo $event['omschrijving']; ?></p>
      </div>
      <a class="payout-button" href="payout.php?event_id=<?php echo $eventId; ?>">
      Entree prijs (per kaartje): € <?php echo $event['entreePrijs']; ?>
      </a>
</body>
</html>
