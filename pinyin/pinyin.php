<?php
# https://stackoverflow.com/questions/1598856/convert-numbered-to-accentuated-pinyin

function pinyin_addaccents($string) {
    # Find words with a number behind them, and replace with callback fn.
    return preg_replace_callback(
        '~([a-zA-ZüÜ]+)(\d)~',
        'pinyin_addaccents_cb',
        $string);
}


# Helper callback
function pinyin_addaccents_cb($match) {
    static $accentmap = null;

    if( $accentmap === null ) {
        # Where to place the accent marks
        $stars =
            'a* e* i* o* u* ü* '.
            'A* E* I* O* U* Ü* '.
            'a*i a*o e*i ia* ia*o ie* io* iu* '.
            'A*I A*O E*I IA* IA*O IE* IO* IU* '.
            'o*u ua* ua*i ue* ui* uo* üe* '.
            'O*U UA* UA*I UE* UI* UO* ÜE*';
        $nostars = str_replace('*', '', $stars);

        # Build an array like Array('a' => 'a*') and store statically
        $accentmap = array_combine(explode(' ',$nostars), explode(' ', $stars));
        unset($stars, $nostars);
    }

    static $vowels =
        Array('a*','e*','i*','o*','u*','ü*','A*','E*','I*','O*','U*','Ü*');

    static $pinyin = Array(
        1 => Array('ā','ē','ī','ō','ū','ǖ','Ā','Ē','Ī','Ō','Ū','Ǖ'),
        2 => Array('á','é','í','ó','ú','ǘ','Á','É','Í','Ó','Ú','Ǘ'),
        3 => Array('ǎ','ě','ǐ','ǒ','ǔ','ǚ','Ǎ','Ě','Ǐ','Ǒ','Ǔ','Ǚ'),
        4 => Array('à','è','ì','ò','ù','ǜ','À','È','Ì','Ò','Ù','Ǜ'),
        5 => Array('a','e','i','o','u','ü','A','E','I','O','U','Ü')
    );

    list(,$word,$tone) = $match;
    # Add star to vowelcluster
    $word = strtr($word, $accentmap);
    # Replace starred letter with accented 
    $word = str_replace($vowels, $pinyin[$tone], $word);
    return $word;
}
?>