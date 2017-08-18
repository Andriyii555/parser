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

if (isset($_POST['url']) and !empty($_POST['url'])
) {
    $lUrl = trim($_POST['url']);
    $lUrl = strip_tags($lUrl);

    $lPage = getPage($lUrl);

    if (isBlockInPage($lPage)) {

        $lPieceHtml = getPieceOfHtml($lPage);

        if (isset($lPieceHtml[0][0]))
            $lString = $lPieceHtml[0][0];

        $lString = strip_tags($lString);// remove tag
        // remove all spaces tabs ...
        $lString = preg_replace('/[\s]+/', '', $lString);
        $lString = html_entity_decode($lString);// decode special chars

        $lCountChars = getLengthString($lString);

        if ($lCountChars > 0) {
            echo "<div class=\"alert alert-success\">".
                "<h1>Так</h1>".
                "<p>Count of chars equals ". $lCountChars
                ."</p>".
                "</div>";
        } else {
            echo "<div class=\"alert alert-warning\">".
                "<h1>Ні</h1>".
                "<p>There arent any text in the block with ".
                "class 'text seo-text' :( <br> <b>Please, enter new link!</b></p>".
                "</div>";
        }

    } else {
        echo "<div class=\"alert alert-danger\">".
            "<h1>Ні</h1>".
            "<p>There arent any block with ".
            "class 'text seo-text' :( <br> <b>Please, enter new link!</b></p>".
            "</div>";
    }
}
