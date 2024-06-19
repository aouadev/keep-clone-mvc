<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id == 0 ? "Add new text note" : "Edit text note" ?></title>
    <base href="<?= $web_root ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <script src="JS/edit_note.js" ></script>
    <script>
    const minLength = <?=Configuration::get('title_min_length'); ?>;
    const maxLength = <?=Configuration::get('title_max_length'); ?>;
    const minDescription = <?=Configuration::get('description_min_length'); ?>;
    const maxDescription = <?=Configuration::get('description_max_length'); ?>;
    </script>
</head>
<body>
    
    <form method="post" action="note/edit_text_note/<?= $id?>" onsubmit="return checkAll();">
        <div class="edit">
            <a class="back" href="note/index"><span class="material-symbols-outlined">arrow_back_ios</span></a>
            <input class="material-symbols-outlined save" type="submit" id="saveButton" value='save'>
        </div>
     
        <label for="title" class="title_note_title">Title</label>
        <input type="text" class="title_edit_note form-control title_add <?= (!empty($errors) ? "border border-danger" : (isset($_POST["title"]) && !empty($_POST["title"]) ? "border border-success" : "")) ?>" id="title" name="title" value="<?= $title ?>">
        <div id="titleError" class="errors"></div>
        <div class="error_add_note_php">
            <?php if (count($errors) != 0): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                </div>
            <?php endif; ?>
        </div>
        
        <label for="content" class="note_body_title">Text</label>
        <textarea class="note_body_text add_text_note" id="description" name="content" ><?=$content?></textarea>
        <div id="errDescription" class="errors"></div>
        
    </form>




</body>
</html>
