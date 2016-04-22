<?php
/**
 # ################################################################################
 # Project:   PHP Link Directory
 #
 # **********************************************************************
 # Copyright (C) 2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
 #
 # This software is for use only to those who have purchased a license.
 # A license must be purchased for EACH installation of the software.
 #
 # By using the software you agree to the terms:
 #
 #    - You may not redistribute, sell or otherwise share this software
 #      in whole or in part without the consent of the the ownership
 #      of PHP Link Directory. Please contact david@david-duval.com
 #      if you need more information.
 #
 #    - You agree to retain a link back to http://www.phplinkdirectory.com/
 #      on all pages of your directory if you purchased any of our "link back"
 #      versions of the software.
 #
 #
 # In some cases, license holders may be required to agree to changes
 # in the software license before receiving updates to the software.
 # **********************************************************************
 #
 # For questions, help, comments, discussion, etc., please join the
 # PHP Link Directory Forum http://www.phplinkdirectory.com/forum/
 #
 # @link           http://www.phplinkdirectory.com/
 # @copyright      2004-2012 NetCreated, Inc. (http://www.netcreated.com/)
 # @projectManager David DuVal <david@david-duval.com>
 # @package        PHPLinkDirectory
 # @version        5.0 Codename Transformer
 # ################################################################################
 */


/**
 * Create an image of a given phrase for image verification (captcha)
 * @author Constantin Bejenaru / Boby <constantin_bejenaru@frozenminds.com> (http://www.frozenminds.com)
 */
class CAPTCHA
{
   /**
    * @shortdesc Path to folder with fonts (with trailing slash!).
    *            This must be readable by PHP.
    * @type string
    */
   var $Fonts_Folder = '../libs/captcha/fonts/';

   /**
    * @shortdesc The minimum size a character should have
    * @type integer
    */
   var $minsize = 30;

   /**
    * @shortdesc The maximum size a character should have
    * @type integer
    */
   var $maxsize = 40;

   /**
    * @shortdesc The maximum degrees of an angle a character should be rotated.
    *            A value of 20 means a random rotation between -20 and 20.
    * @type integer
    */
   var $angle = 30;

   /**
    * @shortdesc The background color of the image in HTML
    * @type string
    */
   var $background_color = "#F98F06";

   /**
    * @shortdesc The image type, supported "png" [default], "jpeg" and "gif"
    * @type string
    */
   var $image_type = "png";

   /**
    * @shortdesc Number of characters the image should include
    * @type integer
    */
   var $phrase_length = 6;

   /**
    * @shortdesc Distorsion level of the image
    * @type integer
    */
   var $image_distorsion_level = 3;

   /**
    * @shortdesc The type of characters for generating random phrase.
    *            Available options: - "alphanumeric" = "ABC..abc...0123.."
    *                               - "alphabetical" = "ABC..abc..."
    *                               - "numeric" = "0123..."
    * @type integer
    */
   var $phrase_type = "alphabetical";

   /**
    * @shortdesc The case of the generated random phrase
    *            Available options: - "default" = both, lowercase and uppercase
    *                               - "lowercase"
    *                               - "uppercase"
    * @type integer
    */
   var $phrase_case = "default";

   /**
    * @shortdesc Create the simplest captcha possible
    *            For users who's servers the other versions can't run,
    *            because of neighter TTF or GD font support
    * @type bool
    */
   var $simple_captcha = 0;

   /**
    * @shortdesc Make sure it's an admin/basic preview
    * @type integer
    */
   var $preview_captcha = 0;

   /**
    * @shortdesc Database tablename to store verification string
    * @type string
    */
   var $db_tablename = '';

/***********************************************************************************/
/*** !! DO NOT CHANGE FROM NOW ON, ONLY IF YOU REALLY KNOW WHAT YOU ARE DOING !! ***/
/***********************************************************************************/

   /**
    * @shortdesc Database object
    * @type object
    */
   var $db = null;

   /**
    * @shortdesc A List with available fonts
    * @type mixed[array|string]
    */
   var $Fonts_Range;

   /**
    * @shortdesc The GD font widths
    * @type mixed[array|int]
    */
   var $gd_font_widths = array();

   /**
    * @shortdesc The total width of the image
    * @type integer
    */
   var $image_width = 0;

   /**
    * @shortdesc The total height of the image
    * @type integer
    */
   var $image_height = 0;

   /**
    * @shortdesc Information about the installed GD version and it's libraries
    * @type misc[array/string/integer]
    */
   var $gd_info;

   /**
    * @shortdesc Generated red color
    * @type integer
    */
   var $color_r;

   /**
    * @shortdesc Generated green color
    * @type integer
    */
   var $color_g;

   /**
    * @shortdesc Generated blue color
    * @type integer
    */
   var $color_b;

   /**
    * @shortdesc Value of the random phrase
    * @type string
    */
   var $phrase;

   /**
    * @shortdesc Constructor/ Initialize the class and configuration
    */
   function CAPTCHA($settings=null)
   {
      /* Test for installed GD library and the FreeType library */
      if (!extension_loaded ('gd'))
         $this->error('no_gd');

      if (function_exists ('gd_info'))
      {
         $this->gd_info = gd_info();
         preg_match ('/[\d]/', $this->gd_info['GD Version'], $match);
         $this->gd_info['GD Version'] = $match[0];
      }
      else
         $this->gd_info = $this->get_gd_info();

      if ($this->gd_info['GD Version'] == 0)
         $this->error('no_gd');

      if ($this->gd_info['PNG Support'] == 0 && $this->gd_info['JPG Support'] == 0 && $this->gd_info['GIF Create Support'] == 0)
         $this->error('no_picture_lib');

      /* Load custom settings */
      if (isset ($settings) && !empty ($settings))
      {
         $this->load_settings($settings);
         unset ($settings);
         $this->check_settings();
      }

      if (!$this->simple_captcha)
      {
         /* Get fonts */
         if ($this->gd_info['FreeType Support'] == 0) /* If FreeType is not supported, search for GDF fonts */
            $font_extension = "gdf,GDF";
         else /* If FreeType is supported, search for TTF fonts */
            $font_extension = "ttf,TTF";
         $this->get_fonts($this->Fonts_Folder,$font_extension);

         /* Check fonts */
         if (is_array ($this->Fonts_Range))
         {
            $temp = array();
            foreach ($this->Fonts_Range as $font)
            {
               if (is_readable ($this->Fonts_Folder.$font))
               {
                  $temp[] = $this->Fonts_Folder.$font;
                  if($this->gd_info['FreeType Support'] == 0)
                  {
                     $handle = @ fopen ($this->Fonts_Folder.$font,"r");
                     $content = @ fread ($handle,12);
                     $this->gd_font_widths[] = ord ($content{8}) + ord ($content{9}) + ord ($content{10}) + ord ($content{11});
                     @ fclose($handle);
                  }
               }
            }
            if (count ($temp) < 1)
            {
               $this->simple_captcha = 1;
            }

         }
         else
            if (!is_readable ($this->Fonts_Folder.$this->Fonts_Range))
            {
               $this->simple_captcha = 1;
            }

         unset ($temp,$font_extension);

         /* Check angle and repair if needed */
         $temp_angle = $this->angle;
         while ($temp_angle < 0)
            $temp_angle += 360;
         if ($temp_angle > 359)
            $temp_angle = $temp_angle % 360;

         $this->angle = $temp_angle;
         unset ($temp_angle);
      }
   }


   /**
    * @shortdesc Create the CAPTCHA security image
    */
   function create_CAPTCHA()
   {
      //Clean phrase
      $this->phrase = preg_replace ('[\s]', '', $this->phrase);

      //Change case
      switch ($this->phrase_case)
      {
         case "lowercase" :
            $this->phrase = strtolower ($this->phrase);
            break;
         case "uppercase" :
            $this->phrase = strtoupper ($this->phrase);
            break;
         case "default" :
         default :
            break;
      }
      //Determine phrase length
      $this->phrase_length = strlen ($this->phrase);

      if (!empty ($this->simple_captcha))
      {
         $this->image_width = 125;
         $this->image_height = 30;
      }
      else
      {
         //Set image size if not available
         if (empty ($this->image_width) || $this->image_width < 5)
         {
//             $this->image_width = intval(($this->phrase_length + 1) * intval(($this->maxsize + $this->minsize) / 1.7));
            $this->image_width = intval (($this->phrase_length + 1) * intval (($this->maxsize + $this->minsize) / 1.9));
         }

         if (empty ($this->image_height) || $this->image_height < 5)
         {
            $this->image_height = intval (2.5 * $this->maxsize);
         }
      }

      /* Create an empty image */
      if ($this->gd_info['GD Version'] >= 2)
         $image = @ imagecreatetruecolor($this->image_width, $this->image_height)
                  or $this->error('gd_initialize');
      else
         $image = @ imagecreate($this->image_width, $this->image_height)
                  or $this->error('gd_initialize');

      $this->image_width = intval (imagesx ($image));
      $this->image_height = intval (imagesy ($image));

      /* Set general background */
      $bg_color = imagecolorallocate ($image, 255, 255, 255);
      imagefill ($image, 0, 0, $bg_color);

      /* Distort image by selected distorsion level */
      if ($this->image_distorsion_level == 2 || $this->image_distorsion_level > 2)
      {
         if (empty($this->background_color) || $this->background_color == "random")
         {
            $this->generate_random_color(200, 255);
            $this->background_color = imagecolorallocate ($image, $this->color_r, $this->color_g, $this->color_b);
            imagefill ($image, 0, 0, $this->background_color);
         }
         elseif ($this->background_color == "gradient")
            $this->gradient_background($image);
         else
         {
            $this->hex_to_rgb($this->background_color);
            $this->background_color = imagecolorallocate ($image, $this->color_r, $this->color_g, $this->color_b);
            imagefill ($image, 0, 0, $this->background_color);
         }
      }
      if ($this->image_distorsion_level == 3 || $this->image_distorsion_level > 3)
      {
         $this->pixelize($image);
      }
      if ($this->image_distorsion_level == 4 || $this->image_distorsion_level > 4)
      {
         $this->sine_wave($image);
         $this->cosine_wave($image);
      }

      /* Write secret phrase */
      if ($this->simple_captcha)
      {
         $this->simple_draw_phrase($image);
      }
      elseif ($this->gd_info['FreeType Support'])
      {
         $this->freetype_draw_phrase($image);
      }
      elseif (!$this->gd_info['FreeType Support'])
      {
         $this->gd_draw_phrase($image);
      }

      if ($this->image_distorsion_level >= 5)
      {
         $this->horizontal_grid($image);
         $this->vertical_grid($image);
         $this->desaturate($image);
      }

      $this->noise($image);

      //Send image to output
      if(!headers_sent())
      {
         switch($this->image_type)
         {
            case "jpeg":
            @ header('Content-transfer-encoding: binary');
            @ header('Content-disposition: inline; filename=image.jpg');
            @ header("Content-Type: image/jpeg");
            @ imagejpeg($image,'',100);
            break;

            case "gif":
            @ header('Content-transfer-encoding: binary');
            @ header('Content-disposition: inline; filename=image.gif');
            @ header("Content-Type: image/gif");
            @ imagegif($image);
            break;

            case "png":
            default:
            @ header('Content-transfer-encoding: binary');
            @ header('Content-disposition: inline; filename=image.png');
            @ header("Content-Type: image/png");
            @ imagepng($image);
            break;
         }
         //Delete image from memory
         @ imagedestroy($image);
      }
      else
         $this->error('headers_sent');
   }

   /**
    * @shortdesc Draw phrase with TTF fonts [BEST]
    */
   function freetype_draw_phrase(&$image)
   {
      for ($i = 0, $i < $x = intval (mt_rand ($this->minsize, $this->maxsize)) / 2; $i < $this->phrase_length; $i++)
      {
         $font = $this->get_random_font();
         $size = intval (mt_rand ($this->minsize, $this->maxsize));
         $box = imagettfbbox ($size, $angle, $this->Fonts_Folder.$font, $this->phrase{$i});
         $y = ($box[3] - $box[7]) + 20;
         $this->generate_random_color(0, 80);
         $color = imagecolorallocate ($image, $this->color_r, $this->color_g, $this->color_b);
         @ imagettftext ($image, $size, $angle, $x, $y - intval ($size / 20), $color, $this->Fonts_Folder.$font, $this->phrase{$i});
         $x += ($box[4] - $box[0]) + intval (mt_rand (5, 10));
      }
      unset ($font, $size, $box, $x, $y, $color, $i, $this->phrase, $this->Fonts_Folder, $this->Fonts_Range);
   }

   /**
    * @shortdesc Draw phrase with GD fonts because of no FreeType support
    */
   function gd_draw_phrase(&$image)
   {
      $x = mt_rand (5, 30);
      for ($i = 0; $i < $this->phrase_length; $i++)
      {
         $font = $this->get_random_font();
         $handle = @ fopen ($this->Fonts_Folder.$font, "r");
         $content = @ fread ($handle,12);
         $font_width = ord ($content{8}) + ord ($content{9}) + ord ($content{10}) + ord ($content{11});
         @ fclose ($handle);
         $y = mt_rand (intval ($this->image_height / 3), $this->image_height - $font_width * 2);
         $this->generate_random_color(0,80);
         $color = imagecolorallocate ($image, $this->color_r, $this->color_g, $this->color_b);
         $font = imageloadfont ($this->Fonts_Folder.$font);
         imagestring ($image, $font, $x, $y, $this->phrase{$i}, $color);
         $x += $font_width + mt_rand (2,10);
      }
      unset ($handle, $content, $font, $font_width, $x, $y, $color, $i, $this->phrase, $this->Fonts_Folder, $this->Fonts_Range);
   }

   /**
    * @shortdesc Draw phrase with a built-in font because of no FreeType and GD font support
    */
   function simple_draw_phrase(&$image)
   {
      $x = mt_rand(5, intval ($this->image_width / 2.5));
      $y = intval ($this->image_height / 2.5);
      $this->generate_random_color(0, 80);
      $color = imagecolorallocate ($image, $this->color_r, $this->color_g, $this->color_b);
      imagestring ($image, 5, $x, $y, $this->phrase, $color);
      unset ($x, $y, $color, $this->phrase);
   }

   /**
    * @shortdesc Create noise, use as one of the last distorsion functions
    * ThanX: http://www.hudzilla.org/phpbook/read.php/11_2_22
    */
   function &noise(&$image)
   {
      for ($i = 0; $i < $this->image_width; $i++)
         for ($j = 0; $j < $this->image_height; $j++)
            if (mt_rand(0, 1))
            {
               $rgb = imagecolorat ($image, $i, $j);
               $red = ($rgb >> 16) & 0xFF;
               $green = ($rgb >> 8) & 0xFF;
               $blue = $rgb & 0xFF;
               $modifier = mt_rand(-20,20);
               $red += $modifier;
               $green += $modifier;
               $blue += $modifier;

               if ($red > 255) $red = 255;
               if ($green > 255) $green = 255;
               if ($blue > 255) $blue = 255;
               if ($red < 0) $red = 0;
               if ($green < 0) $green = 0;
               if ($blue < 0) $blue = 0;

               $newcol = imagecolorallocate ($image, $red, $green, $blue);
               imagesetpixel ($image, $i, $j, $newcol);
            }
      unset ($i, $j, $rgb, $red, $green, $blue, $modifier, $newcol);
   }

   /**
    * @shortdesc Fill the image with random pixels
    */
   function &pixelize(&$image)
   {
      for ($i = 0; $i < $this->image_width * $this->image_width / 20; $i++)
      {
         $this->generate_random_color(0, 150);
         $color = imagecolorallocate ($image, 0, 0, 0);
         imagesetpixel ($image, mt_rand (0, $this->image_width), mt_rand (0, $this->image_height), $color);
      }
      unset ($i, $color);
   }

   /**
    * @shortdesc Gradient background
    */
   function &gradient_background(&$image)
   {
      for ($i = 0; $i <= $this->image_width; $i++)
         for ($j = 0; $j <= $this->image_height; $j++)
         {
            $color = imagecolorallocate ($image, 255, $i, $j);
            imagesetpixel ($image, $i, $j, $color);
         }
      unset ($i, $j, $color);
   }

   /**
    * @shortdesc Interlace effect
    */
   function &interlace(&$image)
   {
      $color = imagecolorallocate ($image, 0, 0, 0);
      for ($i = 1; $i < $this->image_height; $i += 2)
         imageline ($image, 0, $i, $this->image_width, $i, $color);
      unset ($i,$color);
   }

   /**
    * @shortdesc Desaturate image / convert to grayscale
    * ThanX: http://www.hudzilla.org/phpbook/read.php/11_2_20
    */
   function &desaturate(&$image)
   {
      for ($i = 0; $i < $this->image_width; $i++)
         for ($j = 0; $j < $this->image_height; $j++)
         {
            $rgb = imagecolorat ($image, $i, $j);
            $red = ($rgb >> 16) & 255;
            $green = ($rgb >> 8) & 255;
            $blue = $rgb & 255;
            $grey = (int)(($red + $green + $blue) / 3);
            $newcolor = imagecolorallocate ($image, $grey,$grey,$grey);
            imagesetpixel ($image, $i, $j, $newcolor);
         }
      unset ($i, $j, $rgb, $red, $green, $blue, $grey, $newcolor);
   }

   /**
    * @shortdesc Draw a horizontal grid
    */
   function &horizontal_grid(&$image)
   {
      for ($i = 0; $i <= $this->image_height ; $i += 10)
      {
         $this->generate_random_color(0, 170);
         $color = imagecolorallocate ($image, $this->color_r, $this->color_g, $this->color_b);
         imageline ($image, 0, $i, $this->image_width , $i, $color);
      }
      unset ($x,$color);
   }

   /**
    * @shortdesc Draw a vertical grid
    */
   function &vertical_grid(&$image)
   {
      for ($i = 0; $i <= $this->image_width ; $i += 10)
      {
         $this->generate_random_color(0, 170);
         $color = imagecolorallocate ($image, $this->color_r, $this->color_g, $this->color_b);
         imageline ($image, $i, 0, $i, $this->image_height , $color);
      }
      unset ($i, $color);
   }

   /**
    * @shortdesc Draw a sine wave
    * ThanX: http://www.php.net/manual/en/function.imageline.php#9345
    */
   function &sine_wave(&$image)
   {
      $wavenum = 3;
      $wavemultiplier = ($wavenum * 360) / $this->image_width ;
      $curX = 0;
      $curY = 0;
      for ($pt = 0; $pt < $this->image_width ; $pt++)
      {
         $newX = $curX + 1;
         $newY = ($this->image_height /2) + (sin (deg2rad ($newX * $wavemultiplier - 90)) * ($this->image_height / 2));
         $this->generate_random_color(0, 100);
         $color = imagecolorallocate ($image, $this->color_r, $this->color_g, $this->color_b);
         imageline ($image, $curX, $curY, $newX, $newY, $color);
         $curX = $newX;
         $curY = $newY;
      }
      unset($wavenum, $wavemultiplier, $curX,$curY, $newX, $newY, $color, $pt);
   }

   /**
    * @shortdesc Draw a cosine wave
    * ThanX: http://www.php.net/manual/en/function.imageline.php#9345
    */
   function &cosine_wave(&$image)
   {
      $wavenum = 3;
      $wavemultiplier = ($wavenum * 360) / $this->image_width ;
      $curX = 0;
      $curY = $this->image_height ;
      for ($pt = 0; $pt < $this->image_width ; $pt++)
      {
         $newX = $curX + 1;
         $newY = ($this->image_height / 2) + (cos (deg2rad ($newX * $wavemultiplier)) * ($this->image_height /2));
         $this->generate_random_color(0, 100);
         $color = imagecolorallocate ($image, $this->color_r, $this->color_g, $this->color_b);
         imageline ($image, $curX, $curY, $newX, $newY, $color);
         $curX = $newX;
         $curY = $newY;
      }
      unset ($wavenum, $wavemultiplier, $curX, $curY, $newX, $newY, $color, $pt);
   }

   /**
    * @shortdesc Convert HEX color to RGB
    */
   function hex_to_rgb($string="ffffff")
   {
      $string = str_replace ("#", "", $string);
      sscanf ($string, "%2x%2x%2x", $this->color_r, $this->color_g, $this->color_b);
   }


   /**
    * @shortdesc Create random colors
    */
   function generate_random_color($min=120, $max=200)
   {
      $this->color_r = intval (mt_rand ($min, $max));
      $this->color_g = intval (mt_rand ($min, $max));
      $this->color_b = intval (mt_rand ($min, $max));
   }

   /**
    * @shortdesc Choose a random font, either TTF or GD
    */
   function get_random_font()
   {
      if (is_array ($this->Fonts_Range))
      {
         shuffle ($this->Fonts_Range);
         return $this->Fonts_Range[mt_rand(0, count ($this->Fonts_Range) - 1)];
      }
      elseif (is_string ($this->Fonts_Range))
         return $this->Fonts_Range;
      else
         $this->error('no_font_select');
   }

   /**
    * @shortdesc Load settings into class
    */
   function load_settings($settings)
   {
      if (is_array ($settings))
      {
         foreach($settings as $key => $value)
            $settings[$key] = trim ($value);

         $supported_image_type  = array ('png', 'jpg', 'gif');
         $supported_phrase_type = array ('alphanumeric', 'alphabetical', 'numeric');
         $supported_phrase_case = array ('default', 'uppercase', 'lowercase');

         $this->Fonts_Folder = (!empty ($settings['Fonts_Folder']) ? $settings['Fonts_Folder'] : $this->Fonts_Folder);
         $this->minsize = (preg_match ('`^\d+$`', $settings['minsize']) ? intval ($settings['minsize']) : $this->minsize);
         $this->maxsize = (preg_match ('`^\d+$`', $settings['maxsize']) ? intval ($settings['maxsize']) : $this->maxsize);
         $this->angle = (preg_match ('`^\d+$`', $settings['angle']) ? intval ($settings['angle']) : $this->angle);
         $this->background_color = (!empty ($settings['background_color']) ? $settings['background_color'] : $this->background_color);
         $this->image_type = (in_array ($settings['image_type'], $supported_image_type) ? $settings['image_type']: $this->image_type);
         $this->image_distorsion_level = (preg_match ('`^\d+$`', $settings['image_distorsion_level']) ? intval ($settings['image_distorsion_level']) : $this->image_distorsion_level);
         $this->phrase_case = (in_array ($settings['phrase_case'], $supported_phrase_case) ? $settings['phrase_case']: $this->phrase_case);
         $this->simple_captcha = ($settings['simple_captcha'] == 1 ? 1 : 0);
      }
      unset ($settings);
   }

   /**
    * @shortdesc Check and repair loaded settings for variables that are not checked within other functions
    */
   function check_settings()
   {
      $this->minsize = ($this->minsize > 10 ? $this->minsize : 10);
      $this->minsize = ($this->minsize < 60 ? $this->minsize : 60);

      $this->maxsize = ($this->maxsize > 10 ? $this->maxsize : 10);
      $this->maxsize = ($this->maxsize < 60 ? $this->maxsize : 60);

      if ($this->maxsize < $this->minsize)
      {
         $temp = $this->maxsize;
         $this->maxsize = $this->minsize;
         $this->minsize = $temp;
         unset ($temp);
      }
   }

   /**
    * @shortdesc Read content of fontdirectory and populate the Fonts_Range array with font names
    */
   function get_fonts($dirname="fonts/", $extension="ttf,TTF")
   {
      $extension = str_replace (" ", "", $extension);
      $ext = explode (",", $extension);
      if ($handle = @ opendir ($dirname))
      {
         while (false !== ($file = @ readdir ($handle)))
            for ($i = 0; $i < sizeof ($ext); $i++)
               if (strstr ($file, ".".$ext[$i]) && !empty ($ext[$i]))
                   $this->Fonts_Range[] = $file;

         @ closedir ($handle);
      }
      unset ($extension, $ext, $handle, $dirname, $file);
   }

   /**
    * @shortdesc Display an error message and alternatively kill the script
    */
   function error($message="")
   {
      if($message == "no_gd")
         die ("**Error** - There is no GD library installed or enabled. The CAPTCHA security image cannot be used!");
      elseif($message == "no_font")
         die ("**Error** - No TrueTypeFont available for the PHP Captcha security image.");
      elseif($message == "gd_initialize")
         die ("**Error** - Cannot Initialize new GD image stream!");
      elseif($message == "headers_sent")
         die ("**Error** - Headers were already sent!");
      elseif($message == "no_font_select")
         die ("**Error** - Could not select random font!");
      elseif($message == "no_picture_lib")
         die ("**Error** - No library for creating image. Need 'libpng', 'libjpeg' or 'libgif'!");
      else
         die ("**Error** - An error occured!");
   }

    /**
     * @shortdesc Gather information about the installed GD version and it's libraries
     */
   function get_gd_info()
   {
      $array = array (
                     "GD Version"         => "",
                     "FreeType Support"   => 0,
                     "FreeType Support"   => 0,
                     "FreeType Linkage"   => "",
                     "T1Lib Support"      => 0,
                     "GIF Read Support"   => 0,
                     "GIF Create Support" => 0,
                     "JPG Support"        => 0,
                     "PNG Support"        => 0,
                     "WBMP Support"       => 0,
                     "XBM Support"        => 0
                     );
      $gif_support = 0;

      ob_start ();
      eval ("phpinfo();");
      $info = ob_get_contents ();
      ob_end_clean ();

      foreach (explode ("\n", $info) as $line)
      {
         if (strpos ($line, "GD Version") !== false)
         {
            $array["GD Version"] = trim (str_replace ("GD Version", "", strip_tags ($line)));
            preg_match ('/[\d]/', $array["GD Version"], $match);
            $array["GD Version"] = $match[0];
         }
         if(strpos($line, "FreeType Support") !== false)
            $array["FreeType Support"] = trim (str_replace ("FreeType Support", "", strip_tags ($line)));
         if(strpos($line, "FreeType Linkage") !== false)
            $array["FreeType Linkage"] = trim (str_replace ("FreeType Linkage", "", strip_tags ($line)));
         if(strpos($line, "T1Lib Support") !== false)
            $array["T1Lib Support"] = trim (str_replace ("T1Lib Support", "", strip_tags ($line)));
         if(strpos($line, "GIF Read Support") !== false)
            $array["GIF Read Support"] = trim (str_replace ("GIF Read Support", "", strip_tags ($line)));
         if(strpos($line, "GIF Create Support") !== false)
            $array["GIF Create Support"] = trim (str_replace ("GIF Create Support", "", strip_tags ($line)));
         if(strpos($line, "GIF Support") !== false)
            $gif_support = trim (str_replace ("GIF Support", "", strip_tags ($line)));
         if(strpos($line, "JPG Support") !== false)
            $array["JPG Support"] = trim (str_replace ("JPG Support", "", strip_tags ($line)));
         if(strpos($line, "PNG Support") !== false)
            $array["PNG Support"] = trim (str_replace ("PNG Support", "", strip_tags ($line)));
         if(strpos($line, "WBMP Support") !== false)
            $array["WBMP Support"] = trim (str_replace ("WBMP Support", "", strip_tags ($line)));
         if(strpos($line, "XBM Support") !== false)
            $array["XBM Support"] = trim (str_replace ("XBM Support", "", strip_tags ($line)));
      }

      if ($gif_support === "enabled")
      {
         $array["GIF Read Support"]  = 1;
         $array["GIF Create Support"] = 1;
      }

      if ($array["FreeType Support"] === "enabled")
         $array["FreeType Support"] = 1;
      else
         $array["FreeType Support"] = 0;

      if ($array["T1Lib Support"] === "enabled")
         $array["T1Lib Support"] = 1;
      else
         $array["T1Lib Support"] = 0;

      if ($array["GIF Read Support"] === "enabled")
         $array["GIF Read Support"] = 1;
      else
         $array["GIF Read Support"] = 0;

      if ($array["GIF Create Support"] === "enabled")
         $array["GIF Create Support"] = 1;
      else
         $array["GIF Create Support"] = 0;

      if ($array["JPG Support"] === "enabled")
         $array["JPG Support"] = 1;
      else
         $array["JPG Support"] = 0;

      if ($array["PNG Support"] === "enabled")
         $array["PNG Support"] = 1;
      else
         $array["PNG Support"] = 0;

      if ($array["WBMP Support"] === "enabled")
         $array["WBMP Support"] = 1;
      else
         $array["WBMP Support"] = 0;

      if ($array["XBM Support"] === "enabled")
         $array["XBM Support"] = 1;
      else
         $array["XBM Support"] = 0;

      if ($array["JIS-mapped Japanese Font Support"] === "enabled")
         $array["JIS-mapped Japanese Font Support"] = 1;
      else
         $array["JIS-mapped Japanese Font Support"] = 0;

      return $array;
   }
}

?>