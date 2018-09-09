<?php
session_start();

if ( isset($_SESSION['username']) )
{
	header('location:account.php');
}

if($_GET['logoff'] == "true")
{
	session_destroy();
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
						<form>
							Login: <input type="text" name="amuser" size="20" required><br />
							MDP: <input type="password" name="ampass" size="20" required><br />
							<input type="submit" value="submit" /><br /><br />
							<?php
if ( isset($_GET['amuser']) && isset($_GET['ampass']) )
{
	// Recherche si Wallet existe en local et si associe à un compte
	if ($DBIsCo)
	{
		if(DEBUG) { printf("DEBUG: Co OK<br />\r\n"); }
		$DBReq	= 'SELECT wallet FROM comptes WHERE login LIKE "' . $_GET['amuser'] . '" AND mdp LIKE "' . $_GET['ampass'] . '";';
		$rs = $conn->query($DBReq);
 
		if($rs === false)
		{
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
					//$_SESSION['sendaddress'] = $row['wallet'];
					printf('Wallet: ' . $row['wallet'] . '<br />');
				}
				$_SESSION['username'] = $_GET['amuser'];
				$_SESSION['userid'] = time();
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
					</div>
				</div>
			</div>
		</div>
	</body>
</html>
