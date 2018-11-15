<?php

namespace App\Services;

use Utility as Util;
use Illuminate\Support\HtmlString;
use Illuminate\Translation\Translator as Trans;

use Illuminate\Contracts\Support\Htmlable;


class Translator extends Trans {

    /**
     *
     * @param  string  $id
     * @param  array   $parameters
     * @param  string  $domain
     * @param  string  $locale
     *
     * @return \Symfony\Component\Translation\TranslatorInterface|string
     */

    public function transSmart($id = null, $default = null, $isHtml = false, $parameters = [], $domain = 'messages', $locale = null){

        $translatedValue = $this->trans($id, $parameters, $domain, $locale);

        if(Util::hasString($translatedValue) && strcmp($translatedValue, $id) == 0){
            $translatedValue = $default;
        }

        return Util::hasString($translatedValue) ? (($isHtml) ? new HtmlString($translatedValue) : $translatedValue)  : $translatedValue;
    }
}