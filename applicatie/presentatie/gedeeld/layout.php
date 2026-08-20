<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Standaard waarde voor de titel als er geen paginatitel is. -->
    <title><?= htmlspecialchars($paginaTitel ?? 'Pizzeria Sole Machina')?>></title>
    <link rel="icon" type="image/png" href="./images/icon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="<?= htmlspecialchars($bodyKlasse ?? '') ?>">
    
    <?php include __DIR__ . "/header.php";?>
    
    <?php if (!empty($toonBanner)): ?>
        <?php include __DIR__ . "/header_banner.php"; ?>
    <?php endif; ?>

    <?php include $inhoud; ?>

    <?php include __DIR__ . "/footer.php";?>
</body>
</html>