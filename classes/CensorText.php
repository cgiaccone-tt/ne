<?php

class CensorText
{

    /**
     * @param String $wordList
     * @return array
     */
    public static function createWordListArray(String $wordList): array
    {
        /*create array from space delimited string of words in various types of quotes
         * 1. replace all types of quotes with double quotes
         * 2. replace all multiple spaces with single space
         * 3. replace all double quotes with single spaces with a comma for explode using comma delimiter
         * 4. remove double quotes at beginning and end of string that I have ignored in step 3
         * 5. explode string into array
        */

        $wordList = str_replace(array("“", '‘',"'","’",'”'), '"', $wordList);
        $wordList = preg_replace('!\s+!', ' ', $wordList);
        $wordList = str_replace(array('" "', '","'), ',', $wordList);
        $wordList = str_replace('"', '', $wordList);
        $wordArr = explode(',', $wordList);
        return $wordArr;
    }

    /**
     * @param array $wordArr
     * @param String $docToCensor
     * @return String
     */
    public static function censorDocumentText(Array $wordArr, String $docToCensor): String
    {
        //Use case insensitive search and replace to return XXXX for each word in the array
        $docToCensor = str_ireplace($wordArr, 'XXXX', $docToCensor);
        return $docToCensor;
    }

}