<?php
/**
 * @param $url
 * @return mixed (html)
 */
function getPage($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
    $data_fin = curl_exec($ch);
    curl_close($ch);
    return $data_fin;
}

/**
 * @param $aString
 * @return int (count chars of string)
 */
function getLengthString($aString)
{
    return mb_strlen($aString, 'utf8');
}

/**
 * @param $aPage
 * @return bool is some block in page
 */
function isBlockInPage($aPage) {
    if (preg_match_all('#<div class="text seo-text">(.+?)</div>#is', $aPage))
        return true;
    return false;
}

/**
 * @param $aPage
 * @return mixed (finding html)
 */
function getPieceOfHtml($aPage) {
    preg_match_all('#<div class="text seo-text">(.+?)</div>#is', $aPage, $lPieceHtml);
    return $lPieceHtml;
}

/**
 * @param $aPage (html)
 * @return int count of chars in html
 */
function getCountChars($aPage)
{
    $lPieceHtml = getPieceOfHtml($aPage);

    $lString = '';
    if (isset($lPieceHtml[0][0]))
        $lString = $lPieceHtml[0][0];

    $lString = strip_tags($lString);// remove tag
    // remove all spaces tabs ...
    $lString = preg_replace('/[\s]+/', '', $lString);
    $lString = html_entity_decode($lString);// decode special chars

    $lCountChars = getLengthString($lString);

    return $lCountChars;
}


if (isset($_POST['url']) and !empty($_POST['url'])
) {
    $lUrl = trim($_POST['url']);
    $lUrl = strip_tags($lUrl);

    $regex = '/https?\:\/\/[^\" ]+/i';
    preg_match_all($regex, $lUrl, $lUrlArray);

    if (count($lUrlArray[0]) > 0) {
        $lData = [];
        $lCurrentTime = time();
        foreach ($lUrlArray[0] as $url) {
            $url = trim($url);
            $url = strip_tags($url);
            $lPage = getPage($url);

            if (isBlockInPage($lPage)) {
                $lCnt = getCountChars($lPage);
                $lData[] = [
                    'link' => $url,
                    'cnt' => $lCnt,
                    'created_at' => $lCurrentTime,
                ];
            }
        }
        if (count($lData) > 0) {
            require_once('model_parser.php'); // connect to BD
            $i = 0;
            $errors = [];
            foreach ($lData as $value) {
                $query = " INSERT INTO seo_texts (link,cnt,created_at) values (".
                    "'".mysql_real_escape_string($value['link'])."',".
                    "'".mysql_real_escape_string($value['cnt'])."',".
                    "'".mysql_real_escape_string($value['created_at'])."'".
                    ")";

                begin(); // transaction begins
                $result = mysql_query($query);

                if(!$result){
                    $i++;
                    $errors[] = $i;
                    rollback(); // transaction rolls back
                    continue;
                }else{
                    commit(); // transaction is committed
                }
            }
        }
        echo "<div class=\"alert alert-success\">".
            "<h1>Operattion Success!</h1></div>";

        if (count($errors) > 0) {
            echo "<div class=\"alert alert-warning\">".
                "<h1>There are error in stage ".implode(', ', $errors)."</h1></div>";
        }

    } else {
        echo "<div class=\"alert alert-danger\">".
            "<h1>enter link!</h1></div>";
    }
}