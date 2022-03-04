<?php
/*
 *   (c) Semen Alekseev
 *
 *  For the full copyright and license information, please view the LICENSE
 *   file that was distributed with this source code.
 *
 */

use FluffyDollop\Support\Status;

class Security
{
    private static function clean_url(string $url)
    {
        $url = str_replace(array("http://", "https://"), "", strtolower($url));
        if (str_starts_with($url, 'www.')) {
            $url = substr($url, 4);
        }
        $url = explode('/', $url);
        $url = reset($url);
        $url = explode(':', $url);
        return reset($url);

    }

    /**
     * @throws Exception | ErrorException
     */
    function capcha(): void
    {
        if (self::clean_url($_SERVER['HTTP_REFERER']) != self::clean_url($_SERVER['HTTP_HOST'])) {
            die("Hacking attempt!");
        }

        $width = 120;                //Ширина изображения
        $height = 50;                //Высота изображения
        $font_size = 16;            //Размер шрифта
        $let_amount = 5;            //Количество символов, которые нужно набрать
//        $fon_let_amount = 30;		//Количество символов на фоне


        $font = ENGINE_DIR . "/fonts/cour.ttf";    //Путь к шрифту
        if (!file_exists($font)) {
            throw new ErrorException("Невозможно загрузить : " . $font, 0, 0, 'null', 0);
        }

//набор символов
        $letters = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0');

//Цвета для фона
        $background_color = array(random_int(200, 255), random_int(200, 255), random_int(200, 255));

//Цвета для обводки
        $foreground_color = array(random_int(0, 100), random_int(0, 100), random_int(0, 100));

        $src = imagecreatetruecolor($width, $height); //создаем изображение

        $fon = imagecolorallocate($src, $background_color[0], $background_color[1], $background_color[2]); //создаем фон

        imagefill($src, 0, 0, $fon); //заливаем изображение фоном

//то же самое для основных букв
        for ($i = 0; $i < $let_amount; $i++) {
            $color = imagecolorallocatealpha($src, $foreground_color[0], $foreground_color[1], $foreground_color[2], random_int(20, 40)); //Цвет шрифта
            $letter = $letters[random_int(0, count($letters) - 1)];
            $size = random_int($font_size * 2 - 2, $font_size * 2 + 2);
            $x = ($i + 1) * $font_size + random_int(2, 5); //даем каждому символу случайное смещение
            $y = (($height * 2) / 3) + random_int(0, 5);
            $cod[] = $letter; //запоминаем код
            imagettftext($src, $size, random_int(0, 15), $x, $y, $color, $font, $letter);
        }

        $foreground = imagecolorallocate($src, $foreground_color[0], $foreground_color[1], $foreground_color[2]);

        imageline($src, 0, 0, $width, 0, $foreground);
        imageline($src, 0, 0, 0, $height, $foreground);
        imageline($src, 0, $height - 1, $width, $height - 1, $foreground);
        imageline($src, $width - 1, 0, $width - 1, $height, $foreground);

        $cod = $cod ?? null;

        $cod = implode("", $cod); //переводим код в строку

        header("Content-type: image/gif"); //выводим готовую картинку

        imagegif($src);

        $_SESSION['sec_code'] = $cod; //Добавляем код в сессию
    }

    /**
     * @throws JsonException
     */
    function main(): void
    {
        if (self::clean_url($_SERVER['HTTP_REFERER']) != self::clean_url($_SERVER['HTTP_HOST'])) {
            echo 'no';
        }

        $user_code = $_GET['user_code'];

        if ($user_code == $_SESSION['sec_code']) {
            $status = Status::OK;
        } else {
            $status = Status::BAD;
        }
        $response = array(
            'status' => $status,
        );

        _e_json($response);
    }
}