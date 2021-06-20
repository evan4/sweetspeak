<?php
namespace controllers;

class DashboardController extends Controller
{

    public function index()
    {
      if(isset($_SESSION['User']) && !empty($_SESSION['User'])){
        echo $this->twig->render('dashboard/index.twig');
        //include 'views/dashboard/index.php';
      }else{
        include '403.php';
      }
      
    }

    public function logout()
    {
        // Удаляем все переменные сессии.
        $_SESSION = array();
            // Если требуется уничтожить сессию, также необходимо удалить сессионные cookie.
        // Замечание: Это уничтожит сессию, а не только данные сессии!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        // Наконец, уничтожаем сессию.
        session_destroy();
      // $_SESSION['User'] = '';
      // $_SESSION['User_info'] = '';
      // $_SESSION['ValidateFormAccess'] = '';
      // $_SESSION['all_categories'] = '';
      header('Location: ' . '/', true, 302);
    }
}
