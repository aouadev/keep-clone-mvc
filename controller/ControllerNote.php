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
        $my_labels = $user->get_all_my_labels_notes();
        
   
       
        (new View("notes"))->show(["currentPage" => "my_notes", 
                                    "notes_pinned" => $notes_pinned, 
                                    "notes_unpinned" => $notes_unpinned, 
                                     "user" => $user,
                                      "sharers" => $user->shared_by(),
                                    "my_labels" => $my_labels]);
    }


    // Méthode pour bouger la note vers la droite
    public function move_right() : void {
        if (isset($_POST["right"]) && $_POST["right"] != "") {
            $id = $_POST["right"];
            $note = Note::get_note_by_id($id);
            $note->move_right();
            $this->redirect("note", "index");


        } else {
        throw new Exception("Missing ID");
    }
}
    //Méthode pour faire bouger la note vers la gauche
    public function move_left() : void {
        if (isset($_POST["left"]) && $_POST["left"] != "") {
            $id = $_POST["left"];
            $note = Note::get_note_by_id($id);
            $note->move_left();
            $this->redirect("note", "index");


        } else {
        throw new Exception("Missing ID");
    }
        
    }

    //  Méthode pour ajouter ou éditer une texte note
    public function edit_text_note() : void {
        $errors = [];
        if (isset($_GET['param1']) && $_GET['param1'] !== "" 
           ) {
            $id = $_GET['param1'];
      
            if ($id != 0) {
               // $note_id = $_GET['param1'];
                $note = Note::get_note_by_id($id);
                $title = $note->title;
                $content = $note->get_content();
            }
            else {
                $content = '' ;
                $title = '';
            }
        if(isset($_POST['title']) && (isset($_POST['content']))) {
            $user = $this->get_user_or_redirect();
            $title = $_POST['title'];
            $content = $_POST['content'];
            
  
            $errors = $user->validate_title($title);        

            $errors = array_merge($errors, $user->validate_title_note($id, $title));
            if(empty($errors) ) {
                if($id == 0) {
                    $note = new TextNote($title, $user->id, Date("Y-m-d H:i:s"));
                    $weight = $note->max_weight();
                    $note->set_weight($weight + 1);
                    $note->set_content($content);
                    
                
                } else {
            
                    $note->title = $title;
                    $note->set_content($content);
                }
               $note->persist();
               $id = $note->note_id;
               $this->redirect("open_note", "index/$note->note_id");
            }
        } 
    }
        (new view("add_text_note"))->show(["errors"=> $errors, 'title' =>$title, 'content' => $content, 'id' => $id]);
    }
    
    //  Méthode pour ajouter ou éditer une texte note
    public function edit_checklist_note() : void {
        $title_errors = [];
        $items_errors = [];
        $new_item_error = [];
        $error_empty_items = "";
        $new_item = "";
        if (isset($_GET['param1']) && $_GET['param1'] !== "" ) {
            $id = $_GET['param1'];
            if ($id != 0) { // cas de l'édition d'une note
                $note = Note::get_note_by_id($id);
                $title = $note->title;
                $content = $note->get_content();
            }
            else { // cas d'ajout d'une nouvelle note
                $content = [] ;
                $title = '';
            }
        if(isset($_POST['title']) && (isset($_POST['content']))) {
            $user = $this->get_user_or_redirect();
            $title = $_POST['title'];
            $new_content = $_POST['content'];
            for($i = 0; $i < count($new_content); $i++) {
                $content[$i]['content'] = $new_content[$i];
            }
            $title_errors = $user->validate_title($title); 
            $title_errors = array_merge($title_errors, $user->validate_title_note($id, $title));
        
            // vérifier l'unicité des items 
            for($i = 0; $i < count($new_content); $i++) {
                $items_errors[$i] = CheckListNote::validateItems($new_content[$i], $new_content, $id, false);
            }
            $error_empty_items = $this->allEmpty($new_content) ? "at least one item " : ""; // pour avoir au moins un élément dans la checklist
            // ajouter un nouveau item quand on édite la checklist note via le bouton + ou le bouton save
            if (isset($_POST['new_item']) && $_POST['new_item'] != '' && count($title_errors) == 0) { 
                $new_item = $_POST['new_item'];
                //vérifier l'unicité et la longueur du nouveau item 
                $new_item_error = CheckListNote::validateItems($new_item, $new_content, $id, true); 
                if (empty($new_item_error)) {
                    $note->add_item($id, $new_item);
                    $this->redirect("note", "edit_checklist_note/$id");
                }
            }
            // pour supprimer un item pendant le edit checklist note via le bouton -
            if (isset($_POST['delete_item']) && count($title_errors) == 0) {
                $item_id = $_POST['delete_item'];
                $note->delete_item($item_id);
                $this->redirect("note", "edit_checklist_note/$id");
            }
            if(empty($title_errors) && $this->allEmpty($items_errors) && empty($new_item_error) && $error_empty_items == "") {
            
                if($id == 0) {
                    $note = new CheckListNote($title, $user->id, Date("Y-m-d H:i:s"));
                    $weight = $note->max_weight();
                    $note->set_weight($weight + 1);
                } else {
                    $note->title = $title;
                    for($i = 0; $i < count($new_content); $i++) {
                        $content[$i]['content'] = $new_content[$i];
                    }
                }
                $note->set_content($content);
                $note->persist();
           $this->redirect("open_note", "index/$note->note_id");
            }
        }
    
    }
    (new view("add_checklist_note"))->show(["title_errors"=> $title_errors,"items_errors" => $items_errors, 
                                                 'title' =>$title, 'content' => $content, 'id' => $id, 'new_item'=>$new_item,
                                                  'new_item_error' =>$new_item_error, 'itemless' => $error_empty_items]);
                                                }

    public static function allEmpty($array) {
        foreach($array as $arr) {
            if(!empty($arr))
                return false;
        }
        return true;
            

    }
    public function add_item() {
        if (isset($_POST['item_content']) && isset($_GET['param1']) && $_GET['param1'] != '') {
            $note_id = $_GET['param1'];
            $item_content = $_POST['item_content'];
            $note = Note::get_note_by_id($note_id);
            if ($item_content != '') {
            $note->add_item($note_id, $item_content);
            }
            $this->redirect("note", "edit_checklist_note/$note_id");
            
        }


    }
    public function delete_item() {
        if (isset($_POST['delete_item']) 
            && isset($_GET['param1']) && $_GET['param1'] != '') {
            $note_id = $_GET['param1'];
            $item_id= $_POST['delete_item'];
            $note = Note::get_note_by_id($note_id);
            $note->delete_item($item_id);
            $this->redirect("note", "edit_checklist_note/$note_id");
        }
    }

    public function drag_and_drop() {
        if (isset($_POST['update']) && $_POST['update'] === 'update') {
            if (isset($_POST['order_pinned'])) {
                $order_pinned = $_POST['order_pinned'];
                if (is_array($order_pinned)) {
                    for($i = 0; $i < count($order_pinned); $i++) {
                        $idval = $order_pinned[$i];
                        $current_note = Note::get_note_by_id($idval);
                        if ($current_note->pinned == 0) {
                        $current_note->update_pinned(1);
                        }
                        if ($i > 0) {
                            $previousid = $order_pinned[$i - 1];
                            $previeus_note = Note::get_note_by_id($previousid);
                            if(($current_note->weight) > ($previeus_note->weight)) {
                                $j = $i;
                                while($current_note->weight > $previeus_note->weight && $j > 0) {
                                    $current_note->switch_weight($previeus_note);
                                    $previousid = $order_pinned[$j - 1];
                                    $previeus_note = Note::get_note_by_id($previousid);
                                    $j--;
                                }
                            }
                        }
                    }
                    echo json_encode(['status' => 'success']);
                    }
                } 
                if (isset($_POST['order_unpinned'])) {
                    $order_unpinned = $_POST['order_unpinned'];
                    if (is_array($order_unpinned)) {
                        for($i = 0; $i < count($order_unpinned); $i++)  {
                            $idval = $order_unpinned[$i];
                            $current_note = Note::get_note_by_id($idval);
                            if ($current_note->pinned == 1)
                                $current_note->update_pinned(0);
                            if ($i > 0) {
                                $previousid = $order_unpinned[$i - 1];
                                $previeus_note = Note::get_note_by_id($previousid);
                                if(($current_note->weight) > ($previeus_note->weight)) {
                                    $j = $i;
                                    while($current_note->weight > $previeus_note->weight && $j > 0) {
                                        $current_note->switch_weight($previeus_note);
                                        $current_note = $previeus_note;
                                        $previousid = $order_unpinned[$j - 1];
                                        $previeus_note = Note::get_note_by_id($previousid);
                                        $j--;
                                    }
                                }
                            }
                        }
                        echo json_encode(['status' => 'success']);
                    } 
                }
            } else {
                error_log("Update parameter missing or incorrect");
                echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            }
        }

    
    
    
    
    
    


  
    // Supprimer une Note
    public function delete_note()  {
        if (isset($_GET['param1'])) {
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
             $user = $this->get_user_or_redirect();
             if ($user->id == $note->owner || $user->role == "admin") {
            $note->delete($user);
          
            echo json_encode(['status' => 'success']);
            $this->redirect("user", "my_archives");
        } else {
            // Si l'utilisateur n'est pas autorisé, renvoyez une erreur
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        }
       }
       
       // (new View('confirm_delete'))->show(["note" =>$note]);
    }
    
    public function delete_note_php()  {
        if (isset($_GET['param1'])) {
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
             $user = $this->get_user_or_redirect();
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
        $errors = [];
        if (isset($_GET['param1']) && isset($_GET['param1']) !=="") {
            $label = '';
            $note_id = $_GET['param1'];
            $note = Note::get_note_by_id($note_id);
            $url_back_page = "open_note/index/$note_id";
            $array_labels = $note->list_labels();
            $user_id = $note->owner;
            
            $other_labels = $note->other_labels($note_id, $user_id);
            
      
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
        (new view('labels'))->show(["back" => $url_back_page, 
        "labels" => $array_labels,'note_id'=>$note_id,
         "other_labels"=>$other_labels, "errors"=>$errors, "label"=>$label]);
        
    }
}
    public function label_is_unique() {
        $res = "false";
        if (isset ($_GET['param1']) && $_GET['param1'] !== ""
            && isset($_GET['param2']) && $_GET['param2'] !== "") {
                $note = Note::get_note_by_id($_GET['param1']);
                $error = $note->same_label($_GET['param2']);
                if (!empty($error)) {
                     $res = "true";
                }
                   
            }
            echo $res;
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
    public function search() {
        $user = $this->get_user_or_redirect();
        $all_labels = $user->get_all_my_labels();
        $data = [];
        $encoded_data = "";
        $my_notes = [];
        $shared_by = [];
        if (isset($_POST['check_labels'])) {
            $data = $_POST['check_labels'];
            $encoded_data = Tools::url_safe_encode($data);
            $this->redirect('note', "search/$encoded_data");
        }
        if (isset($_GET['param1'])) {
            $encoded_data = $_GET['param1'];
            $decoded_data = Tools::url_safe_decode($_GET['param1']);
            if ($decoded_data !== false) {
                $data = $decoded_data;
                
            }
        
        }
        
        $my_notes = $user->filtred_notes_labels($data, $user->id);
        foreach($user->shared_by() as $shared_by_user) {
            
             $shared_by[] = NoteShare::get_shared_filtred($user->id, $shared_by_user->id, $data);
        }

 

        (new View('search'))->show(["currentPage" => "search", "user" => $user,"sharers" => $user->shared_by(),
                                     'all_labels' => $all_labels, 'check_data' => $data, 'my_notes'=>$my_notes, 'shared_by'=> $shared_by,
                                      "encoded_data"=>$encoded_data]);

    }

    public function get_my_filtred_notes_service() : void {
        $user = $this->get_user_or_redirect();
        
        if (isset($_GET['param1']) && $_GET['param1'] !== "") {
            $encoded_data = $_GET['param1'];
            $decoded_data = json_decode($encoded_data);
             $my_notes = $user->filtred_notes_labels($decoded_data, $user->id);
            $my_notes_as_json = json_encode($my_notes);
            echo $my_notes_as_json;
        } else {
            echo json_encode([]);
        }
    }
    


    //valider l'unicité du titre de la note pour js
     public function title_note_exists() : void {
        $res = "false";
        if (isset ($_GET['param1']) && $_GET['param1'] !== ""
            && isset($_GET['param2']) && $_GET['param2'] !== "") {
                $user = $this->get_user_or_redirect();
                $title = trim($_GET['param2']);
                $error = $user->validate_title_note($_GET['param1'], $title);
                if (!empty($error)) {
                     $res = "true";
                }
                   
            }
            echo $res;

     }

    
    

}
