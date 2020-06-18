
 <div id="contenu">
 <h2>Consultation fiches de frais validée</h2>
 <div class="corpsForm">

<!-- Sélection de la fiche validé à consulter -->
<form action='#' method='post'>
<b> Choix du mois: </b>
	<select id='dateFiche' size="1">

  <option value='all' selected> Toutes les fiches </option>
	<?php
foreach($moisFichesValide as $unMois){
	?>
<option value='<?php echo $unMois['mois'] ?>'> <?php echo transformDate($unMois['mois'])?> </option>
	<?php
}
	?>
</select>
<input type='button' id='envoyer' value='Valider'>
</form>

	<br/> <br/>
<div id='fiche'></div>

  </div>

<script src='include/jquery-3.2.1.min.js'></script>

<script>
var tabFihe = <?php echo json_encode($ficheValide); ?>;
</script>

<script type="text/javascript" src="include/postFicheValide.inc.js"></script>
