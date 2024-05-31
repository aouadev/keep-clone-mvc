<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Confirm_delete</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
   
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
</head>
<body>
    <div class="container_delete_note">
    <div class="main_delete_note">
    <div class="title"><span class="material-symbols-outlined">delete_forever</span> <p> Are you sure ?</p></div>
    <p class="txt_confirm_delete">Do you really want to delete note <strong>"<?=$note->title?>" </strong> all of its dependencires ? </p>
    <p class="txt_confirm_delete">This process cannot be undone.</p>
    <form action="note/delete_note" method="post">
    <div class="btn_confirm_delete" >
       
        <a href="open_note/index/<?=$note->note_id?>" type="button" class="btn btn-secondary">Cancel</a>
    
            <button name="delete_confirm"type="submit" class="btn btn-danger" value="<?=$note->note_id?>">Delete</button>
        </form>
    </div>
   
  
  


   

                    
          
    
  
              
            
     </div>
    </div>
  </body>
</html>





