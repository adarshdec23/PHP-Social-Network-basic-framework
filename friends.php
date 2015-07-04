<?php
/* Project: myAm
  Purpose:
 * Displays the friends page using the friendHandler class
 * $afterSubmit holds the message to be printed after any user requests
 */

//Include the required files and start session
include __DIR__.'/include/friendHandler.php';
include __DIR__.'/include/outline.php';
$fr=sessionInit(TRUE);

/*Checks whether the user accepted a friend request
 * $reqid is the id of the friend request in the friends table
 */
if(isset($_POST['yes']) && isset($_POST['reqid'])){
    
    $fr->acceptReq($_POST['reqid']);
    if(!$fr->err){
        $afterSubmit="Friend added";
    }
    else
        $afterSubmit=$fr->errMessage;
}

/*Checks whether the user rejected a friend request
 * $reqid is the id of the friend request in the friends table
 */
else if(isset ($_POST['no']) && isset($_POST['reqid'])){
    $fr->rejectReq($_POST['reqid']);
    if(!$fr->err){
        $afterSubmit="Request rejected.";
    }
    else
        $afterSubmit=$fr->errMessage;
}

/*Checks whether the user sent a friend request
 * $userid is the id of the friend to whom a request will be sent
 */
else if(isset ($_POST['add']) && isset ($_POST['userid'])){
    $fr->friendReq($_POST['userid']);
    if(!$fr->err){
        $afterSubmit="Friend request sent.";
    }
    else
        $afterSubmit=$fr->errMessage;
}

/*
 * Function to display all friend requests sent to the user
 * Takes fr(represents the user) object as input
 */
function displayFriendReq($fr){

    $result=$fr->sentReq();
    if($result->num_rows>0){
        echo "<table>";
        while($row=$result->fetch_assoc()){
            $reqSenderName=htmlspecialchars($row['username']);
            $reqid=$row['id'];
            echo "<tr>
                    <td>$reqSenderName</td>
                    <td>
                        <form action='friends.php' method='POST'>
                            <input type='hidden' name='reqid' value='$reqid'>
                            <input type='submit' name='yes' value='Yes' class='bluebg'>
                            <input type='submit' name='no' value='No'>
                        </form>
                    </td>
                </tr>";
        }
        echo "</table>";
    }else{
        echo "<h4>No friend requests.</h4>";
    }
}

/*
 * Function to display all friend of the user
 * Takes fr(represents the user) object as input
 */
function displayFriends($fr){
    $result=$fr->getFriends();
    if($result->num_rows<1){
        echo "<h4>You do not have any friends.</h4>";
    }
    else{
        while($row=$result->fetch_assoc()){
            $username=htmlspecialchars($row['username']);
            echo "<h4>$username</h4>";
        }
    }
}

/*
 * Function to display all users who are not friends of the user
 * Takes fr(represents the user) object as input
 */
function displayNonFriends($fr){
    $result=$fr->getNonFriends();
    if($result->num_rows <1){
        echo "<h4>Well,no users left! You are either friends or you have sent requests to very user. </h4>";
    }
    else{
        echo "<table>";
        while($row=$result->fetch_assoc()){
            $username=htmlspecialchars($row['username']);
            $userid=$row['user_id'];
            echo "<tr>
                    <td>$username</td>
                    <td>
                        <form action='friends.php' method='POST'>
                            <input type='hidden' name='userid' value='$userid'>
                            <input type='submit' name='add' value='Add' class='bluebg'>
                        </form>
                    </td>
                </tr>";
        }
        echo "</table>";
    }
}
?>
<!DOCTYPE HTML>
<html>
    <head>
        <title><?php echo $fr->username ; ?></title>
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
            #userName{
                text-decoration: none;
                color:#FFF;
            }
            .btn,.btn:visited{
                text-decoration:none;
                color:#FFF;
                padding:2px 10px;
                background: #AAA;
                float:right;
                margin:0 2% 0 2%;
                font-weight: 400;
            }
            #new{
                margin:7px auto;
                border:1px solid #D3A830;
                background:#FFE59C;
                text-align: center;
                width:30%;
            }
            h4{
                font-weight: 400;
                margin:3px 0 3px 0;
            }
            a,a:visited{
                color:#302E2E;
            }
            .col{
                width:30%;
                float:left;
                margin:20px 0 0 2%;
            }
            td{
                padding: 5px;
            }
            input[type=submit]{
                border: 0;
                padding:1px 5px;
                margin:7px 2px;
                border-radius:2px;
            }
            .bluebg{
                background:#3F6AF8;
                color:#FFF;
            }
        </style>
    </head>
    <body>
        <div id="nav">
            <a href='profile.php'  id="userName"><?php echo htmlspecialchars($fr->username); ?></a>
            <a href="friends.php" class='btn'>Friends</a>
            <a href="logout.php" class='btn'>Logout</a>
        </div>
        <?php
        //Print error message if anys
        if(isset($afterSubmit)){
            echo "<div id='new'>$afterSubmit</div>";
        }
        ?>
        
        <div id='col1' class='col'>
            <h3>Friend Requests</h3>
            <?php
                displayFriendReq($fr);
            ?>
        </div>
        
        
        <div id='col2' class='col'>
            <h3>Friends</h3>
            <?php
                displayFriends($fr);
            ?>
        </div>
        
        
        <div id='col3' class='col'>
            <h3>All users</h3>
            <?php
                displayNonFriends($fr);
            ?>
        </div>
        
        
    </body>
</html>	
