<?php
    include 'conf.php';
    session_start();
    if(isset($_POST['sign_up_username']) && isset($_POST['pass1']) && isset($_POST['pass2']))
    {
        $username = $_POST['sign_up_username'];
        $password1 = $_POST['pass1'];
        $password2 = $_POST['pass2'];

        if (!$conn) 
        {
            die("Connection failed: " . mysqli_connect_error());
        }
        $create_user = true;
        if(userExistsInTheDatabase($username, $conn))
        {
            $create_user = false;
            $_SESSION['username_exists'] = true;
            header("Refresh:0");
        }

        if(!passwords_matching($password1, $password2))
        {
            $create_user = false;
            $_SESSION['passwords_not_matching'] = true;
            header("Refresh:0");
        }
        if(!password_length($password1))
        {
            $create_user = false;
            $_SESSION['password_too_short'] = true;
            header("Refresh:0");
        }
        if(!password_has_all_required_characters($password1))
        {
            $create_user = false;
            $_SESSION['password_needs_other_type_of_characters'] = true;
            header("Refresh:0");
        }
        if($create_user)
        {
            createAccount($username, $password1, $conn);
        }
    }
    else
    {
        header("Location: signup.html.php");
    }
   

   
    // * Functions ---------------------------------------------------------------------------------------------------------
    function createAccount($username, $password, $conn)
    {
        $salt = generateSalt();
        $to_hash = $password . $salt;
        $hash_pass = password_hash($to_hash, PASSWORD_ARGON2I);
        $ip = $_SESSION['ip'];
        $agent = $_SESSION['clientAgent'];

        $sql = "INSERT INTO MyGuests(user, passwd, salt, ip, clientAgent) VALUES ('$username', '$hash_pass', '$salt', '$ip', '$agent')";
        if(mysqli_query($conn, $sql))
        {
            //
            echo "<script>alert('Account created!')</script>";
            session_unset();
            session_destroy();
            header('Refresh:0 url=index.php');
        } 
        else 
        {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    
        mysqli_close($conn);
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
        $SQL = "SELECT * FROM MyGuests WHERE user='$username'";
        $users = mysqli_query($conn, $SQL);
        if(mysqli_num_rows($users) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
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
?>  

