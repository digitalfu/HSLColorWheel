<?php

/**
 *
 *
 *
 *  Example Usage:
 *
 *  Get a color 10% lighter than the one entered
 *  ColorWheel::instance($color)->lightness(10)->hex();
 *
 *
 */

/**
 * Class HSLColorWheel
 *
 */
class HSLColorWheel {

  /**
   * Maximum RBG color value
   * @const int
   */
  const RGB_MAX_VALUE = 255;

  /**
   * Minimum RBG color value
   * @const int
   */
  const RGB_MIN_VALUE = 0;

  /**
   * Minimum Hue degree value
   * @const int
   */
  const HSL_MIN_VALUE = 0;

  /**
   * Maximum Hue degree value
   * @const int
   */
  const HUE_MAX_VALUE = 360;

  /**
   * Maximum Lightness value
   * @const int
   */
  const LIGHTNESS_MAX_VALUE = 1;

  /**
   * Maximum Saturation value
   * @const int
   */
  const SATURATION_MAX_VALUE = 1;

  /**
   * Percentage factor (multiplier)
   * @const int
   */
  const PERCENTAGE_FACTOR = 100;

  /**
   * Decimal Divisor
   * @const int
   */
  const DECIMAL_DIVISOR = 100;

  /**
   * Hexadecimal Color Code Parse Regex
   * @const string
   */
  const HEX_COLOR_CODE_PARSE_REGEX = '/([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2,2})/';

  /**
   * Hexadecimal Color Code
   * @var mixed
   */
  private $hex;

  /**
   * Hue degree value (real number)
   * @var float
   */
  private $hue;

  /**
   * Saturation Real number
   * @var float
   */
  private $saturation;

  /**
   * Lightness Real number
   * @var float
   */
  private $lightness;

  /**
   * Red 8 bit integer value
   * @var int
   */
  private $red;

  /**
   * Green 8 bit integer value
   * @var int
   */
  private $green;

  /**
   * Blue 8 bit integer value
   * @var int
   */
  private $blue;

  /**
   * Get instance helper
   *
   * Gets a new HSLColorWheel instance thus allowing for method chaining without the 'new' keyword
   *
   * @param bool $name
   * @return HSLColorWheel
   */
  public static function instance($name = FALSE) {
    return new HSLColorWheel($name);
  }

  /**
   * Constructor
   * @param bool $hex
   */
  function __construct($hex) {

      $this->hex = $hex;

      $this->_parseHexToHls($this->hex);


    return $this;
  }

  private function _parseHex($hex){

    try{

      $hex = $this->_removeLeadingHashChar($hex);

    } catch(){


    }



  }




  /**
   * To formatted HSL
   * @return array
   */
  public function toHSLFormatted() {
    return array(
      'hue' => $this->hue * self::HUE_MAX_VALUE,
      'saturation' => $this->saturation * self::PERCENTAGE_FACTOR,
      'lightness' => $this->lightness * self::PERCENTAGE_FACTOR
    );
  }

  /**
   * To raw HSL
   * @return array
   */
  public function toHSL() {
    return array(
      'hue' => $this->hue,
      'saturation' => $this->saturation,
      'lightness' => $this->lightness
    );
  }

  /**
   * To Hexadecimal Color Value
   * @return string
   */
  public function toHex() {
    return $this->_addLeadingHashChar(
      $this->_parseRgbToHex($this->red, $this->green, $this->blue)
    );
  }

  /**
   * Add Leading Hash Character
   * @param $string
   * @return mixed
   */
  private function _addLeadingHashChar($string){
    return sprintf("#%s", $string);
  }

  /**
   * Remove Leading Hash Character
   * @param $string
   * @return mixed
   */
  private function _removeLeadingHashChar($string){
    return str_replace("#", "", $string);
  }

  /**
   *
   *
   */
  private function _parseRgbToHex($red, $green, $blue) {
    return $this->_rbgToHex($red, $green, $blue);
  }

  /**
   * Parse HSL values to RBG
   *
   * Parses HSL to RGB and stores the values in the member variables
   *
   * @param $hue
   * @param $saturation
   * @param $lightness
   */
  private function _parseHslToRgb($hue, $saturation, $lightness) {
    list($this->red, $this->green, $this->blue) = $this->_hslToRgb($hue, $saturation, $lightness);
  }

  /**
   *
   * @param $hex
   */
  private function _parseHexToHls($hex) {
    list($this->red, $this->green, $this->blue) = $this->_hexToRgb($hex);
    list($this->hue, $this->saturation, $this->lightness) = $this->_rgbToHsl($this->red, $this->green, $this->blue);
  }



  private function _hslToRgb($hue, $saturation, $lightness) {

    if ($saturation == 0) {
      $red = $lightness * self::RGB_MAX_VALUE;
      $green = $lightness * self::RGB_MAX_VALUE;
      $blue = $lightness * self::RGB_MAX_VALUE;

    } else {

      // Determine if it is a light or dark color (why?) Need to find out what these values are
      if ($lightness < 0.5) {
        $value_2 = $lightness * (1 + $saturation);
      } else {
        $value_2 = ($lightness + $saturation) - ($saturation * $lightness);
      }

      $value_1 = 2 * $lightness - $value_2;

      $red = self::RGB_MAX_VALUE * $this->_hueToRgb($value_1, $value_2, $hue + (1 / 3));
      $green = self::RGB_MAX_VALUE * $this->_hueToRgb($value_1, $value_2, $hue);
      $blue = self::RGB_MAX_VALUE * $this->_hueToRgb($value_1, $value_2, $hue - (1 / 3));
    }
    return array($red, $green, $blue);
  }

  /**
   *
   * @param $v1
   * @param $v2
   * @param $vH
   * @return mixed
   */

  private function _hueToRgb($v1, $v2, $vH) {
    if ($vH < 0) {
      $vH += 1;
    }
    if ($vH > 1) {
      $vH -= 1;
    }
    if ((6 * $vH) < 1) {
      return ($v1 + ($v2 - $v1) * 6 * $vH);
    }
    if ((2 * $vH) < 1) {
      return ($v2);
    }
    if ((3 * $vH) < 2) {
      return ($v1 + ($v2 - $v1) * ((2 / 3) - $vH) * 6);
    }
    return ($v1);
  }


  /**
   * @param $red
   * @param $green
   * @param $blue
   * @return array
   */
  private function _rgbToHsl($red, $green, $blue) {

    $red = $red / self::RGB_MAX_VALUE;
    $green = $green / self::RGB_MAX_VALUE;
    $blue = $blue / self::RGB_MAX_VALUE;

    // Determine the maximum and minimum color
    $min_color = min($red, $green, $blue);
    $max_color = max($red, $green, $blue);

    $delta_max = $max_color - $min_color;

    $lightness = ($max_color + $min_color) / 2;

    // If is a shade of grey, no chroma
    if ($delta_max == 0) {

      $hue = 0;
      $saturation = 0;

    // If is a color, has chroma
    } else {

      if ($lightness < 0.5)
        $saturation = $delta_max / ($max_color + $min_color);
      else
        $saturation = $delta_max / (2 - $max_color - $min_color);


      $delta_red = ((($max_color - $red) / 6) + ($delta_max / 2)) / $delta_max;
      $delta_green = ((($max_color - $green) / 6) + ($delta_max / 2)) / $delta_max;
      $delta_blue = ((($max_color - $blue) / 6) + ($delta_max / 2)) / $delta_max;

      $hue = 0;

      if ($max_color == $red) {
        $hue = $delta_blue - $delta_green;
      } elseif ($max_color == $green) {
        $hue = (1 / 3) + $delta_red - $delta_blue;
      } elseif ($max_color == $blue) {
        $hue = (2 / 3) + $delta_green - $delta_red;
      }

      if ($hue < 0) {
        $hue += 1;
      }
      if ($hue > 1) {
        $hue -= 1;
      }
    }

    return array($hue, $saturation, $lightness);
  }


  /**
   * @param $hex
   * @return array
   */
  private function _hexToRgb($hex) {
    try {
      return array(
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2))
      );
    } catch(HexFormatException $hfe){


    }

  }


  /**
   * Convert given RGB values to hexadecimal value
   * @param $red
   * @param $green
   * @param $blue
   * @return string
   */
  private function _rbgToHex($red, $green, $blue) {
    return sprintf(
      "%2d%2d%2d",
      dechex($this->_roundFloatToInt($red)),
      dechex($this->_roundFloatToInt($green)),
      dechex($this->_roundFloatToInt($blue))
    );
  }

  /**
   * Round a given float before returning the integer value
   * @param $float
   * @return int
   */
  private function _roundFloatToInt($float){
    return intval(round($float));
  }



  public function lightness($amt) {

    $amt = $amt / 100;
    $this->lightness = $amt;

    if ($this->lightness < 0) {
      $this->lightness = 0;
    } else {
      if ($this->lightness > 1) {
        $this->lightness = 1;
      }
    }
    $this->_parseHslToRgb();

    return $this;
  }


  public function saturation($amt) {

    $amt = $amt / 100;
    $this->saturation += $amt;

    if ($this->saturation < 0) {
      $this->saturation = 0;
    } else {
      if ($this->saturation > 1) {
        $this->saturation = 1;
      }
    }

    $this->_parseHslToRgb();

    return $this;
  }


}


class HexFormatException extends Exception{

}





