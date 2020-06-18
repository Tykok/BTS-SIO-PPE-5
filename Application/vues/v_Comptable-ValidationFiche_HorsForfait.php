<table class="listeLegere">

<tr>
    <th> Frais hors forfait </th>
<th> Libelle </th>
<th>  Date </th>
<th> Montant </th>

</tr>

<?php
foreach($infoFicheHorsForfait as $horsFrais){
?>
<tr>
  <th> 1 </th>
  <td> <?= $horsFrais['libelle'] ?> </td>
  <td> <?= $horsFrais['date'] ?> </td>
  <td> <?= $horsFrais['montant'] ?> </td>
</tr>
<?php
}
?>

</table>
