    <table class="listeLegere">

    <tr>
        <th> Frais forfaitisés </th>
  <th> Forfait Etape </th>
  <th>  Frais kilométrique </th>
  <th> Nuitée Hôtel </th>
  <th> Repas Restaurant</th>

    </tr>

    <tr>
      <th> Quantité </th>
      <td> <?= $infoFicheForfait['ETP']['quantite'] ?></td>
      <td> <?= $infoFicheForfait['KM']['quantite'] ?> </td>
      <td> <?= $infoFicheForfait['NUI']['quantite'] ?> </td>
      <td> <?= $infoFicheForfait['REP']['quantite'] ?> </td>
    </tr>
    <tr>
      <th> Montant total (€)</th>
    <td> <?= $infoFicheForfait['ETP']['quantite'] * $fraisForfait['ETP']['montant'] ?></td>
    <td> <?= $infoFicheForfait['KM']['quantite'] * $fraisForfait['KM']['montant'] ?> </td>
    <td> <?= $infoFicheForfait['NUI']['quantite'] * $fraisForfait['NUI']['montant'] ?> </td>
    <td> <?= $infoFicheForfait['REP']['quantite'] * $fraisForfait['REP']['montant'] ?> </td>
    </tr>
    </table>

    <br/>
