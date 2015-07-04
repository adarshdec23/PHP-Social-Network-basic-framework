<?php

$emptyError=FALSE; //Flag to check whether any user provided input is empty.

session_start();

//If user is already logged in, redirect to profile.php
if(isset($_SESSION['userid'])){
    header("Location: profile.php");
    exit();
}

/*
 * If the user requests a singup, call the user::signup function
 * Any error is indicated by user::err and user::errMessage holds the error message
 * On a successful signup redirect to profile.php after setting the requied session variables
 */
if(isset($_POST['signup'])){
    //Check whether all necessary values have been entered.
    if(isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])){
        
        include __DIR__.'/include/user.php';
        $user=new user;
        $user->signup($_POST['username'], $_POST['password']);
        if(!$user->err){
            $_SESSION['userid']=$user->userid;
            $_SESSION['username']=$user->username;
            header("Location: profile.php");
        }
    }
    else {
        $emptyError=TRUE;    
    }
}

/*
 * If the user requests a login, call the user::login function
 * Any error is indicated by user::err and user::errMessage holds the error message
 * On a successful login redirect to profile.php after setting the requied session variables
 */
else if(isset ($_POST['login'])){
    //Check whether all necessary values have been entered.
    if(isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])){
        include __DIR__.'/include/user.php';
        $user=new user;
        $user->login($_POST['username'], $_POST['password']);
        if(!$user->err){
            $_SESSION['userid']=$user->userid;
            $_SESSION['username']=$user->username;
            header("Location: profile.php");
        }
    }
    else {
        $emptyError=TRUE;    
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <style>
            body{
                background: #57D987;
            }
            .errorMsg{
                color:#F00;
            }
            form{
                margin:80px auto;
                text-align: center;
            }
            input{
                border: 0;
                background:#F0F0F0;
                padding:5px;
            }
            input[type=submit]{
                background: #3F6AF8;
                padding:5px 10px;
                color:#FFF;
            }
        </style>
    </head>
    <body>
        <form action="index.php" method="POST">
            
            <?php
            //Print errors, if any
            if($emptyError){
                echo "<span class='errorMsg'>Missing field(s). Please try again.</span>";
            }
            else if(isset ($user)){
                if($user->err)
                    echo "<span class='errorMsg'>$user->errMessage</span>";
            }
            ?>
            <br>
            <br>
            <input type="text" name="username" placeholder="Username">
            <br>
            <br>
            <input type="password" name="password" placeholder="Password">
            <br>
            <br>
            <input type="submit" name="login" value="Login">
            <input type="submit" name="signup" value="Sign Up!">
        </form>
    </body>
</html>
