<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        // Check if the session should be logged out
        session_start();

        // Check if the request is from a logout action (query parameter or other way)
        if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
            session_unset(); // Clear all session variables
            session_destroy(); // Destroy the session
            // Optionally, clear the session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }
        }

        // After logout (or no session), check the session state
        $data = [
            'firstname' => isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true 
                ? $_SESSION['firstname'] 
                : null, // null means logged out
        ];

        return $this->render('home', $data);
    }
}


