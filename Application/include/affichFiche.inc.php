<?php

$ficheValide = $_POST['tabFiche'];
?>


    <div class="encadre">
    <p>
    <h3><?php
    if($_POST['dateEnvoyer'] != 'all') {
    echo "Fiche de frais du mois de  " . $_POST['dateEnvoyer'];
  }else{
    echo "Toutes les fiches de frais";
    }
    ?> : </h3>
    </p>

<?php
if(isset($_POST['dateEnvoyer'])){
  $dateEnvoyer = $_POST['dateEnvoyer'];
}else{
  $dateEnvoyer = 'all';
}
 if($dateEnvoyer == 'all'){
  foreach($ficheValide as $uneFiche){
    $id = $uneFiche['id'];
    $mois = $uneFiche['mois'];
    $transformMois = $uneFiche['transformMois'];
    $prenom = $uneFiche['prenom'];
    $nom = $uneFiche['nom'];
    $nbJustificatifs = $uneFiche['nbJustificatifs'];
    $montantValide = $uneFiche['montantValide'];
    $dateModif = $uneFiche['dateModif'];

  ?>
    <table class="listeLegere">

    <tr>
  <th> Mois </th>
  <th>  Prénom rédacteur </th>
  <th> Nom </th>
  <th> Justificatifs fourni</th>
  <th> Montant validé </th>
  <th rowspan=2>  <br/> <a href='index.php?uc=SuiviPaiementFiche&action=voirFicheFrais&idF=<?php echo $id ?>&dateF=<?php echo $mois ?>'> Consulter </a> </th>
    </tr>

    <tr>
    <td> <?=$transformMois ?></td>
    <td> <?=$prenom ?> </td>
    <td> <?=$nom ?> </td>
    <td> <?=$nbJustificatifs ?> </td>
    <td> <?=$montantValide ?> </td>
    </tr>
    </table>
    <br/> <br/>

    <?php
  }

 } else{
  foreach($ficheValide as $uneFiche){
    $id = $uneFiche['id'];
    $mois = $uneFiche['mois'];
    $transformMois = $uneFiche['transformMois'];
    $prenom = $uneFiche['prenom'];
    $nom = $uneFiche['nom'];
    $nbJustificatifs = $uneFiche['nbJustificatifs'];
    $montantValide = $uneFiche['montantValide'];
    $dateModif = $uneFiche['dateModif'];

    if($dateEnvoyer == $mois){
  ?>
    <table>

    <tr>
      <th> Mois </th>
      <th>  Prénom rédacteur </th>
      <th> Nom </th>
      <th> Justificatifs fourni</th>
      <th> Montant validé </th>
  <th rowspan=2>  <br/> <a href='index.php?uc=SuiviPaiementFiche&action=voirFicheFrais&idF=<?php echo $id ?>&dateF=<?php echo $mois ?>'> Consulter </a>  </th>
    </tr>

    <tr>
    <td> <?=$transformMois ?></td>
    <td> <?=$prenom ?> </td>
    <td> <?=$nom ?> </td>
    <td> <?=$nbJustificatifs ?> </td>
    <td> <?=$montantValide ?> </td>
    </tr>
    </table>
    <br/> <br/>

    <?php
    }
  }
 }
?>
</div>
