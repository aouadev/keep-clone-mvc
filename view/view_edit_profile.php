<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container_login_signup">
        <div class="main_login_signup edit_profile">
            <div class="title">Edit Profile</div>
            <form action="settings/edit_profile" method="post">
                <div class="inputbox text">
                    <i class="material-symbols-outlined">mail</i>
                    <input id="mail" name="mail" type="text" placeholder="<?=$mail?>" value="<?= $mail ?>">
                </div>
                <div class="inputbox text">
                    <i class="material-symbols-outlined">person</i>
                    <input id="full_name" name="name" type="text" placeholder="<?=$name?>" value="<?= $name ?>">
                </div>
                <?php if (count($errors) != 0): ?>
                    <div class="errors">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <div class="inputbox button login">
                    <input type="submit" value="Save Change">
                </div>
                <div class="inputbox button cancel" >
                    <input type="button" value="Cancel" onclick="window.location.href='settings/settings';">
                </div>
            </form>
        </div>
    </div>
</body>
</html>