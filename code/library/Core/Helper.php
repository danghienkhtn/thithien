<?php
/**
 * @author      :   Hiennd
 * @name        :   Core_Helper
 * @version     :   201612
 * @copyright   :   Dahi
 * @todo        :   Core Helper object
 */
class Core_Helper
{
    /**
     * Replace emoji
     * @param <string> $sHex
     * @return <array>
     */
    public static function replaceEmoji($sContent)
    {
        //Set unicode emoji
        $arrEmoji = array(
            "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");

        //Set list image replace
        $arrImages = array(
            '<span class="emoji emoji-e021"></span>',
            '<span class="emoji emoji-e415"></span>',
            '<span class="emoji emoji-e056"></span>',
            '<span class="emoji emoji-e057"></span>',
            '<span class="emoji emoji-e414"></span>',
            '<span class="emoji emoji-e405"></span>',
            '<span class="emoji emoji-e106"></span>',
            '<span class="emoji emoji-e418"></span>',
            '<span class="emoji emoji-e417"></span>',
            '<span class="emoji emoji-e40d"></span>',

            '<span class="emoji emoji-e40a"></span>',
            '<span class="emoji emoji-e404"></span>',
            '<span class="emoji emoji-e105"></span>',
            '<span class="emoji emoji-e409"></span>',
            '<span class="emoji emoji-e40e"></span>',
            '<span class="emoji emoji-e402"></span>',
            '<span class="emoji emoji-e108"></span>',
            '<span class="emoji emoji-e403"></span>',
            '<span class="emoji emoji-e058"></span>',
            '<span class="emoji emoji-e407"></span>',

            '<span class="emoji emoji-e401"></span>',
            '<span class="emoji emoji-e40f"></span>',
            '<span class="emoji emoji-e40b"></span>',
            '<span class="emoji emoji-e406"></span>',
            '<span class="emoji emoji-e413"></span>',
            '<span class="emoji emoji-e411"></span>',
            '<span class="emoji emoji-e412"></span>',
            '<span class="emoji emoji-e410"></span>',
            '<span class="emoji emoji-e107"></span>',
            '<span class="emoji emoji-e059"></span>',

            '<span class="emoji emoji-e416"></span>',
            '<span class="emoji emoji-e408"></span>',
            '<span class="emoji emoji-e40c"></span>',
            '<span class="emoji emoji-e11a"></span>',
            '<span class="emoji emoji-e10c"></span>',
            '<span class="emoji emoji-e32c"></span>',
            '<span class="emoji emoji-e32a"></span>',
            '<span class="emoji emoji-e32d"></span>',
            '<span class="emoji emoji-e328"></span>',
            '<span class="emoji emoji-e32b"></span>',

            '<span class="emoji emoji-e022"></span>',
            '<span class="emoji emoji-e023"></span>',
            '<span class="emoji emoji-e327"></span>',
            '<span class="emoji emoji-e329"></span>',
            '<span class="emoji emoji-e32e"></span>',
            '<span class="emoji emoji-e335"></span>',
            '<span class="emoji emoji-e334"></span>',
            '<span class="emoji emoji-e337"></span>',
            '<span class="emoji emoji-e336"></span>',
            '<span class="emoji emoji-e13c"></span>',

            '<span class="emoji emoji-e330"></span>',
            '<span class="emoji emoji-e331"></span>',
            '<span class="emoji emoji-e326"></span>',
            '<span class="emoji emoji-e03e"></span>',
            '<span class="emoji emoji-e11d"></span>',
            '<span class="emoji emoji-e05a"></span>',
            '<span class="emoji emoji-e00e"></span>',
            '<span class="emoji emoji-e421"></span>',
            '<span class="emoji emoji-e420"></span>',
            '<span class="emoji emoji-e00d"></span>',

            '<span class="emoji emoji-e010"></span>',
            '<span class="emoji emoji-e011"></span>',
            '<span class="emoji emoji-e41e"></span>',
            '<span class="emoji emoji-e012"></span>',
            '<span class="emoji emoji-e422"></span>',
            '<span class="emoji emoji-e22e"></span>',
            '<span class="emoji emoji-e22f"></span>',
            '<span class="emoji emoji-e231"></span>',
            '<span class="emoji emoji-e230"></span>',
            '<span class="emoji emoji-e427"></span>',

            '<span class="emoji emoji-e41d"></span>',
            '<span class="emoji emoji-e00f"></span>',
            '<span class="emoji emoji-e41f"></span>',
            '<span class="emoji emoji-e14c"></span>',
            '<span class="emoji emoji-e201"></span>',
            '<span class="emoji emoji-e115"></span>',
            '<span class="emoji emoji-e428"></span>',
            '<span class="emoji emoji-e51f"></span>',
            '<span class="emoji emoji-e429"></span>',
            '<span class="emoji emoji-e424"></span>',

            '<span class="emoji emoji-e423"></span>',
            '<span class="emoji emoji-e253"></span>',
            '<span class="emoji emoji-e426"></span>',
            '<span class="emoji emoji-e111"></span>',
            '<span class="emoji emoji-e425"></span>',
            '<span class="emoji emoji-e31e"></span>',
            '<span class="emoji emoji-e31f"></span>',
            '<span class="emoji emoji-e31d"></span>',
            '<span class="emoji emoji-e001"></span>',
            '<span class="emoji emoji-e002"></span>',

            '<span class="emoji emoji-e005"></span>',
            '<span class="emoji emoji-e004"></span>',
            '<span class="emoji emoji-e51a"></span>',
            '<span class="emoji emoji-e519"></span>',
            '<span class="emoji emoji-e518"></span>',
            '<span class="emoji emoji-e515"></span>',
            '<span class="emoji emoji-e516"></span>',
            '<span class="emoji emoji-e517"></span>',
            '<span class="emoji emoji-e51b"></span>',
            '<span class="emoji emoji-e152"></span>',

            '<span class="emoji emoji-e04e"></span>',
            '<span class="emoji emoji-e51c"></span>',
            '<span class="emoji emoji-e51e"></span>',
            '<span class="emoji emoji-e11c"></span>',
            '<span class="emoji emoji-e536"></span>',
            '<span class="emoji emoji-e003"></span>',
            '<span class="emoji emoji-e41c"></span>',
            '<span class="emoji emoji-e41b"></span>',
            '<span class="emoji emoji-e419"></span>',
            '<span class="emoji emoji-e41a"></span>',

            '<span class="emoji emoji-e04a"></span>',
            '<span class="emoji emoji-e04b"></span>',
            '<span class="emoji emoji-e049"></span>',
            '<span class="emoji emoji-e048"></span>',
            '<span class="emoji emoji-e04c"></span>',
            '<span class="emoji emoji-e13d"></span>',
            '<span class="emoji emoji-e443"></span>',
            '<span class="emoji emoji-e43e"></span>',
            '<span class="emoji emoji-e04f"></span>',
            '<span class="emoji emoji-e052"></span>',

            '<span class="emoji emoji-e053"></span>',
            '<span class="emoji emoji-e524"></span>',
            '<span class="emoji emoji-e52c"></span>',
            '<span class="emoji emoji-e52a"></span>',
            '<span class="emoji emoji-e531"></span>',
            '<span class="emoji emoji-e050"></span>',
            '<span class="emoji emoji-e527"></span>',
            '<span class="emoji emoji-e051"></span>',
            '<span class="emoji emoji-e10b"></span>',
            '<span class="emoji emoji-e52b"></span>',

            '<span class="emoji emoji-e52f"></span>',
            '<span class="emoji emoji-e109"></span>',
            '<span class="emoji emoji-e528"></span>',
            '<span class="emoji emoji-e01a"></span>',
            '<span class="emoji emoji-e134"></span>',
            '<span class="emoji emoji-e530"></span>',
            '<span class="emoji emoji-e529"></span>',
            '<span class="emoji emoji-e526"></span>',
            '<span class="emoji emoji-e52d"></span>',
            '<span class="emoji emoji-e521"></span>',

            '<span class="emoji emoji-e523"></span>',
            '<span class="emoji emoji-e52e"></span>',
            '<span class="emoji emoji-e055"></span>',
            '<span class="emoji emoji-e525"></span>',
            '<span class="emoji emoji-e10a"></span>',
            '<span class="emoji emoji-e522"></span>',
            '<span class="emoji emoji-e019"></span>',
            '<span class="emoji emoji-e054"></span>',
            '<span class="emoji emoji-e520"></span>',
            '<span class="emoji emoji-e306"></span>',

            '<span class="emoji emoji-e030"></span>',
            '<span class="emoji emoji-e304"></span>',
            '<span class="emoji emoji-e110"></span>',
            '<span class="emoji emoji-e032"></span>',
            '<span class="emoji emoji-e305"></span>',
            '<span class="emoji emoji-e303"></span>',
            '<span class="emoji emoji-e118"></span>',
            '<span class="emoji emoji-e447"></span>',
            '<span class="emoji emoji-e119"></span>',
            '<span class="emoji emoji-e307"></span>',

            '<span class="emoji emoji-e308"></span>',
            '<span class="emoji emoji-e444"></span>',
            '<span class="emoji emoji-e441"></span>',
            '<span class="emoji emoji-e436"></span>',
            '<span class="emoji emoji-e437"></span>',
            '<span class="emoji emoji-e438"></span>',
            '<span class="emoji emoji-e43a"></span>',
            '<span class="emoji emoji-e439"></span>',
            '<span class="emoji emoji-e43b"></span>',
            '<span class="emoji emoji-e117"></span>',

            '<span class="emoji emoji-e440"></span>',
            '<span class="emoji emoji-e442"></span>',
            '<span class="emoji emoji-e446"></span>',
            '<span class="emoji emoji-e445"></span>',
            '<span class="emoji emoji-e11b"></span>',
            '<span class="emoji emoji-e448"></span>',
            '<span class="emoji emoji-e033"></span>',
            '<span class="emoji emoji-e112"></span>',
            '<span class="emoji emoji-e325"></span>',
            '<span class="emoji emoji-e312"></span>',

            '<span class="emoji emoji-e310"></span>',
            '<span class="emoji emoji-e126"></span>',
            '<span class="emoji emoji-e127"></span>',
            '<span class="emoji emoji-e008"></span>',
            '<span class="emoji emoji-e03d"></span>',
            '<span class="emoji emoji-e00c"></span>',
            '<span class="emoji emoji-e12a"></span>',
            '<span class="emoji emoji-e00a"></span>',
            '<span class="emoji emoji-e00b"></span>',
            '<span class="emoji emoji-e009"></span>',

            '<span class="emoji emoji-e316"></span>',
            '<span class="emoji emoji-e129"></span>',
            '<span class="emoji emoji-e141"></span>',
            '<span class="emoji emoji-e142"></span>',
            '<span class="emoji emoji-e317"></span>',
            '<span class="emoji emoji-e128"></span>',
            '<span class="emoji emoji-e14b"></span>',
            '<span class="emoji emoji-e211"></span>',
            '<span class="emoji emoji-e114"></span>',
            '<span class="emoji emoji-e145"></span>',

            '<span class="emoji emoji-e144"></span>', '<span class="emoji emoji-e03f"></span>', '<span class="emoji emoji-e313"></span>', '<span class="emoji emoji-e116"></span>', '<span class="emoji emoji-e10f"></span>', '<span class="emoji emoji-e104"></span>', '<span class="emoji emoji-e103"></span>', '<span class="emoji emoji-e101"></span>', '<span class="emoji emoji-e102"></span>', '<span class="emoji emoji-e13f"></span>', '<span class="emoji emoji-e140"></span>', '<span class="emoji emoji-e11f"></span>', '<span class="emoji emoji-e12f"></span>', '<span class="emoji emoji-e031"></span>', '<span class="emoji emoji-e30e"></span>', '<span class="emoji emoji-e311"></span>', '<span class="emoji emoji-e113"></span>', '<span class="emoji emoji-e30f"></span>', '<span class="emoji emoji-e13b"></span>', '<span class="emoji emoji-e42b"></span>', '<span class="emoji emoji-e42a"></span>', '<span class="emoji emoji-e018"></span>', '<span class="emoji emoji-e016"></span>', '<span class="emoji emoji-e015"></span>', '<span class="emoji emoji-e014"></span>', '<span class="emoji emoji-e42c"></span>', '<span class="emoji emoji-e42d"></span>', '<span class="emoji emoji-e017"></span>', '<span class="emoji emoji-e013"></span>', '<span class="emoji emoji-e20e"></span>', '<span class="emoji emoji-e20c"></span>', '<span class="emoji emoji-e20f"></span>', '<span class="emoji emoji-e20d"></span>', '<span class="emoji emoji-e131"></span>', '<span class="emoji emoji-e12b"></span>', '<span class="emoji emoji-e130"></span>', '<span class="emoji emoji-e12d"></span>', '<span class="emoji emoji-e324"></span>', '<span class="emoji emoji-e301"></span>', '<span class="emoji emoji-e148"></span>', '<span class="emoji emoji-e502"></span>', '<span class="emoji emoji-e03c"></span>', '<span class="emoji emoji-e30a"></span>', '<span class="emoji emoji-e042"></span>', '<span class="emoji emoji-e040"></span>', '<span class="emoji emoji-e041"></span>', '<span class="emoji emoji-e12c"></span>', '<span class="emoji emoji-e007"></span>', '<span class="emoji emoji-e31a"></span>', '<span class="emoji emoji-e13e"></span>', '<span class="emoji emoji-e31b"></span>', '<span class="emoji emoji-e006"></span>', '<span class="emoji emoji-e302"></span>', '<span class="emoji emoji-e319"></span>', '<span class="emoji emoji-e321"></span>', '<span class="emoji emoji-e322"></span>', '<span class="emoji emoji-e314"></span>', '<span class="emoji emoji-e503"></span>', '<span class="emoji emoji-e10e"></span>', '<span class="emoji emoji-e318"></span>', '<span class="emoji emoji-e43c"></span>', '<span class="emoji emoji-e11e"></span>', '<span class="emoji emoji-e323"></span>', '<span class="emoji emoji-e31c"></span>', '<span class="emoji emoji-e034"></span>', '<span class="emoji emoji-e035"></span>', '<span class="emoji emoji-e045"></span>', '<span class="emoji emoji-e338"></span>', '<span class="emoji emoji-e047"></span>', '<span class="emoji emoji-e30c"></span>', '<span class="emoji emoji-e044"></span>', '<span class="emoji emoji-e30b"></span>', '<span class="emoji emoji-e043"></span>', '<span class="emoji emoji-e120"></span>', '<span class="emoji emoji-e33b"></span>', '<span class="emoji emoji-e33f"></span>', '<span class="emoji emoji-e341"></span>', '<span class="emoji emoji-e34c"></span>', '<span class="emoji emoji-e344"></span>', '<span class="emoji emoji-e342"></span>', '<span class="emoji emoji-e33d"></span>', '<span class="emoji emoji-e33e"></span>', '<span class="emoji emoji-e340"></span>', '<span class="emoji emoji-e34d"></span>', '<span class="emoji emoji-e339"></span>', '<span class="emoji emoji-e147"></span>', '<span class="emoji emoji-e343"></span>', '<span class="emoji emoji-e33c"></span>', '<span class="emoji emoji-e33a"></span>', '<span class="emoji emoji-e43f"></span>', '<span class="emoji emoji-e34b"></span>', '<span class="emoji emoji-e046"></span>', '<span class="emoji emoji-e345"></span>', '<span class="emoji emoji-e346"></span>', '<span class="emoji emoji-e348"></span>', '<span class="emoji emoji-e347"></span>', '<span class="emoji emoji-e34a"></span>', '<span class="emoji emoji-e349"></span>', '<span class="emoji emoji-e036"></span>', '<span class="emoji emoji-e157"></span>', '<span class="emoji emoji-e038"></span>', '<span class="emoji emoji-e153"></span>', '<span class="emoji emoji-e155"></span>', '<span class="emoji emoji-e14d"></span>', '<span class="emoji emoji-e156"></span>', '<span class="emoji emoji-e501"></span>', '<span class="emoji emoji-e158"></span>', '<span class="emoji emoji-e43d"></span>', '<span class="emoji emoji-e037"></span>', '<span class="emoji emoji-e504"></span>', '<span class="emoji emoji-e44a"></span>', '<span class="emoji emoji-e146"></span>', '<span class="emoji emoji-e50a"></span>', '<span class="emoji emoji-e505"></span>', '<span class="emoji emoji-e506"></span>', '<span class="emoji emoji-e122"></span>', '<span class="emoji emoji-e508"></span>', '<span class="emoji emoji-e509"></span>', '<span class="emoji emoji-e03b"></span>', '<span class="emoji emoji-e04d"></span>', '<span class="emoji emoji-e449"></span>', '<span class="emoji emoji-e44b"></span>', '<span class="emoji emoji-e51d"></span>', '<span class="emoji emoji-e44c"></span>', '<span class="emoji emoji-e124"></span>', '<span class="emoji emoji-e121"></span>', '<span class="emoji emoji-e433"></span>', '<span class="emoji emoji-e202"></span>', '<span class="emoji emoji-e135"></span>', '<span class="emoji emoji-e01c"></span>', '<span class="emoji emoji-e01d"></span>', '<span class="emoji emoji-e10d"></span>', '<span class="emoji emoji-e136"></span>', '<span class="emoji emoji-e42e"></span>', '<span class="emoji emoji-e01b"></span>', '<span class="emoji emoji-e15a"></span>', '<span class="emoji emoji-e159"></span>', '<span class="emoji emoji-e432"></span>', '<span class="emoji emoji-e430"></span>', '<span class="emoji emoji-e431"></span>', '<span class="emoji emoji-e42f"></span>', '<span class="emoji emoji-e01e"></span>', '<span class="emoji emoji-e039"></span>', '<span class="emoji emoji-e435"></span>', '<span class="emoji emoji-e01f"></span>', '<span class="emoji emoji-e125"></span>', '<span class="emoji emoji-e03a"></span>', '<span class="emoji emoji-e14e"></span>', '<span class="emoji emoji-e252"></span>', '<span class="emoji emoji-e137"></span>', '<span class="emoji emoji-e209"></span>', '<span class="emoji emoji-e154"></span>', '<span class="emoji emoji-e133"></span>', '<span class="emoji emoji-e150"></span>', '<span class="emoji emoji-e320"></span>', '<span class="emoji emoji-e123"></span>', '<span class="emoji emoji-e132"></span>', '<span class="emoji emoji-e143"></span>', '<span class="emoji emoji-e50b"></span>', '<span class="emoji emoji-e514"></span>', '<span class="emoji emoji-e513"></span>', '<span class="emoji emoji-e50c"></span>', '<span class="emoji emoji-e50d"></span>', '<span class="emoji emoji-e511"></span>', '<span class="emoji emoji-e50f"></span>', '<span class="emoji emoji-e512"></span>', '<span class="emoji emoji-e510"></span>', '<span class="emoji emoji-e50e"></span>', '<span class="emoji emoji-e21c"></span>', '<span class="emoji emoji-e21d"></span>', '<span class="emoji emoji-e21e"></span>', '<span class="emoji emoji-e21f"></span>', '<span class="emoji emoji-e220"></span>', '<span class="emoji emoji-e221"></span>', '<span class="emoji emoji-e222"></span>', '<span class="emoji emoji-e223"></span>', '<span class="emoji emoji-e224"></span>', '<span class="emoji emoji-e225"></span>', '<span class="emoji emoji-e210"></span>', '<span class="emoji emoji-e232"></span>', '<span class="emoji emoji-e233"></span>', '<span class="emoji emoji-e235"></span>', '<span class="emoji emoji-e234"></span>', '<span class="emoji emoji-e236"></span>', '<span class="emoji emoji-e237"></span>', '<span class="emoji emoji-e238"></span>', '<span class="emoji emoji-e239"></span>', '<span class="emoji emoji-e23b"></span>', '<span class="emoji emoji-e23a"></span>', '<span class="emoji emoji-e23d"></span>', '<span class="emoji emoji-e23c"></span>', '<span class="emoji emoji-e24d"></span>', '<span class="emoji emoji-e212"></span>', '<span class="emoji emoji-e24c"></span>', '<span class="emoji emoji-e213"></span>', '<span class="emoji emoji-e214"></span>', '<span class="emoji emoji-e507"></span>', '<span class="emoji emoji-e203"></span>', '<span class="emoji emoji-e20b"></span>', '<span class="emoji emoji-e22a"></span>', '<span class="emoji emoji-e22b"></span>', '<span class="emoji emoji-e226"></span>', '<span class="emoji emoji-e227"></span>', '<span class="emoji emoji-e22c"></span>', '<span class="emoji emoji-e22d"></span>', '<span class="emoji emoji-e215"></span>', '<span class="emoji emoji-e216"></span>', '<span class="emoji emoji-e217"></span>', '<span class="emoji emoji-e218"></span>', '<span class="emoji emoji-e228"></span>', '<span class="emoji emoji-e151"></span>', '<span class="emoji emoji-e138"></span>', '<span class="emoji emoji-e139"></span>', '<span class="emoji emoji-e13a"></span>', '<span class="emoji emoji-e208"></span>', '<span class="emoji emoji-e14f"></span>', '<span class="emoji emoji-e20a"></span>', '<span class="emoji emoji-e434"></span>', '<span class="emoji emoji-e309"></span>', '<span class="emoji emoji-e315"></span>', '<span class="emoji emoji-e30d"></span>', '<span class="emoji emoji-e207"></span>', '<span class="emoji emoji-e229"></span>', '<span class="emoji emoji-e206"></span>', '<span class="emoji emoji-e205"></span>', '<span class="emoji emoji-e204"></span>', '<span class="emoji emoji-e12e"></span>', '<span class="emoji emoji-e250"></span>', '<span class="emoji emoji-e251"></span>', '<span class="emoji emoji-e14a"></span>', '<span class="emoji emoji-e149"></span>', '<span class="emoji emoji-e23f"></span>', '<span class="emoji emoji-e240"></span>', '<span class="emoji emoji-e241"></span>', '<span class="emoji emoji-e242"></span>', '<span class="emoji emoji-e243"></span>', '<span class="emoji emoji-e244"></span>', '<span class="emoji emoji-e245"></span>', '<span class="emoji emoji-e246"></span>', '<span class="emoji emoji-e247"></span>', '<span class="emoji emoji-e248"></span>', '<span class="emoji emoji-e249"></span>', '<span class="emoji emoji-e24a"></span>', '<span class="emoji emoji-e24b"></span>', '<span class="emoji emoji-e23e"></span>', '<span class="emoji emoji-e532"></span>', '<span class="emoji emoji-e533"></span>', '<span class="emoji emoji-e534"></span>', '<span class="emoji emoji-e535"></span>', '<span class="emoji emoji-e21a"></span>', '<span class="emoji emoji-e219"></span>', '<span class="emoji emoji-e21b"></span>', '<span class="emoji emoji-e02f"></span>', '<span class="emoji emoji-e024"></span>', '<span class="emoji emoji-e025"></span>', '<span class="emoji emoji-e026"></span>', '<span class="emoji emoji-e027"></span>', '<span class="emoji emoji-e028"></span>', '<span class="emoji emoji-e029"></span>', '<span class="emoji emoji-e02a"></span>', '<span class="emoji emoji-e02b"></span>', '<span class="emoji emoji-e02c"></span>', '<span class="emoji emoji-e02d"></span>', '<span class="emoji emoji-e02e"></span>', '<span class="emoji emoji-e332"></span>', '<span class="emoji emoji-e333"></span>');

        //Return data
        return str_replace($arrEmoji, $arrImages, $sContent);
    }

    /**
     * Remove emoji
     * @param <string> $sHex
     * @return <array>
     */
    public static function removeEmoji($sContent)
    {
        //Set unicode emoji
        $arrEmoji = array("", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");

        //Return data
        return str_replace($arrEmoji, '', $sContent);
    }

    /**
     * Build emoji HTML
     * @return <array>
     */
    public static function buildEmojiHtml()
    {
        //Set unicode emoji
        $arrEmoji = array(
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",
            "", "", "", "", "", "", "", "", "", "",

            "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "");

        //Set list image replace
        $arrImages = array(
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e021"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e415"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e056"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e057"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e414"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e405"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e106"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e418"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e417"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e40d"></span>',

            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e40a"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e404"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e105"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e409"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e40e"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e402"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e108"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e403"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e058"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e407"></span>',

            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e401"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e40f"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e40b"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e406"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e413"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e411"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e412"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e410"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e107"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e059"></span>',

            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e416"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e408"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e40c"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e11a"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e10c"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e32c"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e32a"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e32d"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e328"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e32b"></span>',

            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e022"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e023"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e327"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e329"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e32e"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e335"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e334"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e337"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e336"></span>',
            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e13c"></span>',

            '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e330"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e331"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e326"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e03e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e11d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e05a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e00e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e421"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e420"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e00d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e010"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e011"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e41e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e012"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e422"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e22e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e22f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e231"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e230"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e427"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e41d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e00f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e41f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e14c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e201"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e115"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e428"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e51f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e429"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e424"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e423"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e253"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e426"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e111"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e425"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e31e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e31f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e31d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e001"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e002"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e005"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e004"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e51a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e519"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e518"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e515"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e516"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e517"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e51b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e152"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e04e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e51c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e51e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e11c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e536"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e003"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e41c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e41b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e419"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e41a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e04a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e04b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e049"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e048"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e04c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e13d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e443"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e43e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e04f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e052"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e053"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e524"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e52c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e52a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e531"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e050"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e527"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e051"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e10b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e52b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e52f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e109"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e528"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e01a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e134"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e530"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e529"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e526"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e52d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e521"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e523"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e52e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e055"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e525"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e10a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e522"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e019"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e054"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e520"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e306"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e030"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e304"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e110"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e032"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e305"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e303"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e118"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e447"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e119"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e307"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e308"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e444"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e441"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e436"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e437"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e438"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e43a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e439"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e43b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e117"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e440"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e442"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e446"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e445"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e11b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e448"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e033"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e112"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e325"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e312"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e310"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e126"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e127"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e008"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e03d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e00c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e12a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e00a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e00b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e009"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e316"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e129"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e141"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e142"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e317"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e128"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e14b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e211"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e114"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e145"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e144"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e03f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e313"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e116"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e10f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e104"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e103"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e101"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e102"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e13f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e140"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e11f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e12f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e031"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e30e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e311"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e113"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e30f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e13b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e42b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e42a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e018"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e016"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e015"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e014"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e42c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e42d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e017"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e013"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e20e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e20c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e20f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e20d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e131"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e12b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e130"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e12d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e324"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e301"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e148"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e502"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e03c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e30a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e042"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e040"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e041"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e12c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e007"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e31a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e13e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e31b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e006"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e302"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e319"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e321"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e322"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e314"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e503"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e10e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e318"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e43c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e11e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e323"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e31c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e034"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e035"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e045"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e338"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e047"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e30c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e044"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e30b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e043"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e120"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e33b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e33f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e341"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e34c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e344"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e342"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e33d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e33e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e340"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e34d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e339"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e147"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e343"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e33c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e33a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e43f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e34b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e046"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e345"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e346"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e348"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e347"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e34a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e349"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e036"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e157"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e038"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e153"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e155"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e14d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e156"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e501"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e158"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e43d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e037"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e504"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e44a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e146"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e50a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e505"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e506"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e122"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e508"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e509"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e03b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e04d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e449"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e44b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e51d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e44c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e124"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e121"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e433"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e202"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e135"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e01c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e01d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e10d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e136"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e42e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e01b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e15a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e159"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e432"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e430"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e431"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e42f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e01e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e039"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e435"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e01f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e125"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e03a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e14e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e252"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e137"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e209"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e154"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e133"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e150"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e320"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e123"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e132"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e143"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e50b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e514"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e513"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e50c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e50d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e511"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e50f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e512"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e510"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e50e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e21c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e21d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e21e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e21f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e220"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e221"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e222"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e223"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e224"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e225"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e210"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e232"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e233"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e235"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e234"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e236"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e237"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e238"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e239"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e23b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e23a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e23d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e23c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e24d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e212"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e24c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e213"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e214"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e507"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e203"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e20b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e22a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e22b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e226"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e227"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e22c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e22d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e215"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e216"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e217"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e218"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e228"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e151"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e138"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e139"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e13a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e208"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e14f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e20a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e434"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e309"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e315"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e30d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e207"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e229"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e206"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e205"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e204"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e12e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e250"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e251"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e14a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e149"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e23f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e240"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e241"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e242"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e243"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e244"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e245"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e246"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e247"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e248"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e249"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e24a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e24b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e23e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e532"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e533"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e534"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e535"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e21a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e219"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e21b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e02f"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e024"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e025"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e026"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e027"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e028"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e029"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e02a"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e02b"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e02c"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e02d"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e02e"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e332"></span>', '<span onclick="return mb.common.emojiClick(this);" char="%s" class="emoji emoji_pointer emoji-e333"></span>');

        //Set HTML
        $domHTML = '<div style="display:none;" id="div_tooltip_emoji"><div class="tipcontent"><div style="width:250px;" id="div_tooltip_emoji_content">';

        //Loop to build HTML
        foreach($arrEmoji as $iKey => $sName)
        {
            //Check loop item
            if($iKey == 120)
            {
                break;
            }

            //Add HTML
            $domHTML .= sprintf($arrImages[$iKey], $sName);
        }

        //Add HTML
        $domHTML .= '</div></div></div><input type="hidden" id="hd_tooltip_emoji_content" value="textarea_shout_real"/>';

        //Return data
        return $domHTML;
    }

    /**
     * Set safe HTML without emoji
     * @param <string> $sContent
     * @return <string>
     */
    public static function setSafeHtmlWithoutEmoji($sContent)
    {
        //Check data
        if(empty($sContent))
        {
            return "";
        }

        //Decode UTF-8
        $sContent = rawurldecode($sContent);

        //Strip tags HTML
        $sContent= str_replace(array('<script', '</script'), '#script', $sContent);
        $sContent = strip_tags($sContent);
        $sContent = trim($sContent);

        //Replace HTML
        $sContent = str_replace(array("\r", "\r\n", "\t", "\n"), array('<br/>', '<br/>', ' ', '<br/>'), $sContent);
        $sContent = preg_replace('/<br\/><br\/><br\/>/i', '<br/><br/>', $sContent);

        //Add slashe
        $sContent = str_replace(array("'","/*","*/"), array("&#39;","/\*","*\/"), $sContent);

        //Replace emoji
        $sContent = self::removeEmoji($sContent);

        //Return data
        return $sContent;
    }

    /**
     * Set safe HTML
     * @param <string> $sContent
     * @return <string>
     */
    public static function setSafeHtml($sContent)
    {
        //Check data
        if(empty($sContent))
        {
            return "";
        }

        //Decode UTF-8
        $sContent = rawurldecode($sContent);

        //Strip tags HTML
        $sContent= str_replace(array('<script', '</script'), '#script', $sContent);
        $sContent = strip_tags($sContent);
        $sContent = trim($sContent);

        //Replace HTML
        $sContent = str_replace(array("\r\n","\r","\t", "\n"), array('<br/>', '<br/>', ' ', '<br/>'), $sContent);
        $sContent = preg_replace('/<br\/><br\/><br\/>/i', '<br/><br/>', $sContent);

        //Add slashe
        $sContent = str_replace(array("'","/*","*/"), array("&#39;","/\*","*\/"), $sContent);

        //safe href
        $sContent = self::safeHtmlEnableLink($sContent);

         //Replace emoji
        $sContent = self::replaceEmoji($sContent);

        //Return data
        return $sContent;
    }

    /**
     * Set safe HTML fpr notification
     * @param <string> $sContent
     * @return <string>
     */
    public static function setSafeHtmlForNotify($sContent)
    {
        //Check data
        if(empty($sContent))
        {
            return "";
        }

        //Decode UTF-8
        $sContent = rawurldecode($sContent);

        //Strip tags HTML
        $sContent= str_replace(array('<script', '</script'), '#script', $sContent);
        $sContent = strip_tags($sContent, '<a>,<span>');
        $sContent = trim($sContent);

        //Replace HTML
        $sContent = str_replace(array("\r", "\r\n", "\t", "\n"), array('<br/>', '<br/>', ' ', '<br/>'), $sContent);
        $sContent = preg_replace('/<br\/><br\/><br\/>/i', '<br/><br/>', $sContent);

        //Add slashe
        $sContent = str_replace(array("'","/*","*/"), array("&#39;","/\*","*\/"), $sContent);

        //Replace emoji
        $sContent = self::replaceEmoji($sContent);

        //Return data
        return $sContent;
    }

    /**
     * Set full name
     * @param <string> $firstName
     * @param <string> $lastName
     * @param <string> $middleName
     * @return <string>
     */
    public static function setFullname($firstName, $lastName, $middleName='')
    {
        //Set fullName
        $fullName = $firstName . ' ';

        //Check middle name
        if(!empty($middleName))
        {
            $fullName .= trim($middleName) . ' ';
        }

        //Add lastname
        $fullName .= $lastName;

        //Return data
        return urldecode(strip_tags($fullName));
    }

    /**
     * Get song information from URL
     * @param <string> $sUrl
     * @return <array>
     */
    public static function getSongInfoFromURL($sUrl)
    {
        //Check URL
        if(empty($sUrl))
        {
            return array();
        }

        //Replace data
        $sUrl = str_replace('&amp;', '&', $sUrl);

        //Parse data
        $arrData = explode('?', $sUrl);

        //Check data
        if(!isset($arrData[1]))
        {
            return array();
        }

        //Explode data
        $arrData = explode('&', $arrData[1]);

        //Check data
        if(sizeof($arrData) == 0)
        {
            return array();
        }

        //Loop to put data
        $arrTmp = array();
        foreach($arrData as $sVal)
        {
            //Explode to array
            $arrSplit = explode('=', $sVal);

            //Check size
            if(sizeof($arrSplit) == 2)
            {
                $arrTmp[$arrSplit[0]] = $arrSplit[1];
            }
        }

        //Return data
        return $arrTmp;
    }

    /**
     * Display list user of real
     * @param <array> $arrListLike
     * @return <string>
     */
    public static function getListLikeReal($arrListLike, &$arrListRealID =array(), $postID = null,$total = null)
    {
        //Check data
        if(sizeof($arrListLike['arrLike']) == 0) return '';

        // Set default value
        $arrLikeName = array();
        $sHtmlName = '<a href="%s" realid="%s" class="nick">%s</a>';

        foreach($arrListLike['arrLike'] as $item)
        {
             /* Check in array list real ID*/
            if (!isset($arrListRealID[$item["realid"]]) && $arrListLike['realLoginID'] != $item['realid'])
            {
               $arrListRealID[$item["realid"]] = array(
                   'realid'     => $item['realid'],
                   'picture'    => $item['picture'],
                   'firstname'  => $item['firstname'],
                   'middlename' => $item['middlename'],
                   'lastname'   => $item['lastname'],
                   'urlid'      => $item['urlid']
               );
            }

            //Replace data
            if($arrListLike['realLoginID'] != $item['realid'])
            {
                array_push(
                    $arrLikeName,
                    sprintf(
                        $sHtmlName,
                        Core_Helper::getLinkUser(array(
                            'urlid' => $item['urlid'],
                            'id' => $item['realid']
                            )
                        ),
                        $item['realid'],
                        Core_Helper::setFullname(
                            $item['firstname'],
                            $item['middlename'],
                            $item['lastname']
                        )
                    )
                );
            }
        }

        //Return data
        if(sizeof($arrLikeName) == 0)
        {
            return '';
        }

        //Return data
        return Core_Helper::setListLike($arrLikeName, $total, $postID);
    }

    /**
     * Display list user of virtual
     * @param <array> $arrListLike
     * @return <string>
     */
    public static function getListLikeVirtual($arrListLike, &$arrListVirtualID = array(), $postID = null)
    {
        //Check data
        if(sizeof($arrListLike["arrLike"]) == 0)
        {
            return '';
        }

        //Set default value
        $arrLikeName = array();
        $sHtmlName = '<a href="%s" virtualid="%s" class="nick">%s</a>';

        //Check list like
        if(sizeof($arrListLike['arrLike']) > 0)
        {
            foreach($arrListLike['arrLike'] as $item)
            {
                if (!isset($arrListVirtualID[$item["virtualid"]]) && $arrListLike['virtualLoginID'] != $item['virtualid'])
                {
                   $arrListVirtualID[$item["virtualid"]] = array(
                       'virtualid'     => $item['virtualid'],
                       'picture'       => $item['picture'],
                       'nickname'      => $item['nickname'],
                       'urlid'         => $item['urlid']
                   );
                }

                //Replace data
                if($arrListLike['virtualLoginID'] != $item['virtualid'])
                {
                   array_push(
                        $arrLikeName,
                        sprintf(
                            $sHtmlName,
                            Core_Helper::getLinkUser(array(
                                'urlid' => $item['urlid'],
                                'id' => $item['virtualid']
                                )
                            ),
                            $item['virtualid'],
                            Core_Helper::replaceEmoji($item['nickname'])
                        )
                    );
                }
            }

            //Return data
            if(sizeof($arrLikeName) == 0)
            {
                return '';
            }

             //Return data
             return Core_Helper::setListLike($arrLikeName, null ,$postID);
        }
    }

    /* Get list like
     * size = 1 : A like this
     * size = 2 : A and B likes this
     * size = 3 : A, B and C likes this
     * size = 4 : A, B, C and 1 peoples likes this
     * @param <array> $arrLikeName
     * @return <string>
     */
    public static function setListLike($arrLikeName, $total = null, $postID = null)
    {
        //Get sizeof list like
        $countArrName = sizeof($arrLikeName);

        if ($countArrName == 0) return '';

        if ($total == null) $total = $countArrName;

        //Get locale configuration
        $localeConfig = Core_Global::getLocalesIni();
        $arrLocales = $localeConfig->general->toArray();

        //Check list liked
        switch($countArrName) {
            case 1:
                return $arrLikeName[0] . ((($arrLikeName[0] != $arrLocales["you"]) && $total == 1) ? $arrLocales["likes_this"] : $arrLocales["like_this"]);
                break;
            case 2:
                return $arrLikeName[0] . $arrLocales["and"] . $arrLikeName[1] . $arrLocales["like_this"];
                break;
            case 3:
                return $arrLikeName[0] . ', ' . $arrLikeName[1] . $arrLocales["and"] . $arrLikeName[2] . $arrLocales["like_this"];
                break;
            default :
                return $arrLikeName[0] . ', ' . $arrLikeName[1] . ', ' . $arrLikeName[2] . $arrLocales["and"] . '<a href="javascript:void(0);" class="listlike" id="' . $postID .'">' . (string)($total - 3) . $arrLocales["peoples"] . '</a>' . $arrLocales["like_this"];
                break;
        }
    }

    /**
    * Get link user profile
    * @param <array> $arrUser
    * @return <string>
    */
    public static function getLinkUser($arrUser)
    {
        //Check data
        if(sizeof($arrUser) == 0)
        {
            return BASE_URL;
        }

        if(empty($arrUser['urlid']))
        {
            return BASE_URL . '/' . $arrUser['id'];
        }

        //Return data
        return BASE_URL . '/' . $arrUser['urlid'];
    }

    /**
     *  Pluralize
     *  @param <int> number
     *  @param <string> text
     *  @return <string>
    */

    public static function pluralize($iNumber, $sText)
    {
        //Get current language
        $iLanguageType = Core_Global::getCurrentLanguage();

        //Get locale configuration
        $localeConfig = Core_Global::getLocalesIni();

        //Check data
        if($iNumber == 0)
        {
            if($sText == "comment"){
                return $localeConfig->pluralize->$sText;
            }else{
                return '0 ' . $localeConfig->pluralize->$sText;
            }
        }

        //Return data
        if($iNumber > 1)
        {
            //Check japanese
            if($iLanguageType == 'ja')
            {
                return $iNumber . " " . $localeConfig->pluralize->$sText;
            }

            //Return default
            $sText = $sText . 's';
            return $iNumber . " " . $localeConfig->pluralize->$sText;
        }

        //Return data
        return $iNumber . " " . $localeConfig->pluralize->$sText;
    }

    /**
     *  Convert timstamp to human time
     *  @param <int> $time
     *  @return <string>
    */
    public static function timeToHumanDate($time)
    {
        //Check time
        if(($time < 0) || is_null($time) || empty($time))
        {
            $time = time();
        }

        //Get current language
        $isType = Core_Global::getCurrentLanguage();

        //Get locale configuration
        $localeConfig = Core_Global::getLocalesIni();

        //Check cookie data
        $iTimeZone = Core_Cookie::getCookie('auth_tz');

        //Check token
        if(empty($iTimeZone))
        {
            $iTimeZone = 0;
        }

        //Return data
        return Core_Filter::timeToHumanDate($time, $localeConfig->time->toArray(), $isType, $iTimeZone);
    }

    /**
     * Notification Mapping
     * @param <array> $arrDetail
     * @return <array>
     */
    public static function notifyMapping($arrLogin, $arrDetail)
    {
        //Get locale configuration
        $localeConfig = Core_Global::getLocalesIni();

        //Build list member
        if($arrDetail['type'] != 'admin_send_message')
        {
            if(!empty($arrDetail['realprofile']))
            {
                if(!empty($arrDetail['like_list']))
                {
                    $arrDetail['member_list'] = self::_buildListMember($arrLogin['account']['realid'], $arrDetail['like_list'], $localeConfig);
                }
                else if(!empty($arrDetail['comment_list']))
                {
                    $arrDetail['member_list'] = self::_buildListMember($arrLogin['account']['realid'], $arrDetail['comment_list'], $localeConfig);
                }
                else
                {
                    //Create array member
                    $arrMember = array();
                    $arrMember[] = $arrDetail['realprofile'];

                    //Create list member by real profile
                    $arrDetail['member_list'] = self::_buildListMember($arrLogin['account']['realid'], $arrMember, $localeConfig);

                    //Get url name of profile
                    $sUrlName = isset($arrDetail['realprofile']['urlid']) ? $arrDetail['realprofile']['urlid'] : $arrDetail['realprofile']['realid'];
                }
            }
            else
            {
                if(!empty($arrDetail['like_list']))
                {
                    $arrDetail['member_list'] = self::_buildListMemberVirtual($arrLogin['account']['virtualid'], $arrDetail['like_list'], $localeConfig);
                }
                else if(!empty($arrDetail['comment_list']))
                {
                    $arrDetail['member_list'] = self::_buildListMemberVirtual($arrLogin['account']['virtualid'], $arrDetail['comment_list'], $localeConfig);
                }
                else
                {
                    //Create list member by virtual profile
                    $arrDetail['member_list'] = self::_buildProfileName(self::replaceEmoji($arrDetail['virtualprofile']['nickname']), $localeConfig);

                    //Get url name of profile
                    $sUrlName = empty($arrDetail['virtualprofile']['urlid']) ? $arrDetail['virtualprofile']['virtualid'] : $arrDetail['virtualprofile']['urlid'];
                }
            }
        }

        //Check type of notification detail
        switch($arrDetail['type'])
        {
            case 'real_comment_on_photo_in_reply_share':
            case 'real_like_on_photo_in_share':
            case 'real_like_on_comment_of_photo_in_share':
            case 'real_comment_on_photo_in_share':
                //get post id
                $sPostId = empty($arrDetail['postid_root']) ? $arrDetail['postid']: $arrDetail['postid_root'];

                $arrDetail['link'] = BASE_URL . '/share/detail/' . $sPostId . '.' . $arrDetail['photoid'];
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], "", $localeConfig);
                $arrDetail['icon_type'] = 'share';
                break;
            case 'real_request_connection':
            case 'real_accept_connection':
                //Create some general key value
                $arrDetail['link'] = BASE_URL . '/' . $sUrlName . '/profile';
                $arrDetail['text'] = $localeConfig->msg->$arrDetail['type'];
                $arrDetail['icon_type'] = 'connect';
                break;
            case 'real_shout_on_board':
                $arrDetail['link'] = BASE_URL . '/shout/detail/r.' . $arrDetail['postid'];
                $arrDetail['text'] = $localeConfig->msg->$arrDetail['type'];
                $arrDetail['icon_type'] = 'shout';
                break;
            case 'real_comment_on_shout':
            case 'real_like_on_shout':
                $arrDetail['link'] = BASE_URL . '/shout/detail/r.' . $arrDetail['postid'];
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], "", $localeConfig);
                $arrDetail['icon_type'] = 'comment';
                break;
            case 'real_like_on_comment':
                //Get postid
                $sPostId = empty($arrDetail['postid_root']) ? $arrDetail['postid'] : $arrDetail['postid_root'];

                $arrDetail['link'] = BASE_URL . '/shout/detail/r.' . $sPostId;
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], "", $localeConfig);
                $arrDetail['icon_type'] = 'comment';
                break;
            case 'real_comment_in_share':
                $sPostId = empty($arrDetail['share_postid']) ? $arrDetail['postid']: $arrDetail['share_postid'];

                $arrDetail['link'] = BASE_URL . '/share/detail/' . $arrDetail['postid'];
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], $arrDetail['subject'], $localeConfig);
                $arrDetail['icon_type'] = 'comment';
                break;
            case 'real_comment_on_comment_in_share':
                //Get postid
                $sPostId = empty($arrDetail['postid_root']) ? $arrDetail['postid'] : $arrDetail['postid_root'];

                $arrDetail['link'] = BASE_URL . '/share/commentdetail/postid/' . $sPostId;
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], $arrDetail['subject'], $localeConfig);
                $arrDetail['icon_type'] = 'comment';
                break;
            case 'real_like_on_share_comment':
                //Get postid
                $sPostId = empty($arrDetail['postid_root']) ? $arrDetail['postid'] : $arrDetail['postid_root'];

                $arrDetail['link'] = BASE_URL . '/share/commentdetail/postid/' . $sPostId;
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], '', $localeConfig);
                $arrDetail['icon_type'] = 'comment';
                break;
            case 'real_invite_share_to_you':
            case 'real_send_share_to_you':
                $arrDetail['link'] = BASE_URL . '/share/detail/' . $arrDetail['postid'];
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], $arrDetail['subject'], $localeConfig);
                $arrDetail['icon_type'] = 'share';
                break;
            case 'real_like_on_share':
                $arrDetail['link'] = BASE_URL . '/share/commentdetail/postid/' . $arrDetail['postid'];
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], $arrDetail['subject'], $localeConfig);
                $arrDetail['icon_type'] = 'share';
                break;
            case 'real_edit_share':
                //Get postid
                $sPostId = empty($arrDetail['postid_root']) ? $arrDetail['postid'] : $arrDetail['postid_root'];

                $arrDetail['link'] = BASE_URL . '/share/detail/' . $sPostId;
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], $arrDetail['subject'], $localeConfig);
                $arrDetail['icon_type'] = 'share';
                break;
            case 'send_app_invitation':
                $arrDetail['link'] = BASE_URL . '/shout/virtual?play_dt=' . urlencode($arrDetail['redirect_url']);
                $arrDetail['text'] = $localeConfig->msg->$arrDetail['type'];
                $arrDetail['icon_type'] = 'dreamtown';
                break;
            case 'real_comment_on_photo_in_shout':
            case 'real_like_on_photo_in_shout':
            case 'real_like_on_comment_of_photo_in_shout':
                //Get postid
                $sPostId = empty($arrDetail['postid_root']) ? $arrDetail['postid'] : $arrDetail['postid_root'];

                //Get photoid
                $sPhotoId = empty($arrDetail['photoid']) ? '' : '.' . $arrDetail['photoid'];

                $arrDetail['link'] = BASE_URL . '/shout/detail/r.' . $sPostId . $sPhotoId;
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], '', $localeConfig);
                $arrDetail['icon_type'] = 'comment';
                break;
            case 'virtual_follow':
                $arrDetail['link'] = BASE_URL . '/' . $sUrlName . '/profile';
                $arrDetail['text'] = $localeConfig->msg->$arrDetail['type'];
                $arrDetail['icon_type'] = 'follow';
                break;
            case 'virtual_shout_on_board':
                $arrDetail['link'] = BASE_URL . '/shout/detail/v.' . $arrDetail['postid'];
                $arrDetail['text'] = $localeConfig->msg->$arrDetail['type'];
                $arrDetail['icon_type'] = 'comment';
                break;
			case 'virtual_comment_on_shout':
			case 'virtual_like_on_shout':
                $arrDetail['link'] = BASE_URL . '/shout/detail/v.' . $arrDetail['postid'];
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['virtualid'], $arrDetail, $arrDetail['type'], '', $localeConfig);
                $arrDetail['icon_type'] = 'comment';
                break;
            case 'virtual_like_on_comment':
                //Get postid
                $sPostId = empty($arrDetail['postid_root']) ? $arrDetail['postid'] : $arrDetail['postid_root'];

                $arrDetail['link'] = BASE_URL . '/shout/detail/v.' . $sPostId;
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['virtualid'], $arrDetail, $arrDetail['type'], '', $localeConfig);
                $arrDetail['icon_type'] = 'comment';
                break;
			case 'virtual_send_message_to_you':
			case 'virtual_comment_in_message':
                $arrDetail['link'] = BASE_URL . '/message/detail/postid/' . $arrDetail['postid'];
                $arrDetail['text'] = $localeConfig->msg->$arrDetail['type'];
                $arrDetail['icon_type'] = 'message';
                break;
            case 'real_share_gmap':
			case 'virtual_share_gmap':
                $arrDetail['link'] = BASE_URL . '/' . $sUrlName . '/profile';
                $arrDetail['text'] = $localeConfig->msg->$arrDetail['type'];
                $arrDetail['icon_type'] = 'map';
                break;
			case 'virtual_left_a_greeting':
			case 'virtual_send_bestfriend_request':
			case 'virtual_accepted_app_friend_request':
			case 'virtual_send_a_gift':
            case 'virtual_send_item_request_in_app':
                $arrDetail['link'] = BASE_URL . '/shout/virtual?play_dt=' . urlencode($arrDetail['redirect_url']);
                $arrDetail['text'] = $localeConfig->msg->$arrDetail['type'];
                $arrDetail['icon_type'] = 'dreamtown';
                break;
            case 'virtual_comment_on_photo_in_shout':
            case 'virtual_like_on_photo_in_shout':
            case 'virtual_like_on_comment_of_photo_in_shout':
                //Get postid
                $sPostId = empty($arrDetail['postid_root']) ? $arrDetail['postid'] : $arrDetail['postid_root'];

                //Get photoid
                $sPhotoId = empty($arrDetail['photoid']) ? '' : '.' . $arrDetail['photoid'];

                $arrDetail['link'] = BASE_URL . '/shout/detail/v.' . $sPostId . $sPhotoId;
                $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['virtualid'], $arrDetail, $arrDetail['type'], '', $localeConfig);
                $arrDetail['icon_type'] = 'comment';
                break;
            case 'send_opi_app_invitation':
                $arrDetail['link'] = $arrDetail['redirect_url'];

                if(empty($arrDetail['realprofile']))
                {
                    $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['virtualid'], $arrDetail, $arrDetail['type'], $arrDetail['app_name'], $localeConfig);
                }
                else
                {
                    $arrDetail['text'] = self::_buildTextNotification($arrLogin['account']['realid'], $arrDetail, $arrDetail['type'], $arrDetail['app_name'], $localeConfig);
                }

                $arrDetail['icon_type'] = 'comment';
                break;
            case 'admin_send_message':
                $arrDetail['link'] = '';
                $arrDetail['text'] = html_entity_decode($arrDetail['content']);
                /*Check Mobion URL*/
                if(strpos($arrDetail['text'], DOMAIN) === false)
                {
                    $arrDetail['text'] = str_replace('<a ', '<a target="_blank" ', $arrDetail['text']);
                }
                break;
            default: //Not mapping
                $arrDetail = array();
                break;
        }

        //Return data
        return $arrDetail;
    }

    /**
     * Get info for displaying real profile tooltip
     */
    public static function setInfoForRealProfileTooltip(&$result, $model, $fields=array()) {
        Core_Helper::_setInfoReal($result, $model);

        $fields = array_merge(array('like', 'comment'), $fields);

        foreach ($fields as $field) {
            if (isset($model[$field])) {
                if (is_array($model[$field])) {
                    foreach ($model[$field] as $m)
                        Core_Helper::setInfoForRealProfileTooltip($result, $m);
                } else {
                    Core_Helper::setInfoForRealProfileTooltip($result, $model[$field]);
                }
            }
        }
    }

    private static function _setInfoReal(&$target, $model) {
        if (!isset($model['realid'])) return;
        $target[$model['realid']] = array(
            'realid'        =>  $model['realid'],
            'picture'       =>  $model['picture'],
            'firstname'     =>  $model['firstname'],
            'middlename'    =>  $model['middlename'],
            'lastname'      =>  $model['lastname'],
            'urlid'         =>  $model['urlid']
        );
    }

    /**
     * Get info for displaying virtual profile tooltip
     */
    public static function setInfoForVirtualProfileTooltip(&$result, $model, $fields=array()) {
        Core_Helper::_setInfoVirtual($result, $model);

        $fields = array_merge(array('like', 'comment'), $fields);

        foreach ($fields as $field) {
            if (isset($model[$field])) {
                if (is_array($model[$field])) {
                    foreach ($model[$field] as $m)
                        Core_Helper::setInfoForVirtualProfileTooltip($result, $m);
                } else {
                    Core_Helper::setInfoForVirtualProfileTooltip($result, $model[$field]);
                }
            }
        }
    }

    private static function _setInfoVirtual(&$target, $model) {
        $target[$model['virtualid']] = array(
            'virtualid'     =>  $model['virtualid'],
            'picture'       =>  $model['picture'],
            'nickname'      =>  $model['nickname'],
            'urlid'         =>  $model['virtualid']
        );
    }

    /**
     * Build list member of notification
     * @params: <array> $arrMember
     * @params: <string> $sRealId
     * @Return: <string>
     */
    private static function _buildListMember($sRealId, $arrMember, $localeConfig)
    {
        //Loop to put data
        foreach($arrMember as $iIndex => $member)
        {
            //Check realID
            if(!isset($member['realid']))
            {
                continue;
            }

            //Check member
            if($sRealId == $member['realid'])
            {
                $arrMember[$iIndex]['display_name'] = $localeConfig->general->you;
            }
            else
            {
                //Check data
                $member['firstname'] = isset($member['firstname'])?$member['firstname']:'';
                $member['middlename'] = isset($member['middlename'])?$member['middlename']:'';
                $member['lastname'] = isset($member['lastname'])?$member['lastname']:'';

                //Set data
                $arrMember[$iIndex]['display_name'] = self::_buildProfileName(self::setFullname($member['firstname'],$member['middlename'],$member['lastname']), $localeConfig);
            }
        }

        //Create new member list
        $sMemberList = "";
        $sAnd = $localeConfig->general->and;

        //Format values
        $arrMember = array_values($arrMember);

        //Check data displayname
        if(sizeof($arrMember) > 0 && isset($arrMember[0]['display_name']))
        {
            if(sizeof($arrMember) == 1)
            {
                $sMemberList = $arrMember[0]['display_name'];
            }
            else if(sizeof($arrMember) == 2)
            {
                $sMemberList = $arrMember[0]['display_name'] . $sAnd . $arrMember[1]['display_name'];
            }
            else if(sizeof($arrMember) > 2)
            {
                $sMemberList = $arrMember[0]['display_name'] . ', ' . $arrMember[1]['display_name'] . $sAnd . $arrMember[2]['display_name'];
            }
        }

        //Return data
        return $sMemberList;
    }

    /**
     * Build list member of notification in virtual
     * @params: <array> $arrMember
     * @params: <string> $sRealId
     * @Return: <string>
     */
    private static function _buildListMemberVirtual($sVirtualId, $arrMember, $localeConfig)
    {
        foreach($arrMember as $iIndex => $member)
        {
            if($sVirtualId == $member['virtualid'])
            {
                $arrMember[$iIndex]['display_name'] = $localeConfig->general->you;
            }
            else
            {
                $arrMember[$iIndex]['display_name'] = self::_buildProfileName(self::replaceEmoji($member['nickname']), $localeConfig);
            }
        }

        //Create new member list
        $sMemberList = "";
        $sAnd = $localeConfig->general->and;

        //Check data displayname
        if(sizeof($arrMember) > 0 && isset($arrMember[0]['display_name']))
        {
            if(sizeof($arrMember) == 1)
            {
                $sMemberList = $arrMember[0]['display_name'];
            }
            else if(sizeof($arrMember) == 2)
            {
                $sMemberList = $arrMember[0]['display_name'] . $sAnd . $arrMember[1]['display_name'];
            }
            else if(sizeof($arrMember) > 2)
            {
                $sMemberList = $arrMember[0]['display_name'] . ', ' . $arrMember[1]['display_name'] . $sAnd . $arrMember[2]['display_name'];
            }
        }

        return $sMemberList;
    }

    /**
     * Build Profile Name
     * @params: <string> $sProfileName
     * @params: <object> $localeConfig
     * @Return: <string>
     */
    private static function _buildProfileName($sProfileName, $localeConfig)
    {
        //If locale is ja then add more text San after Profile Name
        $sProfileName = '<span class="notifySender">' . $sProfileName . '</span>' . $localeConfig->general->san;

        //Return text
        return $sProfileName;
    }

    /**
     * Build Text Notification
     * @params: <array> $arrData
     * @params: <string> $sType
     * @params: <string> $sSubject
     * @Return: <string>
     */
    private static function _buildTextNotification($sProfileId, $arrData, $sType, $sSubject, $localeConfig)
    {
        //Get Sub String Subject
        if(!empty($sSubject))
        {
            $sSubject = Core_String::subFullString($sSubject, 0 , 25, '...');
            $sSubject = '<span class="notifySender">'.$sSubject.'</span>';
        }

        /*Check if real notify*/
        if(!empty($arrData['realprofile']))
        {
            //Get array profile
            $arrProfile = empty($arrData['realprofile_waller']) ? $arrData['realprofile'] : $arrData['realprofile_waller'];

            //Check case notication with content of own user
            if($sProfileId == $arrProfile['realid'])
            {
                //Build notification text
                $sNotificationText = sprintf($localeConfig->msg->$sType, $localeConfig->general->your, $sSubject);
            }
            else
            {
                //Build profile name
                $sProfileName = self::_buildProfileName(self::setFullname($arrProfile['firstname'],$arrProfile['middlename'],$arrProfile['lastname']), $localeConfig);

                //Build notification text
                $sNotificationText = sprintf($localeConfig->msg->$sType, sprintf($localeConfig->general->of_s, $sProfileName), $sSubject);
            }
        }
        /*Check if virtual notify*/
        else
        {
            //Get array profile
            $arrProfile = empty($arrData['virtualprofile_waller']) ? $arrData['virtualprofile'] : $arrData['virtualprofile_waller'];

            //Check case notication with content of own user
            if($sProfileId == $arrProfile['virtualid'])
            {
                //Build notification text
                $sNotificationText = sprintf($localeConfig->msg->$sType, $localeConfig->general->your, $sSubject);
            }
            else
            {
                //Build profile name
                $sProfileName = self::_buildProfileName(self::replaceEmoji($arrProfile['nickname']), $localeConfig);

                //Build notification text
                $sNotificationText = sprintf($localeConfig->msg->$sType, sprintf($localeConfig->general->of_s, $sProfileName), $sSubject);
            }
        }


        //Return text
        return $sNotificationText;
    }

    /**
     * Get html tag A for displaying link to virtual profile
     * @param array $profile
     */
    public static function getVirtualNameLink($profile) {
        $format = "<a class=\"nick\" virtualid=\"%s\" href=\"%s\">%s</a>";
        return sprintf($format,
                $profile['virtualid'],
                self::getLinkUser(array(
                    'urlid'=>$profile['urlid'],
                    'id'=>$profile['virtualid']
                )),
                self::replaceEmoji($profile['nickname'])
        );
    }

    /**
     * Get html tag A for displaying link to real profile
     * @param array $profile
     */
    public static function getRealNameLink($profile) {
        $format = "<a realid=\"%s\" href=\"%s\" class=\"nick\">%s</a>";
        return sprintf($format,
                $profile['realid'],
                self::getLinkUser(array(
                    'urlid'=>$profile['urlid'],
                    'id'=>$profile['realid']
                )),
                self::setFullname($profile['firstname'], $profile['lastname'], $profile['middlename'])
        );
    }

    /**
     * Get html display like list
     * @param type $data
     * @param type $realid: current user realid
     * @return 2 DIVs:
     * - 1 DIV contains the like of mine
     * - 1 DIV without the like of mine
     */
    public static function getRealLikeList($data, $realid) {
        /* Template for like list DIV */
        $tpml = "<div id=\"%s\" class=\"status\" style=\"%s\">%s</div>";

        /* Array of users who like not include me */
        $namesWithoutMe = array();

        foreach ($data['like'] as $like) {
            if ($like['realid'] != $realid) {
                array_push($namesWithoutMe, self::getRealNameLink($like));
            }
        }

        /* Get locales */
        $locales = Core_Global::getLocalesIni();

        /* Array of users who like including me */
        $namesWithMe = $namesWithoutMe;
        array_unshift($namesWithMe, $locales->general->you); //add me

        $haslike = $data['haslike'] == 'true';

        /* DIV contains the like of mine */
        $withMeDiv = sprintf($tpml,
                    'withMe' . $data['postid'],
                    $haslike ? '' : 'display: none',
                    self::setListLike(
                        $namesWithMe,
                        $haslike ? $data['countlike'] : ($data['countlike'] + 1),
                        $data['postid']
                    )
                );

        /* DIV not contain the like of mine */
        $withoutMeDiv = sprintf($tpml,
                    'withoutMe' . $data['postid'],
                    ($haslike || $data['countlike'] == 0) ? "display: none" : "",
                    self::setListLike(
                        $namesWithoutMe,
                        $haslike ? ($data['countlike'] - 1) : $data['countlike'],
                        $data['postid']
                    )
                );

        return $withMeDiv . $withoutMeDiv;
    }

    /**
     * Get browser language
     * @param <array> $arrSupportLanguage
     * @return <string>
     */
    public static function getBrowserLanguage($arrSupportLanguage)
    {
        //Check cookie data
        $sLang = Core_Cookie::getCookie('auth_lang');

        //Check language
        if(empty($sLang))
        {
            //Check timezone cookie
            $tzAuth = Core_Cookie::getCookie('auth_tz');

            //Check timezone data
            if(!empty($tzAuth) && ($tzAuth == 7))
            {
                return 'vi';
            }

            //Check accept data
            if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            {
                //Explode language
                $arrLanguage = explode(";", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                $arrLanguage = explode(",", $arrLanguage[0]);

                //Check data
                if(empty($arrLanguage[0]))
                {
                    return 'en';
                }
            }

            //Check list language support
            if(in_array($arrLanguage[0], $arrSupportLanguage) == false)
            {
                return 'en';
            }

            //Split data
            $arrLanguage = explode('-', $arrLanguage[0]);

            //Set data
            $sLang = $arrLanguage[0];
        }

        //Return data
        return $sLang;
    }

     /**
     * setNameShoutToBoard
     * @param <array> $arrSupportLanguage
     * @return <string>
     */
    public static function setNameShoutToBoard($Name,$sUrl,$sID, $loginID = null, $sTypeView = null)
    {
        if(empty($Name)){
            return;
        }

        /*Prepair href*/
        $sTmpHtml = '<a href="' . $sUrl . '"' . $sTypeView . '="' . $sID . '" class="nick">' . $Name . '</a>';

        /*Return*/
        return $sTmpHtml;
    }

     /**
     * safeHtmlEnableLink
     * @param <array> $arrSupportLanguage
     * @return <string>
     */
    public static function safeHtmlEnableLink($string, $trimEnter = true)
    {
         // Kiem tra cho phep xuong dong
        if($trimEnter == true)
        {
            $content = preg_replace("/\n\n+/","\n", $string);
        }
        else
        {
            $content = preg_replace("/\n\n+/","", $string);
        }

        // Kiem tra tren tung dong
        $arrContent = explode("\n", $content);
        foreach($arrContent as $rowKey => $rowValue)
        {
            // Kiem tra tren tung tu
            $arrPlText = explode(" ", $rowValue);
            foreach($arrPlText as $key => $value)
            {
                //Tim noi dung co dang http://... de gan link vao
                $reg = "/http(s)?:\/\/([\w+?\.\w+])+([a-zA-Z0-9\~\!\@\#\$\%\^\&\*\(\)_\-\=\+\\\\\/\?\.\:\;\\\'\,]*)?/";
                preg_match_all($reg, $value, $kq);
                unset($reg);

                //Neu co link
                if(is_array($kq))
                {
                    if(is_array($kq[0]) && count($kq[0]) > 0 )
                    {
                        //Loop data
                        foreach($kq[0] as $url)
                        {
                            //Empty data
                            if(empty($url))
                            {
                                continue;
                            }

                            //Set text
                            $textUrl = "<a href='" . $url . "' target='_blank' onclick='window.open(this.href);return false;'>" . $url . "</a>";
                            $value = str_replace( $url, $textUrl , $value );
                        }
                    }
                }
                unset($kq);
                $arrPlText[$key] = $value;
            }
            $stringFormat = implode(" ",$arrPlText);
            $arrContent[$rowKey] = $stringFormat;
        }
        $stringFormat = implode("<br/>", $arrContent);

        //Return data
        return $stringFormat;
    }

      /**
     * Get status positon shout from device
     * @param <array> $arrSupportLanguage
     * @return <string>
     */
    public static function getPositionShout($string, $hrefApp = null,$app_name = null)
    {
        /*Check param null*/
        if(empty($string))
        {
            return $string;
        }

        /*split to array*/
        $arrStr = explode('.', $string);

        /*Check sizeof*/
        if(!is_array($arrStr) || (sizeof($arrStr) < 2))
        {
            return '';
        }

        /*get position*/
        $sType = $arrStr[0];
        $sAppID = $app_name;

        /*check array*/
        if(empty($sType))
        {
            return $string;
        }

        /*Prepare Html*/
        $sTmpHtml = '<div class="shoutfrom"><em class="%s"></em> via %s <a target="_blank" href="'. $hrefApp . '">%s</a></div>';

        /*Check type */
         switch ($sType) {
            case 'ios':
                return sprintf($sTmpHtml,$sType, 'iOS', $sAppID);
                break;
            case 'adr':
                return sprintf($sTmpHtml,$sType, 'Android', $sAppID);
                break;
            case 'bb':
                return sprintf($sTmpHtml,$sType,'Blackberry', $sAppID);
                break;
            case 'wp':
                return sprintf($sTmpHtml,$sType,'Windows phone', $sAppID);
                break;
            default:
               return '';
        }
    }

      /**
     * Get status positon shout from device
     * @param <array> $arrSupportLanguage
     * @return <string>
     */
    public static function getPositionShoutMobile($string, $hrefApp = null,$app_name = null)
    {
        /*Check param null*/
        if(empty($string))
        {
            return $string;
        }

        /*split to array*/
        $arrStr = explode('.', $string);

        /*Check sizeof*/
        if(!is_array($arrStr) || (sizeof($arrStr) < 2))
        {
            return '';
        }

        /*get position*/
        $sType = $arrStr[0];
        $sAppID = $app_name;

        /*check array*/
        if(empty($sType))
        {
            return $string;
        }
        /*Prepare Html*/
        $sTmpHtml = '<span> via %s <a target="_blank" href="'. $hrefApp . '">%s</a></span>';

        /*Check type */
         switch ($sType) {
            case 'ios':
                return sprintf($sTmpHtml,$sType, 'iOS', $sAppID);
                break;
            case 'adr':
                return sprintf($sTmpHtml,$sType, 'Android', $sAppID);
                break;
            case 'bb':
                return sprintf($sTmpHtml,$sType,'Blackberry', $sAppID);
                break;
            case 'wp':
                return sprintf($sTmpHtml,$sType,'Windows phone', $sAppID);
                break;
            default:
               return '';
        }
    }

    /**
     * Add Url Parameter
     * @params <string> $url
     * @params <string> $paramName
     * @params <string> $paramValue
     * @return <string> $url
     */
    public static function addURLParameter ($url, $paramName, $paramValue)
    {
        // first check whether the parameter is already
        // defined in the URL so that we can just update
        // the value if that's the case.
        if (preg_match('/[?&]('.$paramName.')=[^&]*/', $url))
        {
            // parameter is already defined in the URL, so
            // replace the parameter value, rather than
            // append it to the end.
            $url = preg_replace('/([?&]'.$paramName.')=[^&]*/', '$1='.$paramValue, $url) ;
        }
        else
        {
            // can simply append to the end of the URL, once
            // we know whether this is the only parameter in
            // there or not.
            $url .= strpos($url, '?') ? '&' : '?';
            $url .= $paramName . '=' . $paramValue;
        }
        return $url;
    }

    /**
     * Get years old from birthdate
     * @params <string> $birthday
     * @return <int> $year_diff
     */
    public static function getYearsOld($birthday)
    {
        //Split day, month, year
        list($year,$month,$day) = explode("/",$birthday);

        //Compare with current time
        $year_diff  = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff   = date("d") - $day;

        if ($day_diff < 0)
        {
            $month_diff--;
        }
        if ($month_diff < 0)
        {
            $year_diff--;
        }
        return $year_diff;
    }

    /**
     * Check valid text in post text
     * @params <array>
     * @return <boolean>
     */
    public static function checkValidText($sText)
    {
        /*Lowcase*/
        $sText = strtolower($sText);

        //Get application configuration
        $defaultConfiguration = Core_Global::getApplicationIni();

        //Get array reject text
        $arrInvalidText = explode(',', $defaultConfiguration->app->modules->reject_text_system);

        foreach($arrInvalidText as $sInvalidText)
        {
            if(is_int(strpos($sText, $sInvalidText)))
            {
                /*Return*/
                return false;
            }
        }

        /*Return*/
        return true;
    }

     public static function getFieldSearchByLanguage($sPrefixField, $sKeywork)
     {
        //Escape query string
        $sKeywork = Core_Global::escapeQueryString($sKeywork);

        //Set array to search
        $arrSearch = array(
            $sPrefixField . ':"' . $sKeywork . '"'
        );

        //Check vietnamese language
        if (Core_Valid::isWordJP($sKeywork)) {
            $arrSearch[] = $sPrefixField . '_CJK:' . $sKeywork;
        } else {
            $arrSearch[] = $sPrefixField . '_VN:' . $sKeywork;
        }

        //Return data
        return implode(' OR ', $arrSearch);
    }

    /**
     * Get member description
     * @params <array> $arrData
     * @return <string> $sAddress
     */
    public static function getMemDesc($arrData)
    {
        //Add address information
        $sAddress = '';
        if(!empty($arrData['highschool']))
        {
            $sAddress =  $arrData['highschool'];
        }
        else if(!empty($arrData['college']))
        {
            $sAddress =  $arrData['college'];
        }
        else if(!empty($arrData['hometown']))
        {
            $sAddress =  $arrData['hometown'];
        }
        else if(!empty($arrData['city']))
        {
            $sAddress =  $arrData['city'];
        }
        else if(!empty($arrData['country_full']))
        {
            $sAddress =  $arrData['country_full'];
        }

        //Decode Address
        $sAddress = urldecode($sAddress);

        //Return address
        return $sAddress;
    }

     /**
     * Get member description
     * @params <array> $arrData
     * @return <string> $sAddress
     */
    public static function getContentShout($msg = null, $songInfo = null)
    {
        /*Check if song info is null*/
        $msg = Core_Helper::setSafeHtml($msg);

        if(!isset($songInfo) && empty($songInfo))
        {
            return $msg;
        }

        /*parser Json*/
        try
        {
             $jsonSongInfo = Zend_Json::decode($songInfo);
        }
        catch (Zend_Exception $ex)
        {
            return $msg;
        }

        $TypeUrlMusic = null;

        /*if url singer*/
        if(isset($jsonSongInfo['Type']) && !empty($jsonSongInfo['Type']))
        {
            $TypeUrlMusic = $jsonSongInfo['Type'];
        }

        if(empty($TypeUrlMusic))
        {
            return $msg;
        }

         //Get locale configuration
        $localeConfig = Core_Global::getLocalesIni();
        $arrLocales = $localeConfig->msg->toArray();

        //Check list liked
        switch($TypeUrlMusic)
        {
            case 'create_playlist':
                if(isset($jsonSongInfo['OwnerID']) && !empty($jsonSongInfo['OwnerID']))
                {
                    $htmlMusic = '<div class="shoutinfo"><div class="urlWMEmbed clearfix"><a href="javascript:void(0);" target="_blank" class="urlIcon"><em></em></a>'
                            . '<div class="urlText clearfix">%s<p class="title"><a href="%s" target="_blank">%s</a></p>'
                            . '<span>created by </span><a href="%s" target="_blank">%s</a></p>'
                            . '<p>via <a href="javascript:void(0);">Mobion Music</a></p></div></div></div>';
                      /*if url playlist # null*/
                    if(isset($jsonSongInfo['PlaylistURL']) && !empty($jsonSongInfo['PlaylistURL']))
                    {
                        return sprintf($htmlMusic,'Create playlist: ', urldecode($jsonSongInfo['PlaylistURL']), $jsonSongInfo['PlaylistName'],urldecode($jsonSongInfo['OwnerURL']),urldecode($jsonSongInfo['OwnerName']));
                        break;
                    }
                }
                else
                {
                    /*Html display*/
                    $htmlMusic = '<div class="shoutinfo"><div class="urlWMEmbed clearfix"><a href="javascript:void(0);" target="_blank" class="urlIcon"><em></em></a>'
                            . '<div class="urlText clearfix"><p class="title">%s<a href="%s" target="_blank">%s</a></p>'
                            . '<p>via <a href="javascript:void(0);">Mobion Music</a></p></div></div></div>';

                     /*if url playlist # null*/
                    if(isset($jsonSongInfo['PlaylistURL']) && !empty($jsonSongInfo['PlaylistURL']))
                    {
                        return sprintf($htmlMusic,'Create playlist: ', urldecode($jsonSongInfo['PlaylistURL']), $jsonSongInfo['PlaylistName']);
                        break;
                    }
                }
                return $msg;
                break;
            case 'share_singer':
                /*Html display*/
                $htmlMusic = '<div class="shoutinfo"><div class="urlWMEmbed clearfix"><a href="javascript:void(0);" target="_blank" class="urlIcon"><em></em></a>'
                            . '<div class="urlText clearfix">%s<p class="title"><a href="%s" target="_blank">%s</a></p>'
                            . '<p>via <a href="javascript:void(0);">Mobion Music</a></p></div></div></div>';

                 /*if url share_singer # null*/
                if(isset($jsonSongInfo['SingerURL']) && !empty($jsonSongInfo['SingerURL']))
                {
                    return sprintf($htmlMusic,'Singer: ', urldecode($jsonSongInfo['SingerURL']),$jsonSongInfo['SingerName']);
                    break;
                }

                return $msg;
                break;
             case 'play_song':
                /*Html display*/
                $htmlMusic = '<span>#NowPlaying: <a href="%s">%s</a> by <a href="%s">%s</a><p>via <a href="javascript:void(0);">Mobion Music</a></p></span>';

                 /*if url share_singer # null*/
                if(isset($jsonSongInfo['SongURL']) && !empty($jsonSongInfo['SongURL']))
                {
                    return sprintf($htmlMusic, urldecode($jsonSongInfo['SongURL']), (isset($jsonSongInfo['SongName'])) ? $jsonSongInfo['SongName'] : '', (isset($jsonSongInfo['SingerURL'])) ? $jsonSongInfo['SingerURL'] : '',$jsonSongInfo['SingerName']);
                    break;
                }

                return $msg;
                break;
            default :
                return $msg;
                break;
        }
    }

      /**
     * add space to string
     * @params <string>
     * @return <string> $sAddress
     */
    public static function addWhitespaceToString($string, $picelen = 9, $pad = ' ')
	{
		//Neu la chuoi rong
		if(empty($string))
		{
			return '' ;
		}

		//Trim khoang trang
		$string = trim($string);

		//Tim xem co khoang trang khong
		$whitespacepos = iconv_strpos($string, ' ', 0, 'UTF-8');

		//Neu khong co khoang trang va co le la khong co tieng viet
		if($whitespacepos == false)
		{
			return wordwrap($string, $picelen, $pad, true);
		}

		//Neu co khoang trang thi cat thanh mang xu ly
		$return_string = '' ;
		$arr_string = explode(' ', $string) ;
		$len = sizeof($arr_string);
		$len = $len - 1 ;
		foreach($arr_string as $key => $substring)
		{
			if( iconv_strlen($substring, 'UTF-8') > $picelen )
	        {
	        	$return_string .= wordwrap($substring, $picelen, $pad, true);
	        }
			else
			{
				$return_string .= $substring ;
			}
			if($key < $len)
			{
				$return_string .= ' ';
			}
		}
	    return $return_string ;
	}

    /**
     * Display month and year format by language
     *
     * @param string $monthName
     * @param string $year
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function showMonthYearByLanguage($monthName, $year)
    {
        $language = 'en';
        isset($_SESSION['language']) && $language = $_SESSION['language'];
        $localesObj = Core_Global::getLocalesIni();
        $key = strtolower($monthName);
        $result = '';
        switch($language) {
            case 'en':
                $result =  $localesObj->calendarEvent->$key . ' ' . $year;
                break;
            case 'vi':
                $result =  $localesObj->calendarEvent->$key . ' ' . $year;
                break;
            case 'ja':
                $result = $year . $localesObj->calendarEvent->year . ' ' . $localesObj->calendarEvent->$key;
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * Display week number, month and year format by language
     *
     * @param int $weekNumber
     * @param string $monthName
     * @param tint $year
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function showWeekNumberMonthYearByLanguage($weekNumber, $monthName, $year)
    {
        $language = 'en';
        isset($_SESSION['language']) && $language = $_SESSION['language'];
        $localesObj = Core_Global::getLocalesIni();
        $key = strtolower($monthName);
        $result = '';
        switch($language) {
            case 'en':
                $result =  $localesObj->calendarEvent->week . ' ' . $weekNumber;
                $result .= ', ' . $localesObj->calendarEvent->$key . ' ' . $year;
                break;
            case 'vi':
                 $result =  $localesObj->calendarEvent->week . ' ' . $weekNumber;
                $result .= ', ' . $localesObj->calendarEvent->$key . ' ' . $year;
                break;
            case 'ja':
                $result =  $localesObj->calendarEvent->week . ' ' . $weekNumber;
                $result .= '、 ' . $year . $localesObj->calendarEvent->year . ' ' . $localesObj->calendarEvent->$key;
                break;

            default:
                break;
        }

        return $result;
    }
    /**
     * Dislay day and month by language
     *
     * @param string $textDay
     * @param int $day
     * @param string $monthName
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function showDayMonthByLanguage($textDay, $day, $monthName)
    {
        $textDay = strtolower($textDay);
        $monthName = strtolower($monthName);
        $language = 'en';
        isset($_SESSION['language']) && $language = $_SESSION['language'];
        $localesObj = Core_Global::getLocalesIni();
        $key = strtolower($monthName);
        $result = '';
        switch($language) {
            case 'en':
                $result =  substr($localesObj->calendarEvent->$textDay, 0, 3);
                $result .= ', '. substr($localesObj->calendarEvent->$monthName, 0, 3);
                $result .= ' ' .  $day;
                break;
            case 'vi':
                $result =  $localesObj->calendarEvent->$textDay;
                $result .= ', ' . $day;
                $result .= ' ' . $localesObj->calendarEvent->$monthName;
                break;
            case 'ja':
                $result =  $localesObj->calendarEvent->$textDay;
                $result .= '、';
                $result .= ' ' . $localesObj->calendarEvent->$monthName;
                $result .= $day . $localesObj->calendarEvent->day;
                break;
            default:
                break;
        }

        return $result;
    }
    /**
     * show Text Day By Language
     *
     * @param string $dayName
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function showTextDayByLanguage($dayName)
    {
        $language = 'en';
        isset($_SESSION['language']) && $language = $_SESSION['language'];
        $localesObj = Core_Global::getLocalesIni();
        $key = strtolower($dayName);
        $result = '';
        switch($language) {
            case 'en':
                $result =  substr($localesObj->calendarEvent->$key, 0, 3);
                break;
             default:
                 $result = $localesObj->calendarEvent->$key;
                break;
        }

        return $result;

    }
    /**
     * showHeaderCalendarByWeek
     *
     * @param int $weekNumber
     * @param array $startDate
     * @param array $endDate
     * @param int $year
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function showHeaderCalendarByWeek($weekNumber, $startDate, $endDate, $year)
    {
        if ($startDate['month'] == $endDate['month']) {
            return self::showHeaderCalendarByWeek1($weekNumber, $startDate, $endDate, $year);
        } else {
            return self::showHeaderCalendarByWeek2($weekNumber, $startDate, $endDate, $year);
        }
    }


    /**
     * showHeaderCalendarByWeek that the the start-date and end-date have the same month
     *
     * @param int $weekNumber
     * @param array $startDate
     * @param array $endDate
     * @param int $year
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    private static function showHeaderCalendarByWeek1($weekNumber, $startDate, $endDate, $year)
    {
        $language = 'en';
        isset($_SESSION['language']) && $language = $_SESSION['language'];
        $localesObj = Core_Global::getLocalesIni();
        $monthName = strtolower($startDate['monthName']);
        $result = '';
        switch($language) {
            case 'en':
                $result = $localesObj->calendarEvent->week . ' ' . $weekNumber;
                $result .= ', ';
                $result .= $localesObj->calendarEvent->$monthName;
                $result .= ' ' . $startDate['day'] . ' - ' . $endDate['day'];
                $result .= ', ' . $year;
                break;
            case 'vi':
                $result = $localesObj->calendarEvent->week . ' ' . $weekNumber;
                $result .= ', ';
                $result .= $startDate['day'] . ' - ' . $endDate['day'];
                $result .= ' ' . $localesObj->calendarEvent->$monthName;
                $result .= ' ' . $year;
                break;
            case 'ja':
                //show as this line "2014年11月4日〜8日、第45週"
                $result =  $year . $localesObj->calendarEvent->year;
                $result .= $localesObj->calendarEvent->$monthName;
                $result .= $startDate['day'] . $localesObj->calendarEvent->day;
                $result .= '〜' . $endDate['day'] . $localesObj->calendarEvent->day;
                $result .= '、' . $localesObj->calendarEvent->first . $weekNumber . $localesObj->calendarEvent->week;
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * showHeaderCalendarByWeek that the the start-date and end-date have not the same month
     *
     * @param type $weekNumber
     * @param type $startDate
     * @param type $endDate
     * @param type $year
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    private static function showHeaderCalendarByWeek2($weekNumber, $startDate, $endDate, $year)
    {
        $language = 'en';
        isset($_SESSION['language']) && $language = $_SESSION['language'];
        $localesObj = Core_Global::getLocalesIni();
        $startMonthName = strtolower($startDate['monthName']);
        $endMonthName = strtolower($endDate['monthName']);
        $result = '';
        switch($language) {
            case 'en':
                $result = $localesObj->calendarEvent->week . ' ' . $weekNumber;
                $result .= ', ';
                $result .= $localesObj->calendarEvent->$startMonthName . ' ' . $startDate['day'];
                $result .= ' - ';
                $result .= $localesObj->calendarEvent->$endMonthName . ' ' . $endDate['day'];
                $result .= ', ' . $year;
                break;
            case 'ja':
                //show as this line "2015年7月27日〜8月2日、第31週"
                $result =  $year . $localesObj->calendarEvent->year;
                $result .= $localesObj->calendarEvent->$startMonthName;
                $result .= $startDate['day'] . $localesObj->calendarEvent->day;
                $result .= '〜' . $localesObj->calendarEvent->$endMonthName;
                $result .= $endDate['day'] . $localesObj->calendarEvent->day;
                $result .= '、' . $localesObj->calendarEvent->first . $weekNumber . $localesObj->calendarEvent->week;
                break;
            case 'vi':
                $result = $localesObj->calendarEvent->week . ' ' . $weekNumber;
                $result .= ', ';
                $result .= $startDate['day'] . ' ' . $localesObj->calendarEvent->$startMonthName;
                $result .= ' - ';
                $result .= $endDate['day'] . ' ' . $localesObj->calendarEvent->$endMonthName;
                $result .= ' ' . $year;
                break;
            default:
                break;
        }

        return $result;
    }
    public static function getCssFromEventCategory($cate)
    {
        $css = '';
        switch($cate) {
            case EVENT_TEAM:
                $css = 'team';
                break;
            case EVENT_PROJECT:
                $css = 'project';
                break;
            case EVENT_TOOL:
                $css = 'tools';
                break;
            case EVENT_PERSONAL:
                $css = 'personal';
                break;
            case EVENT_ANUAL_LEAVE:
                $css = 'annual';
                break;
            case EVENT_DEFAULT_GROUP:
                $css = 'default-group';
                break;
            case EVENT_OTHER_GROUP:
                $css = 'other-group';
                break;
            default:
                break;
        }
        return $css;
    }

    public static function getCalendarRightPopupHeader($day, $month, $year)
    {
        $language = 'en';
        isset($_SESSION['language']) && $language = $_SESSION['language'];
        $localesObj = Core_Global::getLocalesIni();

        $date     = mktime(0, 0, 0, $month, $day, $year);
        $dayName       = strtolower(date('l', $date));
        $monthName     = strtolower(date('F', $date));

        $result = '';
        switch($language) {
            case 'en':
                $result .= substr($localesObj->calendarEvent->$monthName, 0, 3);
                $result .= '.';
                $result .= sprintf('%02d', $day);
                $result .= ' ' . $localesObj->calendarEvent->$dayName;
                break;
            case 'vi':
                $result .= ' ' . $localesObj->calendarEvent->$dayName;
                $result .= '.';
                $result .= sprintf('%02d', $day);
                $result .= ' ';
                $result .= $localesObj->calendarEvent->$monthName;
                break;
            case 'ja':
                $result .= $localesObj->calendarEvent->$monthName;
                $result .= '.';
                $result .= sprintf('%02d', $day);
                $result .= ' ' . $localesObj->calendarEvent->$dayName;

                  break;
            default:
                break;
        }

        return $result;
    }
    private static function getPriorityKey($id)
    {
        $result = '';
        switch ($id) {
            case EVENT_LOW_PRIORITY:
                $result = 'lowPriority';
                break;
            case EVENT_NORMAL_PRIORITY:
                $result = 'normalPriority';
                break;
            case EVENT_HEIGH_PRIORITY:
                $result = 'heighPriority';
                break;
            default:
                break;
        }
        return $result;
    }
    public static function getPriorityText($id)
    {
        $localesObj = Core_Global::getLocalesIni();
        $languageKey = self::getPriorityKey($id);
        $result =  $localesObj->calendarEvent->$languageKey;    
        
        return $result;
    }
    public static function getDateByLanguage($datetime)
    {
        $language = 'en';
        isset($_SESSION['language']) && $language = $_SESSION['language'];
 
        $result = '';
        switch($language) {
            case 'en':
                $result = Core_DateTime::convertYmdToMdy($datetime);
                break;
            case 'vi':
                $result = Core_DateTime::convertYmdToDmy($datetime);
                break;
            default:
                $result = $datetime;
                break;
        }

        return $result;        
    }
    /**
     * Dislay day and month by language
     *
     * @param string $textDay
     * @param int $day
     * @param string $monthName
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function getdayMonthHeaderByDay($textDay, $day, $monthName)
    {
        $textDay = strtolower($textDay);
        $monthName = strtolower($monthName);
        $language = 'en';
        isset($_SESSION['language']) && $language = $_SESSION['language'];
        $localesObj = Core_Global::getLocalesIni();
        $key = strtolower($monthName);
        $result = '';
        switch($language) {
            case 'en':
                $result =  $localesObj->calendarEvent->$textDay;
                $result .= ', '. $localesObj->calendarEvent->$monthName;
                $result .= ' ' .  $day;
                break;
            case 'vi':
                $result =  $localesObj->calendarEvent->$textDay;
                $result .= ', ' . $day;
                $result .= ' ' . $localesObj->calendarEvent->$monthName;
                break;
            case 'ja':
                $result =  $localesObj->calendarEvent->$textDay;
                $result .= '、';
                $result .= ' ' . $localesObj->calendarEvent->$monthName;
                $result .= $day . $localesObj->calendarEvent->day;
                break;
            default:
                break;
        }

        return $result;
    }
    
    private static function getGroupNameKey($groupType)
    {
        $result = '';
        switch ($groupType) {
            case DEFAULT_GROUP:
                $result = 'defaultGroup';
                break;
            case TEAM_GROUP:
                $result = 'teamGroup';
                break;
            case PROJECT_GROUP:
                $result = 'projectGroup';
                break;
            case OTHER_GROUP:
                $result = 'otherGroup';
                break;
            default:
                break;
        }
        return $result;        
    }
    public static function getGroupName($groupType)
    {
        $localesObj = Core_Global::getLocalesIni();
        $groupNameKey = self::getGroupNameKey($groupType);
        
        $result =  $localesObj->group->$groupNameKey;    
        
        return $result;     
    }
    
    private static function getAbsenceStatusKey($status) 
    {
        $result = '';
        switch ($status) {
            case ABSENCE_PENDING_STATUS:
                $result = 'pendingStatus';
                break;
            case ABSENCE_OK_STATUS:
                $result = 'okStatus';
                break;
            case ABSENCE_REJECT_STATUS:
                $result = 'rejectStatus';
                break;
            default:
                break;
        }
        return $result;          
    }
    
    public static function getAbsenceStatusText($status)
    {
        $localesObj = Core_Global::getLocalesIni();
        $key = self::getAbsenceStatusKey($status);
        
        $result =  $localesObj->absence->$key;    
        
        return $result;          
    }
    public static function getAbsenceStatusCss($status)
    {
        $result = '';
        switch ($status) {
            case ABSENCE_PENDING_STATUS:
                $result = 'Pending';
                break;
            case ABSENCE_OK_STATUS:
                $result = 'OK';
                break;
            case ABSENCE_REJECT_STATUS:
                $result = 'Reject';
                break;
            default:
                break;
        }
        return $result;          
    }
    public static function getSelectedLanguage($currentLanguage, $compareToLanguage)
    {
        return ((strtolower($currentLanguage) == strtolower($compareToLanguage)) ? ' checked' : '');
    }
    public static function getSelectedTimezone($currentTimezone, $compareToTimezone)
    {
        return ((strtolower($currentTimezone) == strtolower($compareToTimezone)) ? ' checked' : '');
    }
    
    public static function getCheckedNotificationSetting($accountId, $groupId, $notificationType)
    {
        $value = Core_RedisCommon::getNotificationSetting($accountId, $groupId, $notificationType);
        return ($value == 1 ? ' checked' : '');
    }
    /**
      * Translation key to a supported form.
      *
      * @param string $expected_key  Expected key
      *
      * @return Supported key
      */
      public static function GenerateRevisionId($expected_key) {
          if (strlen($expected_key) > 20) $expected_key = crc32( $expected_key);
          $key = preg_replace("[^0-9-.a-zA-Z_=]", "_", $expected_key);
          $key = substr($key, 0, min(array(strlen($key), 20)));
          return $key;
      }            

      public static function getCurUserHostAddress($userAddress = NULL) {
          if (is_null($userAddress)) {$userAddress = self::getClientIp();}
          return preg_replace("[^0-9a-zA-Z.=]", '_', $userAddress);
      }


      public static function getDocEditorKey($stringKey) {                        
            return self::GenerateRevisionId($stringKey);
      }

      public static function getDocEditorValidateKey($fileUri, $keyId = "", $SkeyId = "") {
            return self::GenerateValidateKey(self::getDocEditorKey($fileUri), $keyId, $SkeyId);
      }
      
      public static function getClientIp() {
            $ipaddress =
                  getenv('HTTP_CLIENT_IP')?:
                  getenv('HTTP_X_FORWARDED_FOR')?:
                  getenv('HTTP_X_FORWARDED')?:
                  getenv('HTTP_FORWARDED_FOR')?:
                  getenv('HTTP_FORWARDED')?:
                  getenv('REMOTE_ADDR')?:
                  '';

            return $ipaddress;
      }

      /**
      *  Generate validate key for editor by documentId
      *
      * LFJ7 or "http://helpcenter.onlyoffice.com/content/GettingStarted.pdf"
      *
      * @param string $document_revision_id     Key for caching on service, whose used in editor
      * @param bool   $add_host_for_validate    Add host address to the key
      *
      * @return Validation key
      */
      public static function GenerateValidateKey($document_revision_id, $keyId = "", $SkeyId = "", $add_host_for_validate = true) {
          if (empty($document_revision_id)) return '';

          $document_revision_id = self::GenerateRevisionId($document_revision_id);          

          $primaryKey = NULL;
          $ms = number_format(round(microtime(true) * 1000),0,'.','');

          if ($add_host_for_validate)
          {
              $userIp = self::getClientIp();

              if (!empty($userIp)) {
                  $primaryKey = "{\"expire\":\"\\/Date(" . $ms . ")\\/\",\"key\":\"" . $document_revision_id . "\",\"key_id\":\"" . $keyId . "\",\"user_count\":0,\"ip\":\"" . $userIp . "\"}";
              }
          }

          if ($primaryKey == NULL)
              $primaryKey = "{\"expire\":\"\\/Date(" . $ms . ")\\/\",\"key\":\"" . $document_revision_id . "\",\"key_id\":\"" . $keyId . "\",\"user_count\":0}";
//          sendlog("GenerateValidateKey. primaryKey = " . $primaryKey, "logs/common.log");
          return self::signature_Create($primaryKey, $SkeyId);
      }

      /**
      * Encoding string from object
      *
      * @param object $primary_key     Json of primary key
      * @param string $secret          Secret key for encoding
      *
      * @return Encoding string
      */
      public static function signature_Create($primary_key, $secret) {
          $payload = base64_encode( hash( 'sha256', ($primary_key . $secret), true ) ) . "?" . $primary_key;
          $base64Str = base64_encode($payload);

          $ind = 0;
          for ($n = strlen($base64Str); $n > 0; $n--){
              if ($base64Str[$n-1] === '='){
                  $ind++;
              } else {
                  break;
              }
          }
          $base64Str = str_replace(array('+', '/'), array('-', '_'), trim($base64Str, '==')) . $ind;

          return urlencode($base64Str);
      }

      public static function nocache_headers() {
            $headers = array(
                  'Expires' => 'Wed, 11 Jan 1984 05:00:00 GMT',
                  'Cache-Control' => 'no-cache, must-revalidate, max-age=0',
                  'Pragma' => 'no-cache',
            );
            $headers['Last-Modified'] = false;


            unset( $headers['Last-Modified'] );

            // In PHP 5.3+, make sure we are not sending a Last-Modified header.
            if ( function_exists( 'header_remove' ) ) {
                  @header_remove( 'Last-Modified' );
            } else {
                  // In PHP 5.2, send an empty Last-Modified header, but only as a
                  // last resort to override a header already sent. #WP23021
                  foreach ( headers_list() as $header ) {
                        if ( 0 === stripos( $header, 'Last-Modified' ) ) {
                              $headers['Last-Modified'] = '';
                              break;
                        }
                  }
            }

            foreach( $headers as $name => $field_value )
                  @header("{$name}: {$field_value}");
      }

      /**
      * The method is to convert the file to the required format
      *
      * Example:
      * string convertedDocumentUri;
      * GetConvertedUri("http://helpcenter.onlyoffice.com/content/GettingStarted.pdf", ".pdf", ".docx", "http://helpcenter.onlyoffice.com/content/GettingStarted.pdf", false, out convertedDocumentUri);
      * 
      * @param string $document_uri            Uri for the document to convert
      * @param string $from_extension          Document extension
      * @param string $to_extension            Extension to which to convert
      * @param string $document_revision_id    Key for caching on service
      * @param bool   $is_async                Perform conversions asynchronously
      * @param string $converted_document_uri  Uri to the converted document
      *
      * @return The percentage of completion of conversion
      */
      public static function GetConvertedUri($document_uri, $from_extension, $to_extension, $document_revision_id, $is_async, &$converted_document_uri) {
          $converted_document_uri = "";
          $responceFromConvertService = SendRequestToConvertService($document_uri, $from_extension, $to_extension, $document_revision_id, $is_async);

          $errorElement = $responceFromConvertService->Error;
          if ($errorElement != NULL && $errorElement != "") self::ProcessConvServResponceError($errorElement);

          $isEndConvert = $responceFromConvertService->EndConvert;
          $percent = $responceFromConvertService->Percent . "";

          if ($isEndConvert != NULL && strtolower($isEndConvert) == "true")
          {
              $converted_document_uri = $responceFromConvertService->FileUrl;
              $percent = 100;
          }
          else if ($percent >= 100)
              $percent = 99;

          return $percent;
      }

      /**
      * Request for conversion to a service
      *
      * @param string $document_uri            Uri for the document to convert
      * @param string $from_extension          Document extension
      * @param string $to_extension            Extension to which to convert
      * @param string $document_revision_id    Key for caching on service
      * @param bool   $is_async                Perform conversions asynchronously
      *
      * @return Xml document request result of conversion
      */
      public static function SendRequestToConvertService($document_uri, $from_extension, $to_extension, $document_revision_id, $is_async) {
          if (empty($from_extension))
          {
              $path_parts = pathinfo($document_uri);
              $from_extension = $path_parts['extension'];
          }

          $title = basename($document_uri);
          if (empty($title)) {
              $title = self::guid();
          }

          if (empty($document_revision_id)) {
              $document_revision_id = $document_uri;
          }

          $document_revision_id = self::GenerateRevisionId($document_revision_id);
          $validateKey = self::GenerateValidateKey($document_revision_id, false);

          $urlToConverter = self::generateUrlToConverter($document_uri, $from_extension, $to_extension, $title, $document_revision_id, $validateKey, $is_async);

          $response_xml_data;
          $countTry = 0;

          $opts = array('http' => array(
                  'method'  => 'GET',
                  'timeout' => DOC_SERV_TIMEOUT 
              )
          );

          if (substr($urlToConverter, 0, strlen("https")) === "https") {
              $opts['ssl'] = array( 'verify_peer'   => FALSE );
          }
       
          $context  = stream_context_create($opts);
          while ($countTry < ServiceConverterMaxTry)
          {
              $countTry = $countTry + 1;
              $response_xml_data = file_get_contents($urlToConverter, FALSE, $context);
              if ($response_xml_data !== false){ break; }
          }

          if ($countTry == ServiceConverterMaxTry)
          {
              throw new Exception ("Bad Request or timeout error");
          }

          libxml_use_internal_errors(true);
          $data = simplexml_load_string($response_xml_data);
          if (!$data) {
              $exc = "Bad Response. Errors: ";
              foreach(libxml_get_errors() as $error) {
                  $exc = $exc . "\t" . $error->message;
              }
              throw new Exception ($exc);
          }

          return $data;
      }

      public static function guid(){
          if (function_exists('com_create_guid')) {
              return com_create_guid();
          } else {
              mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
              $charid = strtoupper(md5(uniqid(rand(), true)));
              $hyphen = chr(45);// "-"
              $uuid = chr(123)// "{"
                      .substr($charid, 0, 8).$hyphen
                      .substr($charid, 8, 4).$hyphen
                      .substr($charid,12, 4).$hyphen
                      .substr($charid,16, 4).$hyphen
                      .substr($charid,20,12)
                      .chr(125);// "}"
              return $uuid;
          }
      }

      public static function generateUrlToConverter($document_uri, $from_extension, $to_extension, $title, $document_revision_id, $validateKey, $is_async) {
          $urlToConverterParams = array(
                                      "url" => $document_uri,
                                      "outputtype" => trim($to_extension,'.'),
                                      "filetype" => trim($from_extension, '.'),
                                      "title" => $title,
                                      "key" => $document_revision_id,
                                      "vkey" => $validateKey);

          $urlToConverter = DOC_SERV_CONVERTER_URL . "?" . http_build_query($urlToConverterParams);

          if ($is_async)
              $urlToConverter = $urlToConverter . "&async=true";

          return $urlToConverter;
      }

      /**
      * Generate an error code table
      *
      * @param string $errorCode   Error code
      *
      * @return null
      */
      public static function ProcessConvServResponceError($errorCode) {
          $errorMessageTemplate = "Error occurred in the document service: ";
          $errorMessage = '';

          switch ($errorCode)
          {
              case -8:
                  $errorMessage = $errorMessageTemplate . "Error document VKey";
                  break;
              case -7:
                  $errorMessage = $errorMessageTemplate . "Error document request";
                  break;
              case -6:
                  $errorMessage = $errorMessageTemplate . "Error database";
                  break;
              case -5:
                  $errorMessage = $errorMessageTemplate . "Error unexpected guid";
                  break;
              case -4:
                  $errorMessage = $errorMessageTemplate . "Error download error";
                  break;
              case -3:
                  $errorMessage = $errorMessageTemplate . "Error convertation error";
                  break;
              case -2:
                  $errorMessage = $errorMessageTemplate . "Error convertation timeout";
                  break;
              case -1:
                  $errorMessage = $errorMessageTemplate . "Error convertation unknown";
                  break;
              case 0:
                  break;
              default:
                  $errorMessage = $errorMessageTemplate . "ErrorCode = " . $errorCode;
                  break;
          }

          throw new Exception($errorMessage);
      }

      public static function GetCorrectName($fullFileName) {
          $path_parts = pathinfo($fullFileName);

          $dir = $path_parts['dirname'];
          $ext = $path_parts['extension'];
          $name = $path_parts['basename'];
          $baseNameWithoutExt = substr($name, 0, strlen($name) - strlen($ext) - 1);

          for ($i = 1; file_exists($dir . "/" . $name); $i++)
          {
              $name = $baseNameWithoutExt . " (" . $i . ")." . $ext;
          }
          return $name;
      }

      public static function GetCorrectFolderName($folderPath, $fileName) {          
          $name = $fileName;  
          for ($i = 1; file_exists($folderPath . DIRECTORY_SEPARATOR . $fileName); $i++)
          {
              $name = $fileName . "(" . $i . ")";
          }
          return $name;
      }

      public static function FileSizeConvert($bytes)
      {
          $result = "";  
          $bytes = floatval($bytes);
              $arBytes = array(
                  0 => array(
                      "UNIT" => "TB",
                      "VALUE" => pow(1024, 4)
                  ),
                  1 => array(
                      "UNIT" => "GB",
                      "VALUE" => pow(1024, 3)
                  ),
                  2 => array(
                      "UNIT" => "MB",
                      "VALUE" => pow(1024, 2)
                  ),
                  3 => array(
                      "UNIT" => "KB",
                      "VALUE" => 1024
                  ),
                  4 => array(
                      "UNIT" => "B",
                      "VALUE" => 1
                  ),
              );

          foreach($arBytes as $arItem)
          {
              if($bytes >= $arItem["VALUE"])
              {
                  $result = $bytes / $arItem["VALUE"];
                  $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                  break;
              }
          }
          return $result;
      }
      public static function GetFolderSize($pathFolder, $convert = true)
      {          
            if(!empty($pathFolder) && file_exists($pathFolder)){
                  $size = 0;  
                  $io = popen ( '/usr/bin/du -sb ' . $pathFolder, 'r' );
                  $size = fgets ( $io, 4096);
                  $size = substr ( $size, 0, strpos ( $size, "\t" ) );
                  pclose ( $io );
                  if($convert)
                        return self::FileSizeConvert((int)$size);
                  else
                        return (int)$size;
            }      
            else return 0;
      }

      public static function createNewFolder($pathFolder = DOC_ROOT_PATH)
      {
            if(!empty($pathFolder) && !file_exists($pathFolder))
            {    
                  mkdir($pathFolder, 0777, true);                
                  return true;            
            }
            return false;    
      }

      public static function getDocumentType($filename) {
        $ext = strtolower('.' . pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext == '.txt') return "text";
        if (in_array($ext, explode(',' , EXTS_DOCUMENT))) return "text";
        if (in_array($ext, explode(',' , EXTS_SPREADSHEET))) return "spreadsheet";
        if (in_array($ext, explode(',' , EXTS_PRESENTATION))) return "presentation";        
        return "";
      }

      public static function getInternalExtension($filename) {
            $ext = strtolower('.' . pathinfo($filename, PATHINFO_EXTENSION));
            if ($ext == '.txt') return ".txt";
            if (in_array($ext, explode(',' , EXTS_DOCUMENT))) return ".docx";
            if (in_array($ext, explode(',' , EXTS_SPREADSHEET))) return ".xlsx";
            if (in_array($ext, explode(',' , EXTS_PRESENTATION))) return ".pptx";
            return "";
      }

      public static function checkIsDocs($filename = "")
      {
            if(!empty($filename)){
                  $ext = self::getInternalExtension($filename);
                  switch($ext)
                  {
                        case '.docx':
                        case '.doc':
                        case '.odt':
                        case '.xlsx':
                        case '.xls':
                        case '.ods':
                        case '.pptx':
                        case '.ppt':
                        case '.odp':
                        case '.csv':
                        case '.txt':
                        case '.pdf':                        
/*                      error when convert html -> docs 
                        case '.html':
                        case '.htm':*/
                              return true;
                        default:
                              return false;
                  }
            }
            return false;
      }

      public static function checkIsOpenDocs($filename = "", &$ext = "")
      {
            if(!empty($filename)){
                  $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                  switch($ext)
                  {
                        case 'odt':
                        case 'odp':
                        case 'ods':                        
                              return true;                              
                        default:
                              return false;
                  }
            }
            return false;
      }

      public static function checkIsImage($filename = "")
      {
            if(!empty($filename)){
                  $ext = strtolower('.' . pathinfo($filename, PATHINFO_EXTENSION));
                  if (in_array($ext, explode(',' , EXTS_IMAGE))) return true;                  
            }
            return false;
      }

      public static function checkUploadDocsExtension($filename) {
            $ext = strtolower('.' . pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, explode(',' , DOC_SERV_UPLOAD_DENIED))) return false;            
            return true;
      }

      public static function getDocsExtension($filename) {
            $ext = strtolower('.' . pathinfo($filename, PATHINFO_EXTENSION));
            if ($ext == '.txt') return "txt";
            if (in_array($ext, explode(',' , EXTS_DOCUMENT))) return "docx";
            if (in_array($ext, explode(',' , EXTS_SPREADSHEET))) return "xlsx";
            if (in_array($ext, explode(',' , EXTS_PRESENTATION))) return "pptx";
            if (in_array($ext, explode(',' , EXTS_IMAGE))) return "image";
            return "";
      }

      public static function listAllEntriesFolder($folderPath){
            $files = array();
            $arrFiles = array();
            $arrFilesTmp = array();
            if(is_dir($folderPath)){
                  $files = scandir($folderPath);
                  if(sizeof($files) > 0){
                        foreach($files as $file){
                              if($file != "." && $file != ".."){
                                    $arrFilesTmp['name'] = $file;
                                    $arrFilesTmp['type'] = is_dir($folderPath . DIRECTORY_SEPARATOR . $file);
                                    $arrFiles[] = $arrFilesTmp;      
                              }
                        }
                  }
            }
            return $arrFiles;
      }       

      public static function copyDir($src, $dst) {
            if (file_exists ( $dst ))
                  self::removeDir ( $dst );
            if (is_dir ( $src )) {
                  mkdir ( $dst );
                  $files = scandir ( $src );
                  foreach ( $files as $file )
                        if ($file != "." && $file != "..")
                              self::copyDir ( "$src/$file", "$dst/$file" );
            } else if (file_exists ( $src ))
                  copy ( $src, $dst );
      }

      public static function removeDir($dir) {
            if (is_dir($dir)) {
                  $files = scandir($dir);
                  foreach ($files as $file)
                        if ($file != "." && $file != "..") self::removeDir("$dir/$file");
                  rmdir($dir);
            }
            else if (file_exists($dir)) unlink($dir);
      }
      public static function cleanString($str){
            $str = str_replace(' ', '-', $str); // Replaces all spaces with hyphens.
            $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str); // Removes special chars.            
            $str = str_replace('/^\-+|\-+$/g','', $str);
            return preg_replace('/-+/', '-', $str); // Replaces multiple hyphens with single one.
      }
      public static function stripUnicode ($str){
            $unicode = array(
                  'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
                  'd'=>'đ',
                  'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
                  'i'=>'í|ì|ỉ|ĩ|ị',
                  'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
                  'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
                  'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
                  'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
                  'D'=>'Đ',
                  'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
                  'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
                  'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
                  'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
                  'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
            );
            foreach($unicode as $nonUnicode=>$uni){
                  $str = preg_replace("/($uni)/i", $nonUnicode, $str);
            }
            return $str;
      }
      public static function convertStrToURL($str){
            if(empty($str))
                  return "";
            $str = strtolower($str);
            $str = self::stripUnicode($str);
            $str = self::cleanString($str);
            return $str;
      }
}

