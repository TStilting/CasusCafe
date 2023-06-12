<?php

session_start();

// Check for login
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
  header("location: login.php");
  exit;
}

include("connection.php");

// Redirect if the role is not admin
if ($_SESSION['rol'] != 'admin') {
  header("location: index.php");
  exit;
}

// Get the current date
$currentYear = date('Y');
$currentMonth = date('m');
$currentDay = date('d');

// Create an array for all the months
$months = array();
$months[] = $currentMonth; // Include the current month with today's date as the starting point

for ($b = 1; $b < 12; $b++) {
  if ($currentMonth >= 12) {
    $currentMonth = 1;
    $currentYear++;
  } else {
    $currentMonth++;
  }
  $months[] = $currentMonth;
}

$daycheck = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
$currentDate = (int) date('d');
$startDate = ($currentMonth == $currentMonth && $currentDate < $daycheck) ? $currentDate : 1;
$endDate = ($currentMonth == $currentMonth && $currentDate < $daycheck) ? $daycheck - 1 : $currentDate - 1;

$disabledDays = array();
if ($currentDate > 1) {
  $disabledDays = range(1, $currentDate - 1); // Create an array of days that have already passed
}

// Fetch band names from the database
$bandNames = array();
foreach ($pdo->query("SELECT * FROM Band") as $row) {
  $bandNames[] = $row['bandNaam'];
}

// Process the event submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $eventDate = $_POST['event_date'];
  $bandName = $_POST['band_name'];
  $hoofdAct = isset($_POST['hoofd_act']) ? 1 : 0;
  $eventTime = $_POST['event_time'];
  $eventDuration = $_POST['event_duration'];
  $entree = $_POST['entree'];

  // get bandId
  $bandId = $stmt = $pdo->prepare("SELECT bandId FROM Band WHERE bandNaam=(?)"); $stmt->execute([$bandName]);

  // Store the event in the database
  $stmt = $pdo->prepare("INSERT INTO event (datum, entreePrijs, begistijd) VALUES (?, ?, ?)");
  $stmt->execute([$eventDate, $entree ,$eventTime]);

  $lastInsertedId = $pdo->lastInsertId();

  $stmt = $pdo->prepare("INSERT INTO act (hoofdAct, Event_eventId, Band_bandId) VALUES (?, ?, ?)");
  $stmt->execute([$hoofdAct, $lastInsertedId, $bandId]);

  $lastInsertedActId = $pdo->lastInsertId();

  $stmt = $pdo->prepare("INSERT INTO set (duurInMinuten, Act_actId) VALUES (?, ?)");
  $stmt->execute([$eventDuration, $lastInsertedActId]);

}

// Retrieve events for the selected month
$selectedMonth = $currentMonth;
if (isset($_GET['month'])) {
  $selectedMonth = $_GET['month'];
}

$events = array();
$stmt = $pdo->prepare("SELECT * FROM event WHERE month = ?");
$stmt->execute([$selectedMonth]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Agenda</title>
</head>
<header>
    <a href="admin2.php">Band Toevoegen</a>
    <a href="index.php">Terug naar de Startpagina</a>
</header>

<body onload="checkMonth()">
  <h2>Add Event</h2>
  <form method="POST" action="">

    <label for="event_month">Datum:</label>
    <input type="date" id="event_date" name="event_date" onchange="checkMonth()" required>
    <br>

    <label for="band_name">Band Naam:</label>
    <select id="band_name" name="band_name">
      <?php foreach ($bandNames as $bandName) : ?>
        <option value="<?php echo $bandName; ?>"><?php echo $bandName; ?></option>
      <?php endforeach; ?>
    </select>
    <br>

    <label for="hoofd_act">Is het de hoofdact?</label>
    <input type="checkbox" id="hoofd_act" name="hoofd_act">
    <br>

    <label for="event_time">Start Tijd van het Event:</label>
    <input type="time" id="event_time" name="event_time" required>
    <br>

    <label for="event_duration">Duur (in minuten):</label>
    <input type="number" id="event_duration" name="event_duration" required>
    <br>

    <label for="entree">entreeprijs (in euro):</label>
    <input type="number" id="entree" name="entree" required>
    <br>

    <input type="submit" value="Add Event">
  </form>
  <div>
  <?php
    $stmt = $pdo->prepare("SELECT datum, Band.bandNaam, Act.hoofdAct, entreePrijs, beginstijd, Set.duurInMinuten
    FROM Event
    JOIN Act ON Event.eventId = Act.Event_eventId
    JOIN Band ON Act.Band_bandId = Band.bandId
    JOIN Set ON Act.ActId = Set.Act_actId");
    $stmt->execute();
  ?>
  </div>

  <script type="text/javascript">
    function checkMonth() {
      var month = document.getElementById("event_month").value;
      var daysInMonth = new Date(<?php echo $currentYear; ?>, month - 1, 0).getDate();

      var eventDaySelect = document.getElementById("event_day");
      var selectedDay = eventDaySelect.value; // Store the selected day value

      eventDaySelect.innerHTML = ""; // Clear the options

      for (var day = 1; day <= daysInMonth; day++) {
        var option = document.createElement("option");
        option.value = day;
        option.text = day;
        eventDaySelect.appendChild(option);

        // Set the selected day if it matches the previously selected day
        if (day == selectedDay) {
          option.selected = true;
        }
      }

      var eventMonthSelect = document.getElementById("event_month");
      eventMonthSelect.value = month; // Set the selected month
    }
  </script>
</body>
</html>