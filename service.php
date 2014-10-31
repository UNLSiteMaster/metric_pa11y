<?php
require_once(__DIR__ . '/../../init.php');
$metric_pa11y = \SiteMaster\Core\Plugin\PluginManager::getManager()->getPluginInfo('metric_pa11y');
$options = $metric_pa11y->getOptions();

function getResults($uri, $plugin_options)
{
    $command = $plugin_options['pa11y_path']
        . ' -r json'
        . ' -s ' . escapeshellarg($plugin_options["standard"])
        . ' -c ' . escapeshellarg($plugin_options['html_codesniffer_url'] . 'HTMLCS.js');

    $config_file = __DIR__ . '/config/pa11y.json';
    if (file_exists($config_file)) {
        $command .= ' --config ' . $config_file;
    }

    $command .= ' ' . escapeshellarg($uri);

    /**
     * Execute with a 25 second timeout (just under the default of 30).
     * one or more of pa11y, phantomjs, or node have an issue where child process are not being killed and turn into zombies.
     * $this->exec aims to curb that problem by manually setting a timeout.
     */
    $command_helper = new \SiteMaster\Plugins\Metric_pa11y\CommandHelper();
    list($exitStatus, $output, $stderr) = $command_helper->exec($command, 25);
    
    return $output;
}

header('Content-Type: application/json');

if (!isset($_GET['page'])) {
    throw new \SiteMaster\Core\InvalidArgumentException('A URL is required', 400);
}

if (!$page = \SiteMaster\Core\Auditor\Site\Page::getByID($_GET['page'])) {
    throw new \SiteMaster\Core\InvalidArgumentException('Page Not Found', 400);
}

echo getResults($page->uri, $options);