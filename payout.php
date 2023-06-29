<?php
session_start();

include("connection.php");

// Get the event ID from the URL parameter
if (isset($_GET['event_id'])) {
  $eventId = $_GET['event_id'];
} else {
  // If the event ID is not provided, redirect to the events page
  echo "Er is een fout opgetreden";
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM Event WHERE eventId = ?");
$stmt->execute([$eventId]);
$eventDetails = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $naam = $_POST['name'];
  $email = $_POST['email'];
  $aantal = $_POST['aantal'];

  // Store the order in the database
  $stmt = $pdo->prepare("INSERT INTO Kaartbestelling (naam, email, kaartAantal, Event_eventId) VALUES (?, ?, ?, ?)");
  $stmt->execute([$naam, $email, $aantal, $eventId]);

  // Display a success message
  $successMessage = "Uw kaartjes zijn besteld";

  // Clear the form values
  $name = "";
  $email = "";
  $aantal = "";
}
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="casuscss.css">
  <title>Payout</title>
</head>
<header>
  <a href="index.php">Bekijk meer events</a>
</header>
<body>
  <div class="container">
    <h1 class="center">Bestel Kaartjes</h1>

    <?php if (isset($successMessage)) : ?>
      <p class="success-message"><?php echo $successMessage; ?></p>
    <?php else : ?>
      <p class="center">Event: <?php echo $eventDetails['naam']; ?></p>
      <p class="center">Entree Prijs (per kaartje): <?php echo $eventDetails['entreePrijs']; ?></p>
      <form class="payout-form" method="POST" action="payout.php?event_id=<?php echo $eventId; ?>">
        <label for="name">Naam:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="aantal">Aantal Kaartjes:</label>
        <input type="number" id="aantal" name="aantal" required>

        <input type="submit" value="Bestellen">
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
