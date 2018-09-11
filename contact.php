<?php
session_start();

if (!isset($_SESSION['username']))
{
	header('location:index.php');
}

require_once('includes/config.php');
require_once('includes/jsonRPCClient.php');
require_once('includes/bcfunctions.php');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?php printf(SITENAME);?> - Deposit</title>
		<link rel="stylesheet" href="css/styles.css"  type="text/css" />
	</head>
	<body>
		<div id="main">
			<div id="top"><div style='float:left;position:relative;top:25px;'><h2><?php printf(SITENAME);?></h2></div></div>
			<div id="wrapper">
				<div id="content">
					<div class="innermargin">
						<h1><?php printf(SITENAME);?> Contact</h1>
						<br />
						Pour contacter nos équipes, voici les différents moyen :<br />
						<ul>
							<li>Pour un bug sur le site : <a href=https://github.com/LaFouine25/myCoinWallet target=_blank>GitHub</a></li>
							<li>Par mail : <?php echo MAILSITE;?></li>
						</ul>
						<?php
						$bitcoin = new jsonRPCClient('http://' . USER . ':' . PASS . '@' . SERVER . ':' . PORT .'/',false);
						
						// save current balance
						saveCurrentBalance($bitcoin, $_SESSION['sendaddress']);
						echo "<b>" . $_SESSION['sendaddress'] . "</b>";
						
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
