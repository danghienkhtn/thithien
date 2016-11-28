<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_Filter
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to filter data
 */
if(!isset($GLOBALS['HTMLPurifier_ROOT']))
{
    $GLOBALS['HTMLPurifier_ROOT'] = realpath(realpath(dirname(__FILE__)) .'/../Apache/HTMLPurifier');
    include $GLOBALS['HTMLPurifier_ROOT'].'/HTMLPurifier.includes.php';
    include $GLOBALS['HTMLPurifier_ROOT'].'/HTMLPurifier.autoload.php';
}
class Core_Filter
{
    /**
     * List emotions
     * @var <array>
     */
    private static $arrEmotion = array('>:D<'=>'slide0001_image021.gif','o:)'=>'slide0001_image027.gif',':))'=>'slide0001_image007.gif',':)'=>'slide0001_image004.gif',':D'=>'slide0001_image008.gif','=))'=>'slide0001_image006.gif',':">'=>'slide0001_image018.gif','8->'=>'slide0001_image016.gif',':*'=>'slide0001_image010.gif',';))'=>'slide0001_image005.gif',";)"=>'slide0001_image019.gif','b-)'=>'slide0001_image013.gif','=P~'=>'slide0001_image026.gif','=d>'=>'slide0001_image030.gif',':-h'=>'slide0001_image032.gif',':(('=>'slide0001_image012.gif',':('=>'slide0001_image003.gif','=(('=>'slide0001_image025.gif',':-<'=>'slide0001_image028.gif',':p'=>'slide0001_image014.gif','(:|'=>'slide0001_image017.gif',":|"=>'slide0001_image023.gif',':-$'=>'slide0001_image001.gif','@-)'=>'slide0001_image009.gif',':-&'=>'slide0001_image020.gif',':-s'=>'slide0001_image015.gif','x-('=>'slide0001_image031.gif','\m/'=>'slide0001_image022.gif','(inlove)'=>'slide0001_image011.gif',':x'=>'slide0001_image029.gif',':-?'=>'slide0001_image024.gif','[-('=>'slide0001_image002.gif');

    /**
     * List Vietnamese lower
     * @var <array>
     */
    private static $arrLower= array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","đ","é","è","ẽ","ẻ","ẹ","ê","ễ","ề","ế","ệ","ể","ú","ù","ũ","ụ","ủ","ư","ừ","ứ","ữ","ử","ự","ó","ò","ỏ","õ","ọ","ơ","ờ","ớ","ở","ỡ","ợ","ô","ố","ồ","ổ","ỗ","ộ","á","à","ả","ã","ạ","â","ẩ","ẫ","ậ","ấ","ầ","ă","ắ","ằ","ẵ","ẳ","ặ","í","ì","ỉ","ĩ","ị","ý","ỳ","ỷ","ỹ","ỵ");

    /**
     * List Vietnamese upper
     * @var <array>
     */
    private static $arrUpper= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","Đ","É","È","Ẽ","Ẻ","Ẹ","Ê","Ễ","Ề","Ế","Ệ","Ể","Ú","Ù","Ũ","Ụ","Ủ","Ư","Ừ","Ứ","Ữ","Ử","Ự","Ó","Ò","Ỏ","Õ","Ọ","Ơ","Ờ","Ớ","Ở","Ỡ","Ợ","Ô","Ố","Ồ","Ổ","Ỗ","Ộ","Á","À","Ả","Ã","Ạ","Â","Ẩ","Ẫ","Ậ","Ấ","Ầ","Ă","Ắ","Ằ","Ẵ","Ẳ","Ặ","Í","Ì","Ỉ","Ĩ","Ị","Ý","Ỳ","Ỷ","Ỹ","Ỵ");

    /**
     * List all Vietnamese Utf8
     * @var <array>
     */
    private static $arrCharFrom= array("ạ","á","à","ả","ã","Ạ","Á","À","Ả","Ã","â","ậ","ấ","ầ","ẩ","ẫ","Â","Ậ","Ấ","Ầ","Ẩ","Ẫ","ă","ặ","ắ","ằ","ẳ","ẵ","ẫ","Ă","Ắ","Ằ","Ẳ","Ẵ","Ặ","Ẵ","ê","ẹ","é","è","ẻ","ẽ","Ê","Ẹ","É","È","Ẻ","Ẽ","ế","ề","ể","ễ","ệ","Ế","Ề","Ể","Ễ","Ệ","ọ","ộ","ổ","ỗ","ố","ồ","Ọ","Ộ","Ổ","Ỗ","Ố","Ồ","Ô","ô","ó","ò","ỏ","õ","Ó","Ò","Ỏ","Õ","ơ","ợ","ớ","ờ","ở","ỡ","Ơ","Ợ","Ớ","Ờ","Ở","Ỡ","ụ","ư","ứ","ừ","ử","ữ","ự","Ụ","Ư","Ứ","Ừ","Ử","Ữ","Ự","ú","ù","ủ","ũ","Ú","Ù","Ủ","Ũ","ị","í","ì","ỉ","ĩ","Ị","Í","Ì","Ỉ","Ĩ","ỵ","ý","ỳ","ỷ","ỹ","Ỵ","Ý","Ỳ","Ỷ","Ỹ","đ","Đ");

    /**
     * List all Vietnamese Ascii
     * @var <array>
     */
    private static $arrCharEnd= array("a","a","a","a","a","A","A","A","A","A","a","a","a","a","a","a","A","A","A","A","A","A","a","a","a","a","a","a","a","A","A","A","A","A","A","A","e","e","e","e","e","e","E","E","E","E","E","E","e","e","e","e","e","E","E","E","E","E","o","o","o","o","o","o","O","O","O","O","O","O","O","o","o","o","o","o","O","O","O","O","o","o","o","o","o","o","O","O","O'","O","O","O","u","u","u","u","u","u","u","U","U","U","U","U","U","U","u","u","u","u","U","U","U","U","i","i","i","i","i","I","I","I","I","I","y","y","y","y","y","Y","Y","Y","Y","Y","d","D");

    /**
     * List all Vietnamese VIQR
     * @var <array>
     */
    private static $arrCharEndVN= array("a.","a'","a`","a?","a~","A.","A'","A`","A?","A~","a^","a^.","a^'","a^`","a^?","a^~","A^","A^.","A^'","A^`","A^?","A^~","a(","a(.","a('","a(`","a(?","a(~","a^~","A(","A('","A(`","A(?","A(~","A(.","A(~","e^","e.","e'","e`","e?","e~","E^","E.","E'","E`","E?","E~","e^'","e^`","e^?","e^~","e^.","E^'","E^`","E^?","E^~","E^.","o.","o^.","o^?","o^~","o^'","o^`","O.","O^.","O^?","O^~","O^'","O^`","O^","o^","o'","o`","o?","o~","O'","O`","O?","O~","o+","o+.","o+'","o+`","o+?","o+~","O+","O+.","O+'","O+`","O+?","O+~","u.","u+","u+'","u+`","u+?","u+~","u.~","U.","U+","U+'","U+`","U+?","U+~","U.~","u'","u`","u?","u~","U'","U`","U?","U~","i.","i'","i`","i?","i~","I.","I'","I`","I?","I~","y.","y'","y`","y?","y~","Y.","Y'","Y`","Y?","Y~","dzz","Dzz");

    /**
     * List char filter
     * @var <array>
     */
    private static $arrCharFill = array('/.\'/',"/.`/","/.?/","/.~/","/../","/.^/","/\.(/","/.+/","&amp;","&lt;","&gt;","&apos;","&quot;","&#039;","^","(",")",",","/","?",".","+",":","#","$","&","'", '"', "039;","*","-","“","”","►","`","!","[","]","{","}","@","%","amp;","lt;","gt;","apos;","quot;",";","~");

    /**
     * List char non allowed
     * @var <array>
     */
    private static $arrCharnonAllowed = array("©","®","Æ","Ç","Å","Ç","๏","๏̯͡๏","Î","Ø","Û","Þ","ß","å","","¼","æ","ð","ñ","ø","û","!","ñ","[","\"","$","%","'","(",")","♥","(","+","*","/","\\",",",":","¯","","+",";","<",">","=","?","@","`","¶","[","]","^","~","`","|","","_","?","*","{","}","€","�","ƒ","„","","…","‚","†","‡","ˆ","‰","ø","´","Š","‹","Œ","�","Ž","�","ॐ","۩","�","‘","’","“","”","•","۞","๏","—","˜","™","š","›","œ","�","ž","Ÿ","¡","¢","£","¤","¥","¦","§","¨","«","¬","¯","°","±","²","³","´","µ","¶","¸","¹","º","»","¼","¾","¿","Å","Æ","Ç","?","×","Ø","Û","Þ","ß","å","æ","ç","ï","ð","ñ","÷","ø","ÿ","þ","û","½","☺","☻","♥","♦","♣","♠","•","░","◘","○","◙","♂","♀","♪","♫","☼","►","◄","↕","‼","¶","§","▬","↨","↑","↓","←","←","↔","▲","▼","⌂","¢","→","¥","ƒ","ª","º","▒","▓","│","┤","╡","╢","╖","╕","╣","║","╗","╝","╜","╛","┐","└","┴","┬","├","─","┼","╞","╟","╚","╔","╩","╦","╠","═","╬","╧","╨","╤","╥","╙","╘","╒","╓","╫","╪","┘","┌","█","▄","▌","▐","▀","α","Γ","π","Σ","σ","µ","τ","Φ","Θ","Ω","δ","∞","φ","ε","∩","≡","±","≥","≤","⌠","⌡","≈","°","∙","·","√","ⁿ","²","■","¾","×","Ø","Þ","ð","ღ","ஐ","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","•","","","","","","","","","","","","","","","•","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","","Ƹ", 'Ӝ', 'Ʒ',"★","●",  "♡","ஜ","ܨ");

    /**
     * List char non allowed
     * @var <string>
     */
    private static $censorWords = "{ahole},{Cặc},{andskota},{Cặt},{anus},{Lồn},{arschloch},{Đụ},{ash0le},{Đéo},{ash0les},{Mịa},{asholes},{Địt},{asshole},{Đếch},{assrammer},{Cứt},{azzhole},{Kứt},{b00bs},{Đếk},{b1tch},{Đék},{bassterds},{Đù},{bastard},{Móa},{bitch},{chó đẻ},{blowjob},{De'o},{boffing},{ch.ó},{boiolas},{Đ!t},{boobs},{C-ặc},{breasts},{C-ặt},{buceta},{L-ồn},{butthole},{Đ-ụ},{buttwipe},{Đ-éo},{crack},{M-ịa},{d4mn},{Đ-ịt},{damn},{Đ-ếch},{daygo},{C-ứt},{dcm},{K-ứt},{deck},{Đ-ếk},{dek},{Đ-ék},{dildo},{Đ-ù},{dirsa},{M-óa},{disconme},{C-hó},{disme},{Cặ-c},{dissconme},{Cặ-t},{dissme},{Lồ-n},{ditconme},{Đếc-h},{ditme},{Đé-o},{dziwka},{Mị-a},{ejackulate},{Đị-t},{ejakulate},{Đế-ch},{enculer},{Cứ-t},{fanculo},{Kứ-t},{fatass},{Đế-k},{fcuk},{Đé-k},{feces},{Đ!t},{Felcher},{Mó-a},{ficken},{C.ặc},{Flikker},{C.ặt},{foreskin},{L.ồn},{Fotze},{Đ.ụ},{fuck},{Đ.éo},{fuk},{M.ịa},{Fukin},{Đ.ịt},{Fukk},{Đ.ếch},{futer},{C.ứt},{futkretzn},{K.ứt},{guiena},{Đ.ếk},{helvete},{Đ.ék},{honkey},{Đ.ù},{hoore},{M.óa},{jackoff},{C.hó},{kawk},{Cặ.c},{klootzak},{Cặ.t},{knob},{Lồ.n},{knulle},{Că.c},{kraut},{Đé.o},{kuksuger},{Mị.a},{Kurac},{Đị.t},{kurwa},{Đế.ch},{mamhoon},{Cứ.t},{masochist},{Kứ.t},{masokist},{Đế.k},{monkleigh},{Đé.k},{mouliewop},{Că.t},{mulkku},{Mó.a},{muschi},{Ch.ó},{nastt},{Mi.a},{nepesaurio},{Đi.t},{nutsack},{(.)(.)},{orafis},{( . ) ( . )},{orgasim},{( . )( . )},{orgasm},{( .)(. )},{orgasum},{( . ) ( . )},{oriface},{Đếc.h},{orifice},{c@t},{orifiss},{c@c},{orospu},{F.U.C.K},{p0rn},{F.U.K},{peeenus},{F.C.U.K},{peenus},{F-UCK},{peinus},{FU-CK},{penas},{FUC-K},{penis},{FU.CK},{penus},{FUC.K},{penuus},{lo`n},{phuck},{lo`l},{Phuk},{f-u-c-k},{Phuker},{f-u-ck},{Phukker},{fu-c-k},{pimmel},{f_u_c_k},{pimpis},{f_uck},{pizda},{f_u_ck},{Poonani},{fu_ck},{poontsee},{fuc_k},{porn},{f_u_k},{pr0n},{f.u.k},{preteen},{f-u-k},{puuke},{f.uk},{qahbeh},{f_uk},{rautenberg},{f-uk},{recktum},{fu.k},{rectum},{fu_k},{sadist},{fu-k},{scank},{s-h-i-t},{schaffer},{s-hit},{schlampe},{sh-it},{schlong},{shi-t},{schmuck},{s_h_i_t},{scrotum},{s_hit},{semen},{sh_it},{sex},{shi_t},{sh1t},{s.h.i.t},{sh1ter},{sh.it},{sh1ts},{shi.t},{sh1tter},{s.h!t},{sh1tz},{sh.!t},{sharmuta},{sh!.t},{sharmute},{s_h!t},{shemale},{sh_!t},{shipal},{sh!_t},{shit},{s-h!t},{shiz},{sh-!t},{Shyt},{sh!-t},{skanck},{p.h.u.c.k},{skank},{f.cuk},{skribz},{fc.uk},{skurwysyn},{fcu.k},{sphencter},{f_cuk},{spierdalaj},{fc_uk},{splooge},{fcu_k},{suck},{f.cuk},{suka},{fc.uk},{va1jina},{fcu.k},{vag1na},{f-cuk},{vagiina},{fc-uk},{vagina},{fcu-k},{vaj1na},{buồi},{vajina},{nứng},{vittu},{đéo mẹ},{vullva},{đụ mẹ},{vulva},{địt},{w00se},{kặc},{w0p},{Đụ},{wank},{Đéo},{wh00r},{Vcl},{wh0re},{Bullshit},{whoar},{đụ má},{whore},{địt mẹ},{wichser},{lỗ đít},{xrated},{liếm lìn},{xxx},{mặt lìn},{zabourah},{con phò},{con kặc},{Minh Râu},{việt minh},{việt cộng},{lịt mẹ},{đụ mạ},{đù má},{đụ mẹ},{đéo mẹ},{lỗ đít},{mặt lìn},{liếm lìn},{móc lồn},{ăn lìn},{liếm đít},{con đĩ},{con điếm},{đĩ đực},{điếm đực},{đồ chó},{đồ lợn},{cứt},{ăn kẹc},{bú kẹc},{củ kẹc},{con kẹc},{bú dái},{bú dzái},{mút zái},{bú buồi},{buồi},{bú cu},{bú ku},{con ku},{DIT ME},{Đy~ chó},{Đĩ chó},{ĂN KỨT},{ăn kứt},{cờ ba sọc},{cờ 3 sọc},{nứng cẹc},{nứng kẹt},{dis mẹ},{đis mẹ},{bu'},{bú},{Đjt},{lỒn},{đ ĩ},{LỒN},{deo' ;},{liem^'},{cho'},{cut'},{hiep'},{trYm},{đ ĩ},{đ ĩ},{mặt LỒN},{ĐỴT},{mje},{trYm},{ỈA},{LO^`N},{BUÔ`i},{UCXK},{cailon},{clgt},{concac},{vuto},{cuto}";

    /**
     * HTMLPurifier object
     * @var <HTMLPurifier> 
     */
    private static $htmlPurifier = null;
    
    /**
     * Chen <span> de xuong dong khi moi word vuot qua so ky tu cho phep
     * va chuyen cac url thanh hyperlink
     * @param <string> $string
     * @param <string> $imgUrl
     * @param <string> $imgPath
     * @param <int> $numChar
     * @return <string>
     */
    private static function _buildText($string, $imgUrl, $imgPath='/images/smilley/default/', $numChar=10)
    {
        // Thay the cac emoticon
        $content = self::replaceEmoticons($string, $imgUrl, $imgPath);

        // Loc ra cac tag image de tien hanh cat chuoi
        $reg2 = "/<[^>]*>/Ui";
        preg_match_all($reg2, $content,$kq,PREG_OFFSET_CAPTURE);
        $strSplit = preg_split($reg2, $content, -1, PREG_SPLIT_OFFSET_CAPTURE);
        unset($reg2);

        // Chen <span> de xuong dong khi moi word vuot qua so ky tu cho phep
        // Va chuyen cac url thanh hyperlink
        $i = 0;
        foreach($strSplit as $plText)
        {
            $arrPlText = explode(" ",$plText[0]);
            foreach($arrPlText as $key => $value)
            {
                $value = self::_getUrl($value, $imgUrl, $numChar);
                $arrPlText[$key] = $value;
            }

            $str1 = implode(" ",$arrPlText);
            $lenText = strlen($plText[0]);
            $vitri = $plText[1];
            $lenStrTag = strlen($kq[0][$i][0]);

            if($kq[0][$i][1] + $lenStrTag == $vitri)
            {
                $str1 = $kq[0][$i][0] . $str1;
                $i = $i + 1;
                if($kq[0][$i][1] == $vitri + $lenText)
                {
                    $str1 = $str1 .$kq[0][$i][0];
                    $i = $i + 1;
                }
            }
            $strNew .= $str1;
        }

        $content = $strNew;

        //Free all temp variable
        unset($strSplit, $plText, $str1, $kq, $strNew);

        return $content;
    }

    /**
     * Kiem tra 1 tu truyen neu la dang url thi chuyen sang hyperlink
     * @param <string> $word
     * @param <string> $imgUrl
     * @param <int> $numText
     * @return <string>
     */
    private static function _getUrl($word, $imgUrl, $numText)
    {
        //Tim noi dung co dang http://... de gan link vao
        $reg = "/http(s)?:\/\/([\w+?\.\w+])+([a-zA-Z0-9\~\!\@\#\$\%\^\&\*\(\)_\-\=\+\\\\\/\?\.\:\;\\\'\,]*)?/";
        preg_match_all($reg, $word, $kq);
        unset($reg);

        //Neu co link
        if(is_array($kq))
        {
            if(is_array($kq[0]) && count($kq[0]) > 0)
            {
                $arrCheck = $kq[0];
                $arrCheckLength = count($arrCheck);
                for($i=0; $i<$arrCheckLength; $i++ )
                {
                    $url = $arrCheck[$i];
                    $pos = strpos($url, $imgUrl);
                    if($pos === false)
                    {
                        $splCont = split(":", $url);
                        if( count($splCont) > 2 )
                        {
                            $url = $splCont[0] . ":" . $splCont[1];
                        }
                        $target = Core_String::_wrapWord($url, $numText);
                        $textUrl = "<a href='" . $url . "' target='_blank'>" . $target . "</a>";
                        $word = str_replace( $url, $textUrl , $word );
                    }
                    unset($pos, $arrCheck, $target, $textUrl, $url);
                }
            }
            else
            {
                $word = Core_String::_wrapWord($word, $numText);
            }
        }
        unset($kq);

        return $word;
    }

    /**
    * Remove more whitespace in buffer
    * @param <type> $buffer
    * @return <type>
    */
    public static function removeWhitespaceString($buffer)
    {
        $options = array("drop-proprietary-attributes"=>false,"drop-font-tags"=>true,"drop-empty-paras"=>true,"hide-comments"=>true,"join-classes"=>false,"join-styles"=>false,"indent"=> true,"indent-spaces"=>0);
        $tidy = tidy_parse_string($buffer, $options, 'utf8');
        $tidy->cleanRepair();
        $tidy = str_replace('> <','><',$tidy);
        $tidy = str_replace(' <','<',$tidy);
        $tidy = str_replace('> ','>',$tidy);
        $tidy = str_replace(array("\r","\n","\t"),'',$tidy);
        return $tidy;
    }

    /**
	 * Remove whitespace more in content
	 * @param string $bff
	 * @return string
	 */
	public static function stripBuffer($bff)
	{       
        /*Check buffer*/
        if(empty($bff) || !is_string($bff))
        {
            return '';
        }
        
        /* remove comment */        
        $bff = preg_replace('/<!--(.*)-->/Uis', '', $bff);        
        $bff = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $bff);
        
        /* strip in a line */
        $bff = preg_replace('!\s+!smi', ' ', $bff);
        
        /* remove whitespace */
        $bff = str_replace(array("> <","> </"), array("><","></"), $bff);               
                
        //Return data
		return $bff;
	}

    /**
	 * Tidy clean HTML content
	 * @param <string> $html
	 * @return <string>
	 */
	public static function tidyClean($html)
	{
        //Set options configuration
        $options = array(            
            'show-body-only'    =>  true,
            'wrap'              =>  0,
            'wrap-attributes'   =>  0,
            'preserve-entities' =>  1,
            'output-xhtml'      =>  1,
            'new-inline-tags'   =>  'go',
            'fix-bad-comments'  =>  'no',
            'hide-comments'     =>  'yes',
            'drop-empty-paras'  =>  'yes'
        );
        
        //Return data
        return tidy_repair_string($html, $options, 'utf8');
    }

    /**
     * Kiem tra chuoi truyen vao co dang url thi chuyen sang hyperlink.
     * @param <string> $string : Chuoi can chuyen doi
     * @param <string> $imgUrl : Duong dan thu muc static server cua he thong
     * @param <int> $trimEnter : Cho phep chuoi xuong dong
     * @return <string>
     */
    public static function replaceHyperlink($string, $imgUrl, $trimEnter = true)
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
                    if( is_array($kq[0]) && count($kq[0]) > 0 )
                    {
                        $arrCheck = $kq[0];
                        $arrCheckLength = count($arrCheck);
                        for($i=0; $i<$arrCheckLength; $i++ )
                        {
                            $url = $arrCheck[$i];
                            $pos = strpos( $url, $imgUrl );
                            if($pos === false)
                            {
                                $splCont = split(":", $url);
                                if( count($splCont) > 2 )
                                {
                                    $url = $splCont[0] . ":" . $splCont[1];
                                }
                                $textUrl = "<a href='" . $url . "' target='_blank'>" . $url . "</a>";
                                $value = str_replace( $url, $textUrl , $value );
                            }
                            unset($pos, $arrCheck, $textUrl, $url);
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
        
        return $stringFormat;
    }

    /**
     * Thay the cac phim tat trong chuoi truyen vao thanh cac emoticon tuong ung
     * @param <string> $string
     * @param <string> $imgUrl
     * @param <string> $imgPath
     * @return <string>
     */
    public static function replaceEmoticons($string, $imgUrl, $imgPath='/images/smilley/default/')
    {
        $search = $replace = array();
        foreach(self::$arrEmotion as $key => $value)
        {
            $search[] = Core_String::htmlSpecialchars($key);
            $replace[] = "<img src='" .$imgUrl . $imgPath .$value ."' border='0'>";
        }

        $stringFormat = str_replace($search, $replace, $string);
        $stringFormat = preg_replace("/\n+/","", $stringFormat);

        //Free temp variable
        unset($search, $replace, $string);

        return $stringFormat;
    }

    /**
     * Dinh dang lai chuoi truyen vao nhu sau:
     *  - Thay the phim tat cua cac emoticon.
     *  - Chuyen doi cac url sang hyperlink
     *  - Cat chuoi khi chieu dai cua moi tu vuot qua gioi han cho phep
     * @param <string> $string
     * @param <string> $imgUrl
     * @param <string> $imgPath
     * @param <int> $numChar
     * @param <boolean> $trimEnter
     * @return <string>
     */
    public static function builtTextDisplay($string, $imgUrl, $imgPath='/images/smilley/default/', $numChar = 10, $trimEnter = true)
    {
        //Loai bo tat ca tag
        $content = strip_tags($string);

        // Kiem tra cho phep xuong dong
        if( $trimEnter == true )
        {
            $content = preg_replace("/\n\n+/","\n", $content);
        }
        else
        {
            $content = preg_replace("/\n\n+/","", $content);
        }

        // Kiem tra tren tung dong
        $arrContent = explode("\n", $content);
        foreach($arrContent as $key => $value)
        {
            $value = self::_buildText($value, $imgUrl, $imgPath,  $numChar);
            $arrContent[$key] = $value;
        }
        $content = implode("<br/>", $arrContent);
        
        return $content;
    }

    /**
     * Convert string to upper
     * @param <string> $string
     * @return <string>
     */
    public static function lowerToUpper($string)
    {
        return str_replace(self::$arrLower, self::$arrUpper, $string);
    }

    /**
     * Convert string to lower
     * @param <string> $string
     * @return <string>
     */
    public static function upperTolower($string)
    {
        return str_replace(self::$arrUpper, self::$arrLower, $string);
    }

    /**
    * Convert vietnamese UTF8 to ASCII string
    * @param <string> $tring
    * @param <string> $bit
    * @return <string>
    */
    public static function vnToAscii($tring)
    {        
        $tring = str_replace(self::$arrCharFrom, self::$arrCharEnd, $tring);        
        return str_replace(self::$arrCharnonAllowed, '', $tring);
    }

    /**
    * Convert Iso88591 to vietnamese UTF8 string
    * @param <string> $tring
    * @return <string>
    */
    public static function isoToVn($tring)
    {
        $utf8 = array('ấ'=>'&#7845;','ầ'=>'&#7847;','ẩ'=>'&#7849;','ẫ'=>'&#7851;','ậ'=>'&#7853;','Ấ'=>'&#7844;','Ầ'=>'&#7846;','Ẩ'=>'&#7848;','Ẫ'=>'&#7850;','Ậ'=>'&#7852;','ắ'=>'&#7855;','ằ'=>'&#7857;','ẳ'=>'&#7859;','ẵ'=>'&#7861;','ặ'=>'&#7863;','Ắ'=>'&#7854;','Ằ'=>'&#7856;','Ẳ'=>'&#7858;','Ẵ'=>'&#7860;','Ặ'=>'&#7862;','á'=>'&aacute;','à'=>'&agrave;','ả'=>'&#7843;','ã'=>'&atilde;','ạ'=>'&#7841;','â'=>'&acirc;','ă'=>'&#259;','Á'=>'&Aacute;','À'=>'&Agrave;','Ả'=>'&#7842;','Ã'=>'&Atilde;','Ạ'=>'&#7840;','Â'=>'&Acirc;','Ă'=>'&#258;','ế'=>'&#7871;','ề'=>'&#7873;','ể'=>'&#7875;','ễ'=>'&#7877;','ệ'=>'&#7879;','Ế'=>'&#7870;','Ề'=>'&#7872;','Ể'=>'&#7874;','Ễ'=>'&#7876;','Ệ'=>'&#7878;','é'=>'&eacute;','è'=>'&egrave;','ẻ'=>'&#7867;','ẽ'=>'&#7869;','ẹ'=>'&#7865;','ê'=>'&ecirc;','É'=>'&Eacute;','È'=>'&Egrave;','Ẻ'=>'&#7866;','Ẽ'=>'&#7868;','Ẹ'=>'&#7864;','Ê'=>'&Ecirc;','í'=>'&iacute;','ì'=>'&igrave;','ỉ'=>'&#7881;','ĩ'=>'&#297;','ị'=>'&#7883;','Í'=>'&Iacute;','Ì'=>'&Igrave;','Ỉ'=>'&#7880;','Ĩ'=>'&#296;','Ị'=>'&#7882;','ố'=>'&#7889;','ồ'=>'&#7891;','ổ'=>'&#7893;','ỗ'=>'&#7895;','ộ'=>'&#7897;','Ố'=>'&#7888;','Ồ'=>'&#7890;','Ổ'=>'&#7892;','Ô'=>'&Ocirc;','Ộ'=>'&#7896;','ớ'=>'&#7899;','ờ'=>'&#7901;','ở'=>'&#7903;','ỡ'=>'&#7905;','ợ'=>'&#7907;','Ớ'=>'&#7898;','Ờ'=>'&#7900;','Ở'=>'&#7902;','Ỡ'=>'&#7904;','Ợ'=>'&#7906;','ó'=>'&oacute;','ò'=>'&ograve;','ỏ'=>'&#7887;','õ'=>'&otilde;','ọ'=>'&#7885;','ô'=>'&ocirc;','ơ'=>'&#417;','Ó'=>'&Oacute;','Ò'=>'&Ograve;','Ỏ'=>'&#7886;','Õ'=>'&Otilde;','Ọ'=>'&#7884;','Ô'=>'&Ocirc;','Ơ'=>'&#416;','ứ'=>'&#7913;','ừ'=>'&#7915;','ử'=>'&#7917;','ữ'=>'&#7919;','ự'=>'&#7921;','Ứ'=>'&#7912;','Ừ'=>'&#7914;','Ử'=>'&#7916;','Ữ'=>'&#7918;','Ự'=>'&#7920;','ú'=>'&uacute;','ù'=>'&ugrave;','ủ'=>'&#7911;','ũ'=>'&#361;','ụ'=>'&#7909;','ư'=>'&#432;','Ú'=>'&Uacute;','Ù'=>'&Ugrave;','Ủ'=>'&#7910;','Ũ'=>'&#360;','Ụ'=>'&#7908;','Ư'=>'&#431;','ý'=>'&yacute;','ỳ'=>'&#7923;','ỷ'=>'&#7927;','ỹ'=>'&#7929;','ỵ'=>'&#7925;','Ý'=>'&Yacute;','Ỳ'=>'&#7922;','Ỷ'=>'&#7926;','Ỹ'=>'&#7928;','Ỵ'=>'&#7924;','đ'=>'&#273;','Đ'=>'&#272;');
        $tring = str_replace(array_values($utf8),array_keys($utf8),$tring);
        return str_replace(self::$arrCharnonAllowed, '', $tring);
    }

    /**
     * Convert vietnamese to VIQR for sort
     * @param <string> $tring
     * @return <string>
     */
    public static function vnToViqr($tring)
    {
        $tring = str_replace(self::$arrCharFrom, self::$arrCharEndVN, $tring);
        return str_replace(self::$arrCharnonAllowed, '', $tring);
    }

    /**
     * Convert vietnamese UTF8 to URL string
     * @param <string> $string
     * @return <string>
     */
    public static function vnToUrl($string)
    {
        $string = self::vnToAscii($string);
        $string = str_replace(self::$arrCharFill, '', $string);
        return str_replace(' ', '-', $string);
    }

    /**
     * Convert timstamp to human time
     * @param <int> $time
     * @param <array> $arrOptions
     * @param <string> $isType
     * @param <int> $iTimeZone
     * @return <string> 
     */
    public static function timeToHumanDate($time, $arrOptions, $isType='en', $iTimeZone=0)
    {
        //Get unix timestamp
        $time = round($time/1000);
        
        //Check options
        if(empty($arrOptions))
        {
            return $time;
        }
        
        //Get current
        $time += $iTimeZone*3600;
        
        //Get current date
        $pastDateTime = new DateTime('@' . $time);
        
        //Get current
        $currTimestamp = time() + $iTimeZone*3600;
        
        //Get current dat
        $currDateTime = new DateTime('@' . $currTimestamp);
        
        //Check past time
        if($currTimestamp < $time)
        {
            $pastDateTime = $currDateTime;
        }
        
        //Get diff time
        $interDate = $currDateTime->diff($pastDateTime);
        
        //Get diff infprmation
        $arrDiff = array(
            'Y'         =>  $interDate->y,
            'm'         =>  $interDate->m,
            'd'         =>  $interDate->d,
            'h'         =>  $interDate->h,
            'i'         =>  $interDate->i,
            's'         =>  $interDate->s,
            'days'      =>  $interDate->days,
            'invert'    =>  $interDate->invert
        );
        
        //Add flus day
        $arrDiff['days'] += round(($arrDiff['h'] * 3600 + $arrDiff['i'] * 60 + $arrDiff['s'])/86400);
        
        //Add flus day
        $arrDiff['h'] += round(($arrDiff['i'] * 60 + $arrDiff['s'])/3600);
                
        //Get week number            
        $weekNumber = date('N', $currTimestamp);
                
        //Set in week
        $isInWeek = 0;
        
        //Check in week
        if(($weekNumber > $arrDiff['days']) && ($arrDiff['days'] > 0))
        {
            $isInWeek = 1;
        }
            
        //Check time in week
        if($isInWeek > 0)
        {
            //Get week number            
            $weekNumber = date('N', $time);
            
            //Return data
            return sprintf($arrOptions['weekday'][$weekNumber], date('h:i A', $time));
        }
        else if($arrDiff['days'] == 0)
        {  
            //Check hours
            if($arrDiff['h'] > 12)
            {
                return sprintf($arrOptions['today'], date('h:i A', $time));
            }
            else
            {
                if($arrDiff['h'] > 0)
                {
                    //Set title
                    $title = $arrOptions['hours'];

                    //Check seconds            
                    if($arrDiff['h'] < 2)
                    {
                        $title = $arrOptions['few_hour'];
                    }

                    //Return data
                    return sprintf($title, $arrDiff['h']);
                }
                else if($arrDiff['i'] > 0)
                {
                    //Set title
                    $title = $arrOptions['minutes'];

                    //Check seconds            
                    if($arrDiff['i'] < 2)
                    {
                        $title = $arrOptions['few_minute'];
                    }

                    //Return data
                    return sprintf($title, $arrDiff['i']);
                }
                else
                {
                    //Set title
                    $title = $arrOptions['seconds'];

                    //Check seconds            
                    if($arrDiff['s'] < 5)
                    {
                        $title = $arrOptions['few_second'];
                    }

                    //Return data
                    return sprintf($title, $arrDiff['s']);
                }
            }
        }
        else if($arrDiff['days'] == 1)
        {
            return sprintf($arrOptions['yesterday'], date('h:i A', $time));
        }
                
        //Return data
        return sprintf($arrOptions['days'], (($isType==='vi')?date('d-m-Y', $time):(($isType==='en')?date('m-d-Y', $time):date('Y-m-d', $time))),  date('h:i A', $time));
    }

    /**
    * Removes HTML characters and potentially unsafe scripting words from a string
    * @staticvar <array> $preg_find
    * @staticvar <array> $preg_replace
    * @param <string> $string
    * @return <string>
    */
    public static function removeXss($string)
    {
        //Fix & but allow unicode                
        static $preg_find    = array('#javascript#i', '#vbscript#i');
        static $preg_replace = array('java script',   'vb script');
        return preg_replace($preg_find, $preg_replace, $string);
    }
    
    /**
     * Remove xss by HTMLPurifier
     * @param <string> $string
     * @param <string> $htmlPurifierOptions
     * @return <string> 
     */
    public static function removeByHTMLPurifier($string, $htmlPurifierOptions = array())
    {
        /*Check string*/
        if(empty($string))
        {
            return '';
        }
        
        //Remove URL          
        $string = rawurldecode($string);
        
        //Check HTMLPurifier
        if(is_null(self::$htmlPurifier))
        {
            //Set default configuration
            $htmlPurifierConfig = null;
            
            //Check to set options
            if(is_array($htmlPurifierOptions) && sizeof($htmlPurifierOptions) > 0)
            {
                //Init configuration
                $htmlPurifierConfig = HTMLPurifier_Config::createDefault();

                //Loop to set configuration
                foreach($htmlPurifierOptions as $optionMainKey => $arrOptions)
                {
                    foreach($arrOptions as $optionName => $optionValue)
                    {
                        $htmlPurifierConfig->set($optionMainKey.'.'.$optionName, $optionValue);
                    }
                }
            }
            
            //Set instance
            self::$htmlPurifier = new HTMLPurifier($htmlPurifierConfig);
        }
        
        //Return xss filter data
        return trim(self::$htmlPurifier->purify($string));
    }
    
    /**
     * Add bbcode in content
     * @param <string> $string
     * @return <string> 
     */
    public static function getByBBCode($string, $width=100, $height=100)
    {
        //Allow tags
        $tags = 'b|i|size|color|center|quote|url|img'; 
        
        //Regex to filter data
        while(preg_match_all('`\[('.$tags.')=?(.*?)\](.+?)\[/\1\]`', $string, $matches)) foreach ($matches[0] as $key => $match)
        { 
            //Get data
            list($tag, $param, $innertext) = array($matches[1][$key], $matches[2][$key], $matches[3][$key]); 
            
            //Check tags
            switch($tag)
            { 
                case 'b': $replacement = "<strong>$innertext</strong>"; break; 
                case 'i': $replacement = "<em>$innertext</em>"; break; 
                case 'size': $replacement = "<span style=\"font-size: $param;\">$innertext</span>"; break; 
                case 'color': $replacement = "<span style=\"color: $param;\">$innertext</span>"; break; 
                case 'center': $replacement = "<div class=\"centered\">$innertext</div>"; break; 
                case 'quote': $replacement = "<blockquote>$innertext</blockquote>" . $param? "<cite>$param</cite>" : ''; break; 
                case 'url': $replacement = '<a href="' . ($param? $param : $innertext) . "\">$innertext</a>"; break; 
                case 'img': 
                    //list($width, $height) = preg_split('`[Xx]`', $param); 
                    $replacement = "<img src=\"$innertext\" " . (is_numeric($width)? "width=\"$width\" " : '') . (is_numeric($height)? "height=\"$height\" " : '') . '/>'; 
                    break; 
                case 'video': 
                    $videourl = parse_url($innertext); 
                    parse_str($videourl['query'], $videoquery); 
                    if (strpos($videourl['host'], 'youtube.com') !== FALSE) $replacement = '<embed src="http://www.youtube.com/v/' . $videoquery['v'] . '" type="application/x-shockwave-flash" width="425" height="344"></embed>'; 
                    if (strpos($videourl['host'], 'google.com') !== FALSE) $replacement = '<embed src="http://video.google.com/googleplayer.swf?docid=' . $videoquery['docid'] . '" width="400" height="326" type="application/x-shockwave-flash"></embed>'; 
                    break; 
            } 
            
            //Replace data
            $string = str_replace($match, $replacement, $string); 
        } 
        
        //Return data
        return $string;
    }

    /**
     * Strip out non-digits
     * @param <string> $string
     * @return <string>
     */
    public static function removeNoneDigit($string)
    {
        return preg_replace('/\D/', '', $string);
    }
    
    /**
     * Remove non allow character
     * @param <string> $string
     * @return <string> 
     */
    public static function removeNonAllowCharacter($string)
    {
        return str_replace(self::$arrCharFill, '', $string);
    }

    /**
     * Convert entities to ASCII
     * @param <string> $string
     * @param <boolean> $all
     * @return <string>
     */
    public static function entitiesToAscii($string, $all=true)
    {
        if(preg_match_all('/\&#(\d+)\;/', $string, $matches))
        {
            for ($i = 0, $s = count($matches['0']); $i < $s; $i++)
            {
                $digits = $matches['1'][$i];
                $out = '';
                if ($digits < 128)
                {
                    $out .= chr($digits);
                }
                elseif ($digits < 2048)
                {
                    $out .= chr(192 + (($digits - ($digits % 64)) / 64));
                    $out .= chr(128 + ($digits % 64));
                }
                else
                {
                    $out .= chr(224 + (($digits - ($digits % 4096)) / 4096));
                    $out .= chr(128 + ((($digits % 4096) - ($digits % 64)) / 64));
                    $out .= chr(128 + ($digits % 64));
                }
                $string = str_replace($matches['0'][$i], $out, $string);
            }
        }

        //Check all
        if($all)
        {
            $string = str_replace(array("&amp;", "<", ">", '"', "&apos;", "&#45;"), array("&","<",">","\"", "'", "-"), $string);
        }
        return $string;
    }

    /**
     * convert ASCII to entities string
     * @param <string> $string
     * @return <string>
     */
    public static function asciiToEntities($string)
    {
        $count	= 1;
        $out = '';
        $temp = array();
        for($i = 0, $s = strlen($string); $i < $s; $i++)
        {
            $ordinal = ord($string[$i]);
            if ($ordinal < 128)
            {
                $out .= $string[$i];
            }
            else
            {
                if (count($temp) == 0)
                {
                    $count = ($ordinal < 224) ? 2 : 3;
                }
                $temp[] = $ordinal;
                if(count($temp) == $count)
                {
                    $number = ($count == 3) ? (($temp['0'] % 16) * 4096) + (($temp['1'] % 64) * 64) + ($temp['2'] % 64) : (($temp['0'] % 32) * 64) + ($temp['1'] % 64);
                    $out .= '&#'.$number.';';
                    $count = 1;
                    $temp = array();
                }
            }
        }
        return $out;
    }

    /**
     * strip image tags
     * @param <string> $string
     * @return <string>
     */
    public static function removeImageTags($string)
    {
        $string = preg_replace("#<img\s+.*?src\s*=\s*[\"'](.+?)[\"'].*?\>#", "\\1", $string);
        return preg_replace("#<img\s+.*?src\s*=\s*(.+?).*?\>#", "\\1", $string);
    }

    /**
	 * Lay do dai cua chuoi censored
	 * @param string $string
	 * @return int
	 */
	public static function censoredTextLength($string)
	{
		$string = preg_replace('#&\#([0-9]+);#', '_', $string);
		return strlen($string);
	}

    /**
	 * Loc du lieu
	 * @param string $text	 
	 * @param string $censorchar
	 * @param boolean $flag
	 * @return string
	 */
	public static function fetchCensoredText($text, $censorchar = '*', $flag= true)
	{
		static $arrcensorwords;
        
        //Check Flags
		if($flag==true)
		{
            //Empty array censor
			if(empty($arrcensorwords))
			{
				self::$censorWords = preg_quote(self::$censorWords, '#');
				$arrcensorwords = preg_split('#[,\r\n\t]+#', self::$censorWords, -1, PREG_SPLIT_NO_EMPTY);
			}
            
            //Loop and detect data
			foreach($arrcensorwords AS $arrcensorword)
			{
				if(substr($arrcensorword, 0, 2) == '\\{')
				{
					if (substr($arrcensorword, -2, 2) == '\\}')
					{
						// prevents errors from the replace if the { and } are mismatched
						$arrcensorword = substr($arrcensorword, 2, -2);
					}
					// ASCII character search 0-47, 58-64, 91-96, 123-127
					$nonword_chars = '\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f';
					// words are delimited by ASCII characters outside of A-Z, a-z and 0-9
					
                    $text = preg_replace(
						'#(?<=[' . $nonword_chars . ']|^)' . $arrcensorword . '(?=[' . $nonword_chars . ']|$)#usi',
						str_repeat($censorchar, self::censoredTextLength($arrcensorword)),
                        $text
					);
				}
				else
				{
					$text = preg_replace("#$arrcensorword#usi", str_repeat($censorchar, self::censoredTextLength($arrcensorword)), $text);
				}
			}
		}

        //Return data default
		return $text;
	}

    /**
     * Create SEO Link
     * @param <string> $strName     
     * @return <string>
     */
    public static function setSeoLink($strName)
    {
        return self::vnToUrl($strName);
    }

    /**
     * Escape string when query for solr
     * @param <string> $strQuery
     * @return <string>
     */
    public static function escapeQueryStringForSolr($strQuery)
    {
        $strQuery = str_replace("/", ' ', $strQuery);
        $pattern = '/(\+|-|&&|\|\||!|\(|\)|\{|}|\[|]|\^|"|~|\*|\?|:|\\\)/';
        $replace = '\\\$1';
        $strQuery = preg_replace($pattern, $replace, $strQuery);
        return preg_replace('/[;,:,\\,\[,\],\{,!,^,\}]|OR|AND/', '', $strQuery) ;
    }

    /**
     * Remove none xml string
     * @param <string> $string
     * @return <string>
     */
    public static function removeNoneXml($string)
    {
        return preg_replace('@[\x00-\x08\x0B\x0C\x0E-\x1F]@', ' ', $string);
    }
}

