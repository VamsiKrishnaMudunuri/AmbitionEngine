<?php

namespace App\Services;

use Nahid\Linkify\Linkify;

class LinkRecognition extends Linkify{

    protected function linkifyUrls($text, $options = array('attr' => ''))
    {
        $pattern = '~(?xi)
              (?:
                ((ht|f)tps?://)                    # scheme://
                |                                  #   or
                www\d{0,3}\.                       # "www.", "www1.", "www2." ... "www999."
                |                                  #   or
                www\-                              # "www-"
                |                                  #   or
                [a-z0-9.\-]+\.[a-z]{2,4}(?=/)      # looks like domain name followed by a slash
                |
                [a-z0-9.\-]+\.[a-z]{2,4}  
              )
              (?:                                  # Zero or more:
                [^\s()<>]+                         # Run of non-space, non-()<>
                |                                  #   or
                \(([^\s()<>]+|(\([^\s()<>]+\)))*\) # balanced parens, up to 2 levels
              )*
              (?:                                  # End with:
                \(([^\s()<>]+|(\([^\s()<>]+\)))*\) # balanced parens, up to 2 levels
                |                                  #   or
                [^\s`!\-()\[\]{};:\'".,<>?«»“”‘’]  # not a space or one of these punct chars
              )
        ~';

        $callback = function ($match) use ($options) {
            $caption = $match[0];
            $pattern = "~^(ht|f)tps?://~";

            if (0 === preg_match($pattern, $match[0])) {
                $match[0] = 'http://' . $match[0];
            }

            if (isset($options['callback'])) {
                $cb = $options['callback']($match[0], $caption, false);
                if (!is_null($cb)) {
                    return $cb;
                }
            }

            return '<a href="' . $match[0] . '"' . $options['attr'] . '>' . $caption . '</a>';
        };

        return preg_replace_callback($pattern, $callback, $text);
    }

}