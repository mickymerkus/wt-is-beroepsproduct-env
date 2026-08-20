<main>
    <section class="formulier">
        <a href="index.php">Terug naar Homepage.</a>

        <!-- Todo: dit formulier slaat nog niets op, nmoet databasekant nog opzetten -->
        <form action="bevestig_bestelling.php" method="post">
            <fieldset  class="formulier-sectie">
                <legend>Afleveradres</legend>
                <div class="formulier-veld straat">
                    <label for="straat">Straat</label>
                    <input type="text" id="straat" name="straat">
                </div>
                <div class="formulier-veld huisnummer">
                    <label for="huisnummer">Huisnummer</label>
                    <input type="text" id="huisnummer" name="huisnummer">
                </div>
                <div class="formulier-veld">
                    <label for="stad">Stad</label>
                    <input type="text" id="stad" name="stad">
                </div>
                <div class="formulier-veld postcode">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode">
                </div>
            </fieldset>

            <fieldset  class="formulier-sectie">
                <legend>Betaalwijze</legend>
                <div class="formulier-veld radio">
                    <label for="ideal">Ideal</label>
                    <input type="radio" id="ideal" name="betaalwijze" value="ideal">
                </div>
                <div class="formulier-veld radio">
                    <label for="contant">Contant</label>
                    <input type="radio" id="contant" name="betaalwijze" value="contant">
                </div>
                <select class="bank-dropdown" name="bank" id="bank" aria-label="Bank">
                    <option value="ing">ING</option>
                    <option value="ASN">ASN</option>
                    <option value="rabobank">Rabobank</option>
                </select>
            </fieldset>
            <button type="submit">Bestelling bevestigen</button>

        </form>
    </section>

    <?php include __DIR__ . '/winkelmandje.php'; ?>
</main>
