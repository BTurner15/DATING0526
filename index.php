<?php
/* Bruce Turner, Professor Ostrander, Spring 2019
 * IT 328 Full Stack Web Development
 * Dating IV Assignment: incorporate classes
 * file: index.php  is the default landing page, defines various routes
 * date: Monday, May 27 2019
*/
ini_set('display_errors', 1);
error_reporting(E_ALL);


//Require auto-loads
require_once('vendor/autoload.php');
require_once('model/validation-functions.php');

//Start a session
session_start();

$f3 = Base::instance();
/*
//Turn on Fat-Free error reporting
set_exception_handler(function($obj) use($f3){
    $f3->error(500,$obj->getmessage(),$obj->gettrace());
});
set_error_handler(function($code,$text) use($f3)
{
    if (error_reporting())
    {
        $f3->error(500,$text);
    }
});
*/
$f3->set('DEBUG', 3);


$f3->set('genders', array('Male', 'Female'));
$f3->set('seekSexs', array('Male', 'Female'));
/* Establish our data structures */
$f3->set('states', array('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
    'Colorado', 'Connecticut', 'Delaware', 'Florida', 'Georgia', 'Hawaii', 'Idaho',
    'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine',
    'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri',
    'Montana', 'Nebraska', 'Nevada', 'New Hampshire','New Jersey', 'New Mexico',
    'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon',
    'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas',
    'Utah', 'Vermont', 'Virgina', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming' ));

$f3->set('states_ABBR', array('AL', 'AK', 'AZ', 'AR', 'CA', 'CO', 'CT', 'DE', 'FL', 'GA',
    'HI', 'ID', 'IL', 'IN', 'IA', 'KS', 'KY', 'LA', 'ME', 'MD', 'MA', 'MI', 'MN', 'MS',
    'MO', 'MT', 'NE', 'NV', 'NH', 'NJ', 'NM', 'NY', 'NC', 'ND', 'OH', 'OK', 'OR', 'PA',
    'RI', 'SC', 'SD', 'TN', 'TX', 'UT', 'VT', 'VA', 'WA', 'WV', 'WI', 'WY' ));

$f3->set('indoorInterests', array('tv', 'puzzles', 'movies', 'reading', 'cooking',
         'playing cards', 'board games', 'video games'));
$f3->set('outdoorInterests', array('hiking', 'walking', 'biking',
         'climbing', 'swimming', 'collecting'));

//Define a default route
$f3->route('GET /', function(){
    //don't want any lingering session information
    $_SESSION = array();

    //display landing page Template, which POSTS to personal information
    $view = new Template();
    echo $view->render('views/home.html');

});
//Define a personal information route
$f3->route('GET|POST /perinfo', function($f3) {
    //Display personal information, upon completion REROUTES to profile
    //If form has been submitted, validate. The wrinkle with Dating III is to
    //"
    //- instantiate the appropriate class -- Member or PremiumMember -- depending on whether or not the checkbox was selected
    //- save the form data to the appropriate member object
    //- store the member object in a session variable
    //"
    if(!empty($_POST)) {
        //Get data from form
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $age= $_POST['age'];
        $gender = $_POST['gender'];
        $phone = $_POST['phone'];

        //Add data to hive
        $f3->set('fname', $fname);
        $f3->set('lname', $lname);
        $f3->set('age', $age);
        $f3->set('gender', $gender);
        $f3->set('phone', $phone);

        if (validPerinfoForm()) {

            //Write data to Session
            $_SESSION['fname'] = $_POST['fname'];
            $_SESSION['lname'] = $_POST['lname'];
            $_SESSION['age'] = $_POST['age'];
            $_SESSION['gender'] = $_POST['gender'];
            $_SESSION['phone'] = $_POST['phone'];

            //now fold in the classes...parse on PremiumMember checkbox, if we are in a  mode with
            //an "ordinary" Member then !premium will be true ONLY THIS TIME until submitted

            //be careful here, if it is not set it doesnt exist!
            if(!isset($_POST['premium']))
            {
                $member = new Member($_SESSION['fname'],$_SESSION['lname'],$_SESSION['age'],
                                     $_SESSION['gender'],$_SESSION['phone']);
                $_SESSION['memberType'] = "0";

            }
            else
            {
                $member = new PremiumMember($_SESSION['fname'],$_SESSION['lname'],$_SESSION['age'],
                                            $_SESSION['gender'],$_SESSION['phone']);
                $_SESSION['memberType'] = "1";
            }
            //store the individual either way

            $_SESSION['member'] = $member;

            //print_r($_SESSION['memberType'] == true);
            //we are only going to store in the $_SESSION[] NOT $f3->set('member', $member);

            $f3->reroute('/profile');
        }
    }
    //Display personal information, until REROUTED to  profile in above
    $view = new Template();
    echo $view->render('views/perinfo.html');
});
//Define a debug route
$f3->route('GET|POST /debug', function() {
    // here we will instantiate two Members, one a "regular" Member, and another
    // a PremiumMember. Then use print_r() to look at them, and test the getters and setters

        $buckaroo = new Member('Buckaroo','Turner','24',
                             'Male','1234567890');

        $summer = new PremiumMember('Summer', 'Turner', '18',
                               'Female', '0987654321','Summer@gmail.com',
                                 'WA', 'Male', 'The best female ever!');
        print_r($buckaroo);
        echo "<br>"; echo "<br>";
        $buckaroo->setSeeking("FEMALE");
        $buckaroo->setState("PA");
        $buckaroo->setEmail("Buckaroo@foo.com");
        $buckaroo->setBio("I came to live with Papi, and then Summer joined us");

        print_r($summer);
        echo "<br>"; echo "<br>";
        $summer->setIndoor("in_1 in_2 in_3");
        $summer->setOutdoor("o_A o_B o_C");
        print_r($summer);

        echo "<br>"; echo "<br>";
        print_r($buckaroo);
        //try something you should NOT be able to do: display interests on Member
        echo "<br>"; echo $summer->getIndoor();
        //$buckaroo->setOutdoor("this should error out"); // it does, kind of

});
//Define a profile route
$f3->route('GET|POST /profile', function($f3) {
    //Display profile information, upon completion REROUTES to interests
    //if and only if (IFF - I always liked this acronym!) the individual is a PremiumMember
    //make sure that se have a clean slate here
    $_SESSION['email'] = null;
    $_SESSION['resState'] = null;
    $_SESSION['seekSex'] = null;
    $_SESSION['bio'] = null;

    if(!empty($_POST)) {
        //Add data to hive
        $email = $_POST['email'];
        $resState = $_POST['resState'];
        $seekSex = $_POST['seekSex'];
        $bio = $_POST['bio'];

        $f3->set('email', $email);
        $f3->set('resState', $resState);
        $f3->set('seekSex', $seekSex);
        $f3->set('bio', $bio);
         if (validProfileForm()) {

            $_SESSION['email'] = $_POST['email'];
            $_SESSION['resState'] = $_POST['resState'];
            $_SESSION['seekSex'] = $_POST['seekSex'];
            $_SESSION['bio'] = $_POST['bio'];

            //we must remember Dating III is all about objects.
            //Formerly I USED to store individual data items in the $_SESSION[] data structure by name.
            //continuing to do so is redundant

            $_SESSION['member']->setEmail($_POST['email']);
            $_SESSION['member']->setState($_POST['resState']);
            $_SESSION['member']->setSeeking($_POST['seekSex']);
            $_SESSION['member']->setBio($_POST['bio']);

            $memberType = $_SESSION['memberType'];
            //Ok, the time has come to decide whether to display the interest to the Member (or not)
            if(!$memberType)
            {
                $f3->reroute('/summary');
            }
            else
            {
                $f3->reroute('/interests');
            }
        }
    }
    //Display profile, until REROUTED to  interests in above
    $view = new Template();
    echo $view->render('views/profile.html');
});

//Define a interests route. We will only get here via reroute from profile.html
$f3->route('GET|POST /interests', function($f3) {


    if(!empty($_POST)) {
        //Display interests, until REROUTED to summary
        //form valid?
        if (validInterestsForm()) {
            //Write data to Session "member" object
            $_SESSION['member']->setIndoor($_POST['indoor']);
            $_SESSION['member']->setOutdoor($_POST['outdoor']);

            $f3->reroute('/summary');
        }
    }
    $view = new Template();
    echo $view->render('views/interests.html');

});

//Define a route to check the interest arrays
$f3->route('GET|POST /getid', function() {
    global $dbM;
    $dbM = new Member();

    $row = $dbM->getMemberID('john@example.com');

    print_r($row);
    $memberID = $row['member_id'];
    $memberID = int($memberID);
    //echo '<br>'.gettype($memberID).'<br>';
    $_SESSION['memberID'] = $memberID;
    echo "<br>"; echo "<br>";
    echo $memberID;

 });
//Define a summary route
$f3->route('GET|POST /summary', function($f3) {
    /*
     * want to pause here and ensure that we have not been spoofed with indoor & outdoor interests arrays
     */
    //save the data gathered in interests IF A PREMIUM MEMBER
    global $dbM;
    $dbM = new MemberDB();
    global $dbMI;

    $memberType = $_SESSION['memberType'];

    if($memberType == 1) {
        $indoor = $_SESSION['member']->getIndoor();
        $freshIndoor = array();
        $numIndoor = count($indoor);
        $ctr = -1;
        for ($i = 0; $i < $numIndoor; $i++) {
            if (validIndoor($indoor[$i])) {
                $ctr++;
                $freshIndoor[$ctr] = $indoor[$i];
            } else {
                //skip it
            }
        }
        $_SESSION['indoor'] = $freshIndoor;
        $_SESSION['numIndoor'] = count($freshIndoor);
        //perform a similar duty to check for spoofing in outdoor interests
        $outdoor = $_SESSION['member']->getOutdoor();
        $freshOutdoor = array();
        $numOutdoor = count($outdoor);
        $ctr = -1;
        for ($i = 0; $i < $numOutdoor; $i++) {
            if (validOutdoor($outdoor[$i])) {
                $ctr++;
                $freshOutdoor[$ctr] = $outdoor[$i];
            } else {
                //skip it
            }
        }
        $_SESSION['outdoor'] = $freshOutdoor;
        $_SESSION['numOutdoor'] = count($freshOutdoor);
        //we have the two sets of allowable interests available in the two arrays loaded at
        //the beginning, our buddies indoor[] and outdoor[]
        //just waiting for us! Yeah!

        if (isset($_SESSION['indoor']) && get_class($_SESSION['member']) == "PremiumMember") {
            $_SESSION['member']->setIndoor(implode(", ", $_SESSION['indoor']));
        }

        if (isset($_SESSION['outdoor']) && get_class($_SESSION['member']) == "PremiumMember") {
            $_SESSION['member']->setOutdoor(implode(", ", $_SESSION['outdoor']));
        }
    }
    if($memberType == 0) { //a normal Member
        $dbM->insertMember($_SESSION['fname'],$_SESSION['lname'],$_SESSION['age'],
            $_SESSION['gender'],$_SESSION['phone'],$_SESSION['email'],$_SESSION['resState'],
            $_SESSION['seekSex'],$_SESSION['bio'],0,"");
    }
    else {
        $dbM->insertMember($_SESSION['fname'],$_SESSION['lname'],$_SESSION['age'],
            $_SESSION['gender'],$_SESSION['phone'],$_SESSION['email'],$_SESSION['resState'],
            $_SESSION['seekSex'],$_SESSION['bio'],1,"");
    }
    //we still have to address the member-interests table. First we need his/her member_id
    $memberID = $dbM->lastInsertId();
    $dbM = null;
    //for some reason this is really limping along... getting desperate here... it is NOT
    //finding the new database routines for member_interest table....
    // going to try it manually
    $_SESSION['memberID'] = $memberID;

    //now make the insertions into the member_interest table
    $memberID = $_SESSION['memberID'];


    if($memberType == 1) {
        //we have a total of (numIndoor + numOutdoor) (memberID, interest_id) pairs
        //to add to the member_interest table. process indoor first, then outdoor
        //$dbM = null;
        $dbMI = new Member_interest();

         //$type = 'indoor';
         //print_r($_SESSION['indoor']);
         //echo '<br>'.$_SESSION['numIndoor'].'<br>';
         //$interestID = getIndoorDBIndex($interest);

         for ($i = 0; $i < $_SESSION['numIndoor']; $i++) {
             $interest = array_pop($_SESSION['indoor']);
             $index = -1;
             switch ($interest) {
                 case "tv":
                     $index = 1;
                     break;
                 case "puzzles":
                     $index = 2;
                     break;
                 case "movies":
                     $index = 3;
                     break;
                 case "reading":
                     $index = 4;
                     break;
                 case "cooking":
                     $index = 5;
                     break;
                 case "playing cards":
                     $index = 6;
                     break;
                 case "board games":
                     $index = 7;
                     break;
                 case "video games":
                     $index = 8;
                     break;
                 default:
                     break;
             }
             $interestID = $index;
             //echo '<br>'.' '.$memberID.' '.$interestID.'<br>';
             //ready to insert a (memberID, interestID) pair
             $dbMI = new Member_interest();
             $dbMI->insertMember_Interest($memberID, $interestID);
         }
         //$type = 'outdoor';
         for ($i = 0; $i < $_SESSION['numOutdoor']; $i++) {
            $interest = array_pop($_SESSION['outdoor']);
            $index = -1;
            switch ($interest) {
                case "hiking":
                    $index = 1;
                    break;
                case "walking":
                    $index = 2;
                    break;
                case "biking":
                    $index = 3;
                    break;
                case "climbing":
                    $index = 4;
                    break;
                case "swimming":
                    $index = 5;
                    break;
                case "collecting":
                    $index = 6;
                    break;
                default:
                    break;;
            }
             $interestID = $index;
             //echo '<br>'.' '.$memberID.' '.$interestID.'<br>';
             //ready to insert a (memberID, interestID) pair
             $dbMI = new Member_interest();
             $dbMI->insertMember_Interest($memberID, $interestID);
        }
    }
    //Display summary, which concludes Dating IV
    $view = new Template();
    echo $view->render('views/summary.html');

});
$f3->route('GET /admin', function($f3)
{
    global $dbM;
    global $dbMI;
    global $dbI;

    $dbM = new MemberDB();

    $members = $dbM->getMembers();
    $f3->set('members', $members);

    $view = new Template();
    echo $view->render('views/all-members.html');
});

$f3->route('GET /admin/@member_id',
    function($f3, $params) {
        global $dbM;
        global $dbMI;

        $dbM = new MemberDB();

        $member_id = $params['member_id'];
        $member = $dbM->getMember($member_id);
        $f3->set('member', $member);


        $f3->set('fname', $member->getFname());
        $f3->set('lname', $member->getLname());
        $f3->set('age', $member->getAge());
        $f3->set('gender', $member->getGender());
        $f3->set('phone', $member->getPhone());
        $f3->set('email', $member->getEmail());
        $f3->set('state', $member->getState());
        $f3->set('seeking', $member->getSeeking());
        $f3->set('bio', $member->getBio());

        //if this is a premium member include other field information
        if (get_class($member) == "PremiumMember")
        {
            $f3->set('indoor', $member->getInDoor());
            $f3->set('outdoor', $member->getOutDoor());
        }

        $view = new Template();
        echo $view->render('views/view-member.html');
    });
//Run fat free
$f3->run();