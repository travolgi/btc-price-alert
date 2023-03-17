<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Travolgi">
	<title>Bitcoin Price Alert</title>
	<meta name="description" content="Bitcoin Price Alert: receive an email when the price of bitcoin is higher or lower than the amount you set.">
	<style>
		* {
			padding: 0;
			margin: 0;
			border: 0;
			outline: none;
			list-style: none;
			box-sizing: border-box;
		}
		body {
			min-height: 100vh;
			display: grid;
			place-content: center;
			place-items: center;
			gap: 2rem;
			padding: 1rem;
			background: #202124;
			color: #e8eaed;
		}
		h1 {
			color: #81c995;
		}
		h3 {
			color: #e54242;
		}
	</style>
</head>
<body>

	<?php
	$minPrice = 18000;
	$maxPrice = 26000;
	$currencyCode = 'eur';
	$currency = strtoupper($currencyCode);

	$to = $_ENV['ALERT_EMAIL'];
	$headers = "From: btc-alert@travolgi.com\r\n";
	$headers .= 'Content-Type: text/html; charset=UTF-8';

	$emailSent = false;

	$url = "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=$currencyCode";
	$response = file_get_contents($url);

	if ($response) {
		$data = json_decode($response, true);
		$realtimePrice = $data['bitcoin'][$currencyCode];
		$subject = 'Bitcoin Price Alert';
		
		echo "<p>Min: $minPrice<br>Max: $maxPrice</p>";
		echo "<h1>Bitcoin price: $realtimePrice $currency</h1>";

		if ($realtimePrice > $maxPrice && !$emailSent) {
			$message = "The price of Bitcoin is currently $realtimePrice $currency: above $maxPrice $currency.";
			echo "<p>$message</p>";
			mail($to, $subject, $message, $headers);
			$emailSent = true;
		} elseif ($realtimePrice < $minPrice && !$emailSent) {
			$message = "The price of Bitcoin is currently $realtimePrice $currency: below $minPrice $currency.";
			echo "<p>$message</p>";
			mail($to, $subject, $message, $headers);
			$emailSent = true;
		} elseif ($realtimePrice <= $maxPrice && $realtimePrice >= $minPrice && $emailSent) {
			$emailSent = false;
		}
	} else {
		echo '<h3>Too Many Requests. Try later.</h3>';
	}

	?>

</body>
</html>