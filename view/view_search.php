<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projet Keep</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <script>
        
        let labels, notes, decodedLabels, checkedLabels, checked; 
        $(function() {
            labels =  $("input[name='check_labels[]']");
            notes = $("notes");
           
           labels.click(function() {
            checkedLabels = labels.filter(":checked").map(function() {
                return $(this).val();
            }).get();
            console.log("Checked labels:", checkedLabels);
                getNotes(checkedLabels);
        

            });
             
        
        });
        async function getNotes(labels) {
            
          
          try {
            let encodedLabels = json_encode(labels);
            console.log(encodedLabels);
        
              myNotes = await $.getJSON("note/get_my_filtred_notes_service/" + encodedLabels);
              $("notes").html(myNotes);
              console.log("my notes:", myNotes);

          }catch(e) {
              $("notes").html("<tr><td>Error encountered while retrieving the notes!</td></tr>");
          }
      }
 
    </script>

</head>


<body>
    <?php include('view/menu.php'); ?>
    <?php $back = "back_my_notes" ?>
    <div class="view_notes_header">
        <h1>Search My Notes</h1>
    </div>
    <div class="search_title_tag">Search notes by tags :</div> 

    <form action="note/search" method="post">
    <div class='search_form'>
      
            <?php for ($i = 0; $i < Count($all_labels); $i++): ?>
                <div class="search_items">
                    <input class="uncheck_item_icon" id='search_check_uncheck_icon' name="check_labels[]" type="checkbox" value="<?=$all_labels[$i]['label'] ?>"
                    <?= in_array($all_labels[$i]['label'], $check_data)? 'checked' : ''; ?>/>
                    <div class="check_uncheck_label"><?= $all_labels[$i]['label']; ?></div>
                </div>
            <?php endfor; ?>
    </div>
    <noscript>
    <button type="submit" name="search" class="btn btn-primary m-3" value="<?=$all_labels?>">Search</button>

    </noscript>
    </form>
 

    <div class="your_notes_title" id="notes"><?=count($my_notes) == 0 ? '' : 'Your notes :'?></div>
   
    <div class="my_notes_labels">
    <?php if (count($my_notes) != 0): ?>
         
         <?php foreach ($my_notes as $notes): ?> 
            <?php foreach ($notes as $note_item): ?>
            <?php $back = "search_$encoded_data"?>
        <a class="link-note-archivee" href="open_note/index/<?=$note_item["id"]?>/<?=$back ?>"> 
             <div class="note-archivee">
                <?php include("note_in_list.php") ?>
            </div>
             <?php endforeach;?>
             </a>
         <?php endforeach; ?>
     <?php endif; ?>
    </div>
    <div class="search_shared">
 
    <?php if(count($shared_by) != 0): ?>
       <?php foreach($shared_by as $notes): ?>
        <?php foreach($notes as $note_item): ?>
            <div>Notes shared by  <?=USER::get_user_by_id($note_item['owner'])->full_name;?> :</div>
            <?php $back = "search_$encoded_data"?>
            <a class="link-note-archivee" href="open_note/index/<?=$note_item["id"]?>/<?=$back ?>"> 
            <div class="note-archivee">
                <?php include("note_in_list.php") ?>
            </div>
        <?php endforeach;?>
        </a>
        <?php endforeach;?>
         
    
        <?php endif; ?>
  
    
    </div>


  
   
 
          


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>



</body>

</html>