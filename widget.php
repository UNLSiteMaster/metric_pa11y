<?php
require_once(__DIR__ . '/../../init.php');

header('Content-Type: application/javascript');
$html_code_sniffer_path = 'http://squizlabs.github.io/HTML_CodeSniffer/build/';
$config_file = __DIR__ . '/config/pa11y.json';
$config = array(
    'path' => $html_code_sniffer_path,
    'show' => array(
        'error' => true,
        'warning' => false,
        'notice' => false,
    ),
    'ignoreMsgCodes' => array()
);
if (file_exists($config_file)) {
    $data = file_get_contents($config_file);
    if ($pa11y = json_decode($data)) {
        if (isset($pa11y->ignore)) {
            //Merge in ignored rules
            $config['ignoreMsgCodes'] = $pa11y->ignore;
        }
    }
}
?>
(function ()  {
    var path = '<?php echo $html_code_sniffer_path ?>';
    jQuery.getScript(path + 'HTMLCS.js', function() {
        var options = <?php echo json_encode($config) ?>;
        var standard = 'WCAG2AA';
        HTMLCSAuditor.run(standard, null, options);
    });
})();
