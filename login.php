<?php

$servername = "localhost";
$username = "root";
$password = "";


$conn = new PDO("mysql:host=$servername;dbname=CasusCafe", $username, $password);


$zin = inloggen();

function inloggen() {
    session_start();
    if (isset($_POST['Submit'])) {
        $email = isset($_POST['email']) ? $_POST['email'] : "";
        if (isset($_POST['wachtwoord'])){
            $wachtwoord =$_POST['wachtwoord'] ;
        }

        global $conn;
        if ($email != "" && $wachtwoord != ""){
            $query = "select count(*) from logins where email='".$email."' and wachtwoord='".$wachtwoord."'";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $resultaat = $stmt->fetch();
                if ($resultaat[0] == 1) {
                    return "<p>Toegang Verleend</p>";
                    return "";
                } else {
                    return "<p>Geen Toegang</p>";
                    return "";
                }
        } else {
            return "<p>Vul alles in</p>";
        }

    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<form action="" method="post" name="Login_Form">
    <table width="400" border="0" align="center" cellpadding="5" cellspacing="1" class="Table">
        <?php if(isset($zin)){?>
            <tr>
                <td colspan="2" align="center" valign="top"><?php print $zin;?></td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="2" align="left" valign="top"><h3>Login</h3></td>
        </tr>
        <tr>
            <td align="right" valign="top">Email</td>
            <td><input name="email" type="text" class="Input"></td>
        </tr>
        <tr>
            <td align="right">Wachtwoord</td>
            <td><input name="wachtwoord" type="password" class="Input"></td>
        </tr>
        <tr>
            <td></td>
            <td><input name="Submit" type="submit" value="Login" class="Button"></td>
        </tr>
    </table>
</form>
</body>
</html>