<?php

namespace Celtic34fr\ContactCore\Tests\Widget;

use Bolt\Widget\Injector\AdditionalTarget;
use Bolt\Widget\Injector\RequestZone;
use Celtic34fr\ContactCore\Widget\CourrielsWidget;
use PHPUnit\Framework\TestCase;

class CourrielsWidgetTest extends TestCase
{
    public function testWidget()
    {
        $widget = new CourrielsWidget();
        $this->assertSame($widget->getName(), 'Contact Courriels Widget');
        $this->assertEquals(300, $widget->getPriority());
        $this->assertEquals(0, $widget->getCacheDuration());

        $this->assertSame($widget->getTemplate(), '@contactcore/widget/courriels.html.twig');
        $this->assertEquals(RequestZone::FRONTEND, $widget->getZone());
        $this->assertEquals(ADDITIONALTARGET::WIDGET_BACK_DASHBOARD_ASIDE_TOP, $widget->getTargets());
    }
}