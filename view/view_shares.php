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
        <div class="edit">
            <a class="back" href="<?= $_SESSION['previous_page'] ?>"><span class="material-symbols-outlined">arrow_back_ios</span></a> 
          
        </div>
        <div class="container  h-100">
        <h2 class="text-white"> Shares :</h2>
        <div class="container  h-100">
    <h2 class="text-white"> Shares :</h2>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="m-3 combobox">
            <select class="form-select" aria-label="Default select example">
                <option selected>-User-</option>
                <?php foreach($users as $user):?> 
                    <option value="<?=$user->full_name?>"><?= $user->full_name ?></option>
                <?php endforeach ?>
            
            </select>
        </div>
        <div class="m-3">
            <select class="form-select" aria-label="Default select example">
                <option selected>-permission-</option>
                <option value="Reader">Reader</option>
                <option value="Editor">Editor</option>
            
            </select>
        </div>
        <div class="btn btn-primary btn-lg m-3">+</div>
    </div>

        </div>
      
</body>
</html>