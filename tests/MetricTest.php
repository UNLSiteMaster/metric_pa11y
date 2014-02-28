<?php
namespace SiteMaster\Plugins\Metric_pa11y;

class MetricTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getJSON()
    {
        $metric = new Metric('metric_pa11y');

        $json = $metric->getResults('http://wdn.unl.edu/');
        
        $this->assertNotEquals(false, $json);
    }
}
