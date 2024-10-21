<?php

$user_file = 'com/user/user.xml';

//////////////////////////////////////////////////////
// Validar usuario
function ValidateUser($username, $password) {
    global $user_file;

    if (file_exists($user_file)) {
        $users = simplexml_load_file($user_file);
        foreach ($users->user as $user) {
            if ((string)$user->username == $username && (string)$user->password == $password) {
                return true; // Usuario válido
            }
        }
    }
    return false; // Usuario no válido
}

//////////////////////////////////////////////////////
// Registrar un nuevo usuario
function RegisterUser($username, $password) {
    global $user_file;

    // Cargar o crear el archivo XML de usuarios
    if (file_exists($user_file)) {
        $users = simplexml_load_file($user_file);
    } else {
        $users = new SimpleXMLElement('<users></users>');
    }

    // Verificar si el usuario ya existe
    foreach ($users->user as $user) {
        if ((string)$user->username == $username) {
            echo json_encode(['error' => 'El nombre de usuario ya está en uso']);
            return;
        }
    }

    // Añadir el nuevo usuario
    $new_user = $users->addChild('user');
    $new_user->addChild('username', $username);
    $new_user->addChild('password', $password); // Nota: en un sistema real, nunca guardes la contraseña en texto plano

    // Guardar el archivo XML
    $users->asXML($user_file);

    echo json_encode(['success' => 'Usuario registrado con éxito']);
}

//////////////////////////////////////////////////////
// Obtener información de un usuario (por ejemplo, para perfil)
function GetUserInfo($username) {
    global $user_file;

    if (file_exists($user_file)) {
        $users = simplexml_load_file($user_file);
        foreach ($users->user as $user) {
            if ((string)$user->username == $username) {
                // Devolver la información del usuario
                return [
                    'username' => (string)$user->username,
                ];
            }
        }
    }
    echo json_encode(['error' => 'Usuario no encontrado']);
    return null;
}
?>
