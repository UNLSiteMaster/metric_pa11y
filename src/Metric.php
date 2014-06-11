<?php
namespace SiteMaster\Plugins\Metric_pa11y;

use SiteMaster\Core\Auditor\Logger\Metrics;
use SiteMaster\Core\Auditor\MetricInterface;
use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\Auditor\Scan;
use SiteMaster\Core\Auditor\Site\Page;

class Metric extends MetricInterface
{
    /**
     * Default grading method.
     * Start with 100 points.
     * Subtract 10 points for every error, unless a specific value was specified
     */
    const GRADE_METHOD_DEFAULT = 1;
    
    /**
     * Grade as pass fail.
     */
    const GRADE_METHOD_PASS_FAIL = 3;

    /**
     * @param string $plugin_name
     * @param array $options
     */
    public function __construct($plugin_name, array $options = array())
    {
        $options = array_replace_recursive(array(
            'pa11y_path' => 'pa11y',
            'standard' => 'WCAG2AA', //The standard to test against (Section508, WCAG2A, WCAG2AA (default), WCAG2AAA)
            'help_text_general' => 'To locate this error on your page install the bookmarklet found in the metric description and run it on your page.',
            'grading_method' => self::GRADE_METHOD_DEFAULT,
            'point_deductions' => array(
                'default' => 10,
            ),
        ), $options);

        parent::__construct($plugin_name, $options);
    }
    
    /**
     * Get the human readable name of this metric
     *
     * @return string The human readable name of the metric
     */
    public function getName()
    {
        return 'Accessibility - via pa11y.org';
    }

    /**
     * Get the Machine name of this metric
     *
     * This is what defines this metric in the database
     *
     * @return string The unique string name of this metric
     */
    public function getMachineName()
    {
        return 'pa11y';
    }

    /**
     * Determine if this metric should be graded as pass-fail
     *
     * @return bool True if pass-fail, False if normally graded
     */
    public function isPassFail()
    {
        if ($this->options['grading_method'] == self::GRADE_METHOD_PASS_FAIL) {
            return true;
        }

        return false;
    }

    /**
     * Scan a given URI and apply all marks to it.
     *
     * All that this
     *
     * @param string $uri The uri to scan
     * @param \DOMXPath $xpath The xpath of the uri
     * @param int $depth The current depth of the scan
     * @param \SiteMaster\Core\Auditor\Site\Page $page The current page to scan
     * @param \SiteMaster\Core\Auditor\Logger\Metrics $context The logger class which calls this method, you can access the spider, page, and scan from this
     * @throws \Exception
     * @return bool True if there was a successful scan, false if not.  If false, the metric will be graded as incomplete
     */
    public function scan($uri, \DOMXPath $xpath, $depth, Page $page, Metrics $context)
    {
        $results = $this->getREsults($uri);
        $marks = array();
        
        if (!isset($results['results'])) {
            return false;
        }
        
        foreach ($results['results'] as $result) {
            if ($result['type'] != 'error') {
                continue;
            }

            $machine_name = 'pa11y_' . md5($result['code']);
            if (isset($marks[$machine_name])) {
                //Only store one instance of each error (because we are not matching it with exact context)
                continue;
            }

            $help_text = $this->options['help_text_general'];
            
            if (isset($this->options['help_text'][$result['code']])) {
                $help_text .= $this->options['help_text'][$result['code']];
            }

            $techniques = $this->getTechniques($result['code']);
            if (!empty($techniques)) {
                $help_text .= PHP_EOL . '***' . PHP_EOL . 'View WCAG documentation for techniques used to find this error:' . PHP_EOL;
                foreach ($techniques as $technique) {
                    $help_text .= PHP_EOL . ' * [' . $technique . '](http://www.w3.org/TR/WCAG20-TECHS/' . $technique . ')';
                }
            }
            
            $marks[$machine_name] = true;
            
            $mark = $this->getMark($machine_name, $result['message'], $this->getPointsForCode($result['code']), $result['message'], $help_text);

            $page->addMark($mark);
        }

        return true;
    }

    /**
     * Get the techniques used to find the error
     * 
     * @param string $code
     * @return array - array of technique codes
     */
    public function getTechniques($code)
    {
        $parts = explode('.', $code);
        
        if (!isset($parts[4])) {
            return array();
        }
        
        return explode(',', $parts[4]);
    }

    /**
     * Get the points to deduct for a given code
     * 
     * @param string $code
     * @return int
     */
    public function getPointsForCode($code)
    {
        if (isset($this->options['point_deductions'][$code])) {
            return $this->options['point_deductions'][$code];
        }
        
        return $this->options['point_deductions']['default'];
    }

    /**
     * Get the results for a given uri
     * 
     * @param $uri
     * @return bool|mixed
     */
    public function getResults($uri)
    {
        $command = $this->options['pa11y_path'] . ' -r json -s ' . escapeshellarg($this->options["standard"]);
        
        $config_file = dirname(__DIR__) . '/config/pa11y.json';
        if (file_exists($config_file)) {
            $command .= ' --config ' . $config_file;
        }
        echo $config_file . PHP_EOL;
        
        $command .= ' ' . escapeshellarg($uri);

        list($exitStatus, $json, $stderr) = \Hiatus\exec($command, array(), 30);
        
        if (!$data = json_decode($json, true)) {
            return false;
        }
        
        return $data;
    }
}
