<main>
    <section class="formulier">
        <!-- Geef de mogelijke foutmeldingen weer -->
        <?php if ($fouten): ?>
            <ul class="foutmeldingen">
                <?php foreach ($fouten as $fout): ?>
                    <li><?= htmlspecialchars($fout) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="bevestig_bestelling.php" method="post">
            <?php if (!$gebruiker): ?>
                <fieldset class="formulier-sectie">
                    <legend>Je gegevens</legend>
                    <div class="formulier-veld">
                        <label for="naam">Voor- en Achternaam</label>
                        <input type="text" id="naam" name="naam" required
                               value="<?= htmlspecialchars($_POST['naam'] ?? '') ?>">
                    </div>
                </fieldset>
            <?php endif; ?>

                        <fieldset class="formulier-sectie">
                <legend>Afleveradres</legend>
                <!-- Staat er al een adres bij het account? Dan vullen we het vast in -->
                <div class="formulier-veld straat">
                    <label for="straat">Straat</label>
                    <input type="text" id="straat" name="straat" required
                           value="<?= htmlspecialchars($_POST['straat'] ?? $opgeslagenAdres['straat']) ?>">
                </div>
                <div class="formulier-veld huisnummer">
                    <label for="huisnummer">Huisnummer</label>
                    <input type="text" id="huisnummer" name="huisnummer" required
                           value="<?= htmlspecialchars($_POST['huisnummer'] ?? $opgeslagenAdres['huisnummer']) ?>">
                </div>
                <div class="formulier-veld">
                    <label for="stad">Stad</label>
                    <input type="text" id="stad" name="stad" required
                           value="<?= htmlspecialchars($_POST['stad'] ?? $opgeslagenAdres['stad']) ?>">
                </div>
                <div class="formulier-veld postcode">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode" required
                           value="<?= htmlspecialchars($_POST['postcode'] ?? $opgeslagenAdres['postcode']) ?>">
                </div>
            </fieldset>


            <fieldset class="formulier-sectie">
                <legend>Betaalwijze</legend>
                <div class="formulier-veld radio">
                    <label for="ideal">Ideal</label>
                    <input type="radio" id="ideal" name="betaalwijze" value="ideal"
                           <?= ($_POST['betaalwijze'] ?? '') === 'ideal' ? 'checked' : '' ?>>
                </div>
                <div class="formulier-veld radio">
                    <label for="contant">Contant</label>
                    <input type="radio" id="contant" name="betaalwijze" value="contant"
                           <?= ($_POST['betaalwijze'] ?? '') === 'contant' ? 'checked' : '' ?>>
                </div>
                <select class="bank-dropdown" name="bank" id="bank" aria-label="Bank">
                    <option value="ing">ING</option>
                    <option value="ASN">ASN</option>
                    <option value="rabobank">Rabobank</option>
                </select>
            </fieldset>

            <!-- De echte controle op een leeg mandje zit in de logica, dit is alleen UX -->
            <button type="submit" name="actie" value="bestellen"
                    <?= $winkelmandjeRegels ? '' : 'disabled' ?>>Bestelling bevestigen</button>
        </form>
    </section>

    <?php include __DIR__ . '/winkelmandje.php'; ?>
</main>
