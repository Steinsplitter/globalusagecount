<?php
/**
 * @author Steinsplitter / https://commons.wikimedia.org/wiki/User:Steinsplitter
 * @copyright 2016 GlobalUsageCount authors
 * @license http://unlicense.org/ Unlicense
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8">
        <title>GlobalUsageCount</title>
        <link rel="stylesheet" href="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap.min.css">
        <style>
        body {
                padding-top: 60px;
        }
        </style>
</head>
<body>
        <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
        <div class="container">
<?php
function i18nheader($hname) {
echo "          <a class=\"brand\" href=\"#\">$hname</a>
                <div class=\"nav-collapse collapse\">
                        <ul id=\"toolbar-right\" class=\"nav pull-right\">
                        </ul>
                </div>
        </div>
        </div>
        </div>
        <div class=\"container\"> ";
}

function i18nparser($url) {
        $con = curl_init();
        $to = 4;
        curl_setopt($con, CURLOPT_URL, $url);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($con, CURLOPT_CONNECTTIMEOUT, $to);
        curl_setopt($con,CURLOPT_USERAGENT,'GlobalUsageCount interface parser, running on toollabs / tools.wmflabs.org/globalusagecount');
        $data = curl_exec($con);
        curl_close($con);
        return $data;
}
$getd2 = $_GET['lang'];
if(preg_match("/^[a-z]{1,4}(-[a-z]{1,4}|)+$/",$getd2)) {
     $lang = htmlspecialchars($getd2);
} else {
     $lang = "en";
}
$i18n = i18nparser('https://commons.wikimedia.org/w/index.php?title=Commons:User_scripts/GlobalUsageCount_i18n/'. $lang . '&action=raw&ctype=text/javascript');
if (strpos($i18n, 'a') === false)
{
  i18nheader("GlobalUsageCount");
  echo "Ooop :-(. No interface translation aviable for ". $lang .". <a href=\"https://commons.wikimedia.org/w/index.php?title=Special:Translate&group=page-Commons%3AUser+scripts%2FGlobalUsageCount+i18n&language=". $lang ."&action=page&filter=\">Please help with the translation!</a>";
}
else
{
  $i18nhe = preg_replace("/(.+\n*<!--header:|-->(.+[.\n]*)*)/", "", $i18n);
  i18nheader(htmlspecialchars($i18nhe));
  $i18ntr = preg_replace("/\<noinclude\>.+\n*\<\/noinclude\>/", "", $i18n);
  $esc= htmlspecialchars($i18ntr);
  echo preg_replace("/\n/", "<br>", $esc);
}
echo "<br><br>\n        <form class=\"input-prepend input-append\">";
echo "<span class=\"add-on\">File:</span>";
echo "                <input type=\"text\" value=\"" . $lang . "\" name=\"lang\" id=\"lang\" class=\"hidden\" type=\"hidden\" style = \"display:none; visibility:hidden;\" />";
?>
                <input type="text" value="" name="file" id="fast" class="input-medium appendedPrependedInput appendedInputButton"/>
                <button type="submit" class="btn">&#128269;</button>
        </form>
        <br/>
<?php
$getd = $_GET['file'];
if (isset($getd)) {
        $tools_pw = posix_getpwuid(posix_getuid());
        $tools_mycnf = parse_ini_file($tools_pw['dir'] . "/replica.my.cnf");
        $db = new mysqli('commonswiki.labsdb', $tools_mycnf['user'], $tools_mycnf['password'],
                'commonswiki_p');
        if ($db->connect_errno)
                die("Failed to connect to the database: (" . $db->connect_errno . ") " . $db->connect_error);
        $r = $db->query('SELECT COUNT(gil_page) AS count FROM globalimagelinks WHERE gil_to  = "' . str_replace(" ", "_", $db->real_escape_string($getd)) . '" LIMIT 1;');
        unset($tools_mycnf, $tools_pw);
}
        echo "<p><big>". htmlspecialchars($getd) ."</big></p>";
$row = $r->fetch_assoc();

echo "<div style = \"text-align:center; font-size: 150px; padding: 0.5em; border-color:Black; border-style:solid; border-width:1pt\">" . $row['count'] ."</div>";

        $r->close();
        $db->close();
?>
</div>
</div>
</body>
</html>
