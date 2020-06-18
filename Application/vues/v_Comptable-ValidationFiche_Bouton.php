<br/>

<form action='index.php?uc=SuiviPaiementFiche&action=misePaiement&idF=<?= $_GET['idF']?>&dateF=<?= $_GET['dateF']?>&etat=MP'
  method='post' onsubmit="return confirm('Mettre en paiement cette fiche ?');">
<input type='submit' id='paiement' value='Mise en paiement'>
</form>


<form action='index.php?uc=SuiviPaiementFiche&action=rembourse&idF=<?= $_GET['idF']?>&dateF=<?= $_GET['dateF']?>&etat=RB'
  method='post' onsubmit="return confirm('Cette fiche a bien été remboursé ?');">
<input type='submit' id='rembourse' value='Remboursé'>
</form>

</div>
