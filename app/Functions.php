<?php
class Functions {

    /**
     * @param $user_id
     * @param $avatar_hash
     * @return string
     */
    public static function getAvatarUrl($user_id, $avatar_hash) {
        if (!$avatar_hash) {
            return null;
        }

        $isGif = substr($avatar_hash, 0, 2) == "a_";

        $base_url   = "https://cdn.discordapp.com/avatars/";
        return $base_url.$user_id.'/'.$avatar_hash.'.'.($isGif ? 'gif' : 'png').'';
    }
    
    public static function friendlyTitle($string, $wordLimit = 0){
        $separator = '-';

        if($wordLimit != 0){
            $wordArr = explode(' ', $string);
            $string = implode(' ', array_slice($wordArr, 0, $wordLimit));
        }

        $quoteSeparator = preg_quote($separator, '#');

        $trans = array(
            '&.+?;'                 => '',
            '[^\w\d _-]'            => '',
            '\s+'                   => $separator,
            '('.$quoteSeparator.')+'=> $separator
        );

        $string = strip_tags($string);
        foreach ($trans as $key => $val){
            $string = preg_replace('#'.$key.'#i', $val, $string);
        }

        $string = strtolower($string);

        return trim(trim($string, $separator));
    }
    
    public static function generateString($n = 15) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    public static function debug($array) {
        echo "<pre>".json_encode($array, JSON_PRETTY_PRINT)."</pre>";
    }

    public static function printStr($str) {
        echo "<pre>".$str."</pre>";
    }

    public static function elapsed( $ptime ) {
        $etime = time() - $ptime;

        if ( $etime < 1 ) {
            return '0 seconds - '.$etime;
        }

        $a = array(
            12 * 30 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60 => 'month',
            24 * 60 * 60 => 'day',
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ( $a as $secs => $str ) {
            $d = $etime / $secs;
            if ( $d >= 1 ) {
                $r = round( $d );
                return $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
            }
        }
    }

    public static function formatSeconds($secondsLeft) {
        $minuteInSeconds = 60;
        $hourInSeconds = $minuteInSeconds * 60;
        $dayInSeconds = $hourInSeconds * 24;
      
        $days = floor($secondsLeft / $dayInSeconds);
        $secondsLeft = $secondsLeft % $dayInSeconds;
      
        $hours = floor($secondsLeft / $hourInSeconds);
        $secondsLeft = $secondsLeft % $hourInSeconds;
      
        $minutes= floor($secondsLeft / $minuteInSeconds);
      
        $seconds = $secondsLeft % $minuteInSeconds;
      
        $timeComponents = array();
      
        if ($days > 0) {
          $timeComponents[] = $days . " day" . ($days > 1 ? "s" : "");
        }
      
        if ($hours > 0) {
          $timeComponents[] = $hours . " hour" . ($hours > 1 ? "s" : "");
        }
      
        if ($minutes > 0) {
          $timeComponents[] = $minutes . " minute" . ($minutes > 1 ? "s" : "");
        }
      
        if ($seconds > 0) {
          $timeComponents[] = $seconds . " second" . ($seconds > 1 ? "s" : "");
        }
      
        if (count($timeComponents) > 0) {
          $formattedTimeRemaining = implode(", ", $timeComponents);
          $formattedTimeRemaining = trim($formattedTimeRemaining);
        } else {
          $formattedTimeRemaining = "No time remaining.";
        }
      
        return $formattedTimeRemaining;
      
      }

    public static function getLastNDays($days, $format = 'n j'){
        $m  = date("m");
        $de = date("d");
        $y  = date("Y");

        $dateArray = [];

        for($i = 0; $i <= $days - 1; $i++){
            $dateArray[] = date($format, mktime(0,0,0,$m,($de-$i),$y));
        }

        return array_reverse($dateArray);
    }
}
?>