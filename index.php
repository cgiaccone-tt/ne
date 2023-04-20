<?php
include_once 'classes/CensorText.php';

/*
 * The request is for a demo of a censoring application that will take in quoted words and phrases that are
 * space or comma delimited and then censor them in a document that is pasted into a text area.
 * The censoring will be done by replacing the word or phrase with XXXX.
 * The censoring will be case insensitive.
 * I will keep it simple and not concern myself with creating a view and will just output the html inline
 * which I would not do in an application if I were creating it for production.
 */

//check if form has been submitted, if not, display the empty form,
//if so, display the form with the censored document results
if (isset($_POST) && !empty($_POST)) {
    $wordArr = CensorText::createWordListArray($_POST['censoredWordList']);
    //remove empty elements from array
    $wordArr = array_filter($wordArr, function($val){return !empty($val);});
    //remove whitespace from array elements
    $wordArr = array_map('trim', $wordArr);
    //create a string of words with quotes around them to display in the input field
    $wordList = implode(', ', array_map(function($val){return sprintf("'%s'", $val);}, $wordArr));
    //use htmlspecialchars to prevent XSS.  The output is in a text area so it is probably not necessary but it
    //is a good practice to use it.
    $docCensored = htmlspecialchars(CensorText::censorDocumentText($wordArr, $_POST['docToCensor']));

    //output form with censored document and input text
    $censoredHtml = <<<EOT
        <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>DOD</title>
                <link rel="stylesheet" href="css/main.css">
                <script type="text/javascript">
                    function validateForm(event) {
                        let censoredWordList = document.forms["censorApp"]["censoredWordList"].value;
                        let docToCensor = document.forms["censorApp"]["docToCensor"].value;
                        if (censoredWordList == "" || docToCensor == "") {
                            alert("Please enter a list of words to censor and a document to censor.");
                            return false;
                        }
						document.censorApp.submit();
                    }
                    //copy the censored document to the clipboard
                    function copyToClipboard() {
                        let copyText = document.getElementById("docCensored");
						copyText.disabled = false;
                        copyText.select();
                        copyText.setSelectionRange(0, 99999); /*For mobile devices*/
                        document.execCommand("copy");
						copyText.disabled = true;
                    }
					
					function resetApplication(){
						//redirect to index.php
                        window.location.href = "index.php";
					}
                </script>
            </head>
            <body>
                <h2>Department of Defense</h2>
                <div class="mainContainerClass">
                    <h3>Document Censor Application Demonstration</h3>
                    <p>Enter a list of words to censor encapsulated by single or double quotes and separated by commas or spaces. 
                    Then, copy and past the document to censor in the filed provided.</p>
                    <form name="censorApp" id="censorApp" action="index.php" method="post" onsubmit="event.preventDefault(); validateForm()">
                        <div class="docCensoredContainer">
                            <div>
                                <label for="censoredWordList" class="labelClass">Keywords</label>
                            </div>
                            <div>
                                <input type="text" name="censoredWordList" class="censoredWordList" value="{$wordList}">
                            </div>
                            <div>
                                <label for="docToCensor" class="labelClass">Document</label>
                            </div>
                            <div>
                                <textarea name="docToCensor" class="docToCensor">{$_POST['docToCensor']}</textarea>
                            </div>
                            <button type="submit" class="submitBtn">Submit</button>
                        </div>
                        <div class="docCensoredContainer docCensoredResult">
                            <div>
                                <label for="docCensored" class="labelClass">Censored Document</label>
                            </div>
                            <div>
                                <p>
                                    <button type="button" class="copyBtn" onclick="copyToClipboard()">Copy to clipboard</button>
                                    <button type="button" class="clearBtn" onclick="resetApplication()">Reset Application</button>
                                </p>
                                <textarea name="docCensored" class="docCensored" id="docCensored" disabled>{$docCensored}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </body>
        </html>
EOT;


    echo $censoredHtml;
} else {
    echo '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title>DOD</title>
                    <link rel="stylesheet" href="css/main.css">
                    <script type="text/javascript">
                    //basic form validation
                    function validateForm(event) {
                        let censoredWordList = document.forms["censorApp"]["censoredWordList"].value;
                        let docToCensor = document.forms["censorApp"]["docToCensor"].value;
                        if (censoredWordList == "" || docToCensor == "") {
                            alert("Please enter a list of words to censor and a document to censor.");
                            return false;
                        }
						document.censorApp.submit();
                    }
                    </script>
                </head>
                <body>
                    <h2>Department of Defense</h2>
                    <div class="mainContainerClass">
                        <h3>Document Censor Application Demonstration</h3>
                        <p>Enter a list of words to censor encapsulated by single or double quotes and separated by commas or spaces. 
                        Then, copy and past the document to censor in the filed provided.</p>
                        <form name="censorApp" id="censorApp" action="index.php" method="post" onsubmit="event.preventDefault(); validateForm()">
                            <div class="docCensoredContainer">
                                <div>
                                    <label for="censoredWordList" class="labelClass">Keywords</label>
                                </div>
                                <div>
                                    <input type="text" name="censoredWordList" class="censoredWordList" value="">
                                </div>
                                <div>
                                    <label for="docToCensor" class="labelClass">Document</label>
                                </div>
                                <div>
                                    <textarea name="docToCensor" class="docToCensor"></textarea>
                                </div>
                                <button type="submit" class="submitBtn">Submit</button>
                            </div>
                        </form>
                    </div>
                </body>
            </html>';
}