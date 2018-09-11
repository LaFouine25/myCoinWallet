<?php
session_start();

if ( isset($_SESSION['username']) )
{
	header('location:account.php');
}
require('includes/config.php');
require_once('includes/dbconnect.php');
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Wallet <?php printf(SITENAME);?></title>
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
							<input type="submit" value="submit" /><br /><br />
							<?php
if ( isset($_POST['amuser']) && isset($_POST['ampass']) )
{
	// Recherche si Wallet existe en local et si associe à un compte
	if ($DBIsCo)
	{
		if(DEBUG) { printf("DEBUG: Co OK<br />\r\n"); }
		$DBReq	= 'SELECT wallet, anonymiser FROM comptes WHERE login LIKE "' . $_POST['amuser'] . '" AND mdp LIKE "' . $_POST['ampass'] . '";';
		$rs = $conn->query($DBReq);
 
		if($rs === false)
		{
			// Login/MDP erreur, on propose l'enregistrement.
			if(DEBUG) { printf('DEBUG: SQL Co Err.'); }
			trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
		}
		else
		{
			$rows_returned = $rs->num_rows;
			if ($rows_returned == 1)
			{
				$rs->data_seek(0);
				while($row = $rs->fetch_assoc())
				{
					$_SESSION['sendaddress']= $curaddress = $sendaddress = $row['wallet'];
					printf('Wallet: ' . $row['wallet'] . '<br />');
				}
				$_SESSION['username']	= $_POST['amuser'];
				$_SESSION['userid']		= time();
				
				switch ($row['anonymiser'])
				{
					case "1":
						$_SESSION['anon'] = 1;
						break;
					case "0":
						$_SESSION['anon'] = 0;
						break;
					default:
						$_SESSION['anon'] = 0;
				}
				
				if(DEBUG) { printf('DEBUG: Session OK <br />');}
				echo "<script language=javascript>document.location.reload(true);</script>";
			}
			else
			{
				if(DEBUG) {printf('DEBUG: Erreur LogMdp.' . $DBReq);}
				echo "<b>Erreur couple Login/MDP</b><br />";
			}
		}
	}
}
else
{
	//Rien n'est saisie dans le formulaire
	if ( isset($_SESSION['username']) )
	{
		if(DEBUG) { printf('DEBUG: ' . $_SESSION['username']); }
	}
}
?>
						</table></form>
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
