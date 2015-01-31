<?php
/**
 * Created by PhpStorm.
 * User: Pasenger
 * Date: 2015/1/30
 * Time: 16:47
 */

class SignupController extends \Phalcon\Mvc\Controller
{
    public function indexAction()
    {
        echo "<h1>signup index.</h1>";
    }

    public function registerAction(){
        $user = new Users();

//        $name = $this->request->getPost("name");
//        $email = $this->request->getPost("email");
//
//        $user->name = $name;
//        $user->email = $email;
//
//        $success = $user->save();

        $success = $user->save($this->request->getPost(), array('name','email'));

        if($success){
            echo "Thanks for registering!";
        }else{
            echo "Sorry, the follwing problems were generated:";
            foreach($user->getMessages() as $message){
                echo $message->getMessage(), "<br />";
            }
        }

        $this->view->disable();
    }

}