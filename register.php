<?php
session_start();

if ( isset($_SESSION['username']) )
{
	header('location:account.php');
}
require('includes/config.php');
require_once('includes/jsonRPCClient.php');
require_once('includes/bcfunctions.php');
require_once('includes/dbconnect.php');
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Register Wallet <?php printf(SITENAME);?></title>
		<link rel="stylesheet" href="css/styles.css"  type="text/css" />
	</head>
	<body>
		<div id="main">
			<div id="top"><div><h2><?php printf(SITENAME);?></h2></div></div>
			<div id="wrapper">
				<div id="content">
					<div class="innermargin">
						<h1>Login - <?php printf(SITENAME);?></h1>
						<br />
						<form method=POST>
							Login: <input type="text" name="amuser" size="20" required><br />
							MDP: <input type="password" name="ampass" size="20" required><br />
							Email: <input type="text" name="ammail" size="20" required><br />
							<input type="submit" value="submit" /><br /><br />
							<?php
if ( isset($_POST['amuser']) && isset($_POST['ampass']) && isset($_POST['ammail']) )
{
	// Recherche si Wallet existe en local et si associe à un compte
	if ($DBIsCo)
	{
		if(DEBUG) { printf("DEBUG: Co OK<br />\r\n"); }
		$DBReq	= 'SELECT 1 FROM comptes WHERE login LIKE "' . mysql_real_escape_string($_POST['amuser']) . '" OR email LIKE "' . mysql_real_escape_string($_POST['ammail']) . '";';
		$rs = $conn->query($DBReq);
 
		if($rs === false)
		{
			// Le Mail ET le Login ne sont pas déjà utilisé, on peut créer le compte ET le Wallet
			$bitcoin = new jsonRPCClient('http://' . USER . ':' . PASS . '@' . SERVER . ':' . PORT .'/',false);
			
			// Définition variables
			$curaddress = $bitcoin->getaccountaddress($_POST['amuser']);
			$login 		= mysql_real_escape_string($_POST['amuser']);
			$mdp		= mysql_real_escape_string($_POST['ampass']);
			$email		= mysql_real_escape_string($_POST['ammail']);
			
			// Requete
			$DBReq = "INSERT INTO comptes VALUES ('$curaddress', 'RVN', '$login', '$mdp', '0', '$email');";
			$rs = $conn->query($DBReq);
			
			// Envoi d'un mail de récap.
			mail($email, "Bienvenue sur le Wallet " . SITENAME, "Votre inscription a bien été prise en compte $login");
		}
		else
		{
			$rows_returned = $rs->num_rows;
			if ($rows_returned == 1)
			{
				?>
				<br />
				Le Login et/ou le Mail que vous avez utilisé existe déjà en base, merci d'en choisir un(des) autre(s).<br />
				<?php
			}
		}
	}
}
?>
						</table></form>
						<br />
						<a href="register.php">Créer un compte gratuit</a>
						<br />
						Merci d'utiliser notre Wallet Online. Celui-ci vous permet de sécuriser vos Crypto Coins sans avoir à télécharger l'entier de la BlockChain.<br />
						Vous pouvez également effectuer des transactions vers d'autres Wallet d'un simple clic.<br />
						Ce Wallet exploite le Pool de minage : <a href=http://<?php echo SERVER;?> target=blank><?php echo SITENAME;?></a><br />
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
