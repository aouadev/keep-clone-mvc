<?php
require_once "framework/Model.php";
require_once "Note.php";
require_once "NoteShare.php";
require_once "Note.php";


class User extends Model
{

    public function __construct(public string $mail, public string $hashed_password, public string $full_name, public ?string $role = "user", public ?int $id = NULL)
    {
    }



    public static function get_user_by_mail(string $mail): User|false
    {
        $query = self::execute("SELECT * FROM Users where mail = :mail", ["mail" => $mail]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["id"]);
        }
    }
    public static function get_user_by_id(int $user_id): User|false
    {
        $query = self::execute("SELECT * FROM Users where id = :id", ["id" => $user_id]);

        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["id"]);
        }
    }
    public static function get_user_by_name(string $user_name): User|false
    {   $data = [];
        $query = self::execute("SELECT * FROM Users where full_name = :name", ["name" => trim($user_name)]);
        var_dump(trim($user_name));
        $data = $query->fetch();
        var_dump($data); // tableau vide !
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"], $data["id"]);
        }
    }


    public function persist(): User
    {
        if (self::get_user_by_mail($this->mail)) {

            self::execute(
                "UPDATE Users SET mail=:mail, hashed_password=:hashed_password, full_name=:full_name, role=:role WHERE id=:id ",
                ["mail" => $this->mail, "hashed_password" => $this->hashed_password, "full_name" => $this->full_name, "role" => $this->role]
            );
        } else {
            self::execute(
                "INSERT INTO Users(mail,hashed_password,full_name,role) VALUES (:mail,:hashed_password,:full_name,:role)",
                ["mail" => $this->mail, "hashed_password" => $this->hashed_password, "full_name" => $this->full_name, "role" => $this->role]
            );
            $user = self::get_user_by_id(self::lastInsertId());
            $this->id = $user->id;
        }
        return $this;
    }



    public static function get_users(): array
    {
        $query = self::execute("SELECT * FROM Users", []);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["mail"], $row["hashed_password"], $row["full_name"], $row["role"]);
        }
        return $results;
    }


    public function validate($mail): array {
        $errors = [];
        if (!strlen($mail) > 0) {
            $errors[] = "⚠Mail is requiered.";
        }
        if (!(preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $mail))) {
            $errors[] = "⚠Email must have a valid structure.";
        }
        return $errors;
    }

    private static function check_password($clear_password, string $hash): bool {
        return $hash === Tools::my_hash($clear_password);
    }

    public static function validate_login(string $mail, string $password): array
    {
        $errors = [];
        $user = User::get_user_by_mail($mail);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $errors[] = "⚠Wrong password. Please try again.";
            }
        } else {
            $errors[] = "⚠Can't find the user. Please sign up.";
        }
        return $errors;
    }
    private static function validate_password(string $password): array
    {
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = "⚠Password length must be up 8 char.";
        }
        if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors[] = "⚠Password must contain one uppercase letter, one number and punctuation mark.";
        }

        return $errors;
    }
    public static function validate_passwords(string $password, string $password_confirm, $currentPassword): array
    {
        $errors = User::validate_password($password, $currentPassword);
        if ($password != $password_confirm) {
            $errors[] = "⚠You have to enter twice the same password.";
        }
        return $errors;
    }
    public function validate_unicity(string $mail): array
    {
        $errors = [];
        $user = self::get_user_by_mail($mail);
        if ($user) {
            $errors[] = "⚠This user already exists.";
        }
        return $errors;
    }

    public function validate_name(string $name): array
    {
        $minLength = Configuration::get('full_name_min_length');
        $errors = [];
        if (!strlen($name) > 0) {
            $errors[] = "⚠ is required.";
        }
        if (!(strlen($name) >= $minLength)) {
            $errors[] = "⚠must have mutch than 3 char";
        }
        return $errors;
    }




    public function get_archives() : array{
        return Note::get_archives($this);
        
    }

    public function get_shared_by(int $ownerid) : array {
        return NoteShare1::get_shared_by($this->id, $ownerid);
        
    }


    public function shared_by() : array {
        
        $shared =  NoteShare1::get_shared_note($this);
        $ids = [];
        foreach($shared as $shared_note) {
          $id = $shared_note->owner;
            $ids[]= $id;
        }
        $idsUnique = array_unique($ids);
        $sharers = [];
        foreach($idsUnique as $userid) {
            $user = User::get_user_by_id($userid);
            $sharers[] = $user;
        }
        return $sharers;
    }

    public function get_notes_pinned(): array
    {
        return Note::get_notes_pinned($this);
    }
    public function get_notes_unpinned(): array
    {
        return Note::get_notes_unpinned($this);
    }


    public function updateProfile(string $newFullName, string $newMail): void
    {
        $this->mail = $newMail;
        $this->full_name = $newFullName;

        $sql = "UPDATE users SET full_name = :full_name, mail = :mail WHERE id = :id";
        $params = [':full_name' => $newFullName, ':mail' => $newMail, ':id' => $this->id];

        try {
            $stmt = parent::execute($sql, $params);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du profil : " . $e->getMessage());
        }
    }

    public function setPassword($newPassword)
    {
        $hashedPassword = Tools::my_hash($newPassword);
        $this->hashed_password = $hashedPassword;
    }

    public function getHashedPassword()
    {
        return $this->hashed_password;
    }

    public function updatePassword($newPassword)
    {

        $this->setPassword($newPassword);

        $sql = "UPDATE users SET hashed_password = :hashed_password WHERE id = :id";
        $params = array(':hashed_password' => $this->getHashedPassword(), ':id' => $this->id);

        try {
            $stmt = parent::execute($sql, $params);
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la mise à jour du mot de passe : " . $e->getMessage());
        }
    }
    public function get_users2() : array{
        $query = self::execute("SELECT * FROM Users", []);
        return $query->fetchAll();
        
    }
    
    public function edit_profile(string $mail, string $name): void{
        self::execute("UPDATE users SET mail =:mail, full_name = :name WHERE id = :id ", ["mail" => $mail, "name"=>$name, "id" => $this->id]);    
    }

    public function validate_title_note($title){
        $error = [];
        $query = self::execute("select title from notes where owner = :user_id and title = :title", ['user_id'=>$this->id, 'title'=>$title]);
        $data = $query->fetchAll();
        if (count($data) != 0) {
            $error[] = "⚠already exists";
        }
        return $error;
        
    }
    public function get_all_labels() : array {
        $data = [];
        $query = self::execute("select distinct label from notes join note_labels 
        on note_labels.note = notes.id where notes.owner = :user_id " , ["user_id"=>$this->id]);
        $data = $query->fetchAll();
        return $data;
    }
    public function other_labels($note_id) :array {
        $data = [];
        $query = self::execute("SELECT DISTINCT label FROM notes
                                JOIN note_labels ON note_labels.note = notes.id WHERE notes.owner = :user_id
                                AND label NOT IN (SELECT label FROM note_labels 
                                WHERE note_labels.note = :note_id )", ["user_id"=>$this->id, "note_id" => $note_id]);
        $data = $query->fetchAll();
        return $data;
    
    }
    public function get_all_my_labels() {
        $query = self::execute("SELECT id, label FROM notes JOIN note_labels ON 
                                note_labels.note = notes.id WHERE owner = :owner", ["owner" => $this->id]);
         return $query->fetchAll();                       
    }
}

