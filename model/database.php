<?php
/*
 * CREATE TABLE Member(`member_id` int(11) NOT NULL,
                       `fname` varchar(25) DEFAULT NULL,
                       `lname` varchar(25) DEFAULT NULL,
                       `age` int(6) DEFAULT NULL,
                       `gender` varchar(6) DEFAULT NULL,
                       `phone` varchar(10) DEFAULT NULL,
                       `email` varchar(30) DEFAULT NULL,
                       `state` varchar(2) DEFAULT NULL,
                       `seeking` varchar(6) DEFAULT NULL,
                       `bio` varchar(255) DEFAULT NULL,
                       `premium` tinyint(4) DEFAULT NULL,
                       `image` varchar(255) DEFAULT NULL
                        );
 */


// We will do this exactly as the instructor did in the pdo project, and
// in the same order. ANother point of reference is the grc-student GitHub example
// First, provide the database connection via the mandatory database credentials
// stored as constants outside of public_html
require_once ('/home/bturnerg/config.php');

class Database
{
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

    function getMembers()
    {
        global $dbh;

        $dbh = $this->connect();
        //1. define the query
        $sql = "SELECT * FROM Members ORDER BY lname";

        //2. prepare the statement
        $statement = $dbh->prepare($sql);

        //3. bind parameters

        //4. execute the statement
        $statement->execute();

        //5. return the result
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        //print_r($result);
        return $result;
    }

    function insertMember($fname, $lname, $age, $gender, $phone, $email, $state, $seeking, $bio, $premium, $image, $interests)
    {
        global $dbh;

        $dbh = $this->connect();
        //1. define the query

        $sql = "INSERT INTO Members(`fname`, `lname`, `age`, `gender`, `phone`, `email`, `state`, `seeking`, `bio`, `premium`, `interests`)
            VALUES (:fname, :lname, :age, :gender, :phone, :email, :state, :seeking, :bio, :premium, :interests)";

        //2. prepare the statement
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
        $statement->bindParam(':premium', $premium, PDO::PARAM_BOOL);
        $statement->bindParam(':interests', $interests, PDO::PARAM_STR);

        // echo $statement;

        //4. execute the statement
        $success = $statement->execute();

        //5. return the result
        return $success;

    }

    function getMember($id)
    {
        global $dbh;
        $dbh = $this->connect();

        //1. define the query
        $sql = "SELECT * FROM Members WHERE member_id = :id";

        //2. prepare the statement
        $statement = $dbh->prepare($sql);

        //3. bind parameters
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        //4. execute the statement
        $statement->execute();

        //5. return the result
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
}