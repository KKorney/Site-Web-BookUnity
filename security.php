<?php

function protectionXSS($variable) {
    return htmlspecialchars($variable, ENT_QUOTES, 'UTF-8');
}

function emailSanitize($variable) {
    return filter_var($variable, FILTER_SANITIZE_EMAIL);
}

function emailValidate($variable) {
    return filter_var($variable, FILTER_VALIDATE_EMAIL) !== false;
}

function telValidate($variable) {
    // Supprimer les espaces et verifier si c'est un numero valide
    $variable = str_replace(' ', '', $variable);

    // Verifie si le numero ne contient que des chiffres OU commence par '+' suivi de chiffres
    return ctype_digit($variable) || (substr($variable, 0, 1) === '+' && ctype_digit(substr($variable, 1)));
}


?>

    

