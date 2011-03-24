<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Com.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * Ressource file for com and net idn validation
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
return array(
    1  => '/^[\x{002d}0-9\x{0400}-\x{052f}]{1,63}$/iu',
    2  => '/^[\x{002d}0-9\x{0370}-\x{03ff}]{1,63}$/iu',
    3  => '/^[\x{002d}0-9a-z\x{ac00}-\x{d7a3}]{1,17}$/iu',
    4  => '/^[\x{002d}0-9a-z·à-öø-ÿāăąćĉċčďđēĕėęěĝğġģĥħĩīĭįıĵķĸĺļľłńņňŋōŏőœŕŗřśŝşšţťŧũūŭůűųŵŷźżž]{1,63}$/iu',
    5  => '/^[\x{002d}0-9A-Za-z\x{3400}-\x{3401}\x{3404}-\x{3406}\x{340C}\x{3416}\x{341C}' .
'\x{3421}\x{3424}\x{3428}-\x{3429}\x{342B}-\x{342E}\x{3430}-\x{3434}\x{3436}' .
'\x{3438}-\x{343C}\x{343E}\x{3441}-\x{3445}\x{3447}\x{3449}-\x{3451}\x{3453}' .
'\x{3457}-\x{345F}\x{3463}-\x{3467}\x{346E}-\x{3471}\x{3473}-\x{3477}\x{3479}-\x{348E}\x{3491}-\x{3497}' .
'\x{3499}-\x{34A1}\x{34A4}-\x{34AD}\x{34AF}-\x{34B0}\x{34B2}-\x{34BF}\x{34C2}-\x{34C5}\x{34C7}-\x{34CC}' .
'\x{34CE}-\x{34D1}\x{34D3}-\x{34D8}\x{34DA}-\x{34E4}\x{34E7}-\x{34E9}\x{34EC}-\x{34EF}\x{34F1}-\x{34FE}' .
'\x{3500}-\x{3507}\x{350A}-\x{3513}\x{3515}\x{3517}-\x{351A}\x{351C}-\x{351E}\x{3520}-\x{352A}' .
'\x{352C}-\x{3552}\x{3554}-\x{355C}\x{355E}-\x{3567}\x{3569}-\x{3573}\x{3575}-\x{357C}\x{3580}-\x{3588}' .
'\x{358F}-\x{3598}\x{359E}-\x{35AB}\x{35B4}-\x{35CD}\x{35D0}\x{35D3}-\x{35DC}\x{35E2}-\x{35ED}' .
'\x{35F0}-\x{35F6}\x{35FB}-\x{3602}\x{3605}-\x{360E}\x{3610}-\x{3611}\x{3613}-\x{3616}\x{3619}-\x{362D}' .
'\x{362F}-\x{3634}\x{3636}-\x{363B}\x{363F}-\x{3645}\x{3647}-\x{364B}\x{364D}-\x{3653}\x{3655}' .
'\x{3659}-\x{365E}\x{3660}-\x{3665}\x{3667}-\x{367C}\x{367E}\x{3680}-\x{3685}\x{3687}' .
'\x{3689}-\x{3690}\x{3692}-\x{3698}\x{369A}\x{369C}-\x{36AE}\x{36B0}-\x{36BF}\x{36C1}-\x{36C5}' .
'\x{36C9}-\x{36CA}\x{36CD}-\x{36DE}\x{36E1}-\x{36E2}\x{36E5}-\x{36FE}\x{3701}-\x{3713}\x{3715}-\x{371E}' .
'\x{3720}-\x{372C}\x{372E}-\x{3745}\x{3747}-\x{3748}\x{374A}\x{374C}-\x{3759}\x{375B}-\x{3760}' .
'\x{3762}-\x{3767}\x{3769}-\x{3772}\x{3774}-\x{378C}\x{378F}-\x{379C}\x{379F}\x{37A1}-\x{37AD}' .
'\x{37AF}-\x{37B7}\x{37B9}-\x{37C1}\x{37C3}-\x{37C5}\x{37C7}-\x{37D4}\x{37D6}-\x{37E0}\x{37E2}' .
'\x{37E5}-\x{37ED}\x{37EF}-\x{37F6}\x{37F8}-\x{3802}\x{3804}-\x{381D}\x{3820}-\x{3822}\x{3825}-\x{382A}' .
'\x{382D}-\x{382F}\x{3831}-\x{3832}\x{3834}-\x{384C}\x{384E}-\x{3860}\x{3862}-\x{3863}\x{3865}-\x{386B}' .
'\x{386D}-\x{3886}\x{3888}-\x{38A1}\x{38A3}\x{38A5}-\x{38AA}\x{38AC}\x{38AE}-\x{38B0}' .
'\x{38B2}-\x{38B6}\x{38B8}\x{38BA}-\x{38BE}\x{38C0}-\x{38C9}\x{38CB}-\x{38D4}\x{38D8}-\x{38E0}' .
'\x{38E2}-\x{38E6}\x{38EB}-\x{38ED}\x{38EF}-\x{38F2}\x{38F5}-\x{38F7}\x{38FA}-\x{38FF}\x{3901}-\x{392A}' .
'\x{392C}\x{392E}-\x{393B}\x{393E}-\x{3956}\x{395A}-\x{3969}\x{396B}-\x{397A}\x{397C}-\x{3987}' .
'\x{3989}-\x{3998}\x{399A}-\x{39B0}\x{39B2}\x{39B4}-\x{39D0}\x{39D2}-\x{39DA}\x{39DE}-\x{39DF}' .
'\x{39E1}-\x{39EF}\x{39F1}-\x{3A17}\x{3A19}-\x{3A2A}\x{3A2D}-\x{3A40}\x{3A43}-\x{3A4E}\x{3A50}' .
'\x{3A52}-\x{3A5E}\x{3A60}-\x{3A6D}\x{3A6F}-\x{3A77}\x{3A79}-\x{3A82}\x{3A84}-\x{3A85}\x{3A87}-\x{3A89}' .
'\x{3A8B}-\x{3A8F}\x{3A91}-\x{3A93}\x{3A95}-\x{3A96}\x{3A9A}\x{3A9C}-\x{3AA6}\x{3AA8}-\x{3AA9}' .
'\x{3AAB}-\x{3AB1}\x{3AB4}-\x{3ABC}\x{3ABE}-\x{3AC5}\x{3ACA}-\x{3ACB}\x{3ACD}-\x{3AD5}\x{3AD7}-\x{3AE1}' .
'\x{3AE4}-\x{3AE7}\x{3AE9}-\x{3AEC}\x{3AEE}-\x{3AFD}\x{3B01}-\x{3B10}\x{3B12}-\x{3B15}\x{3B17}-\x{3B1E}' .
'\x{3B20}-\x{3B23}\x{3B25}-\x{3B27}\x{3B29}-\x{3B36}\x{3B38}-\x{3B39}\x{3B3B}-\x{3B3C}\x{3B3F}' .
'\x{3B41}-\x{3B44}\x{3B47}-\x{3B4C}\x{3B4E}\x{3B51}-\x{3B55}\x{3B58}-\x{3B62}\x{3B68}-\x{3B72}' .
'\x{3B78}-\x{3B88}\x{3B8B}-\x{3B9F}\x{3BA1}\x{3BA3}-\x{3BBA}\x{3BBC}\x{3BBF}-\x{3BD0}' .
'\x{3BD3}-\x{3BE6}\x{3BEA}-\x{3BFB}\x{3BFE}-\x{3C12}\x{3C14}-\x{3C1B}\x{3C1D}-\x{3C37}\x{3C39}-\x{3C4F}' .
'\x{3C52}\x{3C54}-\x{3C5C}\x{3C5E}-\x{3C68}\x{3C6A}-\x{3C76}\x{3C78}-\x{3C8F}\x{3C91}-\x{3CA8}' .
'\x{3CAA}-\x{3CAD}\x{3CAF}-\x{3CBE}\x{3CC0}-\x{3CC8}\x{3CCA}-\x{3CD3}\x{3CD6}-\x{3CE0}\x{3CE4}-\x{3CEE}' .
'\x{3CF3}-\x{3D0A}\x{3D0E}-\x{3D1E}\x{3D20}-\x{3D21}\x{3D25}-\x{3D38}\x{3D3B}-\x{3D46}\x{3D4A}-\x{3D59}' .
'\x{3D5D}-\x{3D7B}\x{3D7D}-\x{3D81}\x{3D84}-\x{3D88}\x{3D8C}-\x{3D8F}\x{3D91}-\x{3D98}\x{3D9A}-\x{3D9C}' .
'\x{3D9E}-\x{3DA1}\x{3DA3}-\x{3DB0}\x{3DB2}-\x{3DB5}\x{3DB9}-\x{3DBC}\x{3DBE}-\x{3DCB}\x{3DCD}-\x{3DDB}' .
'\x{3DDF}-\x{3DE8}\x{3DEB}-\x{3DF0}\x{3DF3}-\x{3DF9}\x{3DFB}-\x{3DFC}\x{3DFE}-\x{3E05}\x{3E08}-\x{3E33}' .
'\x{3E35}-\x{3E3E}\x{3E40}-\x{3E47}\x{3E49}-\x{3E67}\x{3E6B}-\x{3E6F}\x{3E71}-\x{3E85}\x{3E87}-\x{3E8C}' .
'\x{3E8E}-\x{3E98}\x{3E9A}-\x{3EA1}\x{3EA3}-\x{3EAE}\x{3EB0}-\x{3EB5}\x{3EB7}-\x{3EBA}\x{3EBD}' .
'\x{3EBF}-\x{3EC4}\x{3EC7}-\x{3ECE}\x{3ED1}-\x{3ED7}\x{3ED9}-\x{3EDA}\x{3EDD}-\x{3EE3}\x{3EE7}-\x{3EE8}' .
'\x{3EEB}-\x{3EF2}\x{3EF5}-\x{3EFF}\x{3F01}-\x{3F02}\x{3F04}-\x{3F07}\x{3F09}-\x{3F44}\x{3F46}-\x{3F4E}' .
'\x{3F50}-\x{3F53}\x{3F55}-\x{3F72}\x{3F74}-\x{3F75}\x{3F77}-\x{3F7B}\x{3F7D}-\x{3FB0}\x{3FB6}-\x{3FBF}' .
'\x{3FC1}-\x{3FCF}\x{3FD1}-\x{3FD3}\x{3FD5}-\x{3FDF}\x{3FE1}-\x{400B}\x{400D}-\x{401C}\x{401E}-\x{4024}' .
'\x{4027}-\x{403F}\x{4041}-\x{4060}\x{4062}-\x{4069}\x{406B}-\x{408A}\x{408C}-\x{40A7}\x{40A9}-\x{40B4}' .
'\x{40B6}-\x{40C2}\x{40C7}-\x{40CF}\x{40D1}-\x{40DE}\x{40E0}-\x{40E7}\x{40E9}-\x{40EE}\x{40F0}-\x{40FB}' .
'\x{40FD}-\x{4109}\x{410B}-\x{4115}\x{4118}-\x{411D}\x{411F}-\x{4122}\x{4124}-\x{4133}\x{4136}-\x{4138}' .
'\x{413A}-\x{4148}\x{414A}-\x{4169}\x{416C}-\x{4185}\x{4188}-\x{418B}\x{418D}-\x{41AD}\x{41AF}-\x{41B3}' .
'\x{41B5}-\x{41C3}\x{41C5}-\x{41C9}\x{41CB}-\x{41F2}\x{41F5}-\x{41FE}\x{4200}-\x{4227}\x{422A}-\x{4246}' .
'\x{4248}-\x{4263}\x{4265}-\x{428B}\x{428D}-\x{42A1}\x{42A3}-\x{42C4}\x{42C8}-\x{42DC}\x{42DE}-\x{430A}' .
'\x{430C}-\x{4335}\x{4337}\x{4342}-\x{435F}\x{4361}-\x{439A}\x{439C}-\x{439D}\x{439F}-\x{43A4}' .
'\x{43A6}-\x{43EC}\x{43EF}-\x{4405}\x{4407}-\x{4429}\x{442B}-\x{4455}\x{4457}-\x{4468}\x{446A}-\x{446D}' .
'\x{446F}-\x{4476}\x{4479}-\x{447D}\x{447F}-\x{4486}\x{4488}-\x{4490}\x{4492}-\x{4498}\x{449A}-\x{44AD}' .
'\x{44B0}-\x{44BD}\x{44C1}-\x{44D3}\x{44D6}-\x{44E7}\x{44EA}\x{44EC}-\x{44FA}\x{44FC}-\x{4541}' .
'\x{4543}-\x{454F}\x{4551}-\x{4562}\x{4564}-\x{4575}\x{4577}-\x{45AB}\x{45AD}-\x{45BD}\x{45BF}-\x{45D5}' .
'\x{45D7}-\x{45EC}\x{45EE}-\x{45F2}\x{45F4}-\x{45FA}\x{45FC}-\x{461A}\x{461C}-\x{461D}\x{461F}-\x{4631}' .
'\x{4633}-\x{4649}\x{464C}\x{464E}-\x{4652}\x{4654}-\x{466A}\x{466C}-\x{4675}\x{4677}-\x{467A}' .
'\x{467C}-\x{4694}\x{4696}-\x{46A3}\x{46A5}-\x{46AB}\x{46AD}-\x{46D2}\x{46D4}-\x{4723}\x{4729}-\x{4732}' .
'\x{4734}-\x{4758}\x{475A}\x{475C}-\x{478B}\x{478D}\x{4791}-\x{47B1}\x{47B3}-\x{47F1}' .
'\x{47F3}-\x{480B}\x{480D}-\x{4815}\x{4817}-\x{4839}\x{483B}-\x{4870}\x{4872}-\x{487A}\x{487C}-\x{487F}' .
'\x{4883}-\x{488E}\x{4890}-\x{4896}\x{4899}-\x{48A2}\x{48A4}-\x{48B9}\x{48BB}-\x{48C8}\x{48CA}-\x{48D1}' .
'\x{48D3}-\x{48E5}\x{48E7}-\x{48F2}\x{48F4}-\x{48FF}\x{4901}-\x{4922}\x{4924}-\x{4928}\x{492A}-\x{4931}' .
'\x{4933}-\x{495B}\x{495D}-\x{4978}\x{497A}\x{497D}\x{4982}-\x{4983}\x{4985}-\x{49A8}' .
'\x{49AA}-\x{49AF}\x{49B1}-\x{49B7}\x{49B9}-\x{49BD}\x{49C1}-\x{49C7}\x{49C9}-\x{49CE}\x{49D0}-\x{49E8}' .
'\x{49EA}\x{49EC}\x{49EE}-\x{4A19}\x{4A1B}-\x{4A43}\x{4A45}-\x{4A4D}\x{4A4F}-\x{4A9E}' .
'\x{4AA0}-\x{4AA9}\x{4AAB}-\x{4B4E}\x{4B50}-\x{4B5B}\x{4B5D}-\x{4B69}\x{4B6B}-\x{4BC2}\x{4BC6}-\x{4BE8}' .
'\x{4BEA}-\x{4BFA}\x{4BFC}-\x{4C06}\x{4C08}-\x{4C2D}\x{4C2F}-\x{4C32}\x{4C34}-\x{4C35}\x{4C37}-\x{4C69}' .
'\x{4C6B}-\x{4C73}\x{4C75}-\x{4C86}\x{4C88}-\x{4C97}\x{4C99}-\x{4C9C}\x{4C9F}-\x{4CA3}\x{4CA5}-\x{4CB5}' .
'\x{4CB7}-\x{4CF8}\x{4CFA}-\x{4D27}\x{4D29}-\x{4DAC}\x{4DAE}-\x{4DB1}\x{4DB3}-\x{4DB5}\x{4E00}-\x{4E54}' .
'\x{4E56}-\x{4E89}\x{4E8B}-\x{4EEC}\x{4EEE}-\x{4FAC}\x{4FAE}-\x{503C}\x{503E}-\x{51E5}\x{51E7}-\x{5270}' .
'\x{5272}-\x{56A1}\x{56A3}-\x{5840}\x{5842}-\x{58B5}\x{58B7}-\x{58CB}\x{58CD}-\x{5BC8}\x{5BCA}-\x{5C01}' .
'\x{5C03}-\x{5C25}\x{5C27}-\x{5D5B}\x{5D5D}-\x{5F08}\x{5F0A}-\x{61F3}\x{61F5}-\x{63BA}\x{63BC}-\x{6441}' .
'\x{6443}-\x{657C}\x{657E}-\x{663E}\x{6640}-\x{66FC}\x{66FE}-\x{6728}\x{672A}-\x{6766}\x{6768}-\x{67A8}' .
'\x{67AA}-\x{685B}\x{685D}-\x{685E}\x{6860}-\x{68B9}\x{68BB}-\x{6AC8}\x{6ACA}-\x{6BB0}\x{6BB2}-\x{6C16}' .
'\x{6C18}-\x{6D9B}\x{6D9D}-\x{6E12}\x{6E14}-\x{6E8B}\x{6E8D}-\x{704D}\x{704F}-\x{7113}\x{7115}-\x{713B}' .
'\x{713D}-\x{7154}\x{7156}-\x{729F}\x{72A1}-\x{731E}\x{7320}-\x{7362}\x{7364}-\x{7533}\x{7535}-\x{7551}' .
'\x{7553}-\x{7572}\x{7574}-\x{75E8}\x{75EA}-\x{7679}\x{767B}-\x{783E}\x{7840}-\x{7A62}\x{7A64}-\x{7AC2}' .
'\x{7AC4}-\x{7B06}\x{7B08}-\x{7B79}\x{7B7B}-\x{7BCE}\x{7BD0}-\x{7D99}\x{7D9B}-\x{7E49}\x{7E4C}-\x{8132}' .
'\x{8134}\x{8136}-\x{81D2}\x{81D4}-\x{8216}\x{8218}-\x{822D}\x{822F}-\x{83B4}\x{83B6}-\x{841F}' .
'\x{8421}-\x{86CC}\x{86CE}-\x{874A}\x{874C}-\x{877E}\x{8780}-\x{8A32}\x{8A34}-\x{8B71}\x{8B73}-\x{8B8E}' .
'\x{8B90}-\x{8DE4}\x{8DE6}-\x{8E9A}\x{8E9C}-\x{8EE1}\x{8EE4}-\x{8F0B}\x{8F0D}-\x{8FB9}\x{8FBB}-\x{9038}' .
'\x{903A}-\x{9196}\x{9198}-\x{91A3}\x{91A5}-\x{91B7}\x{91B9}-\x{91C7}\x{91C9}-\x{91E0}\x{91E2}-\x{91FB}' .
'\x{91FD}-\x{922B}\x{922D}-\x{9270}\x{9272}-\x{9420}\x{9422}-\x{9664}\x{9666}-\x{9679}\x{967B}-\x{9770}' .
'\x{9772}-\x{982B}\x{982D}-\x{98ED}\x{98EF}-\x{99C4}\x{99C6}-\x{9A11}\x{9A14}-\x{9A27}\x{9A29}-\x{9D0D}' .
'\x{9D0F}-\x{9D2B}\x{9D2D}-\x{9D8E}\x{9D90}-\x{9DC5}\x{9DC7}-\x{9E77}\x{9E79}-\x{9EB8}\x{9EBB}-\x{9F20}' .
'\x{9F22}-\x{9F61}\x{9F63}-\x{9FA5}\x{FA28}]{1,20}$/iu',
    6 => '/^[\x{002d}0-9A-Za-z]{1,63}$/iu',
    7 => '/^[\x{00A1}-\x{00FF}]{1,63}$/iu',
    8 => '/^[\x{0100}-\x{017f}]{1,63}$/iu',
    9 => '/^[\x{0180}-\x{024f}]{1,63}$/iu',
    10 => '/^[\x{0250}-\x{02af}]{1,63}$/iu',
    11 => '/^[\x{02b0}-\x{02ff}]{1,63}$/iu',
    12 => '/^[\x{0300}-\x{036f}]{1,63}$/iu',
    13 => '/^[\x{0370}-\x{03ff}]{1,63}$/iu',
    14 => '/^[\x{0400}-\x{04ff}]{1,63}$/iu',
    15 => '/^[\x{0500}-\x{052f}]{1,63}$/iu',
    16 => '/^[\x{0530}-\x{058F}]{1,63}$/iu',
    17 => '/^[\x{0590}-\x{05FF}]{1,63}$/iu',
    18 => '/^[\x{0600}-\x{06FF}]{1,63}$/iu',
    19 => '/^[\x{0700}-\x{074F}]{1,63}$/iu',
    20 => '/^[\x{0780}-\x{07BF}]{1,63}$/iu',
    21 => '/^[\x{0900}-\x{097F}]{1,63}$/iu',
    22 => '/^[\x{0980}-\x{09FF}]{1,63}$/iu',
    23 => '/^[\x{0A00}-\x{0A7F}]{1,63}$/iu',
    24 => '/^[\x{0A80}-\x{0AFF}]{1,63}$/iu',
    25 => '/^[\x{0B00}-\x{0B7F}]{1,63}$/iu',
    26 => '/^[\x{0B80}-\x{0BFF}]{1,63}$/iu',
    27 => '/^[\x{0C00}-\x{0C7F}]{1,63}$/iu',
    28 => '/^[\x{0C80}-\x{0CFF}]{1,63}$/iu',
    29 => '/^[\x{0D00}-\x{0D7F}]{1,63}$/iu',
    30 => '/^[\x{0D80}-\x{0DFF}]{1,63}$/iu',
    31 => '/^[\x{0E00}-\x{0E7F}]{1,63}$/iu',
    32 => '/^[\x{0E80}-\x{0EFF}]{1,63}$/iu',
    33 => '/^[\x{0F00}-\x{0FFF}]{1,63}$/iu',
    34 => '/^[\x{1000}-\x{109F}]{1,63}$/iu',
    35 => '/^[\x{10A0}-\x{10FF}]{1,63}$/iu',
    36 => '/^[\x{1100}-\x{11FF}]{1,63}$/iu',
    37 => '/^[\x{1200}-\x{137F}]{1,63}$/iu',
    38 => '/^[\x{13A0}-\x{13FF}]{1,63}$/iu',
    39 => '/^[\x{1400}-\x{167F}]{1,63}$/iu',
    40 => '/^[\x{1680}-\x{169F}]{1,63}$/iu',
    41 => '/^[\x{16A0}-\x{16FF}]{1,63}$/iu',
    42 => '/^[\x{1700}-\x{171F}]{1,63}$/iu',
    43 => '/^[\x{1720}-\x{173F}]{1,63}$/iu',
    44 => '/^[\x{1740}-\x{175F}]{1,63}$/iu',
    45 => '/^[\x{1760}-\x{177F}]{1,63}$/iu',
    46 => '/^[\x{1780}-\x{17FF}]{1,63}$/iu',
    47 => '/^[\x{1800}-\x{18AF}]{1,63}$/iu',
    48 => '/^[\x{1E00}-\x{1EFF}]{1,63}$/iu',
    49 => '/^[\x{1F00}-\x{1FFF}]{1,63}$/iu',
    50 => '/^[\x{2070}-\x{209F}]{1,63}$/iu',
    51 => '/^[\x{2100}-\x{214F}]{1,63}$/iu',
    52 => '/^[\x{2150}-\x{218F}]{1,63}$/iu',
    53 => '/^[\x{2460}-\x{24FF}]{1,63}$/iu',
    54 => '/^[\x{2E80}-\x{2EFF}]{1,63}$/iu',
    55 => '/^[\x{2F00}-\x{2FDF}]{1,63}$/iu',
    56 => '/^[\x{2FF0}-\x{2FFF}]{1,63}$/iu',
    57 => '/^[\x{3040}-\x{309F}]{1,63}$/iu',
    58 => '/^[\x{30A0}-\x{30FF}]{1,63}$/iu',
    59 => '/^[\x{3100}-\x{312F}]{1,63}$/iu',
    60 => '/^[\x{3130}-\x{318F}]{1,63}$/iu',
    61 => '/^[\x{3190}-\x{319F}]{1,63}$/iu',
    62 => '/^[\x{31A0}-\x{31BF}]{1,63}$/iu',
    63 => '/^[\x{31F0}-\x{31FF}]{1,63}$/iu',
    64 => '/^[\x{3200}-\x{32FF}]{1,63}$/iu',
    65 => '/^[\x{3300}-\x{33FF}]{1,63}$/iu',
    66 => '/^[\x{3400}-\x{4DBF}]{1,63}$/iu',
    67 => '/^[\x{4E00}-\x{9FFF}]{1,63}$/iu',
    68 => '/^[\x{A000}-\x{A48F}]{1,63}$/iu',
    69 => '/^[\x{A490}-\x{A4CF}]{1,63}$/iu',
    70 => '/^[\x{AC00}-\x{D7AF}]{1,63}$/iu',
    71 => '/^[\x{D800}-\x{DB7F}]{1,63}$/iu',
    72 => '/^[\x{DC00}-\x{DFFF}]{1,63}$/iu',
    73 => '/^[\x{F900}-\x{FAFF}]{1,63}$/iu',
    74 => '/^[\x{FB00}-\x{FB4F}]{1,63}$/iu',
    75 => '/^[\x{FB50}-\x{FDFF}]{1,63}$/iu',
    76 => '/^[\x{FE20}-\x{FE2F}]{1,63}$/iu',
    77 => '/^[\x{FE70}-\x{FEFF}]{1,63}$/iu',
    78 => '/^[\x{FF00}-\x{FFEF}]{1,63}$/iu',
    79 => '/^[\x{20000}-\x{2A6DF}]{1,63}$/iu',
    80 => '/^[\x{2F800}-\x{2FA1F}]{1,63}$/iu'

);