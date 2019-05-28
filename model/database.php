<?php
/**
 * Bruce Turner, Professor Ostrander, Spring 2019
 * IT 328 Full Stack Web Development
 * Dating IV Assignment: use data base
 * file: database.php
 * date: Monday, May 27 2019
 * class Database
 *
 * Here I want to conform to the required PEAR coding standards from the git go
 * " Apply PEAR Standards to your class files, including a class-level docblock
 *   above each class, and a docblock above each function. "
 *
 * indent 4 spaces
 * line length max 80 characters
 * class names begin with a upper case
 * private members (variables & functions) are preceded with an underscore
 * constants are all Uppercase
 * add PHPDoc to each class & function
 */
//3456789_123456789_123456789_123456789_123456789_123456789_123456789_1234567890
// the above is 80 characters
/*
 * //"Copy your CREATE TABLE statements into a block comment at the top of your
 * //Database class."
CREATE TABLE member(`member_id` int AUTO_INCREMENT PRIMARY KEY,
                       `fname` varchar(25) DEFAULT NULL,
                       `lname` varchar(25) DEFAULT NULL,
                       `age` int(6) DEFAULT NULL,
                       `gender` varchar(6) DEFAULT NULL,
                       `phone` varchar(10) DEFAULT NULL,
                       `email` varchar(30) DEFAULT NULL,
                       `state` varchar(2) DEFAULT NULL,
                       `seeking` varchar(6) DEFAULT NULL,
                       `bio` varchar(255) DEFAULT NULL,
                       `premium` tinyint DEFAULT NULL,
                       `image` varchar(255) DEFAULT NULL
                        );

*/
/*CREATE TABLE interest(`interest_id` int AUTO_INCREMENT PRIMARY KEY,
                       `interest` varchar(40) DEFAULT NULL,
                       `type` varchar(8) DEFAULT NULL);
*/
/*
 * CREATE TABLE member_interest(member_id int,  interest_id int);
 */
// We will do this exactly as the instructor did in the pdo project, and
// in the same order. Another point of reference is the grc-student GitHub example
// First, provide the database connection via the mandatory database credentials
// stored as constants outside of public_html
require('/home/bturnerg/config.php');

class Database
{

    function __construct()
    {
        $this->connect();
    }
    /**
     * establish a data base connection
     */

    function connect()
    {
        try {
            //Instantiate a database object
            $dbh = new PDO(DB_DSN, DB_USERNAME,
                DB_PASSWORD);
            //echo "Connected to database!!!";
            return $dbh;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return;
        }
    }
    function getLastID()
    {
        global $dbh;

        $dbh = $this->connect();
        // 1. define the query
        $sql = "SELECT LAST_INSERT_ID()";
        // 2. prepare the statement
        $statement = $dbh->prepare($sql);
        // 3. bind parameters

        // 4. execute the statement
        $statement->execute();
        // 5. return the result
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        $memberID = row[0];
        return $memberID;

    }

    function getmemberID($email)
    {
        global $dbh;

        $dbh = $this->connect();
        // 1. define the query
        $sql = "SELECT * FROM member WHERE email = :email";
        // 2. prepare the statement
        $statement = $dbh->prepare($sql);
        // 3. bind parameters
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        // 4. execute the statement
        $statement->execute();
        // 5. return the result
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    function getMembers()
    {
        global $dbh;

        $dbh = $this->connect();
        // 1. define the query
        $sql = "SELECT * FROM member ORDER BY lname";

        // 2. prepare the statement
        $statement = $dbh->prepare($sql);

        // 3. bind parameters

        // 4. execute the statement
        $statement->execute();

        // 5. return the result
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    function insertMember($fname, $lname, $age, $gender, $phone, $email, $state, $seeking, $bio, $premium, $image)
    {
        global $dbh;

        $dbh = $this->connect();
        // 1. define the query

        $sql = "INSERT INTO member(`fname`, `lname`, `age`, `gender`, `phone`, `email`, `state`, `seeking`, `bio`, `premium`, `image`)
            VALUES (:fname, :lname, :age, :gender, :phone, :email, :state, :seeking, :bio, :premium, :image)";

        // 2. prepare the statement
        $statement = $dbh->prepare($sql);

        //3. bind parameters
        $statement->bindParam(':fname', $fname, PDO::PARAM_STR);
        $statement->bindParam(':lname', $lname, PDO::PARAM_STR);
        $statement->bindParam(':age', $age, PDO::PARAM_INT);
        $statement->bindParam(':gender', $gender, PDO::PARAM_STR);
        $statement->bindParam(':phone', $phone, PDO::PARAM_STR);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':state', $state, PDO::PARAM_STR);
        $statement->bindParam(':seeking', $seeking, PDO::PARAM_STR);
        $statement->bindParam(':bio', $bio, PDO::PARAM_STR);
        $statement->bindParam(':premium', $premium, PDO::PARAM_INT);
        $statement->bindParam(':image', $image, PDO::PARAM_STR);

        // 4. execute the statement
        $success = $statement->execute();

        // 5. return the result
        return $success;

    }

    function getMember($id)
    {
        global $dbh;
        $dbh = $this->connect();

        // 1. define the query
        $sql = "SELECT * FROM member WHERE member_id = :id";

        // 2. prepare the statement
        $statement = $dbh->prepare($sql);

        // 3. bind parameters
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        // 4. execute the statement
        $statement->execute();

        // 5. return the result
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        //check if this is a premium member
        if($result['premium'] == 1)
        {
            $interests = explode(',',$result['interests']);
            /*
                        $interests = explode(';',$result['interests']);
                        $outdoor = explode(',',$interests[0]);
                        $indoor = explode(',',$interests[1]);
            */
            return new PremiumMember($result['fname'], $result['lname'], $result['age'],
                $result['gender'], $result['phone'], $result['email'], $result['state'],
                $result['seeking'], $result['bio'],$interests,"");
        }

        // if not premium we dont need some fields
        return new Member($result['fname'], $result['lname'], $result['age'],
            $result['gender'], $result['phone'], $result['email'], $result['state'],
            $result['seeking'], $result['bio']);
    }
    function insertMember_Interest($member_id, $interest_id)
    {
        global $dbh;

        $dbh = $this->connect();
        //echo $member_id.'<br>';
        // 1. define the query

        $sql = "INSERT INTO member_interest(`member_id`, `interest_id`) VALUES (:member_id, :interest_id)";

        // 2. prepare the statement
        $statement = $dbh->prepare($sql);

        //3. bind parameters
        $statement->bindParam(':member_id', $member_id, PDO::PARAM_INT);
        $statement->bindParam(':interest_id', $interest_id, PDO::PARAM_INT);

        // 4. execute the statement
        $success = $statement->execute();

        // 5. return the result
        return $success;
    }
}