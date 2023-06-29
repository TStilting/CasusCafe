<!doctype html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="casuscss.css">
  <title>CasusCafe Agenda</title>
</head>
<header>
  <a href="login.php">login</a>
</header>
<body>
<div>
  <div class="center">
  <h1>Events</h1>
  </div>
  <?php
  include("connection.php");

  // Retrieve all events with associated bands
  $stmt = $pdo->query("SELECT Event.eventId, DATE_FORMAT(Event.beginstijd, '%d-%m-%Y %H:%i') AS beginstijd, DATE_FORMAT(Event.eindtijd, '%d-%m-%Y %H:%i') AS eindtijd,
  Event.naam, Event.omschrijving, GROUP_CONCAT(Band.bandNaam SEPARATOR ', ') AS bandNamen, Event.entreePrijs
  FROM Event
  JOIN Act ON Event.eventId = Act.Event_eventId
  JOIN Band ON Act.Bands_bandId = Band.bandId GROUP BY Event.eventId
  ORDER BY beginstijd;");
  $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($events) > 0) {
    foreach ($events as $event) {
      $eventId = $event['eventId'];
      echo '<a href="event.php?event_id=' . $eventId . '">';
      echo '<div class="event">';
      echo '<div class="details">';
      echo '<p><b>Naam:</b> ' . $event['naam'] . '</p>';
      echo '<p>' . $event['omschrijving'] . '</p>';
      echo '<p><b>Van:</b> ' . $event['beginstijd'] . ' <b>Tot:</b> ' . $event['eindtijd'] . '</p>';
      echo '<p><b>Bands:</b> ' . $event['bandNamen'] . '</p>';
      echo '</div>';
      echo '<div class="payout-div">';
      echo '<a class="payout-button" href="payout.php?event_id=' . $eventId . '">Entree prijs (per kaartje): € '. $event['entreePrijs'] . '</a>';
      echo '</div>';
      echo '</div>';
      echo '</a>';
    }
  } else {
    echo '<div class="display-details">';
    echo '<p class="omschrijving">Er zijn op dit moment geen events gepland</p>';
    echo '</div>';
  }
  ?>
</div>
</body>
</html>
