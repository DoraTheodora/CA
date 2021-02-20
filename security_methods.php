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


?>