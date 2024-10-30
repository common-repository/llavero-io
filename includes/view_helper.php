<?php


class CiberLlaveroViewHelper 
{

    // $type ["WARN","SUCCESS", "ERROR"]
    public static function printMessage($type, $message){

        $styleClass = "";
        if ($type == "WARN"){
            $styleClass = "update-nag";
        }
        if ($type == "SUCCESS"){
            $styleClass = "updated";
        }
        if ($type == "ERROR"){
            $styleClass = "error";
        }

        echo "<div  class='notice ". $styleClass ."' ><p>";
        echo $message ;
        echo "</p></div>";
    }


}

