<?php
class LogoutController extends AdminController
{

  public function index($id = '')
  {
    try {
      $this->logout();
      return Router::to(['/']);
    } catch (\Exception $e) {
      var_dump($e);
      exit();
    }
  }
 
}
