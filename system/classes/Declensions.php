<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

namespace Mozg\classes;

class Declensions
{

    public function __construct(
        public array $declensions
    )
    {
    }

    /**
     * Declension of the word
     * @param int $num
     * @param string $type
     * @return string
     */
    final public function makeWord(int $num, string $type): string
    {
        $str_len_num = strlen($num);
//        if ($num <= 21) {
//            $num = $num;
//        }
        if ($str_len_num == 2) {
            $parse_num = substr($num, 1, 2);
            $num = str_replace('0', '10', $parse_num);
        } elseif ($str_len_num == 3) {
            $parse_num = substr($num, 2, 3);
            $num = str_replace('0', '10', $parse_num);
        } elseif ($str_len_num == 4) {
            $parse_num = substr($num, 3, 4);
            $num = str_replace('0', '10', $parse_num);
        } elseif ($str_len_num == 5) {
            $parse_num = substr($num, 4, 5);
            $num = str_replace('0', '10', $parse_num);
        }

        if ($num == 0) {
            return $this->declensions[$type][0];
        } elseif ($num == 1) {
            return $this->declensions[$type][1];
        } elseif ($num < 5) {
            return $this->declensions[$type][2];
        } elseif ($num < 21) {
            return $this->declensions[$type][3];
        } elseif ($num == 21) {
            return $this->declensions[$type][4];
        }
        return '';
    }

}