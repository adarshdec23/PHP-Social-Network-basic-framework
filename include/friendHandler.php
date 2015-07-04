<?php

/* Project: myAm
 * By: Adarsh
 * Started On: 20-Aug-2014 19:04:21
  Purpose:
 * Contains all functions required on friends.php page.
 * friendHandler class extends user class.
 */

include __DIR__.'/user.php';


class friendHandler extends user{
    
    function __construct() {
        parent::__construct();
    }
    
    //Function to send a friend request to the $friend_id
    function friendReq($friend_id){
        
        if(!is_numeric($friend_id)){
            $this->err=TRUE;
            $this->errMessage="Probable injection attack.";
            return;
        }
        //Check whether the they are already friends
        $stmt=  $this->link->prepare("SELECT status FROM friends
                                      WHERE (friend1=? AND friend2=?) OR (friend1=? AND friend2=?)");
        $stmt->bind_param("dddd",  $this->userid,$friend_id,$friend_id,  $this->userid);
        $stmt->execute();
        $result=$stmt->get_result();
        
        if($result->num_rows>0){
            $this->err=TRUE;
            $row=$result->fetch_assoc();
            if($row['status'] == 1){
                $this->errMessage="You are already friend.";
                return;
            }
            else{
                $this->errMessage="Awaiting confirmation.";
                return;
            }
            
        }
        $stmt->close();
        
        //If the users are not friends, then send a request
        $stmt_insert=  $this->link->prepare("INSERT INTO friends (friend1,friend2) VALUES('$this->userid',?)");
        $stmt_insert->bind_param("d",$friend_id);
        if(!$stmt_insert->execute()){
            $this->err=TRUE;
            $this->errMessage="Database error, please try again.";
            return;
        }
        $stmt_insert->close();
    }
    
    //Function to accept a friend request, identified by $request_id
    function acceptReq($request_id){
        
        if(!is_numeric($request_id)){
            $this->err=TRUE;
            $this->errMessage="Probable injection attack.";
            return;
        }
        $stmt=$this->link->prepare("UPDATE friends SET status='1' WHERE id=?");
        $stmt->bind_param("d",$request_id);
        if(!$stmt->execute()){
            $this->err=TRUE;
            $this->errMessage="DB ERROR.";
            return;
        }
        $stmt->close();
    }
    
    //Function to reject a friend request, identified by $request_id
    function rejectReq($request_id){
        if(!is_numeric($request_id)){
            $this->err=TRUE;
            $this->errMessage="Probable injection attack.";
            return;
        }
        $stmt=$this->link->prepare("DELETE FROM friends WHERE id=?");
        $stmt->bind_param("d",$request_id);
        if(!$stmt->execute()){
            $this->err=TRUE;
            $this->errMessage="DB ERROR.";
            return;
        }
        $stmt->close();
    }
    
    //Obtain all friend requests sent to the user
    function sentReq(){
        $result= $this->link->query("SELECT f.id,f.friend1,u.username 
                                    FROM friends f,users u
                                    WHERE f.friend2=$this->userid
                                        AND f.status='0'
                                        AND u.user_id=f.friend1") or die("DB Error");
        return $result;
    }
    
    //Get a list of all friends of the user
    function getFriends(){
        if(func_num_args()==0)
            $userid=  $this->userid;
        else
            $userid=  func_get_args (0);
        $query="
            SELECT u1.user_id,u1.username
            FROM users u1,friends f1
            WHERE f1.friend1=$userid
            AND f1.friend2=u1.user_id
            AND f1.status='1'
                UNION
            SELECT u2.user_id,u2.username
            FROM users u2,friends f2
            WHERE f2.friend2=$userid
            AND f2.friend1=u2.user_id
            AND f2.status='1'
            LIMIT 20;
            ";
        $result= $this->link->query($query);
        return $result;
    }
    
    //Get a list of all user who are not friends with the user.
    function getNonFriends(){
        $query="
            SELECT DISTINCT u.user_id,u.username
            FROM users u,friends f
            WHERE u.user_id NOT IN(
                SELECT u1.user_id
                FROM users u1,friends f1
                WHERE f1.friend1=$this->userid
                AND f1.friend2=u1.user_id
                    UNION
                SELECT u2.user_id
                FROM users u2,friends f2
                WHERE f2.friend2=$this->userid
                AND f2.friend1=u2.user_id
            )
            AND u.user_id <> $this->userid
            LIMIT 20
             ";
        $result=$this->link->query($query);
        return $result;
    }
    
    
}
?>
