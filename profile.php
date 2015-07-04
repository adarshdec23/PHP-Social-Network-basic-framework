<?php
/* Project: myAm
 * By: Adarsh
 * Started On: 21-Aug-2014 19:07:53
  Purpose:
 * 
 */

//Include the required files and start session
include __DIR__.'/include/outline.php';
include __DIR__.'/include/user.php';
$user=  sessionInit();

/*
 * Check whether the user updated their status.
 * If yes, call user::updateStatus function
 */
if(isset($_POST['updateContent'])){
    
    $user->updateStatus($_POST['updateContent']);
    if($user->err)
        $afterSubmit=$user->errMessage;
    else {
        $afterSubmit="Updated, your friends will be able to view your status.";
    }
}

//Display the status updates of all friends
function displayUpdates($user){
    
    $result=$user->friendUpdates();
    if($result->num_rows <1){
        echo "<h5>No friend updates</h5>";
    }
    else{
        
        while($row=$result->fetch_assoc()){
            
            $username=$row['username'];
            $update_content=$row['update_content'];
            echo "
                <h6>Update from <i>".htmlspecialchars($username)."</i></h6>
                <h5>".htmlspecialchars($update_content)."</h5>";
        }
    }
}


?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?php echo $user->username?></title>
        <meta charset="UTF-8">
        <style>
            *{
                margin:0;
                padding:0;
            }
            body{
                background: #A7F2A3;
            }
            #nav{
                color:#FFF;
                background: #302E2E;
                padding:10px;
                font-weight: bold;
            }
            #nav a,#nav a:visited{
                text-decoration:none;
                color:#FFF;
                padding:2px 10px;
                background: #AAA;
                float:right;
                margin:0 5% 0 0;
                font-weight: 400;
            }
            #new{
                margin:7px auto;
                border:1px solid #D3A830;
                background:#FFE59C;
                text-align: center;
                width:30%;
            }
            #col1{
                width:40%;
                float:left;
                margin-top: 20px;
                margin-left: 2%;
            }
            h3{
                color:#302E2E;
            }
            h6{
                color:#337030;
                font-size: 14px;
                margin-top: 8px;
            }
            h5{
                border:1px solid #AAA;
                color:#354434;
                font-weight: 400;
                font-size: 15px;
                padding:2px;
                margin-bottom: 8px;
            }
            #col2{
                margin-top: 20px;
                float:right;
                width:30%;
            }
            #col2 h3{
                color: #354434;
                margin-left: 40px; 
            }
            form{
                width:90%;
                display:block;
                text-align:center;
                margin: 5px auto;
            }
            textarea{
                border: 0;
                background: #AAA;
                height:150px;
                width: 250px;
                padding:1px;
            }
            input[type=submit]{
                border:0;
                margin-top: 15px;
                padding:5px 10px;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <div id="nav">
            <span id="userName"><?php echo htmlspecialchars($user->username); ?></span>
            <a href="friends.php">Friends</a>
            <a href="logout.php">Logout</a>
        </div>
        
        <?php
            //Print error message if anys
            if(isset($afterSubmit)){
                echo "<div id='new'>$afterSubmit</div>";
            }
        ?>
        
        <div id="col1">
            <h3>Updates</h3>
            <?php
                displayUpdates($user);
            ?>
        </div>
        <div id="col2">
            <h3>Post a new update</h3>
            <form method="POST" action="profile.php">
                <textarea placeholder="Type your status update.." name="updateContent"></textarea>
                <br>
                <input type="submit">
            </form>
        </div>
    </body>
</html>	
