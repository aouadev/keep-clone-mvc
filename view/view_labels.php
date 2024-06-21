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
  
    <script src="lib/jquery-3.7.1.min.js" type="text/javascript"></script>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
 
    <script>
        const minLength = <?=Configuration::get('label_min_length'); ?>;
        const maxLength = <?=Configuration::get('label_max_length'); ?>;
        const id = <?=$note_id?> ;
       let labels = <?=json_encode(array_column($labels, 'label'));?>;
        let label, errLabel;

        function checkLabel() {
            errLabel.html("");
            let ok = true;
       
            if (label.val().length < minLength || label.val().length > maxLength) {
                errLabel.append("Label must be cooooo between 2 and 10");
                ok = false;
            }
           
            return ok;
        }
        function updateBtnAdd() {
            isValid = checkLabel() && checkUnicity();
            label.removeClass("is-valid is-invalid")
            labelClass =  isValid ? "is-valid" : "is-invalid";
            $("#label").addClass(labelClass);
            $("#add_label_btn").prop("disabled", !isValid);
        }
        
      /*  async function checkLabelUnicity() {
            let ok = true;
            const data = await $.getJSON("note/Label_is_unique/" + id + "/"+ label.val());
            if (data) {
                errLabel.append("<p>A note cannot contain the same label Twice.</p>");
                ok = false;
        }
        
        return ok;
        }*/
        function checkUnicity() { //  une fonction non async pour gérer la désactivation activation du bouton add 
            let ok = true;
            errLabel.html("");
            console.log(labels);
            console.log(label.val());
            if (labels.includes(label.val())) {
                $('#errLabel').append("<p>A note cannot contain the same label Twice.</p>");
                console.log(errLabel.val());
                ok = false;
            }
            return ok;
    }

        async function checkAll() {
            let ok = checkLabel();
            if (ok) {
                ok = await checkLabelUnicity();
            }
            return ok;
        }
        $(function() {
            label = $("#label");
            errLabel = $("#errLabel");
            label.bind("input", function() {
                updateBtnAdd();
            });
  
         
      


        });
  
    </script>
    <script>

    </script>
</head>
<body>
  
    <div class="container mt-3">
        <div class="">
            <a class="back" href="<?=$back?>"><span class="material-symbols-outlined">arrow_back_ios</span></a> 
          
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
        <form action="note/open_labels/<?=$note_id?>" method = "post" onsubmit="return checkAll();">
            <label class="label_title ">Add a new Label</label>
            <div class="add_label">

                <input list="labels_list" type="text" class="label_txt " name="label" id="label"
                autocomplete="off" value="<?=$label?>" placeholder="Type to search or create..."> <!-- j'ai supprimé la gestion de la couleur de l'input car le placeholder devient invisible-->
               
                <datalist  id="labels_list" >
               
                <?php foreach ($other_labels as $label): ?>
                    
                
                        <option value="<?=$label['label']?>"></option>
                    
                    <?php endforeach; ?>
                </datalist>
                <div class="col-btn ">
                    <input class="btn btn-primary btn-plus" 
                    id="add_label_btn" value="+" type="submit">
                </div>
            
            </div>
            <div class="errors" id="errLabel"></div>
        
            
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