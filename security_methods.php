<?php
//* Theodora Tataru
//* C00231174
//* Secure login system
//* 2021

    function delete_session_at_log_out()
    {
        require 'conf.php';
        $ip = getIPAddress();
        $agent = getClientAgent();
        $client_session = filter($_COOKIE['sesh']);
        $sql = "DELETE FROM ActiveSessions WHERE ip = ? AND clientAgent = ? AND session_id = ?";
        $query = $conn->prepare($sql);
        $query->bind_param("sss", $ip, $agent, $client_session);
        if(!$query->execute())
        {
            "Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
        }
    }
    function check_session_id()
    {
        require 'conf.php';
        $client_session = filter($_COOKIE['sesh']);
        if($_COOKIE['sesh'] != $client_session)
        {
            header('Location: logout.php');
        }
        $ip = getIPAddress();
        $agent = getClientAgent();
        $sql = "SELECT session_id, ip, clientAgent FROM ActiveSessions WHERE session_id= ? AND ip = ? AND clientAgent = ? ";
        $query = $conn->prepare($sql);
        $query->bind_param("sss", $client_session, $ip, $agent);
        $query->execute();
        $result = $query->get_result()->fetch_assoc();
        if(!empty($result))
        {
            $session = $result['session_id'];
            if($session != $_COOKIE['sesh'])
            {
                header('Location: logout.php');
            }
        }
        else
        {
            //"Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
            header('Location: logout.php');
        }
        $query->close();
    }

    function save_active_session()
    {
        require 'conf.php';
        $ip = getIPAddress();
        $agent = getClientAgent();
        $sess_id = session_id();
        $start_date_time = date("Y-m-d H:i:s");

        $sql = "INSERT INTO ActiveSessions(session_id, ip, clientAgent, date_time) VALUES (?, ?, ?, ?)";
        $query = $conn->prepare($sql);
        $query->bind_param("ssss", $sess_id, $ip, $agent, $start_date_time);
        if(!$query->execute()) 
        {
            "Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
        } 
        $query->close();
    }

    function lock_user()
    {
        require 'conf.php';
        $ip = $_SESSION['ip'];
        $agent = $_SESSION['clientAgent'];
        $sql = "SELECT * FROM NoGuests WHERE ip=? AND clientAgent=?";
        $query = $conn->prepare($sql);
        $query->bind_param("ss", $ip, $agent);
        $query->execute();
        //$suspiciousAgent = mysqli_query($conn, $sql);
        //$rows = mysqli_num_rows($suspiciousAgent);
        $results = $query->get_result()->fetch_assoc();
        if(!empty($results))
        {
            $start_date_time = date("Y-m-d H:i:s");
            $locked_until = date('Y-m-d H:i:s',strtotime('+3 minutes',strtotime($start_date_time)));
            
            $sql = "UPDATE NoGuests SET locked_until = ? WHERE ip=? AND clientAgent=?";
            $query = $conn->prepare($sql);
            $query->bind_param("sss", $locked_until, $ip, $agent);
            if(!$query->execute())
            {
                "Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
            }
            else
            {
                log_activity("failed auth more than 5 times", $ip, $agent, "blocked");
            }  
            $query->close();
        }
        else 
        {
            $start_date_time = date("Y-m-d H:i:s");
            $locked_until = date('Y-m-d H:i:s',strtotime('+3 minutes',strtotime($start_date_time)));
            $sql = "INSERT INTO NoGuests(ip, clientAgent, locked_until) VALUES (?, ?, ?)";
            $query = $conn->prepare($sql);
            $query->bind_param("sss",$ip, $agent, $locked_until);
            if(!$query->execute()) 
            {
                "Failed to connect to MySQL: (" . $query->connect_errno . ") " . $query->connect_error;
            } 
            else
            {
                log_activity("failed auth more than 5 times", $ip, $agent, "blocked");
            }
            $query->close();
        }
        if(is_user_locked()) 
        {
            header("Location: blocked.php");
        }
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
        require 'conf.php';
        $ip = getIPAddress();
        $agent = getClientAgent();
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
        require 'conf.php';
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
                $to_hash = $salt . $password; //! SALT IS FIRST!!! - SO YOU CAN'T SEE WHERE THE PREDICTABILITY BEGINS
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
                //! Cookie Values
                setcookie('sesh', session_id(), $_SESSION['max_session_duration'], '/');
                //! End Cookie Values
                save_active_session(); 
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
        require 'conf.php';
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
            $index = rand(0, strlen($characters) -1); //!RAND_INT
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
        return $sanitizedString;
    }
    
    function log_activity($action, $ip, $agent, $outcome)
    {
        require 'conf.php';
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
        $to_hash = $salt . $password;
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
        $salt = '';
        for($i = 0; $i < $length; $i++)
        {
            $num = random_int(0, 36);
            $char = base_convert($num, 10, 36);
            $salt = $salt.$char;
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
        //! could contain something bad - sanitize it! can be spoofed
        //! store the session ID in the database while the user is online
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
        require 'conf.php';
        $salt = generateSalt();
        $to_hash = $salt .$password;
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
        require 'conf.php';
        $sql = "SELECT salt, passwd FROM MyGuests WHERE user=?";
        $query = $conn->prepare($sql);
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result()->fetch_assoc();
        if(!empty($result))
        {
            $salt = $result['salt'];
            $pass = $result['passwd'];
            $to_hash = $salt .  $password;
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
        require 'conf.php';
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