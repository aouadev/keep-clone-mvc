<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Note</title>
    <base href="<?= $web_root ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <script src="JS/edit_errors.js" ></script>
</head>
<body>
    <form method="post" action="note/edit_checklist_note/<?= $id?>">
        <div class="edit">
            <a class="back" href="note/index"><span class="material-symbols-outlined">arrow_back_ios</span></a>
            <input class="material-symbols-outlined save" type="submit" id="saveButton" value='save'>
        </div>
        <label for="title" class="title_note_title">Title</label>
        <input type="text" class="title_edit_note form-control title_add <?= (!empty($title_errors) ? "border border-danger" :
         (isset($_POST["title"]) && !empty($_POST["title"]) ? "border border-success" : "")) ?>" id="title" name="title" value="<?= $title ?>">
        <div class="error_add_note_php">
            <?php if (count($title_errors) != 0): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($title_errors as $error): ?>
                            <li><?= $error?></li>
                            <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <label for="content" class="note_body_title">Items</label>
        <?php if($id == 0): ?>
            <ul class="add_cn">
            <?php $val = count($content) == 0 ? 5 : count($content); ?>
            <?php for($i = 0;$i < $val; $i++) : ?>
              
                        <li> <input type="text" class="item_new" id="content" name="content[]" value="<?= count($content) == 0 ? '' : $content[$i]['content']?>"></li>
                        <div class="error_add_cn_note">
                            <?php if (count($items_errors) != 0): ?>
                                <div class="errors">
                                    <ul>
                                        <?php foreach ($items_errors[$i] as $error): ?>
                                            <li><?= $error?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                 
                    <?php endfor; ?>
                    </ul>
                <?php else : ?>
                    <div class="edit_cn">
                    <?php for($i = 0;$i < count($content); $i++) : ?>
                   
                    <div class="edit_cn_item">
                        <div class="add_cn_check_icon">
                            <?php if($content[$i]['checked']): ?>
                                <div class="material-symbols-outlined " id="check_item_icon">check</div>
                            <?php else : ?>
                                <div class="uncheck_item_icon"></div>
                            <?php endif; ?>
                        </div>
                        <input type="text"  class="item_edit" id=<?=$content[$i]['checked'] ? 'content_checked' : 'content_unchecked'?> name='content[]' value="<?= $content[$i]['content'] ?>">
                        <button name="delete_item"class="btn btn-danger btn-delete-label" value="<?=$content[$i]['id']?>" type="submit">-</button>
                    </div>
                    <div class="error_add_cn_note">
                            <?php if (count($items_errors) != 0): ?>
                                <div class="errors">
                                    <ul>
                                        <?php foreach ($items_errors[$i] as $error): ?>
                                            <li><?= $error?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                    </div>
                    <?php endfor; ?>
                    <div class="add_new_item">
                    <label class="">New Item</label>
                <div class="add_label">
                    <input  type="text" class="label_txt" name="item_content" value="<?=$item_content?>">
                    <div class="col-btn ">
                        <input class="btn btn-primary btn-plus" value="+" type="submit">
                    </div>
                </div>
                    </div>
                    </div>
              
               
                <?php endif; ?>
      
    </form>
    <script>
    var userId = <?= json_encode($note->owner); ?>;
    console.log(userId); // Pour vérifier que la valeur est correctement passée
    </script>
    <script src="JS/edit_errors.js"></script>

</body>
</html>