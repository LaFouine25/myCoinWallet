<?php
session_start();
require_once('includes/config.php');
require_once('includes/jsonRPCClient.php');
require_once('includes/bcfunctions.php');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?php printf(SITENAME);?> - Withdraw</title>
		<link rel="stylesheet" href="css/styles.css"  type="text/css" />
	</head>
	<body>
		<div id="main">
			<div id="top"><div style='float:left;position:relative;top:25px;'><h2><?php printf(SITENAME);?></h2></div></div>
			<div id="wrapper">
				<div id="content">
					<div class="innermargin">
						<h1><?php printf(SITENAME);?> Withdraw</h1>
						<br />
							<?php
							
							$bitcoin = new jsonRPCClient('http://' . USER . ':' . PASS . '@' . SERVER . ':' . PORT .'/',false);
							
							// check for session address
							if(isset($_SESSION['sendaddress'])) {
								$sendaddress = refreshAddressIfStale($bitcoin,$_SESSION['sendaddress']); // session exists, check if its been used before
								$_SESSION['sendaddress'] = $sendaddress;
							} else {
								// if address already exists in wallet (or new unfortunately), check the balance and set as main receivable address if zero
								$curaddress = $bitcoin->getaccountaddress($_SESSION['username']);
								$sendaddress = refreshAddressIfStale($bitcoin,$curaddress);
								$_SESSION['sendaddress'] = $sendaddress;
							}
							
							// save current balance
							saveCurrentBalance($bitcoin, $_SESSION['sendaddress']);
							
							$userBalance = $_SESSION['userbalance'];
							
							// check for post request
							if(isset($_POST['sendaddress'])) {
								if(isset($_POST['sendamount'])) {
									$postSendAddress = $_POST['sendaddress'];
									$postSendAmount = $_POST['sendamount'];
									//echo $postSendAddress;
									//echo $postSendAmount;
									
									if($postSendAmount > $_SESSION['userbalance']) { // they tried to send more money than they have this is possible as accounts can go negative
										echo "<font color='red'><b>Vous ne pouvez pas envoyer de Coins, votre compte est vide!</b></font>";
									} elseif($postSendAmount < 0) {	// they tried to send a negative number
										echo "<font color='red'><b>Essayez d'être positif.</b></font>";
									} else {	// probably should be more error checking here, todo or something
										$transid = $bitcoin->sendfrom($_SESSION['username'], $postSendAddress, floatval($postSendAmount), 6); // require minimum 6 confirmations of credit
										if(isset($transid)) {
											echo "<font color='red'><b>Fond correctement envoyés.</b></font><br />";
											echo "Transaction Id: ". $transid . "<br />";
										}
									}
								}
							}
							
							// save current balance
							saveCurrentBalance($bitcoin, $_SESSION['sendaddress']);
							
							$userBalance = $_SESSION['userbalance'];
							
							echo "Current Balance: ". $userBalance ."<br />";
							
							// echo send form
							echo "Actuellement, ";
							if($userBalance > 0) {
								echo "Vous pouvez envoyer des fonds.<form id=\"sendfund\" name=\"sendfund\" method=\"post\" action=\"withdraw.php\">
								Address <input name=\"sendaddress\" type=\"text\" id=\"textfield\" value=\"\" size=\"50\" /><br />
								Amount &nbsp;<input name=\"sendamount\" type=\"text\" id=\"textfield\" value=\"\" size=\"10\" />
								<input type=\"submit\" name=\"button\" id=\"button\" value=\"Send\" /></form>";
							} else {
								echo "Vous ne pouvez pas envoyer de RavenCoin, si vous venez dans déposez sur votre Wallet, il faut attendre 100 confirmations pour que puissiez les envoyer à nouveau.<br />";
							}
							?>
					</div>
				</div>
			</div>
			<div id="menu">
				<div class="menumargin">
					<a href='index.php'>Acceuil</a>
					<a href='account.php'>Compte</a>
					<a href='deposit.php'>dépôt</a>
					<a href='withdraw.php'>Transfert</a>
					<a href='contact.php'>Contact</a>
					<a href='logout.php'>Logout</a>
				</div>
			</div>
			<div id="footer"><a href="index.php">Acceuil</a> | <a href="account.php">Compte</a> | <a href="deposit.php">dépôt</a> | <a href="withdraw.php">Transfert</a> | <a href="contact.php">Contact</a> | <a href="#">Logout</a> | </div>
		</div>
	</body>
</html>
