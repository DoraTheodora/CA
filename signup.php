<?php
    // ! Needs to be removed from here
    $conn = mysqli_connect("localhost:3306", "root", "", "tt");

    //TODO hash and salt the password
    $username = $_POST['username'];
    $password = $_POST['pass'];
    $salt = generateSalt();
    

    if (!$conn) 
    {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "INSERT INTO MyGuests(user, passwd, salt) VALUES ('$username', '$password', '$salt')";
    if(mysqli_query($conn, $sql))
    {
        echo "New record created successfully";
    } 
    else 
    {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);



    // * Functions
    function generateSalt()
    {
        $length = 64;
        $characters = '0123456789!@#$%^&*()|}{:[]<>?/.,|\~`abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $salt = "";
        for($i = 0 ; $i < $length; $i++)
        {
            $index = rand(0, strlen($characters) -1);
            $salt .= $characters[$index];
        }
        return $salt;
    } 
?>  