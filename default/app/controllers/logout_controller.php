<?php
class LogoutController extends AdminController
{

  public function index($id = '')
  {
    try {
      MyAuth::cerrar_sesion();
      $this->logout();
      return Redirect::to('/');
    } catch (\Exception $e) {
      var_dump($e);
      exit();
    }
  }
 
}
