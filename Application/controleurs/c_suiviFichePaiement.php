<?php
$action = $_REQUEST['action'];
include("vues/v_sommaireComptable.php");

switch($action){

	case 'suivrePaiement':{

// On teste avant d'inclure le formulaire de recherches des fiches valides
		try {
			$ficheValide = $pdo->getInfosVisiteur_EtatVA();
			$moisFichesValide = $pdo->getLesMoisFichesValides();

					include("vues/v_Comptable-SuiviFiche.php");
		}catch(Exception $e){
			$message = $e->getMessage();
			include('vues/v_message.php');
		}
        break;
	}


	case 'voirFicheFrais':{

		$infoVisiteur = $pdo->getInfoLeVisiteur($_GET['idF']);
		$fraisForfait = $pdo->getFraisForfait();

include('vues/v_Comptable-ValidationFiche_titre.php');

$ok = true; // Variable qui va permettre de dire OUI/NON à l'affichage des boutons

// On effectue un try catch avat de récupérer le résultat pour vérifier qu'il y as uelque'chose!!!
try {
	$infoFicheForfait = $pdo->getInfoFicheForfait($_GET['idF'], $_GET['dateF']);
			include("vues/v_Comptable-ValidationFiche_Forfait.php");
}catch(Exception $e){
	$ok = false;
	$message = $e->getMessage();
	include('vues/v_message.php');
}

// On réeffectue un try catch avat de récupérer le résultat pour vérifier qu'il y as uelque'chose!!!
try {
$infoFicheHorsForfait = $pdo->getInfoFicheHorsForfait($_GET['idF'], $_GET['dateF']);
		include("vues/v_Comptable-ValidationFiche_HorsForfait.php");
}catch(Exception $e){

// Petite vérification afin de vérifier que les boutons pourront ou non être affichés
	if(!$ok){
				$ok = false;
	}

	$message = $e->getMessage();
	include('vues/v_message.php');
}

// Affichage des boutons si les conditions sont remplies
if($ok){
	include('vues/v_Comptable-ValidationFiche_Bouton.php');
}
		break;
	}


	case 'misePaiement':{
$pdo->setEtatFiche($_GET['idF'], $_GET['dateF'], $_GET['etat']);

$infoVisiteur = $pdo->getInfoLeVisiteur($_GET['idF']);
$message = "La fiche de paiement de " . $infoVisiteur['nom'] . " " . $infoVisiteur['prenom'] .
 "<br/>
Faite en " . transformDate($_GET['dateF']) . " a bien été mise en paiement";

include('vues/v_message.php');
break;
	}


	case 'rembourse':{
$pdo->setEtatFiche($_GET['idF'], $_GET['dateF'], $_GET['etat']);

$infoVisiteur = $pdo->getInfoLeVisiteur($_GET['idF']);
$message = "La fiche de paiement de " . $infoVisiteur['nom'] . " " . $infoVisiteur['prenom'] .
 "<br/>
Faite en " . transformDate($_GET['dateF']) . " a bien été spécifié \"remboursé\"";

include('vues/v_message.php');
break;
	}
}
