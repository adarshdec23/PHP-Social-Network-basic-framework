<?php
/* Project: myAm
  Purpose:
 * The user class defines a user.
 */
class user{
    public $userid,$username;
    
    public $link;//Database link
    
    //Define a set of static variables which determine the maximum and minimum acceptable lengths
    //of username,password and status update
    private static $minPassLen=6,$minUsernameLen=4;
    private static $maxPassLen=50,$maxUsernameLen=50;
    private static $minUpdateLen=4;
    
    public $err,$errMessage="";
    
    function __construct() {
        
        include __DIR__.'/newCon.php';
        $this->link=$link;
        $this->err=false;
        
    }
    
    //Function to check all values when a user requests a sign up.
    //Takes username and password as input, and sets user::err on failure. Other wise passes control
    //to signupDB
    function signup($username,$password){
        //Check the lenght of username
        if(strlen($username)>user::$minUsernameLen  && strlen($username)<user::$maxUsernameLen){
            
            //Check the lenght of the password provided
            if(strlen($password)>user::$minPassLen && strlen($password)<user::$maxPassLen){
                
                $password= sha1($password); 
                /*
                 * NOTE : Use custom encryption for password.  
                 *      : sha1 used here
                 */
                //Pass control to signupDB
                $this->signupDB($username,$password);
            }
            else{
                $this->err=TRUE;
                $this->errMessage="Password too short/long";
                return;
            }
        }
        
        else{
            $this->err=TRUE;
            $this->errMessage="Username too short/long";
            return;
        }
    }
    
     //Takes verified inputs from user::signup and writes it to database
    private function signupDB($username,$password){
        
        $stmt= $this->link->prepare("INSERT INTO users (username,password) VALUES(?,?)");
        $stmt->bind_param("ss",$username,$password);
        if(!$stmt->execute()){
            $this->err=TRUE;
            $this->errMessage="Username already in use. Try a different one.";
            return;
        }
        $this->userid=  $this->link->insert_id;
        $this->username= $username;
        $stmt->close();
    }
    
    //Function to log a user in. Sets user::err on an error/failure.
    function login($username,$password){

        $password=  sha1($password);
        $stmt= $this->link->prepare("SELECT * FROM users WHERE username=? AND password=?");
        $stmt->bind_param("ss",$username,$password);
        if(!$stmt->execute()){
            $this->err=TRUE;
            $this->errMessage="DB error. Please try again.";
            return;
        }
        $result=$stmt->get_result();
        if($result->num_rows<1){
            $this->err=TRUE;
            $this->errMessage="Invaild username/password.";
            return;
        }
        $row=$result->fetch_assoc();
        $this->userid=$row['user_id'];
        $this->username=$row['username'];
    }
    
    //Function to update the status of the user.
    function updateStatus($status){
        
        if(strlen($status)<user::$minUpdateLen){
            $this->err=TRUE;
            $this->errMessage="Status update should be atleast 4 characters long.";
            return;
        }
        
        $status=  substr($status, 0,200);
        date_default_timezone_set("UTC");
        $curTime = date("Y-m-d H:i:s");
        
        $stmt=  $this->link->prepare("INSERT INTO updates (user_id,update_content,update_time) VALUES($this->userid,?,'$curTime')");
        $stmt->bind_param("s",$status);
        if(!$stmt->execute()){
            $this->err=TRUE;
            $this->errMessage="DB Error. Please try again.";
            return;
        }
        
        $stmt->close();
        
    }
    
    /*
     * Obtain the status updates of friends which is then printed on profile.php
     * Return the result
     */
    function friendUpdates(){
        $query="
            SELECT up.user_id,member.username,up.update_content,up.update_time
            FROM updates up
            JOIN(
                SELECT u.user_id,u.username
                FROM users u
            )member ON member.user_id=up.user_id 
            JOIN(
                SELECT u1.user_id 'temp_id'
                FROM users u1,friends f1
                WHERE f1.friend1=$this->userid
                AND f1.friend2=u1.user_id
                AND f1.status='1'
                    UNION
                SELECT u2.user_id
                FROM users u2,friends f2
                WHERE f2.friend2=$this->userid
                AND f2.friend1=u2.user_id
                AND f2.status='1'
            ) temp_tbl ON temp_tbl.temp_id=member.user_id
            ORDER BY up.update_time DESC
            LIMIT 20
            ;";
        $result= $this->link->query($query);
        return $result;
    }
}
?>