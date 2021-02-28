<?php
//* Theodora Tataru
//* C00231174
//* Secure login system
//* 2021
    function log_out_after_one_hour()
    {

    }

    function auto_logout($time_session_started)
    {
        $now = time();
        $diff = $now - $time_session_started;
        if ($diff > 3000)
        {          
            return true;
        }
        else
        {
            return false;
        }
    }

    function is_user_locked()
    {
        //? This method checks is the device the users uses to log in is locked or not
        include 'conf.php';
        $ip = $_SESSION['ip'];
        $agent = $_SESSION['clientAgent'];
        $sql = "SELECT MAX(locked_until) FROM NoGuests WHERE ip=? AND clientAgent=?";
        $query = $conn->prepare($sql);
        $query->bind_param("ss",$ip, $agent);
        $query->execute();
        $suspiciousUser = $query->get_result()->fetch_array();
        $query->close();
        $locked_until = strtotime($suspiciousUser[0]);
        $blocked_time = $locked_until-time();
        /*echo $sql;
        echo "<br>Blocked until:".$locked_until;
        echo "<br>Current time: ".time();
        echo "<br>Blocked time: ".$blocked_time;*/
        $_SESSION['lockedTime'] = $locked_until;
        if(!empty($suspiciousUser))
        {
            if($blocked_time > 0)
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
            return false;
        }
    }
    function validateUser($username, $password)
    {
        //? This method checks the user's credentials
        include 'conf.php';
        if(!is_user_locked())
        {
            $sql = "SELECT salt, passwd FROM MyGuests WHERE user=?";
            $query = $conn->prepare($sql);
            $query->bind_param("s", $username);
            $query->execute();
            $result = $query->get_result()->fetch_assoc();
            $ip = $_SESSION['ip'];
            $agent = $_SESSION['clientAgent'];
            if(!empty($result))
            {
                $salt = $result['salt'];
                $pass = $result['passwd'];
                $to_hash = $password . $salt;
                if(password_verify($to_hash, $pass))
                {
                    logIn($username, $pass, $conn);
                }
                else
                {
                    log_activity("authentication", $ip, $agent, "invalid credentials");
                    $_SESSION['incorrect_credentials'] = true;
                    header('Refresh:0');
                }
            }
            else
            {
                log_activity("authentication", $ip, $agent, "invalid credentials");
                $_SESSION['incorrect_credentials'] = true;
                header('Refresh:0');
            }
            $query->close();
        }
        else
        {
            $_SESSION['blocked'] = true;
            header("Location: blocked.php");
        }
    }
    function logIn($username, $pass, $conn)
    {
        $SQL = "SELECT user FROM MyGuests WHERE user=? AND passwd=?";
        $query = $conn->prepare($SQL);
        $query->bind_param("ss",$username, $pass);
        $users = $query->execute();
        //$login_user = mysqli_query($conn, $SQL);
        $results = $query->get_result()->fetch_assoc();
        if(!empty($results))
        {
            $ip = $_SESSION['ip'];
            $userAgent = $_SESSION['clientAgent'];
            session_unset();
            session_destroy();
            $now = date("Y-m-d H:i:s");
            $sql = "UPDATE MyGuests SET login_date = ?, ip = ?, clientAgent = ? WHERE user=? AND passwd=?";
            $query = $conn->prepare($sql);
            $query->bind_param("sssss",$now, $ip, $userAgent, $username, $pass);
            if($query->execute())
            {
                log_activity("authentication", $ip, $userAgent, "approved");
                //$result = mysqli_query($conn, $sql);
                session_id(generateRandomSessionID());
                session_start(); 
                $_SESSION['time_user_logged_in'] = time();
                $_SESSION['max_idle_duration'] = 600;
                $_SESSION['max_session_duration'] = time() + 3600;
                $_SESSION["id_s"] = session_id();
                $_SESSION["name"] = $username;    
                if(is_admin($username))
                {
                    $_SESSION["is_admin"] = true;
                }
                else
                {
                    $_SESSION["is_admin"] = false;
                }
                header('Location: profile.php');
                $_SESSION['login_attempts'] = 0;
            }
            else
            {
                log_activity("authentication", $ip, $userAgent, "server error");
            }
            $query->close();
            
        }
        else
        {
            $_SESSION['incorrect_credentials'] = true;
            header('Refresh:0');
            echo $query->error;
        }  
    }
    function is_admin($username)
    {
        include 'conf.php';
        $sql = "SELECT isAdmin FROM MyGuests WHERE user=?";
        $query = $conn->prepare($sql);
        $query->bind_param("s",$username);
        if(!$query->execute()) 
        {
            "Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
            return false;
        }
        $results = $query->get_result()->fetch_assoc();
        if(!empty($results))
        {
            if($results['isAdmin'] == 1)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;
    }
    function generateRandomSessionID()
    {
        $length = 64;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $session_id = "";
        for($i = 0 ; $i < $length; $i++)
        {
            $index = rand(0, strlen($characters) -1);
            $session_id .= $characters[$index];
        }
        return $session_id;
    }

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
            header('Refresh:0 url=login.html.php');
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

    function getClientAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    function getIPAddress()
    {
        //whether ip is from the share internet  
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {  
            $ip = $_SERVER['HTTP_CLIENT_IP'];  
        }  
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        }  
        //whether ip is from the remote address  
        else
        {  
            $ip = $_SERVER['REMOTE_ADDR'];  
        }  
        return $ip;  
    }

    function change_password($username, $password)
    {
        include 'conf.php';
        $salt = generateSalt();
        $to_hash = $password . $salt;
        $hash_pass = password_hash($to_hash, PASSWORD_ARGON2I);
        $agent = getClientAgent();
        $ip = getIPAddress();
        $isAdmin = 0;

        $sql = "UPDATE MyGuests SET passwd = ?, salt = ? WHERE user = ? AND ip = ? AND clientAgent = ?";
        $query = $conn->prepare($sql);
        $query->bind_param("sssss",$hash_pass, $salt, $username, $ip, $agent);
        if($query->execute())
        {
            log_activity("change password", $ip, $agent, "password changed");
            echo "<script>alert('Password change!!')</script>";
            session_unset();
            session_destroy();
            header('Refresh:0 url=login.html.php');
        }
        else
        {
            log_activity("change password", $ip, $agent, "password not changed");
            echo $query->error;
        }
        $query->close();
    }

    function check_existing_password($username, $password)
    {
        include 'conf.php';
        $sql = "SELECT salt, passwd FROM MyGuests WHERE user=?";
        $query = $conn->prepare($sql);
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result()->fetch_assoc();
        if(!empty($result))
        {
            $salt = $result['salt'];
            $pass = $result['passwd'];
            $to_hash = $password . $salt;
            if(password_verify($to_hash, $pass))
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
            return false;
        }
    }

    function new_pass_is_the_same_as_old_pass($new_pass, $old_pass)
    {
        return $new_pass == $old_pass;
    }

    function get_log()
    {
        include 'conf.php';
        $sql = "SELECT * FROM Logs";
        if($query = $conn->query($sql))
        {
            echo '
            <span class="login100-form-title p-b-20">
                    Activity logs <br><br>
                </span>';
            echo "<table class='styled-table'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ACTION PERFORMED</th>
                            <th>IP</th>
                            <th>AGENT</th>
                            <th>DATE TIME</th>
                            <th>OUTCOME</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class='active-row'>
                        ";        
            while($row = $query->fetch_assoc())
            {
                echo "<tr>";
                echo "<td>".$row["id"]."</td>";
                echo "<td>".$row["action_performed"]."</td>";
                echo "<td>".$row["ip"]."</td>";
                echo "<td>".$row["clientAgent"]."</td>";
                echo "<td>".$row["date_time"]."</td>";
                echo "<td>". $row["outcome"]."</td>";
                echo "</tr>";
            }
            echo '
                </tr>
                </table>';
        }
        $query->free();
    }

?>