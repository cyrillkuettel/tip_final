<?php
/** Copyright 2018 Hochschule Luzern - Informatik
 *
 * Provides access to the TIP guestbook table in the MySQL database.
 * Supports listing of all guestbook items, inserting new items and removing 
 * an exiting item.    
 * @author Peter Sollberger <peter.sollberger@hslu.ch>
 */
class GuestbookAccess
{
    private $db;
    
    /**
     * Opens the database.
     */
    public function __construct()
    {
        $username = "root";
        $password = "";
        $database = "mydb";
        
        // Open the database
        $this->db = mysqli_connect("localhost", $username, $password);        
        if ($this->db == false) {
            die("Unable to connect to database");
        } 
        
        // Select database
        mysqli_select_db($this->db, $database);
    }
    
    /**
     * Closes the database.
     */
    public function __destruct()
    {
        mysqli_close($this->db);
    }
    
    /**
     * Return in an table (two-dimensional array) all entries of the guest book.
     * Each row of the table represents one entry in the guest book.
     * @return table[...]["Index"]   --> Integer: Index of the entry (for deleting)
     *         table[...]["Name"]    --> String: name of the user
     *         table[...]["eMail"]   --> String: e-Mail of the user
     *         table[...]["Comment"] --> String: The guest book entry (as text)
     *         table[...]["Date"]    --> String: Date and time of the entry
     */
    public function getEntries()
    {
        // Make querry
        $result = mysqli_query($this->db, "SELECT * FROM guestbook");
        
        $table = false;
        $i = 0;
        while ($row = mysqli_fetch_array($result)) {
            $table[$i]["Index"]   = $row["indes"];
            $table[$i]["Date"]    = $row["cur_date"];
            $table[$i]["Name"]    = $row["namep"];
            $table[$i]["eMail"]   = $row["email"];
            $table[$i]["Comment"] = $row["comment"];
            $i++;
        }
        
        mysqli_free_result($result);
        
        return $table;
    }
    
    /**
     * Evaluates current time and adds a new guestbook entry with given name, 
     * e-Mail and comment.
     * @param String $name    User name
     * @param String $eMail   User e-mail address
     * @param String $comment The entry text
     * @return On success: Integer Index generated by the database for the entry
     *         On failure: Boolean false
     */
    function addEntry($name, $eMail, $comment)
    {   
        function debugthis($data) {
            $output = $data;
            if (is_array($output))
                $output = implode(',', $output);
        
             echo "<script>console.log('text " . $output . "' );</script>";
        }
        
        // For security: supress SQL injection
        $name    = mysqli_real_escape_string($this->db, $name);
        $eMail   = mysqli_real_escape_string($this->db, $eMail);
        $comment = mysqli_real_escape_string($this->db, $comment);
        

        // Add entry to the database
        $result = mysqli_query($this->db, "INSERT INTO guestbook (namep, email, comment) VALUES ('$name', '$eMail', '$comment')");
        
        

        if ($result)
        {
            $result = mysqli_insert_id($this->db);
        } else {
           
        }

        return $result;
    }
    
    /**
     * Return in an lis(one-dimensional array) all data of one guest book entry.
     * @return list["Index"]   --> Integer: Index of the entry (for deleting)
     *         list["Name"]    --> String: name of the user
     *         list["eMail"]   --> String: e-Mail of the user
     *         list["Comment"] --> String: The guest book entry (as text)
     *         list["Date"]    --> String: Date and time of the entry
     */
    public function getEntry($index)
    {
        // For security: supress SQL injection
        settype($index, 'Integer');

        // Make querry
        $result = mysqli_query($this->db, "SELECT * FROM guestbook WHERE indes = '$index'");
        
        $list = false;
        $row = mysqli_fetch_array($result);
        if ($row != false) {            
            $list["Index"]   = $row["indes"];
            $list["Date"]    = $row["cur_date"];
            $list["Name"]    = $row["namep"];
            $list["eMail"]   = $row["email"];
            $list["Comment"] = $row["comment"];
        }
        
        mysqli_free_result($result);
        
        return $list;
    }
        
    /**
     * Removes the entry with the given index from the database.
     * @param Integer $index Index of the entry to remove
     * @return Boolean true on success.
     */
    function removeEntry($index)
    {
        // For security: supress SQL injection
        settype($index, 'Integer');
        
        $result = mysqli_query($this->db, "DELETE FROM guestbook WHERE indes = '$index'");
        
        return $result;
    }
   
}
?>