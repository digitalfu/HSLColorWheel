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

  const RGB_MAX_DECIMAL_VALUE = 255;

  public static $instance;
  private $hex;
  private $hue;
  private $saturation;
  private $lightness;
  private $red;
  private $green;
  private $blue;

  function __construct($hex = FALSE) {
    $hex = str_replace("#", "", $hex);
    $this->hex = $hex;
    if ($this->hex) {
      $this->_parse_hex_to_hls($this->hex);
    }
    return $this;
  }

  public static function instance($name = FALSE) {
    if (!self::$instance) {
      self::$instance = new ColorWheel($name);
    }
    return self::$instance;
  }

  public function hsl($formatted = FALSE) {

    if ($formatted) {
      return array(
        'hue' => 360 * $this->hue,
        'saturation' => $this->saturation * 100,
        'lightness' => $this->lightness * 100
      );
    } else {
      return array('hue' => $this->hue, 'saturation' => $this->saturation, 'lightness' => $this->lightness);
    }

  }

  public function hex() {
    $this->_parse_rgb_to_hex();
    return "#" . $this->hex;
  }

  /* Color Parsers
  ***************************************************/
  private function _parse_rgb_to_hex() {
    $this->hex = $this->_rbg_to_hex($this->red, $this->green, $this->blue);
  }

  private function _parse_hsl_to_rgb() {
    $test = $this->_hsl_to_rgb($this->hue, $this->saturation, $this->lightness);
    list($this->red, $this->green, $this->blue) = $test;

  }

  private function _parse_hex_to_hls($hex) {
    list($this->red, $this->green, $this->blue) = $this->_hex_to_rgb($hex);

    list($this->hue, $this->saturation, $this->lightness) = $this->_rgb_to_hsl($this->red, $this->green, $this->blue);
  }

  /* Color Converters
  ***************************************************/

  private function _hsl_to_rgb($hue, $saturation, $lightness) {

    if ($saturation == 0) {
      $red = $lightness * self::RGB_MAX_DECIMAL_VALUE;
      $green = $lightness * self::RGB_MAX_DECIMAL_VALUE;
      $blue = $lightness * self::RGB_MAX_DECIMAL_VALUE;

    } else {

      // Determine if it is a light or dark color (why?) Need to find out what these values are
      if ($lightness < 0.5) {
        $value_2 = $lightness * (1 + $saturation);
      } else {
        $value_2 = ($lightness + $saturation) - ($saturation * $lightness);
      }

      $value_1 = 2 * $lightness - $value_2;

      $red = self::RGB_MAX_DECIMAL_VALUE * $this->_hue_to_rgb($value_1, $value_2, $hue + (1 / 3));
      $green = self::RGB_MAX_DECIMAL_VALUE * $this->_hue_to_rgb($value_1, $value_2, $hue);
      $blue = self::RGB_MAX_DECIMAL_VALUE * $this->_hue_to_rgb($value_1, $value_2, $hue - (1 / 3));
    }
    return array($red, $green, $blue);
  }

  private function _hue_to_rgb($v1, $v2, $vH) {
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

  private function _rgb_to_hsl($red, $green, $blue) {

    $red = $red / 255;
    $green = $green / 255;
    $blue = $blue / 255;

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
      if ($lightness < 0.5) {
        $saturation = $delta_max / ($max_color + $min_color);
      } else {
        $saturation = $delta_max / (2 - $max_color - $min_color);
      };

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

  private function _hex_to_rgb($hex) {
    $red = hexdec(substr($hex, 0, 2));
    $green = hexdec(substr($hex, 2, 2));
    $blue = hexdec(substr($hex, 4, 2));
    return array($red, $green, $blue);
  }

  private function _rbg_to_hex($red, $green, $blue) {
    return (str_pad(dechex(intval(round($red))), 2, "0", STR_PAD_LEFT) .
      str_pad(dechex(intval(round($green))), 2, "0", STR_PAD_LEFT) .
      str_pad(dechex(intval(round($blue))), 2, "0", STR_PAD_LEFT));
  }

  public function lightness($amt) {

    $amt = $amt / 100;
    $this->lightness += $amt;

    if ($this->lightness < 0) {
      $this->lightness = 0;
    } else {
      if ($this->lightness > 1) {
        $this->lightness = 1;
      }
    }
    $this->_parse_hsl_to_rgb();

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

    $this->_parse_hsl_to_rgb();

    return $this;
  }


} // End of Color Wheel Class






