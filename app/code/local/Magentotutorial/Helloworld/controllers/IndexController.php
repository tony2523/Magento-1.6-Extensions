 <?php 
 class Magentotutorial_Helloworld_IndexController extends Mage_Core_Controller_Front_Action {        

    public function indexAction() {

       // echo 'Hello Index!';

       $this->loadLayout();
       $this->renderLayout();
    }
    public function goodbyeAction()
    {
    	 $this->loadLayout();
       $this->renderLayout();
    }
    public function paramsAction() {
    echo '<dl>';            
    foreach($this->getRequest()->getParams() as $key=>$value) {
        echo '<dt><strong>Param: </strong>'.$key.'</dt>';
        echo '<dt><strong>Value: </strong>'.$value.'</dt>';
    }
    echo '</dl>';
}

}
  ?>