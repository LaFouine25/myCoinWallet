<?php
session_start();

require_once('includes/config.php');
require_once('includes/jsonRPCClient.php');
require_once('includes/bcfunctions.php');
require_once('includes/dbconnect.php');

// Modification en BDD de la valeur de Anonyme
if (isset($_POST['anon']))
{
	$DBReq = "UPDATE anonymiser FROM comptes WHERE login LIKE '" . $_SESSION['username'] . "';";
	$conn->query($DBReq);
	$_SESSION['anon'] = $_POST['anon'];
}

if (!isset($_SESSION['username']))
{
	header('location:index.php');
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?php printf(SITENAME);?> - Account</title>
		<link rel="stylesheet" href="css/styles.css"  type="text/css" />
	</head>
	<body>
		<div id="main">
			<div id="top"><div style='float:left;position:relative;top:25px;'><h2><?php printf(SITENAME);?></h2></div></div>
			<div id="wrapper">
				<div id="content">
					<div class="innermargin">
						<h1><?php printf(SITENAME);?> Account</h1>
						<br />
							<?php							
							$bitcoin = new jsonRPCClient('http://' . USER . ':' . PASS . '@' . SERVER . ':' . PORT .'/',false);
							// check for session address
							
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
							} else {
								$_SESSION['sendaddress'] = $curaddress = $sendaddress = $bitcoin->getaccountaddress($_SESSION['username']);
							}
							// save current balance
							saveCurrentBalance($bitcoin, $_SESSION['sendaddress']);
							
							// Affichage de l'adresse/solde de Wallet du client.
														
							$userBalance = $_SESSION['userbalance'];
							$singleconfirmBalance = number_format($bitcoin->getbalance($_SESSION['username'], 0),8); // set to zero, this is near instant, set to one one on the side of caution
							if($singleconfirmBalance > 0) {		// user has unconfirmed transactions
								$unconfirmedBalance = $singleconfirmBalance - $userBalance;
							}
							?>
							<div>
							<form>
								<fieldset>
									<legend>Changement de Wallet à chaque dépôt ?</legend>

									<div>
										<input type="radio" id="1" name="anon" <?php if($_SESSION['anon'] == 1) echo "checked";?> />
										<label for="oui">Oui - traçabilité complexe</label>
									</div>

									<div>
										<input type="radio" id="0" name="anon" <?php if($_SESSION['anon'] == 0) echo "checked";?>/>
										<label for="non">Non</label>
									</div>

								</fieldset>
								<button type=submit value=submit>Enregistrer</button>
							</form>
							</div>
							
							<?php							
							echo "Wallet : ". $sendaddress ."<br />";
							echo "Balance: ". $userBalance ."<br />";
							if((isset($unconfirmedBalance)) && ($unconfirmedBalance > 0)) {
								echo "Balance en attente: ". $unconfirmedBalance ."<br />";
							}
							
							echo "<h2>Transactions récentes:</h2><table>";
							$transactions = $bitcoin->listtransactions($_SESSION['username']);
							foreach($transactions as $trans) {
								if(isset($trans['account'])) {
									$transacct = $trans['account'];
								} else {
									$transacct = '';
								}
								if(isset($trans['address'])) {
									$transaddress = $trans['address'];
								} else {
									$transaddress = '';
								}
								if(isset($trans['category'])) {
									$transcategory = $trans['category'];
								} else {
									$transcategory = '';
								}
								if(isset($trans['amount'])) {
									$transamount = $trans['amount'];
								} else {
									$transamount = '';
								}
								if(isset($trans['confirmations'])) {
									$transconfirmations = $trans['confirmations'];
								} else {
									$transconfirmations = '';
								}
								if(isset($trans['blockhash'])) {
									$transblockhash = $trans['blockhash'];
								} else {
									$transblockhash = '';
								}
								if(isset($trans['blockindex'])) {
									$transblockindex = $trans['blockindex'];
								} else {
									$transblockindex = '';
								}
								if(isset($trans['blocktime'])) {
									$transblocktime = $trans['blocktime'];
								} else {
									$transblocktime = '';
								}
								if(isset($trans['txid'])) {
									$transtxid = $trans['txid'];
								} else {
									$transtxid = '';
								}
								if(isset($trans['time'])) {
									$transtime = $trans['time'];
								} else {
									$transtime = '';
								}
								if(isset($trans['timereceived'])) {
									$transtimereceived = $trans['timereceived'];
								} else {
									$transtimereceived = '';
								}
								/*
								$transaddress = $trans['address'];
								$transcategory = $trans['category'];
								$transamount = $trans['amount'];
								$transconfirmations = $trans['confirmations'];
								$transblockhash = $trans['blockhash'];
								$transblockindex = $trans['blockindex'];
								$transblocktime = $trans['blocktime'];
								$transtxid = $trans['txid'];
								$transtime = $trans['time'];
								$transtimereceived = $trans['timereceived'];
								*/
								
								echo "<tr><td>Address:</td><td>". $transaddress ."</td></tr>";
								echo "<tr><td>Amount:</td><td>". number_format($transamount, 8) ."</td></tr>";
								echo "<tr><td>Category:</td><td>". $transcategory ."</td></tr>";
								echo "<tr><td>Confirmations:</td><td>". $transconfirmations ."</td></tr>";
								echo "<tr><td>Blockhash:</td><td>". $transblockhash ."</td></tr>";
								echo "<tr><td>txid:</td><td>". $transtxid ."</td></tr>";
								echo "<tr><td>Time:</td><td>". date("Y - M - d H:i:s", $transtime) ."</td></tr>";
								echo "<tr><td>&nbsp;</td></tr>";
							}
							?>
						</table>
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
