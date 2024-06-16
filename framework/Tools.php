<?php

require_once 'View.php';

class Tools
{

    //nettoie le string donné
    public static function sanitize(string $var) : string {
        return trim(filter_var($var, FILTER_SANITIZE_SPECIAL_CHARS));
        }

    //dirige vers la page d'erreur
    public static function abort(string $err) : void {
        http_response_code(500);
        (new View("error"))->show(array("error" => $err));
        die;
    }

    //renvoie le string donné haché.
    public static function my_hash(string $password) : string {
        $prefix_salt = "vJemLnU3";
        $suffix_salt = "QUaLtRs7";
        return md5($prefix_salt . $password . $suffix_salt);
    }

    /**
 * Permet d'encoder un string au format base64url, c'est-à-dire un format base64 dans lequel
 * les caractères '+' et '/' sont remplacés respectivement par '-' et '_', ce qui permet d'utiliser le
 * résultat dans un URL.
 * @param string $data Le string à encoder.
 * @return string Le string encodé.
 */
private static function base64url_encode(string $data) : string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

/**
 * Permet de décoder un string encodé au format base64url.
 * @param string $data Le string à décoder.
 * @return string Le string décodé.
 */
private static function base64url_decode(string $data) : string {
    return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
}

/**
 * Permet d'encoder une structure de donnée (par exemple un tableau associatif ou un objet) au format base64url.
 * @param mixed $data La structure de données à encoder.
 * @return string Le string résultant de l'encodage.
 */
public static function url_safe_encode(mixed $data) : string {
    return self::base64url_encode(gzcompress(json_encode($data), 9));
}

/**
 * Permet de décoder un string au format base64url.
 * @param string Le string à décoder.
 * @return mixed $data La structure de données décodée. 
 */
public static function url_safe_decode(string $data) : mixed {
    return json_decode(@gzuncompress(self::base64url_decode($data)), true, 512, JSON_OBJECT_AS_ARRAY);
}


}
