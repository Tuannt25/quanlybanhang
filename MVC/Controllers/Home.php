<?php
class Home extends Controller {
  public function Index(){
    // show shop as home
    $this->redirect("Shop/Index");
  }
}
?>