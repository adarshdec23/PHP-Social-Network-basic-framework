<?php

/* Project: myAm
  Purpose:
 * This function contains one function which starts the session, creates and initialises objects of
 *  class user or friendHandler.
 * If no input s provided an object of class user is returned
 * On an input TRUE, object of class friendHandler is returned
 */
function sessionInit($friendHandler=FALSE){
    
    session_start();
    
    if(!isset($_SESSION['userid']) || !isset($_SESSION['username'])){
        
        header("Location: /myam/index.php");
        //In case header does not work, kill script
        die("Please login to continue.");
    }
    if(!$friendHandler){
        $user=new user;
        $user->userid=$_SESSION['userid'];
        $user->username=$_SESSION['username'];
        return $user;
    }
    else{
        $friend=new friendHandler;
        $friend->userid=$_SESSION['userid'];
        $friend->username=$_SESSION['username'];
        return $friend;
    }
}
?>