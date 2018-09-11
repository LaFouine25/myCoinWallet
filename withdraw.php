<?php
session_start();

if(!isset($_SESSION['username']))
	header('location:index.php');

require_once('includes/config.php');
require_once('includes/jsonRPCClient.php');
require_once('includes/bcfunctions.php');
require_once('includes/dbconnect.php');

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
							
							// Controle adresse du client avec Anon si besoin
							if($_SESSION['anon'] == 1)
							{
								if(isset($_SESSION['sendaddress'])) {
									$sendaddress = refreshAddressIfStale($bitcoin,$_SESSION['sendaddress']); // session exists, check if its been used before
									$_SESSION['sendaddress'] = $sendaddress;
								} else {
									// if address already exists in wallet (or new unfortunately), check the balance and set as main receivable address if zero
									$curaddress = $bitcoin->getaccountaddress($_SESSION['username']);
									$sendaddress = refreshAddressIfStale($bitcoin,$curaddress);
									$_SESSION['sendaddress'] = $sendaddress;
								}
								$DBReq = "UPDATE comptes SET wallet = '" . $_SESSION['sendaddress'] . "' WHERE login LIKE '" . $_SESSION['username'] . "';";
								$conn->query($DBReq);
							if(DEBUG) printf("DEBUG: Enregistre en BDD le Wallet avec -> " . $DBReq);
							}
							
							// save current balance
							saveCurrentBalance($bitcoin, $_SESSION['sendaddress']);
							
							$userBalance = $_SESSION['userbalance'];
							$estimatefee = $bitcoin->estimatesmartfee(6);
							
							if (DEBUG) printf('DEBUG: Var $estimatefee -->' . number_format($estimatefee["feerate"], 8) . '<br />');
							
							// check for post request
							if(isset($_POST['sendaddress'])) {
								if(isset($_POST['sendamount'])) {
									$postSendAddress = $_POST['sendaddress'];
									$postSendAmount = $_POST['sendamount'];
									//echo $postSendAddress;
									//echo $postSendAmount;
									
									if($postSendAmount + $estimatefee["feerate"] > $_SESSION['userbalance']) { // they tried to send more money than they have this is possible as accounts can go negative
										echo "<font color='red'><b>Vous ne pouvez pas envoyer de Coins, la somme envoyé dépasse votre solde!</b></font>";
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
							
							echo "Solde disponible: " . $userBalance . "<br />";
							echo "TX Fee Blockchain: "  . number_format($estimatefee["feerate"], 8) . "<br />";
							
							// echo send form
							echo "Actuellement, ";
							if($userBalance > 0) {
								echo "Vous pouvez envoyer des fonds.<form id=\"sendfund\" name=\"sendfund\" method=\"post\" action=\"withdraw.php\">
								Adresse <input name=\"sendaddress\" type=\"text\" id=\"textfield\" value=\"\" size=\"50\" /><br />
								Montant <input name=\"sendamount\" type=\"text\" id=\"textfield\" value=\"\" size=\"10\" /> (<i>sans les txfees</i>)
								<input type=\"submit\" name=\"button\" id=\"button\" value=\"Envoyer\" /></form>";
							} else {
								echo "Vous ne pouvez pas envoyer de RavenCoin, si vous venez dans déposer sur votre Wallet, il faut attendre en moyenne 6 confirmations (<i>6 minutes</i>) pour que puissiez les envoyer à nouveau.<br />";
							}
							?>
					</div>
				</div>
			</div>
			<div id="menu">
				<div class="menumargin">
					<a href='index.php'>Acceuil</a>
					<a href='account.php'>Compte</a>
					<a href='deposit.php'>Dépôt</a>
					<a href='withdraw.php'>Transfert</a>
					<a href='contact.php'>Contact</a>
					<a href='logout.php'>Logout</a>
				</div>
			</div>
			<div id="footer"><a href="index.php">Acceuil</a> | <a href="account.php">Compte</a> | <a href="deposit.php">Dépôt</a> | <a href="withdraw.php">Transfert</a> | <a href="contact.php">Contact</a> | <a href="#">Logout</a> | </div>
		</div>
	</body>
</html>
