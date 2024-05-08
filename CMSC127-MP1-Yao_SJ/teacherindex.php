<?php
	//DATABASE PARAMETERS
	$user = 'root';
	$password = 'YAOshawjie122002@';
	$dsn = "mysql:host=localhost:3307;dbname=thirdyear_courses";

	try {
		$pdo = new PDO($dsn, $user, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $ex) {
		echo "Connection failed: " . $ex->getMessage();
	}
	
	if (($_SERVER["REQUEST_METHOD"] == "POST") && isset($_POST["emailaddress"])) {
		// Get user input
		$inputemailaddress = $_POST["emailaddress"];
		$inputPassword = $_POST["password"];

		// Query the database to check credentials
		$sql = "SELECT * FROM teachers WHERE Email_Address = '$inputemailaddress' AND Password = '$inputPassword' AND ADMIN = FALSE"; 
		$result = $pdo->query($sql);
		

		// Using rowCount() to get the number of rows
		$user = $result->fetch(PDO::FETCH_ASSOC);

		if ($user) {
			// User exists, set session and redirect
			$_SESSION["user"] = [
				'emailaddress' => $inputemailaddress,
			];

			// Redirect to the next PHP file if credentials are correct
			header("Location: thirdyear_courses.php");
			exit();
		} else {
			$error = "&emsp;&emsp;Invalid Email Address or password. Please try again.";
		}
	}
	elseif (($_SERVER["REQUEST_METHOD"] == "POST") && !isset($_POST["emailaddress"])){
		 header("Location: thirdyear_courses.php");
			exit();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="thirdyear_courses.css" />
    <title>Teacher Page</title>
</head>
<body>
	<br/><h1>TEACHER</h1><br/>
	
	<div class = "container">
		<img src="UP-System-UP-Cebu-Logo.png" alt="UP Logo">
	</div>	

    <?php
    if (isset($error)) {
        echo "<p style='color: red;'>$error</p>";
    }
    ?>

    <form method="post" action="" id="form1" class = "otherforms">
	
        <br/><label for="emailaddress">Email Address:</label>
        <input type="text" id="emailaddress" name="emailaddress" required><br/>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br/><br/>

        <input type="submit" value="Login" name="form1">
		
    </form>
	&emsp;&emsp;<button onclick="window.location.href='student_courses.php'">Go Back</button>
</body>
</html>

