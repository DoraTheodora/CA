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

?>