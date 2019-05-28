<?php
/*
 * CREATE TABLE member_interest(member_id int,  interest_id int);
 */
require('/home/bturnerg/config.php');
class Member_interest
{
    function __construct()
    {
        $this->connect();
    }

    function connect()
    {
        try {
            //Instantiate a database object
            $dbMI = new PDO(DB_DSN, DB_USERNAME,
                DB_PASSWORD);
            //echo "Connected to database!!!";
            return $dbMI;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return;
        }
    }

    function insertMember_Interest($memberID, $interestID)
    {
        global $dbMI;
        echo '<br>'.' '.$memberID.' '.$interestID.'<br>';
        $dbMI = $this->connect();
        //echo $member_id.'<br>';
        // 1. define the query

        $sql = "INSERT INTO member_interest(`member_id`, `interest_id`) VALUES (:memberID, :interestID)";

        // 2. prepare the statement
        $statement = $dbMI->prepare($sql);

        //3. bind parameters
        $statement->bindParam(':memberID', $memberID, PDO::PARAM_INT);
        $statement->bindParam(':interestID', $interestID, PDO::PARAM_INT);

        // 4. execute the statement
        $success = $statement->execute();

        // 5. return the result
        return $success;
    }
}