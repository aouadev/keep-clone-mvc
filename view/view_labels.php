<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Labels</title>
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
            <a class="back" href="<?=$url_back_page?>"><span class="material-symbols-outlined">arrow_back_ios</span></a> 
          
        </div>
        
        <p class="view-share-title"> Labels :</p>
    
        <div class="view-share-items">
        <div class="share-list mb-2">
            <?php if(count($labels) == 0): ?>
                <p class="not_shared">This note does not have a label yet.</p>
            <?php else : ?> 
                
                 
                <?php foreach ($labels as $row): ?>
                    <?php $i =$row['note'] ?>
                    <form action="note/open_labels/<?=$i?>/<?=$i?>" method="post">
                    <div class="share-item mb-2">

                        <div class="label_content p-2"><?= $row['label'] ?></div>
                      
                        <button name="delete"class="btn btn-danger btn-delete-label" value="<?=$row['label']?>" type="submit">-</button>
                  
                    </div>
                    </form>
                <?php endforeach; ?>
                
            <?php endif; ?>
        </div>
        <form action="note/open_labels/<?=$note_id?>" method = "post">
            <label class="label_title">Add a new Label</label>
            <div class="add_label">
                <input list="labels_list" type="text" class="label_txt" name="label" autocomplete="off" value="<?=$label?>" placeholder="Type to search or create...">
               
                <datalist  id="labels_list" >
               
                <?php foreach ($other_labels as $label): ?>
                    
                
                        <option value="<?=$label['label']?>"></option>
                    
                    <?php endforeach; ?>
                </datalist>
                <div class="col-btn ">
                    <input class="btn btn-primary btn-plus" value="+" type="submit">
                </div>
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
        </form>
    
      
          
         
  
</div>

        
        
</body>
</html>