<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View_share</title>
    <base href="<?= $web_root ?>" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <script src="JS/edit_errors.js" ></script>
</head>
<body>
    <div class="container mt-3">
        <div class="">
            <a class="back" href="<?= $_SESSION['previous_page'] ?>"><span class="material-symbols-outlined">arrow_back_ios</span></a> 
          
        </div>
        
        <p class="view-share-title"> Shares :</p>
        <div class="view-share-items">
        <div class="share-list mb-2">
            <?php if(count($list) == 0): ?>
                <p class="not_shared">This note is not shared yet.</p>
            <?php else : ?> 
                
                 
                <?php foreach ($list as $row): ?>
                    <?php $i =$row['user'] ?>
                    <form action="note/editor_and_delete_btn/<?=$note_id?>/<?=$i?>" method="post">
                <div class="share-item mb-2">

                        <div class="share-name p-2"><?= $row['full_name'] ?> (<?=$row['editor'] ?>)</div>
                        <button name="editor"class="btn btn-primary btn-recycle " type="submit"><img src="images/change_circle.svg" alt="change_circle"></button>
                        <button name="delete"class="btn btn-danger btn-delete " type="submit">-</button>
                  
                </div>
                    </form>
                <?php endforeach; ?>
                
            <?php endif; ?>
        </div>
        <?php if(count($users)> 0): ?>
        <div class="dropdown-view-share ">
                <div class=" col-users">
                    <form action="note/share_note/<?=$note_id?>" method="post">
                    <select name="selected_user" class="form-select select-user" aria-label="Default select example">
                        <option class="dropdown-item" selected>-User-</option>
                        <?php foreach($users as $user):?>
                            <option class="dropdown-item" value="<?=$user->full_name?>"><?= $user->full_name ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-permission">
                        <select name="selected_permission" class="form-select select-permission" aria-label="Default select example">
                            <option class="dropdown-item" selected>-permission-</option>
                            <option class="dropdown-item" value="Reader">Reader</option>
                            <option class="dropdown-item" value="Editor">Editor</option>
                        </select>
                    </div>
                    <div class="col-btn ">
                        <input class="btn btn-primary btn-plus" type="submit" value="+">
                    </div>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
