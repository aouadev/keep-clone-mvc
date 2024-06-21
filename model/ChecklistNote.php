<?php
require_once "framework/Model.php";
require_once "User.php";
require_once "CheckListNoteItem.php";

class CheckListNote extends Note
{
     
    private $content = [];
    public function get_content(): array 
    {   
        if ($this->content == null) {
            
            $query = self::execute("SELECT * FROM checklist_note_items 
            WHere checklist_note = :id order by checked, id ", ["id" => $this->note_id]);
            $this->content = $query->fetchAll();
        
        }
        return $this->content;
    }

    public function set_content($data){
        $this->content = $data;
    }

    public function get_type(): string
    {
        return TypeNote::CLN;
    }
    public function  get_note(): Note |false
    {
        $query = self::execute("SELECT * FROM Notes JOIN checklist_notes ON notes.id = checklist_notes.id WHERE notes.id = :id", ["id" => $this->note_id]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new CheckListNote($data['id'], $data['title'], $data['owner'], $data['created_at'], $data['pinned'], $data['archived'], $data['weight'], $data['edited_at']);
        }
    }
    public static function get_note_by_id(int $note_id): Note |false
    {

        $query = self::execute("SELECT * FROM notes WHERE id = :id", ["id" => $note_id]);
        $data = $query->fetch();
        if (count($data) !== 0) {
            return new CheckListNote(
              
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
    public function isPinned(): bool
    {
        return $this->pinned;
    }

    public function update(){}

    public function new(): void
    {
        self::execute("INSERT INTO `checklist_notes`(`id`) VALUES (:id)",
        ["id"=> $this->note_id]);
    }

    public static function validateItems($item, $array_items, $id, $add_new) : array {
        $errors = [];
        $min_length = Configuration::get('item_min_length');
        $max_length = Configuration::get('item_max_length');
  
        if (($id != 0 && strlen($item) < $min_length) || (strlen($item) > $max_length)) {
            $errors[] ="Item length must be between $min_length and $max_length char";
        }
    
        $occurences = array_filter($array_items, function($e) use ($item) {
                return $e == $item;
        });
        if (count($occurences) > ($add_new ? 0 : 1 ) && strlen($item) > 0) { // un boolean add_new pour pouvoir utiliser cette méthode dans la validation 
            $errors[] = "Item must be unique";                              // d'un nouveau item dans le cas d'édition d'une note, dans ce cas le item n'est pas 
        }                                                                   //  dans la liste des items donc occurence doit être 0
        return $errors;
    
    }
  

}
