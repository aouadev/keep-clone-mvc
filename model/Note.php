<?php

use function PHPSTORM_META\map;

require_once "framework/Model.php";
require_once "User.php";
require_once "TextNote.php";
require_once "ChecklistNote.php";
require_once "framework/Configuration.php";

enum TypeNote
{
    const TN = "TextNote";
    const CLN = "ChecklistNote";
}


abstract class Note extends Model
{
    public function __construct(
        public String $title,
        public int $owner,
        public string $created_at,
        public int $weight = 0,
        public int $note_id = 0,
        public int $pinned = 0,
        public int $archived = 0,
        
        public ?string $edited_at = NULL
    ) {
    }
  

    public abstract function get_type();
    public abstract function get_content();
    public abstract function set_content(?string $data);
    public abstract function get_note();
    public abstract function isPinned();
    public abstract function update();

    public function set_weight($weight) {
        $this->weight = $weight;
    }


    public static function get_created_at(int $id): String
    {
        $query = self::execute("SELECT created_at from notes WHERE id = :id", ["id" => $id]);
        $data = $query->fetchColumn();

        return $data;
    }
    public static function get_edited_at(int $id): String | null
    {
        $query = self::execute("SELECT edited_at from notes WHERE id = :id", ["id" => $id]);
        $data = $query->fetchColumn();

        return $data;
    }
    public function isShared_as_editor(int $userid): bool
    {
        $query = self::execute("SELECT * FROM note_shares WHERE note = :id and user =:userid and editor = 1", ["id" => $this->note_id, "userid" => $userid]);
        $data = $query->fetchAll();
        return count($data) !== 0;
    }
    public function isShared_as_reader(int $userid): bool
    {
        $query = self::execute("SELECT * FROM note_shares WHERE note = :id and user =:userid and editor = 0", ["id" => $this->note_id, "userid" => $userid]);
        $data = $query->fetchAll();
        return count($data) !== 0;
    }
    public function in_my_archives(int $userid): int
    {
        $query = self::execute("SELECT archived FROM notes WHERE owner = :userid and id = :id", ["userid" => $userid, "id" => $this->note_id]);
        $data = $query->fetchColumn();
        return $data;
    }
    public function is_pinned(int $userid): int
    {
        $query = self::execute("SELECT pinned FROM notes WHERE owner = :userid and id = :id", ["userid" => $userid, "id" => $this->note_id]);
        $data = $query->fetchColumn();
        return $data;
    }


    public static function get_archives(User $user): array
    {
        $archives = [];
        $query = self::execute("SELECT id, title FROM notes WHERE owner = :ownerid AND archived = 1 ORDER BY -weight", ["ownerid" => $user->id]);
        $archives = $query->fetchAll();
        $content_checklist = [];
        foreach ($archives as &$row) {
            $dataQuery = self::execute("SELECT content FROM text_notes WHERE id = :note_id", ["note_id" => $row["id"]]);
            $content = $dataQuery->fetchColumn();

            if (!$content) {
                $dataQuery = self::execute("SELECT content, checked FROM checklist_note_items WHERE checklist_note = :note_id order by checked, id ", ["note_id" => $row["id"]]);
                $content_checklist = $dataQuery->fetchAll();
            }
            $row["content"] = $content;
            $row["content_checklist"] = $content_checklist;
        }
        return $archives;
    }

    // Archiver désarchiver, épingler dépingler et gestion du poids
    // méthode de permutation qui va être utilisser dans toutes les opérations
    private function permut_notes(Array $array, int $index) {    
        $weight_max = Note::get_note_by_id($array[0])->weight;
        for($i = 0; $i < $index; ++$i) {
            $note_next = Note::get_note_by_id($array[$i + 1]);
            $weight = $note_next->weight;
            self::execute("UPDATE notes SET weight = :val  WHERE id = :id", ["val" => -$i,"id" => $array[$i + 1]]);
            self::execute("UPDATE notes SET weight = :val  WHERE id = :id", ["val" =>$weight,"id" => $array[$i]]);
        }
        self::execute("UPDATE notes SET weight = :val  WHERE id = :id", ["val" =>$weight_max,"id" => $this->note_id]);
        
    }

    // attribuer le poids max de sa liste à la note pinned ou unpinned 
    private function permut_notes_pin_unpin(int $pinned) {
        $query = self::execute("SELECT id from notes WHERE owner = :owner AND pinned = :val ORDER BY -weight",["owner"=> $this->owner, "val" => $pinned]);
        $res = $query->fetchAll();
        $array = array_column($res, 'id'); 
        $index = array_search($this->note_id, $array);
        $this->permut_notes($array, $index);
    }

    //  Archiver une note et la désépingler puis lui attribuer  le poid maximum des notes archivés
    public function archive(): void {
        self::execute("UPDATE notes SET archived = :val  WHERE id = :id ", ["val" => 1, "id" => $this->note_id]);
        if ($this->pinned = 1) {
            self::execute("UPDATE notes SET pinned = :val  WHERE id = :id", ["val" => 0, "id" => $this->note_id]);
        }
        $query = self::execute("SELECT id from notes WHERE owner = :owner AND archived = :val ORDER BY -weight",["owner"=> $this->owner, "val" => 1]);
        $res = $query->fetchAll();
        $array = array_column($res, 'id'); 
        $index = array_search($this->note_id, $array);
        $this->permut_notes($array, $index);

    }

    // désarchiver une note et lui attribuer le poids max des notes non épinglés
    public function unarchive(): void {
     self::execute("UPDATE notes SET archived = :val  WHERE id = :id", ["val" => 0, "id" => $this->note_id]);
     $this->permut_notes_pin_unpin(0);
 
    }

    // épingler une note et lui attribuer le poids max des notes épinglés
    public function pin(): void {
        self::execute("UPDATE notes SET pinned = :val  WHERE id = :id", ["val" => 1,"id" => $this->note_id]);
        $this->permut_notes_pin_unpin(1);
    }
 
    //désépingler une note et lui attribuer le poids max des notes  non épinglés
    public function unpin(): void{
        self::execute("UPDATE notes SET pinned = :val  WHERE id = :id", ["val" => 0, "id" => $this->note_id]);
        $this->permut_notes_pin_unpin(0);
    }






  

    public function  get_weight(): int
    {
        return $this->weight;
    }
    public function insert() : void {
        self::execute(
            "INSERT INTO Notes(title,owner,created_at,edited_at,pinned,archived,weight) VALUES (:title,:owner,:created_at,:edited_at,:pinned,:archived,:weight)",
            [
                "title" => $this->title,
                "owner" => $this->owner,
                "created_at" => $this->created_at,
                "edited_at" => $this->edited_at,
                "pinned" => $this->pinned,
                "archived" => $this->archived,
                "weight" => $this->weight,
            ]
        );
        $note = self::get_note_by_id(self::lastInsertId());
        $this->note_id = $note->note_id;
        if($this->get_type() == TypeNote::TN) {
            self::execute("INSERT INTO text_notes (id, content) VALUES (:id, :content)", ['id'=>$this->note_id, 'content'=>$this->get_content()]);
        }
        
    }
    public function update_note() : void {
        self::execute('UPDATE Notes SET title = :title, pinned = :pinned, archived = :archived, weight = :weight, edited_at = NOW() WHERE id = :note_id', [
            'title' => $this->title,
            'pinned' => $this->pinned ? 1 : 0,
            'archived' => $this->archived ? 1 : 0,
            'weight' => $this->weight,
            'note_id' => $this->note_id,
        ]);
        if($this->get_type() == TypeNote::TN) {
            self::execute("UPDATE text_notes SET content = :content WHERE id = :id", ['id'=>$this->note_id, 'content'=>$this->get_content()]);
        }
    }










    // Supprime une note
    public function delete(User $initiator): Note |false
    {
        $user = User::get_user_by_id($this->owner);
        // permet la suppression en cascade pour éviter problèmes suite aux dépendances
        if ($user == $initiator) {
            self::execute("DELETE FROM checklist_note_items WHERE checklist_note = :note_id", ['note_id' => $this->note_id]);
            self::execute("DELETE FROM text_notes WHERE id = :note_id", ['note_id' => $this->note_id]);
            self::execute("DELETE FROM checklist_notes WHERE id = :note_id", ['note_id' => $this->note_id]);
            self::execute("DELETE FROM note_shares WHERE note = :note_id", ['note_id' => $this->note_id]);
            self::execute("DELETE FROM Notes WHERE id = :note_id", ['note_id' => $this->note_id]);
            return $this;
        }
        return false;
    }

    public function validate_title($title): array
    {
        $errors = [];

        $minLength = Configuration::get('title_min_length');
        $maxLength = Configuration::get('title_max_length');
  
        $errors = [];
        if (!strlen($title) > 0) {
            $errors[] = "⚠ is required.";
        }
        if (strlen($title) < $minLength) 
            $errors[] =  "⚠must have mutch than 3 char";
        elseif(strlen($title) > $maxLength) {
            $errors[] = "⚠must have min than 25 char";
        }
        return $errors;
    }
 /*   public function validate_title()
    {
        $errors = [];
        $minLength = Configuration::get('title_min_length');
        $maxLength = Configuration::get('title_max_length');

        // Vérifie la longueur du titre
        if (strlen($this->title) < $minLength || strlen($this->title) > $maxLength) {
            $errors[] = "Le titre doit avoir au minimum $minLength caractères et au maximum $maxLength caractères.";
        }

        // Vérifie si le titre est unique pour cet utilisateur
        $query = self::execute("SELECT COUNT(*) FROM notes WHERE title = :title AND owner = :owner AND id != :id", [
            'title' => $this->title,
            'owner' => $this->owner,
            'id' => $this->note_id ?? 0
        ]);
        if ($query->fetchColumn() > 0) {
            $errors[] = "Une autre note avec le même titre existe déjà.";
        }

        return $errors;
    }*/

    public function validate_content()
    {
        $minLength = Configuration::get('description_min_length');
        $maxLength = Configuration::get('description_max_length');
        $errors = [];
        // Vérifie la longueur du contenu
        if ((strlen($this->get_content()) < $minLength && strlen($this->get_content()) > 0) || strlen($this->get_content()) > $maxLength) {
            $errors[] = "Le contenu de la note doit contenir entre 5 et 800 caractères.";
        }

        return $errors;
    }




    public function persist(): Note {  
        if ($this->note_id == null) 
            $this->insert();
        else 
            $this->update_note();
        return $this;
    }

    public function is_weight_unique(int $id): int
    {
        $query = self::execute("SELECT id, MAX(weight) from notes where id = :note_id group by id", ["note_id" => $id]);
        $data = $query->fetch();
        return $data['id'];
    }


    public function get_note_up(User $user, int $note_id, int $weight, bool $pin): Note | false
    {
        $query = self::execute("
        SELECT * FROM notes n
        WHERE owner = :ownerid AND n.id <> :note_id AND archived = 0 AND pinned = :pin AND weight > :weight 
        ORDER BY weight LIMIT 1
        ", ["ownerid" => $user->id, "note_id" => $note_id, "pin" => $pin, "weight" => $weight]);

        $data = $query->fetch();
        if (!$data) {
            return false;
        } else return Note::create_Note($data);
    }

    public function get_note_down(User $user, int $note_id, int $weight, bool $pin): Note | false
    {
        $query = self::execute("
         SELECT * FROM notes n
         WHERE owner = :ownerid AND n.id <> :note_id AND archived = 0 AND pinned = :pin AND weight < :weight 
         ORDER BY -weight LIMIT 1
         ", ["ownerid" => $user->id, "note_id" => $note_id, "pin" => $pin, "weight" => $weight]);

        $data = $query->fetch();
        if (!$data)
            return false;

        else return Note::create_Note($data);
    }

    public function move_db(Note $second): Note
    {
        $weight_second = $second->get_weight();
        $second_id = $second->note_id;
        self::execute(
            'UPDATE notes SET weight = :weight_note2 WHERE id = :id_note1',
            ['id_note1' => $this->note_id, 'weight_note2' => $weight_second]
        );
        self::execute('UPDATE notes SET weight = :weight_note1 WHERE id = :id_note2', ['id_note2' => $second_id, 'weight_note1' => $this->weight]);


        return $this;
    }

    public static function get_note_by_id(int $note_id): Note |false
    {
        return Note::is_Text_note($note_id) ? TextNote::get_note_by_id($note_id) : CheckListNote::get_note_by_id($note_id);
    }
    public static function is_Text_note(int $id): bool
    {
        $query = self::execute("SELECT content FROM text_notes where id = :id", ["id" => $id]);
        $data = $query->fetchAll();
        return count($data) !== 0;
    }
    public static function create_Note($data): Note | false
    {
        if (count($data) !== 0) {
            return Note::is_Text_note($data['id']) ? new TextNote(
              
                $data['title'],
                $data['owner'],
                $data['created_at'],
                $data['weight'],
                $data['id'],
                $data['pinned'],
                $data['archived'],
                $data['edited_at']
            ) :
                new CheckListNote(
                 
                    $data['title'],
                    $data['owner'],
                    $data['created_at'],
                    $data['weight'],
                    $data['id'],
                    $data['pinned'],
                    $data['archived'],
                    $data['edited_at']
                );
        }
    }
    public static function update_drag_and_drop($count, $idval)
    {
        self::execute("UPDATE notes SET weight = :count, WHERE id = :id", ['count' => $count, 'id' => $idval]);
    }
    public function share_note(User $user, int $editor) {
        self::execute("INSERT INTO note_shares(note, user, editor) VALUES (:note, :user, :editor)", ["note" =>$this->note_id, "user"=>$user->id, "editor"=>$editor]);
    }
    public function share_list() : array {
        $data = [];
        $query = self::execute("select * from note_shares where note = :id", ['id' => $this->note_id]);
        $data = $query->fetchAll();
        foreach($data as &$row) { 
            $row['editor'] = $row['editor'] == 1 ? "editor" : "reader";
            $user = User::get_user_by_id($row['user']);
            $row['full_name'] = $user->full_name ?? 'N/A';
        }
        
        return $data;
    }
    public function list_share_users() : array {
        $query = self::execute("select * from users where id not in (select user from note_shares where note = :id) and id not in (select owner from notes where id = :id)", ["id"=>$this->note_id]);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = User::get_user_by_mail($row['mail']);
        }
      
        return $results;
      
        
    }  

    public function set_editor_and_reader(int $user_id): void
    {
        if ($this->as_editor($user_id)) {
            self::execute("UPDATE note_shares SET editor = :val WHERE note = :id And user = :user", ["val" => 0, "id" => $this->note_id, 'user'=>$user_id]);
        } else {
            self::execute("UPDATE note_shares SET editor = :val WHERE note = :id And user = :user" , ["val" => 1, "id" => $this->note_id, 'user'=>$user_id]);
        }
    }
    
    public function as_editor(int $user_id): bool
    {
        $query = self::execute("SELECT editor FROM note_shares WHERE note = :id AND user = :user_id", ['id' => $this->note_id, 'user_id' => $user_id]);
        $result = $query->fetch();
        return $result && $result['editor'] == 1;
    }
    public function delete_from_share(int $user_id) {
        self::execute("delete from note_shares where note = :note_id and user = :user_id", ['note_id'=>$this->note_id, 'user_id'=>$user_id]);
    }
    // le poids maximum dde toutes les notes
    public function max_weight()  {
        $query = self::execute("select MAX(weight) as max_weight from notes where owner = :current_user", ['current_user'=>$this->owner]);
        $data = $query->fetch();
        return $data['max_weight'];
    }

    // Le poids max des notes pinned 
    public function max_notes_pinned() {
        $query = self::execute("SELECT MAX(weight) AS max_weight FROM notes WHERE  owner = :current_user AND pinned = :pinned",
                             ['current_user'=>$this->owner, 'pinned' => 1]);
        $data = $query->fetch();
        return $data['max_weight'];
    }

      // Le poids max des notes unpinned 
    public function max_notes_unpinned() {
        $query = self::execute("SELECT MAX(weight) AS max_weight FROM notes WHERE  owner = :current_user AND pinned = :pinned",
                             ['current_user'=>$this->owner, 'pinned' => 0]);
        $data = $query->fetch();
        return $data['max_weight'];
    }
        // Le poids max des notes archived 
    public function max_notes_archived() {
        $query = self::execute("SELECT MAX(weight) AS max_weight FROM notes WHERE  owner = :current_user AND archived = :archived",
                                 ['current_user'=>$this->owner, 'archived' => 1]);
        $data = $query->fetch();
        return $data['max_weight'];
    }


  
  
    public function delete_label($note_id, $label){
        self::execute("delete from note_labels where note = :id and label = :label", ['id'=>$note_id, 'label'=>$label]);
    }
    public function add_label($note_id, $label) {
        var_dump($label);
        self::execute("INSERT into note_labels(note, label) VALUES (:id, :label)", ['id' => $note_id, 'label' =>$label]);
    }
    
    public function validate_label(string $label) {
        $errors = [];
        $minLength = Configuration::get('label_min_length');
        $maxLength = Configuration::get('label_max_length');
        if (!strlen($label) > 0) {
            $errors[] = "⚠ is required.";
        }
        if (strlen($label) < $minLength || strlen($label) > $maxLength)
            $errors[] =  "⚠ Label length must be between 2 and 10";
        if ($this->same_label($label)) 
            $errors[] = "⚠ A note cannot contain the same label Twice";
        return $errors;
    }

    private function same_label($label) {
        $query = self::execute("select label from note_labels where note = :id and label = :label", ["id" => $this->note_id, "label"=>$label]);
        return $query->fetch();
    }

    public function list_labels() : array{
        $data = [];
        $query = self::execute("select distinct * from note_labels where note = :id", ["id" => $this->note_id]);
        $data = $query->fetchAll();
    
        return $data;
    }
    public function get_all_labels(int $user_id) : array {
        $data = [];
        $query = self::execute("select distinct label from notes join note_labels 
        on note_labels.note = notes.id where notes.owner = :user_id " , ["user_id"=>$user_id]);
        $data = $query->fetchAll();
        return $data;
    }

    public function other_labels(int $note_id, int $user_id) :array {
        $data = [];
        $query = self::execute("SELECT DISTINCT label FROM notes
                                JOIN note_labels ON note_labels.note = notes.id WHERE notes.owner = :user_id
                                AND label NOT IN (SELECT label FROM note_labels 
                                WHERE note_labels.note = :note_id )", ["user_id"=>$user_id, "note_id" => $note_id]);
        $data = $query->fetchAll();
        return $data;
    
    }
   
 

    

 
}
