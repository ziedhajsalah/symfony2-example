<?php

namespace EventBundle\Twig;

use EventBundle\Util\DateUtil;

class EventExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'event';
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('ago', [$this, 'calculateAgo'])
        ];
    }

    public function calculateAgo(\DateTime $dateTime)
    {
        return DateUtil::ago($dateTime);
    }
}