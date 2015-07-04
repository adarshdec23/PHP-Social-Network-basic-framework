1 : Installation instructions:
    -Unzip and place the containing folder in your document root on a PHP enabled server.
    
    -Import the file - socialNetwork.sql onto your preferred MySQL database.
    
    -[IMPORTANT] Alter the file /[foldername]/include/newCon.php . Further instruction are provided in the file itself.
    
    -[IMPORTANT] On a non-Apache server, delete the files '/[foldername]/.htaccess' and '/[foldername]/include/.htaccess'
    

2: Naming convention :
    -Most variable names are self-explanatory.
    
    -Variable 'fr' is an object of the class friendHandler [More in the file /include/friendHandler.php] 
    

3: Sufficient comments have been provided wherever necessary. 

4: The table 'friends' contains an enumeration of (0,1).
   0 -> A friend request has been sent, and is currently awaiting confirmation.
   
   1 -> The two users friend1 and friend2 are friends. 
