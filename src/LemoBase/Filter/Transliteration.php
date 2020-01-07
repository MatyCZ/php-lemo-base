<?php

namespace LemoBase\Filter;

use Laminas\Filter\AbstractFilter;

class Transliteration extends AbstractFilter
{
    /**
     * Unique ID prefix used for allowing comments
     */
    const UNIQUE_ID_PREFIX = '__LemoBase_Filter_Transliteration__';

    /**
     * Defined by Laminas\Filter\FilterInterface
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if (setlocale(LC_ALL, '0') == 'C') {
            setlocale(LC_ALL, \Locale::getDefault());
        }

        $transliterator = \Transliterator::createFromRules(':: Any-Latin; :: Latin-ASCII;', \Transliterator::FORWARD);

        return $transliterator->transliterate($value);
    }
}
