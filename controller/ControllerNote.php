<?php

require_once "framework/Controller.php";
require_once "framework/View.php";
require_once "model/User.php";
require_once "framework/Tools.php";

class ControllerNote extends Controller
{
    public function index(): void
    {
        $user = $this->get_user_or_redirect();
        $notes_pinned = $user->get_notes_pinned();
        $notes_unpinned = $user->get_notes_unpinned();
        $my_labels = $user->get_all_my_labels();
   
       
        (new View("notes"))->show(["currentPage" => "my_notes", 
                                    "notes_pinned" => $notes_pinned, 
                                    "notes_unpinned" => $notes_unpinned, 
                                     "user" => $user,
                                      "sharers" => $user->shared_by(),
                                    "my_labels" => $my_labels]);
    }

    public function move_up(): void
    {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["up"]) && $_POST["up"] != "") {
            $id = $_POST["up"];
            $note = Note::get_note_by_id($id);
            if ($note === false)
                throw new Exception("undefined note");
            $other = $note->get_note_up($user, $id, $note->get_weight(), $note->isPinned());
            $note->move_db($other);
            $this->redirect("note", "index");
        } else {
            throw new Exception("Missing ID");
        }
    }
    public function move_down(): void
    {
        $user = $this->get_user_or_redirect();
        if (isset($_POST["down"]) && $_POST["down"] != "") {
            $id = $_POST["down"];
            $note = Note::get_note_by_id($id);
            if ($note === false)
                throw new Exception("undefined note");
            $other = $note->get_note_down($user, $id, $note->get_weight(), $note->isPinned());
            $other->move_db($note);
            $this->redirect("note", "index");
        } else {
            throw new Exception("Missing ID");
        }
    }

    //  Méthode pour ajouter ou éditer une texte note
    public function edit_text_note() : void {
        if (isset($_GET['param1']) && isset($_GET['param1']) !== "") {
            $errors = [];
            $id = $_GET['param1'];
            if ($id != 0) {
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
            $title = $note->title;
            $content = $note->get_content();
            } else {
                $title = '';
                $content = '';
            }
        if(isset($_POST['title']) && isset($_POST['content'])) {
            $user = $this->get_user_or_redirect();
            $title = $_POST['title'];
            $content = $_POST['content'];
            $errors = $user->validate_name($title); 
            if($id == 0) {
                $errors = array_merge($errors, $user->validate_title_note($title));
                $note = new TextNote($title, $user->id, Date("Y-m-d H:i:s"));
                $weight = $note->max_weight();
                $note->set_weight($weight+1);
                $note->set_content($content);
            } else {
                $errors = array_merge($errors, $user->validate_title_note($title));
                $note->title = $title;
                $note->set_content($content);
            }
            if(empty($errors)) {
                $note->persist();
                $this->redirect("open_note", "index/$note->note_id");
            }
        } 
    }
        (new view("add_text_note"))->show(["errors" => $errors, 'title' =>$title, 'content' => $content, 'id' => $id]);
        
    }
         
    

    public function drag_and_drop() {
        
        if(isset($_POST['arrayorder'] , $_POST['update'])) {
            $array = $_POST['arrayorder'];
            if($_POST['update'] == "update") {
                $count = 1;
                foreach ($array as $idval) {
                    NOTE::update_drag_and_drop($count, $idval);
                    $count++;
    }

}
    }
        
    }


  
    public function add_checklist_note()
    {
        $user = $this->get_user_or_redirect();
        $errors = [];
        // Vérification des doublons pour les éléments
        $duplicateErrors = [];
        $duplicateItems = [];

        if(isset($_POST['title']) && $_POST['title'] == "") {
            $errors['title'] = "Title required";
        }
        if (isset($_POST['title'], $_POST['items']) && $_POST['title'] != "") {
            $title = Tools::sanitize($_POST['title']);
            $items = $_POST['items'];
            // Initialisation d'un tableau pour les éléments non vides
            $non_empty_items = [];

            // Parcours des éléments pour ne sauvegarder que les non vides
            foreach ($items as $item) {
                if (!empty($item)) {
                    $non_empty_items[] = $item;
                }
            }
            $note = new ChecklistNote(
                0,
                $title,
                $user->id,
                date("Y-m-d H:i:s"),
                false,
                false,
                0
            );
            $errors = $note->validate_title($title);


            foreach ($non_empty_items as $key => $item) {
                if (in_array($item, $duplicateItems)) {
                    // Stocker l'erreur de doublon avec l'indice correspondant
                    $duplicateErrors["item_$key"] = "Items must be unique.";
                }
                $duplicateItems[] = $item;
            }


            // Combinaison des erreurs de doublons avec d'autres erreurs
            $errors = array_merge($errors, $duplicateErrors);
        } 
        if (empty($errors) && isset($_POST['title'], $_POST['items']) && $_POST['title'] != "") {
            $note->persist();
            $note->new();
                // Parcours des erreurs de doublons
            foreach ($non_empty_items as $key ) {
                // Création d'une nouvelle instance de CheckListNoteItem
                $content = $key; // Récupération du contenu de l'élément
                $checklistNoteId = $note->note_id; // Récupération de l'identifiant de la note de checklist
                $checked = false; // Par défaut, l'élément n'est pas coché
                
                // Création de l'instance CheckListNoteItem
                $checklistItem = new CheckListNoteItem(
                    0, // L'identifiant sera généré automatiquement par la base de données
                    $checklistNoteId,
                    $content,
                    $checked
                );
                
                // Enregistrement de l'élément dans la base de données
                $checklistItem->persist();
                }
            
            $this->redirect("openNote", "index", $note->note_id);
        }

        // Afficher la vue avec les erreurs
        (new View("add_checklist_note"))->show(["errors" => $errors]);
    }

  
    // Supprimer une Note
    public function delete_note()  {
        if (isset($_POST['delete_note'])) {
            $note_id = $_POST['delete_note'];
            $note = Note::get_note_by_id($note_id);
        }
        if (isset($_POST['delete_confirm'])) {
            $user = $this->get_user_or_redirect();
            $note_id = $_POST['delete_confirm'];
            $note = Note::get_note_by_id($note_id);
            $note->delete($user);
               $this->redirect("user", "my_archives");
        }
        (new View('confirm_delete'))->show(["note" =>$note]);
    }

    public function edit_checklist_note(): void

    {
        
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_GET["param1"]) && isset($_GET["param1"]) !== "") {
            $id = $_GET['param1'];

            $note = CheckListNote::get_note_by_id($id);
            // Vérifie si la note existe
            if ($note === false) {
                throw new Exception("Undefined note");
            }
            if (isset($_POST["title"]) && $_POST["title"] != "") {
                $title = Tools::sanitize($_POST["title"]);
                $note = Note::get_note_by_id($id);
                $errors = $note->validate_title($title);
                if (empty($errors)) {

                    $note->title = $title;
                    $note->persist();
                }
            }
            if (isset($_POST['delete']) && $_POST['delete']) {
                $item_id = $_POST["delete"];
                $item = CheckListNoteItem::get_item_by_id($item_id);
                if ($item === false) {
                    throw new Exception("Undefined checklist item");
                }
                // Supprime l'élément de la liste de contrôle
                $item->delete();
                $this->redirect("openNote", "edit/$id");
            }
            if (isset($_POST['new']) && $_POST["new"] != "") {
                $new_item_content = Tools::sanitize($_POST['new']);
                $new_item = new CheckListNoteItem(5, $note->note_id, $new_item_content, 0);
                $new_item->persist();
                $this->redirect("openNote", "edit/$id");
            }
            $this->redirect("openNote", "index/$id");
        }
    }




    public function open_shares() {
        if (isset($_GET['param1']) && isset($_GET['param1']) !=="") {
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
            
            $aray_users= $note->list_share_users();
         
            $list_share = $note->share_list();
            $url_back_page = "open_note/index/$note_id";
            (new view('shares'))->show(["users" => $aray_users, "list" => $list_share, "note_id" => $note_id, "url_back_page" => $url_back_page]); 
        }  
        
    }
    
    public function share_note() {
        if (isset($_POST['selected_user']) && isset($_POST['selected_permission']) && isset($_GET['param1'])) {
            $editor = $_POST['selected_permission'] == "Editor" ? 1 : 0;
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
            $user_id = $_POST['selected_user'];
            if($user_id != null) {
                $user = User::get_user_by_id($user_id);
                $note->share_note($user, $editor);
            }
            $this->redirect("note", "open_shares/$note_id");
        }
    }

    public function open_labels()  {
        if (isset($_GET['param1']) && isset($_GET['param1']) !=="") {
            $label = '';
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
            $url_back_page = "open_note/index/$note_id";
            $array_labels = $note->list_labels();
            $user = User::get_user_by_id($note->owner);
            $array_all_labels = $user->get_all_labels();
            $other_labels = $user->other_labels($note_id);
            $errors = [];
            if (isset($_POST['delete'])) {
                $label = $_POST['delete'];
                $note->delete_label($note_id, $label);
                $this->redirect("note", "open_labels/$note_id");
            }
            if (isset($_POST['label']) && isset($_POST['label']) != "") {
                $label = $_POST['label'];
                $errors = $note->validate_label($label);
                if(count($errors) == 0) {
                    $note->add_label($note_id, $label);
                    $this->redirect("note", "open_labels/$note_id");
                }
               
    }
        (new view('labels'))->show(["url_back_page" => $url_back_page, 
        "labels" => $array_labels, "all_labels"=> $array_all_labels,'note_id'=>$note_id,
         "other_labels"=>$other_labels, "errors"=>$errors, "label"=>$label]);
        
    }
}
    public function editor_and_delete_btn() {
        if(isset($_GET['param1']) && isset($_GET['param1']) !=="" 
        && isset($_GET['param2']) && isset($_GET['param1']) !=="") {
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
            $user_id = $_GET['param2'];
            if(isset($_POST['editor'])) {
                $note->set_editor_and_reader($user_id);
               
            }
            else if(isset($_POST['delete'])) {
                $note->delete_from_share($user_id);
            }
            $this->redirect("note", "open_shares/$note_id");
        }
    }
    public function delete_label()  {
      
        if (isset($_POST['delete']) && isset($_GET['param1'])) {
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
            $label = $_POST['delete'];
            $note->delete_label($note_id, $label);
            $this->redirect("note", "open_labels/$note_id");
        }
        
    }
    public function add_label(){
        if (isset($_POST['label']) && isset($_GET['param1']) && isset($_POST['label']) != "") {
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
            $label = $_POST['label'];
            $errors[] = $note->validate_label($label);
            if(count($errors) == 0) {
                $note->add_label($note_id, $label);
                
            }
            $this->redirect("note", "open_labels/$note_id");
         

        }
        
    }

    
    

}
