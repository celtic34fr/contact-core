<?php

namespace Celtic34fr\ContactCore\Widget;

use Bolt\Widget\BaseWidget;
use Bolt\Widget\Injector\AdditionalTarget as ADDITIONALTARGET;
use Bolt\Widget\Injector\RequestZone;
use Bolt\Widget\TwigAwareInterface;

class CourrielsWidget extends BaseWidget implements TwigAwareInterface
{
    public function __construct()
    {
        $this->name = 'Contact Courriels Widget';
        $this->target = ADDITIONALTARGET::WIDGET_BACK_DASHBOARD_ASIDE_TOP;
        $this->priority = 300;
        $this->template = '@contactcore/widget/courriels.html.twig';
        $this->zone = RequestZone::FRONTEND;
        $this->cacheDuration = 0;
    }
 
    public function run(array $params = []): ?string
    {
        return parent::run([]);
    }
}