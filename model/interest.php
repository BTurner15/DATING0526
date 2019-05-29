<?php
/**
 * Bruce Turner, Professor Ostrander, Spring 2019
 * IT 328 Full Stack Web Development
 * Dating IV Assignment: use data base
 * file: interest.php
 * date: Tuesday, May 28 2019
 * class Interest
 *
 */
/*CREATE TABLE interest(`interest_id` int AUTO_INCREMENT PRIMARY KEY,
                       `interest` varchar(40) DEFAULT NULL,
                       `type` varchar(8) DEFAULT NULL);

INSERT INTO interest(interest, type) VALUES('tv','indoor'); ... VALUES('collecting','outdoor');
NOTE: correlates to two arrays in index.php already in the hive:


[0] thru [7] in with interest_id's of 1...8
'indoorInterests', array('tv', 'puzzles', 'movies', 'reading', 'cooking', 'playing cards',
                         'board games', 'video games'));
[8] thru [13] in with interest_id's of 9...148
'outdoorInterests', array('hiking', 'walking', 'biking', 'climbing', 'swimming', 'collecting'));
*/

require('/home/bturnerg/config.php');
class Interest
{
    function __construct()
    {
        $this->connect();
    }

    function connect()
    {
        try {
            //Instantiate a database object
            $dbI = new PDO(DB_DSN, DB_USERNAME,
                DB_PASSWORD);
            //echo "Connected to database!!!";
            return $dbI;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return;
        }
    }

    function insertInterest($interest_Str, $type_Str)
{
    global $dbI;
    //echo '<br>'.' '.$interest_Str.' '.$type_Str.'<br>';
    $dbMI = $this->connect();

    // 1. define the query

    $sql = "INSERT INTO interest('interest`,'type') VALUES (:interest_Str, :type_Str)";

    // 2. prepare the statement
    $statement = $dbI->prepare($sql);

    //3. bind parameters
    $statement->bindParam(':interest_Str', $interest_Str, PDO::PARAM_STR);
    $statement->bindParam(':type_str', $type_Str, PDO::PARAM_STR);

    // 4. execute the statement
    $success = $statement->execute();

    // 5. return the result
    return $success;
}
    function getInterests()
    {   //get ALL the interest rows
        global $dbI;

        $dbI = $this->connect();
        // 1. define the query
        $sql = "SELECT * FROM interest ORDER BY interest_id";

        // 2. prepare the statement
        $statement = $dbI->prepare($sql);

        // 3. bind parameters

        // 4. execute the statement
        $statement->execute();

        // 5. return the result
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }
    function getInterest($interestID)
    {   //get JUST the interest rows associated with the integer "$interestID"
        global $dbI;

        $dbI = $this->connect();
        // 1. define the query
        $sql = "SELECT * FROM interest WHERE interest_id = :interestID ";

        // 2. prepare the statement
        $statement = $dbI->prepare($sql);

        // 3. bind parameters
        $statement->bindParam(':interestID', $interestID, PDO::PARAM_INT);

        // 4. execute the statement
        $statement->execute();

        // 5. return the result
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result;
    }
}