
<h3>Fiche de frais du mois <?php echo $numMois . "-" . $numAnnee ?> : 
</h3>
<div class="encadre">
    
    <form method="POST" action="index.php?uc=validerfichefrais&action=modification">
          <caption>Eléments forfaitisés</caption>
        <table class="listeLegere">
            <tr>
                <?php
                foreach ($lesFraisForfait as $unFraisForfait) {
                    $libelle = $unFraisForfait['libelle'];
                    ?>	
                    <th> <?php echo $libelle ?></th>
                    <?php
                }
                ?>
                  
            </tr>
            <tr>
                <?php
                foreach ($lesFraisForfait as $unFraisForfait) {
                    $quantite = $unFraisForfait['quantite'];
                    $idFrais = $unFraisForfait['idfrais'];
                    ?>
                    <td class="qteForfait"><input type="text" size="10" maxlength="5" name="lesFrais[<?php echo $idFrais ?>]" value="<?php echo $quantite ?> "></td>


                    <?php
                }
                ?><td><input type="submit" value="Modifier" ></td>
                    
            </tr>
        </table>
    </form>
    <table class="listeLegere">
        <caption>Descriptif des éléments hors forfait -<?php echo $nbJustificatifs ?> justificatifs reçus -
        </caption>
        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>
            <th class='montant'>Montant</th> 
            <th class='supprimer'>Supprimer</th> 
            <th class='reporter'>Reporter</th> 
        </tr>
        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $id = $unFraisHorsForfait['id'];
            $date = $unFraisHorsForfait['date'];
            $libelle = $unFraisHorsForfait['libelle'];
            $montant = $unFraisHorsForfait['montant'];
            ?>

            <tr>
                <td><?php echo $date ?></td>
                <td><?php echo $libelle ?></td>
                <td><?php echo $montant ?></td>
                <td><a onclick="return confirm('Voulez vous réellement refuser cette fiche ?')" href="index.php?uc=validerfichefrais&action=supprimer&id=<?php echo $id;?>">Refuser</a></td>
                <td><a href="index.php?uc=validerfichefrais&action=reporter&id=<?php echo $id;?>">Reporter</a></td>
            </tr>
            <?php
        }
        ?>
    </table>

</div>
    <div class="piedForm">
<form action="index.php?uc=validerfichefrais&action=valider&mois=<?= $_POST['lstMois']?>&id=<?= $_POST['lstVisiteurs']?>&idEtat=VA"
  method='post' onsubmit="return confirm('Voulez vous valider cette fiche ?');">
<input type='submit' id='valider' value='Valider'>
</form>
      </div>
</div>

