<?php
if(!isset($_REQUEST['action'])){
	$_REQUEST['action'] = 'demandeConnexion';
}
$action = $_REQUEST['action'];
switch($action){
	case 'demandeConnexion':{
		include("vues/v_connexion.php");
		break;
	}
	case 'valideConnexion':{
            if ($pdo->verifConnexionVst($_REQUEST['login'], $_REQUEST['mdp'])) {
                $login = $_REQUEST['login'];
                $mdp = $_REQUEST['mdp'];
                $visiteur = $pdo->getInfosVisiteur($login, $mdp);
                $id = $visiteur['id'];
                $nom = $visiteur['nom'];
                $prenom = $visiteur['prenom'];
                connecter($id, $nom, $prenom,$login,$mdp);
                include("vues/v_sommaire.php");
                break;
            } elseif ($pdo->verifConnexionCmpt($_REQUEST['login'], $_REQUEST['mdp'])) {
                $login = $_REQUEST['login'];
                $mdp = $_REQUEST['mdp'];
                $comptable = $pdo->getInfosComptable($login, $mdp);
                $id = $comptable['id'];
                $nom = $comptable['nom'];
                $prenom = $comptable['prenom'];
                connecter($id, $nom, $prenom,$login,$mdp);
                include("vues/v_sommaireComptable.php");
                break;
            } else {
                ajouterErreur("Login ou mot de passe incorrect");
                include("vues/v_erreurs.php");
                
            }
	}
        	case 'deconnexion':{
		deconnecter();
                include("vues/v_connexion.php");
		break;
	}
	default :{
		include("vues/v_connexion.php");
		break;
	}
}
?>