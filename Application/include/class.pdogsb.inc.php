<?php
/**
 * Classe d'accès aux données.

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoGsb{

	private static $serveur = 'mysql:host=localhost';
        private static $bdd = 'dbname=gsbv2';
        private static $user = 'root';
        private static $mdp = '';
        private static $monPdo;
        private static $monPdoGsb = null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */
	private function __construct(){
    	PdoGsb::$monPdo = new PDO(PdoGsb::$serveur.';'.PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp);
		PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function __destruct(){
		PdoGsb::$monPdo = null;
	}
/**
 * Fonction statique qui crée l'unique instance de la classe

 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();

 * @return l'unique objet de la classe PdoGsb
 */
	public  static function getPdoGsb(){
		if(PdoGsb::$monPdoGsb==null){
			PdoGsb::$monPdoGsb= new PdoGsb();
		}
		return PdoGsb::$monPdoGsb;
	}
/**
 * Retourne les informations d'un visiteur

 * @param $login
 * @param $mdp
 * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
*/
	public function getInfosVisiteur($login, $mdp){
		$req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom from visiteur
		where visiteur.login='$login' and visiteur.mdp='$mdp'";
		$rs = PdoGsb::$monPdo->query($req);
		$ligne = $rs->fetch();
		return $ligne;
	}

            public function getInfosComptable($login, $mdp) {
                $req = "select comptable.id as id, comptable.nom as nom, comptable.prenom as prenom from comptable
		where comptable.login='$login' and comptable.mdp='$mdp'";
                $rs = PdoGsb::$monPdo->query($req);
                $ligne = $rs->fetch();
                return $ligne;
        }

/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
 * concernées par les deux arguments

 * La boucle foreach ne peut être utilisée ici car on procède
 * à une modification de la structure itérée - transformation du champ date-

 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
 * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif
*/
	public function getLesFraisHorsForfait($idVisiteur,$mois){
	    $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur ='$idVisiteur'
		and lignefraishorsforfait.mois = '$mois' ";
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		$nbLignes = count($lesLignes);
		for ($i=0; $i<$nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] =  dateAnglaisVersFrancais($date);
		}
		return $lesLignes;
	}
/**
 * Retourne le nombre de justificatif d'un visiteur pour un mois donné

 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
 * @return le nombre entier de justificatifs
*/
	public function getNbjustificatifs($idVisiteur, $mois){
		$req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		return $laLigne['nb'];
	}
/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
 * concernées par les deux arguments

 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
 * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif
*/
	public function getLesFraisForfait($idVisiteur, $mois){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle,
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois'
		order by lignefraisforfait.idfraisforfait";
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}
/**
 * Retourne tous les id de la table FraisForfait

 * @return un tableau associatif
*/
	public function getLesIdFrais(){
		$req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
		$res = PdoGsb::$monPdo->query($req);
		$lesLignes = $res->fetchAll();
		return $lesLignes;
	}
/**
 * Met à jour la table ligneFraisForfait

 * Met à jour la table ligneFraisForfait pour un visiteur et
 * un mois donné en enregistrant les nouveaux montants

 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
 * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
 * @return un tableau associatif
*/
	public function majFraisForfait($idVisiteur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$dateNow = date('Y-m-d'); // Récupére la date du système

                $req = "UPDATE `fichefrais`
                SET `dateModif` = '$dateNow', `idEtat` = '$lesFrais'
                WHERE `fichefrais`.`idVisiteur` = '$idVisiteur' AND `fichefrais`.`mois` = '$mois';";

			PdoGsb::$monPdo->exec($req);
		}

	}
/**
 * met à jour le nombre de justificatifs de la table ficheFrais
 * pour le mois et le visiteur concerné

 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
*/
	public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs){
		$req = "update fichefrais set nbjustificatifs = $nbJustificatifs
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
		PdoGsb::$monPdo->exec($req);
	}
/**
 * Teste si un visiteur possède une fiche de frais pour le mois passé en argument

 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
 * @return vrai ou faux
*/
	public function estPremierFraisMois($idVisiteur,$mois)
	{
		$ok = false;
		$req = "select count(*) as nblignesfrais from fichefrais
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		if($laLigne['nblignesfrais'] == 0){
			$ok = true;
		}
		return $ok;
	}
/**
 * Retourne le dernier mois en cours d'un visiteur

 * @param $idVisiteur
 * @return le mois sous la forme aaaamm
*/
	public function dernierMoisSaisi($idVisiteur){
		$req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
		$res = PdoGsb::$monPdo->query($req);
		$laLigne = $res->fetch();
		$dernierMois = $laLigne['dernierMois'];
		return $dernierMois;
	}


	/**
	 * [ListeFicheValider description]
	 */
	    public function ListeFicheValider ()    {
        $req = "select * from fichefrais, visiteur where id = idVisiteur and idEtat='CL' ;";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetchAll();
        return $laLigne;
    }



/**
 * Crée une nouvelle fiche de frFais et les lignes de frais au forfait pour un visiteur et un mois donnés

 * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
 * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles
 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
*/
	public function creeNouvellesLignesFrais($idVisiteur,$mois){
		$dernierMois = $this->dernierMoisSaisi($idVisiteur);
		$laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur,$dernierMois);
		if($laDerniereFiche['idEtat']=='CR'){
				$this->majEtatFicheFrais($idVisiteur, $dernierMois,'CL');

		}
		$req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat)
		values('$idVisiteur','$mois',0,0,now(),'CR')";
		PdoGsb::$monPdo->exec($req);
		$lesIdFrais = $this->getLesIdFrais();
		foreach($lesIdFrais as $uneLigneIdFrais){
			$unIdFrais = $uneLigneIdFrais['idfrais'];
			$req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite)
			values('$idVisiteur','$mois','$unIdFrais',0)";
			PdoGsb::$monPdo->exec($req);
		 }
	}
/**
 * Crée un nouveau frais hors forfait pour un visiteur un mois donné
 * à partir des informations fournies en paramètre

 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
 * @param $libelle : le libelle du frais
 * @param $date : la date du frais au format français jj//mm/aaaa
 * @param $montant : le montant
*/
public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
        $dateFr = dateFrancaisVersAnglais($date);
        $req = "insert into lignefraishorsforfait (idVisiteur,mois,libelle,date,montant)
		values(:idVisiteur,:mois,:libelle,:dateFr,:montant)";
        $res = PdoGsb::$monPdo->prepare($req);
        $res->execute(array('idVisiteur' => $idVisiteur, 'mois' => $mois, 'libelle' => $libelle, 'dateFr' => $dateFr, 'montant' => $montant));
    }
/**
 * Supprime le frais hors forfait dont l'id est passé en argument

 * @param $idFrais
*/
	public function supprimerFraisHorsForfait($idFrais){
		$req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
		PdoGsb::$monPdo->exec($req);
	}
/**
 * Retourne les mois pour lesquel un visiteur a une fiche de frais

 * @param $idVisiteur
 * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant
*/
	public function getLesMoisDisponibles($idVisiteur){
		$req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur'
		order by fichefrais.mois desc ";
		$res = PdoGsb::$monPdo->query($req);
		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch();
		}
		return $lesMois;
	}
/**
 * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné

 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
 * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état
*/
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $req = "select fichefrais.idEtat as idEtat, fichefrais.dateModif as dateModif, fichefrais.nbJustificatifs as nbJustificatifs,
			fichefrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join etat on fichefrais.idEtat = etat.id
			where fichefrais.idVisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }



/**
 * Modifie l'état et la date de modification d'une fiche de frais

 * Modifie le champ idEtat et met la date de modif à aujourd'hui
 * @param $idVisiteur
 * @param $mois sous la forme aaaamm
 */

	public function majEtatFicheFrais($idVisiteur,$mois,$etat){
		$req = "update ficheFrais set idEtat = '$etat', dateModif = now()
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
		PdoGsb::$monPdo->exec($req);
	}






//-----------------------------------------------------------------------MODIFIE-----------------------------------------------
/**
 * [verifConnexionVst description]
 * @param  [type] $login [description]
 * @param  [type] $mdp   [description]
 * @return [type]        [description]
 */
        function verifConnexionVst($login,$mdp){
        $visiteur = PdoGsb::getInfosVisiteur($login,$mdp);
        if(!empty($visiteur)){
            return true;
        }else{
            return false;
        }
}



/**
 * [verifConnexionCmpt description]
 * @param  [type] $login [description]
 * @param  [type] $mdp   [description]
 * @return [type]        [description]
 */
function verifConnexionCmpt($login,$mdp){
        $comptable = PdoGsb::getInfosComptable($login,$mdp);
        if(!empty($comptable)){
            return true;
        }else{
            return false;
        }
}




/**
 * [getLesVisiteurs description]
 * @return [type] [description]
 */
public function getLesVisiteurs() {
        $req = "select id, nom, prenom from visiteur order by nom asc";
        $res = PdoGsb::$monPdo->query($req);
        $lesVisiteurs = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $id = $laLigne['id'];
            $nom = $laLigne['nom'];
            $prenom = $laLigne['prenom'];
            $lesVisiteurs["$id"] = array(
                "id" => "$id",
                "nom" => "$nom",
                "prenom" => "$prenom"
            );
            $laLigne = $res->fetch();
        }
        return $lesVisiteurs;
    }



/**
 * [getIdvisiteurFicheFrais description]
 * @return boolean [description]
 */
        public function getIdvisiteurFicheFrais() {
        $req = "select distinct(idVisiteur), concat(nom,' ',prenom) as 'nom' from fichefrais inner join visiteur on fichefrais.idVisiteur=visiteur.id";
        $res = PdoGsb::$monPdo->query($req);
        $ligne = $res->fetchAll();
        return $ligne;
    }


/**
 * [refuserfrais description]
 * @param  [type]  $id [description]
 * @return boolean     [description]
 */
        public function refuserfrais($id) {
        $req = "UPDATE lignefraishorsforfait SET `libelle` = CONCAT('REFUSE : ', libelle) WHERE id = '$id' AND libelle NOT LIKE 'REFUSE :%';";
        PdoGsb::$monPdo->exec($req);
    }




/**
 * [getMoisSuivant description]
 * @param  [type]  $annee [description]
 * @param  [type]  $mois  [description]
 * @param  [type]  $id    [description]
 * @return boolean        [description]
 */
        public function getMoisSuivant($annee, $mois, $id) {
       $ladate =$annee.''.$mois; // concatene les dates
        $req = "update lignefraishorsforfait set mois = '$ladate' where id='$id' ";

        PdoGsb::$monPdo->exec($req);
        }


/**
 * [reporter description]
 * @param  boolean $MoisPlus   [description]
 * @param  [type]  $idVisiteur [description]
 * @param  [type]  $id         [description]
 * @return [type]              [description]
 */
               public function reporter($MoisPlus,$idVisiteur,$id) {
        $req = "UPDATE `lignefraishorsforfait` SET `mois`='" . $MoisPlus . "' WHERE `idVisiteur`='" . $idVisiteur . "' and `id`='" . $id . "'";

        PdoGsb::$monPdo->exec($req);
        }


        /**
         * getLesFichesFrais Méthode permettant de récupérer les fiches de frais et de les retourner pour l'affichage
         * @return array tableau contenant toutes les fiches de frais
         */
public function getLesFichesFrais(){
	$req = "SELECT * FROM `fichefrais`";
	$res = PdoGsb::$monPdo->query($req);
	$lesLignes = $res->fetchAll();

	$nbLignes = count($lesLignes);
	for ($i=0; $i<$nbLignes; $i++){
		$date = $lesLignes[$i]['dateModif'];
		$lesLignes[$i]['dateModif'] =  dateAnglaisVersFrancais($date);
	}

	return $lesLignes;
}



/**
 * getLesMoisFicheFrais Récupératiion de tout les mois concernant les fiches de frais
 * @return array retourne tout les mois concernés par les fiches de frais
 */
public function getLesMoisFicheFrais(){
	$req = "select fichefrais.mois as mois from  fichefrais";
	$res = PdoGsb::$monPdo->query($req);
	$lesMois =array();
	$laLigne = $res->fetch();
	while($laLigne != null)	{
		$mois = $laLigne['mois'];
		$numAnnee =substr( $mois,0,4);
		$numMois =substr( $mois,4,2);
		$lesMois["$mois"]=array(
		 "mois"=>"$mois",
		"numAnnee"  => "$numAnnee",
		"numMois"  => "$numMois"
		 );
		$laLigne = $res->fetch();
	}
	return $lesMois;
}


/**
 * getInfosVisiteur_EtatVA  récupération des fiches de frais des visiteurs pour un état X
 * @return array Retourne les lignes du Tableau
 * Si rien n'est trouvé par ailleurs il génére une exception
 */
public function getInfosVisiteur_EtatVA(){
	$req = "select `id`,`nom`,`prenom`,`mois`,`nbJustificatifs`,`montantValide`,`dateModif`,`idEtat`
	from visiteur INNER JOIN fichefrais
	ON visiteur.id = fichefrais.idVisiteur
	where fichefrais.idEtat='VA'";

	$rs = PdoGsb::$monPdo->query($req);

  $lesLigne = array();
	$ligne = $rs->fetchAll();

foreach($ligne as $uneInfo)	{
$uneInfo['transformMois'] = transformDate($uneInfo['mois']);
$lesLigne[] = $uneInfo;
  }


if(count($lesLigne) == 0){
  throw new Exception('Aucune fiche de frais n\'est en état \"Validée\"');
}
	return $lesLigne;
}



/**
 * getLesMoisFichesValides retourne tout les mois concernés par des fiches validés
 * @return array tableau de tout les mois
 */
	public function getLesMoisFichesValides(){

		$req = "SELECT fichefrais.mois AS mois
		FROM  fichefrais
WHERE fichefrais.idEtat='VA'
		ORDER BY fichefrais.mois DESC";

		$res = PdoGsb::$monPdo->query($req);

		$lesMois =array();
		$laLigne = $res->fetch();
		while($laLigne != null)	{
			$mois = $laLigne['mois'];
			$numAnnee =substr( $mois,0,4);
			$numMois =substr( $mois,4,2);
			$lesMois["$mois"]=array(
		     "mois"=>"$mois",
		    "numAnnee"  => "$numAnnee",
			"numMois"  => "$numMois"
             );
			$laLigne = $res->fetch();
		}
		return $lesMois;
	}




/**
 * [getInfoFicheForfait description]
 * @param  [type] $id       Identifiant du visiteur
 * @param  [type] $dateFiche  Date de la fiche, car le même visteur peut avoir plusieurs fiches (mois qui différencie)
 * @return array()            retourne un tableau contenant le résultat de notre requête SQL
 */

  public function getInfoFicheForfait($id, $dateFiche) { //Permet l'envoi des informations nécessaires à la sélection d'une fiche
    $req = "SELECT  lignefraisforfait.quantite, lignefraisforfait.idFraisForfait, fichefrais.mois
    FROM lignefraisforfait INNER JOIN visiteur
    ON lignefraisforfait.idVisiteur = visiteur.id
    INNER JOIN fichefrais
    ON fichefrais.idVisiteur = visiteur.id
    WHERE visiteur.id = '$id'
    AND fichefrais.mois = '$dateFiche'";

    $rs = PdoGsb::$monPdo->query($req);
    $ligne = $rs->fetch(); // Récupére la première ligne pour travailler et structurer le rendu du tableau
$infoFiche = array();

while($ligne != null)	{
$infoFiche[$ligne['idFraisForfait']] = $ligne; /* On identifie l'information reçue par l'identifiant du forfait
celanous permettra d'être mieux organisé dans notre code*/
  $ligne = $rs->fetch();
}


// On génére une erreur si le tableau que l'on va retourner ne contient rien
if(count($infoFiche) == 0){
  throw new Exception('Aucun frais forfaitisés pour ce visiteur');
}


    return $infoFiche;
      }



/**
 * getInfoFicheHorsForfait retourne toutes les informations des frais hors forfait pour une fiche à une date précise
 * @param  int $idV  identifiant des frais hors forfait
 * @param  string $dateF date correspondant aux frais hors forfait
 * @return array     un array contenant le résultat de notre requête SQL
 */
      public function getInfoFicheHorsForfait($idV, $dateF){
    		$req = "SELECT *
                FROM `lignefraishorsforfait`
                WHERE `idVisiteur` = '$idV'
                AND mois = '$dateF'";
    		$rs = PdoGsb::$monPdo->query($req);
    		$ligne = $rs->fetchAll();

        // On génére une erreur si le tableau que l'on va retourner ne contient rien
        if(count($ligne) == 0){
          throw new Exception('Aucun frais hors forfait pour ce visiteur');
        }

    		return $ligne;
    	}



/**
 * getInfoLeVisiteur retourne le nom-préom du visiteur ayant pour id $id
 * @param  int $id identifiant du visiteur recherché
 * @return array   retourne une ligne de tableau contenant les informations nécessaires
 */
      public function getInfoLeVisiteur($id) {
              $req = "select id, nom, prenom from visiteur
                  where id = '$id'";
              $res = PdoGsb::$monPdo->query($req);

              $laLigne = $res->fetch();
              return $laLigne;
          }



/**
 * getFraisForfait permet l'envoi de données concernant les forfaits, identifiés par leurs identifiants dans un tableau
 * à double entrée
 * @return array retourne une ligne correspondant au différent frais de remboursement proposé
 */
          public function getFraisForfait() {
                  $req = "SELECT * FROM `fraisforfait` ";
                  $res = PdoGsb::$monPdo->query($req);
                  $laLigne = $res->fetch();
                  $infoFiche = array();

                  while($laLigne != null)	{
                  $infoFiche[$laLigne['id']] = $laLigne;
                    $laLigne = $res->fetch();
                  }
                      return $infoFiche;
              }


/**
 * setEtatFiche met à jour l'état d'une fiche X à une date Y
 * @param int $idFiche   Identifiant X de la fiche
 * @param string $dateFiche Date Y de la fiche
 * @param string $etat      état dans lequel la fiche doit être modifié
 */
              public function setEtatFiche($idFiche, $dateFiche, $etat){
                $dateNow = date('Y-m-d'); // Récupére la date du système

                $req = "UPDATE `fichefrais`
                SET `dateModif` = '$dateNow', `idEtat` = '$etat'
                WHERE `fichefrais`.`idVisiteur` = '$idFiche' AND `fichefrais`.`mois` = '$dateFiche';";

                $res = PdoGsb::$monPdo->query($req);
              }



							/**
							 * reportfiche permet de reporter une des ligne de frais forfaitisés vers le mois prochain
							 * @param  string $MoisPlus   permet d'obtenir le mois de la fiche de frais
							 * @param  int  $idVisiteur identifiant du visiteur
							 * @param  int  $id         identifiant du frais frofait
							 */
                 public function reportfiche($MoisPlus, $idVisiteur, $id){
            $req = "UPDATE `lignefraisforfait` SET `mois`='" . $MoisPlus . "' WHERE `idVisiteur`='" . $idVisiteur . "' and `idFraisForfait`='" . $id . "'";
            PdoGsb::$monPdo->exec($req);
              }



/**
 * majEtatFiche permet la mise à jour de l'état des fiches qui n'ont pas été cloturés (concerne les fiches du
 * mois précédent)
 */
							public function majEtatFiche(){
								$dateNow = date('Ym'); // Récupére la date du système
								$dateNow = (int)$dateNow; // casting de type pour trouver effectuer un calcul
								$dateNow = $dateNow - 1;

								$req = "Select * from fichefrais
								where idEtat = 'CR'
								AND $dateNow = '$dateNow'"; // Sélection de toutes les fiches CR du mois précédent au système

                $res = PdoGsb::$monPdo->query($req);
  							$lesLigne = $res->fetchAll(); // Tableau des fiches CR du mois précédent

// For each pour modifier l'état de haque fiche du mois précédent
								foreach($lesLigne as $uneLigne){

$idV = $uneLigne['idVisiteur'];
									$req = "UPDATE `fichefrais` SET `idEtat`='CL'
									WHERE idVisiteur = '$idV'";

									PdoGsb::$monPdo->query($req);
								}
							}
}
?>
