<?php
class Controller {
    protected function view($view, $data = []) {
        extract($data);
        $base_path = dirname(dirname(__DIR__)) . '/app/views';
        require_once $base_path . '/layout/header.php';
        require_once $base_path . '/' . $view . '.php';
        require_once $base_path . '/layout/footer.php';
    }
    
    protected function redirect($url) {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit();
    }
}