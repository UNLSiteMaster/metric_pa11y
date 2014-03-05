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
     * @param string $plugin_name
     * @param array $options
     */
    public function __construct($plugin_name, array $options = array())
    {
        $options = array_merge_recursive($options, array(
            'pa11y_path' => 'pa11y',
            'standard' => 'WCAG2AA', //The standard to test against (Section508, WCAG2A, WCAG2AA (default), WCAG2AAA)
            'help_text_general' => 'To locate this error on your page install the bookmarklet found in the metric description and run it on your page.'
        ));

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
        return true;
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
            
            $marks[$machine_name] = true;
            
            $mark = $this->getMark($machine_name, $result['code'], 1, $result['message'], $help_text);

            $page->addMark($mark);
        }

        return true;
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
        
        $json = exec(escapeshellcmd($command));
        
        if (!$data = json_decode($json, true)) {
            return false;
        }
        
        return $data;
    }
}
