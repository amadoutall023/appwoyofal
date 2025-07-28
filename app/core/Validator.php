<?php
namespace App\Core;
 class Validator{

   private static array $errors;
   private static $instance = null;

   public function __construct(){
    self::$errors = [];
   }

   public static function getInstance(){
    if(self::$instance === null){
        self::$instance = new Validator();
    }
    return self::$instance;
   }

   public  static function isEmail($key,$email,$message = "Email invalide"){
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        self::addError(  $key,$message);
    }
   }

   public static function isEmpty($key, $value){
    if(empty($value)){
        self::addError($key,"Champ obligatoire");
    }
   }

   public static function minLength($key, $value, $minLength, $message = "La longueur minimale est de 3 caractères"){
    if(strlen($value) < $minLength){
        self::addError($key, $message);
    }
   }


   public static function addError(string $field, string $message) {
      self::$errors[$field] = $message;
    }

   public static function getErrors(){
    return self::$errors;
   }

   public static function isValid(){
    return count(self::$errors) === 0;
   }


// $rules = [
//     'email' => [
//         ['rule' => 'isEmpty'],
//         ['rule' => 'isEmail'],
//         ['rule' => 'minLength', 'params' => [4, "L'email doit contenir au moins 4 caractères"]],
//     ],
//     'password' => [
//         ['rule' => 'isEmpty'],
//         ['rule' => 'minLength', 'params' => [4, "Le mot de passe doit contenir au moins 4 caractères"]],
//     ],
// ];
// public static function validate(array $data, array $rules) {
//     self::$errors = []; // Réinitialiser les erreurs avant de valider

//     foreach ($rules as $field => $fieldRules) {
//         $value = $data[$field] ?? null;

//         foreach ($fieldRules as $ruleData) {
//             $rule = $ruleData['rule'];
//             $params = $ruleData['params'] ?? [];

//             if (method_exists(self::class, $rule)) {
//                 self::$rule($field, $value, ...$params);
//             }
//         }
//     }
// }


}