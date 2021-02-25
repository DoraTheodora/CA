<?php
//* Theodora Tataru
//* C00231174
//* Secure login system
//* 2021

    function filter($toClean)
    {
        $symbolsToBeReplaced = Array('&', '<', '>',  '(', ')', '{', '}', '[' ,']', '"', "'", ';' , '/', '\\');
        $replaceSymbols = Array('&amp', '&lt', '&gt', '&#40', '&#41', '&#123', '&#125',
                                '&#91', '&#93', '&#34', '&#39', '&#59', '&#47', '&#92');
        $sanitizedString = str_replace($symbolsToBeReplaced, $replaceSymbols, $toClean);
        #if($sanitizedString != $toClean)
        #{
        #	return "No injections here buddy!";
        #}
        return $sanitizedString;
    }
    
    function log_activity($action, $ip, $agent, $outcome)
    {
        include 'conf.php';
        $sql = "INSERT INTO Logs(action_performed, ip, clientAgent, date_time, outcome) VALUES (?,?,?,?,?)";
        if($query = $conn->prepare($sql))
        {
            $now = date("Y-m-d H:i:s");
            $query->bind_param("sssss", $action, $ip, $agent, $now, $outcome);
            if(!$query->execute())
            {
                "Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
            }
            $query->close();
        }
    }

    function createAccount($username, $password, $conn)
    {
        $salt = generateSalt();
        $to_hash = $password . $salt;
        $hash_pass = password_hash($to_hash, PASSWORD_ARGON2I);
        $ip = $_SESSION['ip'];
        $agent = $_SESSION['clientAgent'];
        $isAdmin = 0;

        $sql = "INSERT INTO MyGuests(user, passwd, salt, ip, clientAgent,isAdmin) VALUES (?,?,?,?,?,?)";
        $query = $conn->prepare($sql);
        $query->bind_param("sssssi",$username, $hash_pass, $salt, $ip, $agent, $isAdmin);
        if($query->execute())
        {
            log_activity("sign up attempted", $ip, $agent, "account created");
            echo "<script>alert('Account created!')</script>";
            session_unset();
            session_destroy();
            header('Refresh:0 url=index.php');
        }
        else
        {
            log_activity("sign up attempted", $ip, $agent, "account not created, error on the server side");
            echo $query->error;
        }
        $query->close();
    }

    function generateSalt()
    {
        $length = 96;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $salt = "";
        for($i = 0 ; $i < $length; $i++)
        {
            $index = rand(0, strlen($characters) -1);
            $salt .= $characters[$index];
        }
        return $salt;
    } 

    function userExistsInTheDatabase($username, $conn)
    {
        $SQL = "SELECT * FROM MyGuests WHERE user=?";
        $query = $conn->prepare($SQL);
        $query->bind_param("s",$username);
        if($query->execute())
        {
            $users = $query->get_result()->fetch_assoc();
            if(!empty($users))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            echo $query->error;
        }
        $query->close();
    }

    function passwords_matching($password, $confirm)
    {
        if($password !== $confirm) 
        {
            return false;
        }
        return true;
    }

    function password_length($password)
    {
        return strlen($password) >= 8;
    }

    function password_has_all_required_characters($password) 
    {
        // minimum: 8 characters && 1 number &&  1 uppercase letter &&  1 lowercase letter && 1 symbol

        $containsLowercase = preg_match('/[a-z]/',$password);
        $containsUppercase = preg_match('/[A-Z]/',$password);
        $containsNumbers = preg_match('/\d/',$password);
        $containsSpecial = preg_match('/[^a-zA-Z\d]/',$password);

        return $containsLowercase && $containsUppercase && $containsNumbers && $containsSpecial;
    }

    function username_is_in_password($password, $username)
    {
        return strpos($password, $username) !== false;
    }

?>