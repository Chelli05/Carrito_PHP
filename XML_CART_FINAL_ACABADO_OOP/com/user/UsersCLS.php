<?php

require_once('com/product/ProductCLS.php');

class CLSUser {
    private string $userFile;

    public function __construct(string $userFile) {
        $this->userFile = $userFile;
    }

    public function login(string $username, string $password): bool {
        $users = $this->loadUsers();
        foreach ($users->user as $user) {
            if ((string)$user->username === $username && (string)$user->password === $password) {
                $_SESSION['authenticated'] = true;
                $_SESSION['username'] = $username;
                return true;
            }
        }
        return false;
    }

    public function logout(): void {
        session_unset();
        session_destroy();
    }

    public function register(string $username, string $password): void {
        $users = $this->loadUsers();
        $newUser = $users->addChild('user');
        $newUser->addChild('username', $username);
        $newUser->addChild('password', $password);
        $users->asXML($this->userFile);
    }

    private function loadUsers(): SimpleXMLElement {
        if (file_exists($this->userFile)) {
            return simplexml_load_file($this->userFile);
        }
        $users = new SimpleXMLElement('<users></users>');
        $users->asXML($this->userFile);
        return $users;
    }
}

?>
