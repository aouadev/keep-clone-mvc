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
            <?php foreach ($list as $row): ?>
            <div class="share-item mb-2">
                
                <div class="share-name p-2"><?= $row['full_name'] ?> (<?=$row['editor'] ?>)</div>
                <button class="btn btn-primary btn-recycle "><img src="images/change_circle.svg" alt="change_circle"></button>
                <button class="btn btn-danger btn-delete ">-</button>
            </div>
            <?php endforeach; ?>
        </div>
     
            <div class="dropdown-view-share ">
                <div class=" col-users">
                    <div class="dropdown">
                        <button class="btn  dropdown-toggle custom-dropdown-users " type="button" data-bs-toggle="dropdown" aria-expanded="false">-Users-
                            </button>
                            <ul class="dropdown-menu">
                                <?php foreach($users as $user):?> 
                                <li><a class="dropdown-item" href="#"><?= $user->full_name ?></a></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                </div>
                <div class="col-permission">
                    <div class="dropdown ">
                        <button class="btn dropdown-toggle custom-dropdown-permission " type="button" data-bs-toggle="dropdown" aria-expanded="false">-Permission-
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Reader</a></li>
                            <li><a class="dropdown-item" href="#">Editor</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-btn ">
                    <div class="btn btn-primary btn-plus">+</div>
                </div>
            </div>
        </div>
    </div>
                                
    </body>
</html>
