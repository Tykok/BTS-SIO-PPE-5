 <div id="contenu">
    <h2>Mes fiches de frais</h2>
    <h3>Mois à sélectionner : </h3>
    <form action="index.php?uc=suiviFrais&action=voirDetailFrais" method="post">
        <div class="corpsForm">

            <p>

                <label for="lstFiche" accesskey="n">Fiche à valider : </label>
                <select id="lstFiche" name="lstFiche">
                    <?php
                    foreach ($listeFiche as $uneFiche) {
                        echo "<option value=" . $uneFiche['mois'] . "/" . $uneFiche['id'] . ">" . $uneFiche['nom'] . " " . $uneFiche['prenom'] . " - " . $uneFiche['mois'] . "</option>";
                    }
                    ?>    

                </select>
            </p>
        </div>
        <div class="piedForm">
            <p>
                <input id="ok" type="submit" value="Valider" size="20" />
                <input id="annuler" type="reset" value="Effacer" size="20" />
            </p> 
        </div>

    </form>
