# metric_pa11y
Accessibility metric powered by pa11y

## Installation
You will need to do two things to prepare this metric for installation.

### 1. Install pa11y
If you are running centos or the vagrant box, you can simply run `sudo install/install-centos.sh`.

Otherwise, you will need to follow the [install process](https://github.com/nature/pa11y) outlined by pa11y.

You will know that the install was successful if you can run the `pa11y` command in command line.

### 2. Install composer dependencies
This should be pretty easy.

From the root of the sitemaster project:

1. `cd plugins/metric_pa11y`
2. `composer install`

### 3. Configuration

Edit the `Config::set('PLUGINS', ...` array in `config.inc.php` to include metric_pa11y. It will look something like this:

```
'metric_pa11y' => array(
      //(required) Define the weight of the plugin for grading (you can think of this as the percentage of the total page grade)
      'weight' => 20,
      
      //(optional) Define a custom html_codesniffer url (if you have customized html_codesniffer for example)
      //'html_codesniffer_url' => 'https://webaudit.unl.edu/plugins/metric_pa11y/html_codesniffer/build/'
),
```

## pa11y configuration
You can ignore rules for pa11y as described here: https://github.com/nature/pa11y/blob/master/README.md
The configuration file should reside at `plugins/metric_pa11y/config/pa11y.json`

This plugin also generates a custom HTML_CodeSniffer bookmarklet which only displays errors by default and ignores rules defined in the config file.
The bookmarklet is available in the description for the pa11y metric on page scan pages.

