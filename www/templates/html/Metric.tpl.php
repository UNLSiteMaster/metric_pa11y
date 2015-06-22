<p>
    Pa11y can't catch all accessibility errors. It'll catch many of them, but you should do manual checking as well.
</p>
<p>
    Only <?php echo $context->options['standard'] ?> errors are being reported. To locate and fix errors on your page, drag this link to your browser bookmarks bar:
    <a class="wdn-button" href="javascript:(function(){var e='<?php echo $base_url ?>plugins/metric_pa11y/widget.php';var t='<?php echo $base_url ?>www/js/vendor/jquery.js';var n=function(e,t){var n=document.createElement('script');n.onload=function(){n.onload=null;n.onreadystatechange=null;t.call(this)};n.onreadystatechange=function(){if(/^(complete|loaded)$/.test(this.readyState)===true){n.onreadystatechange=null;n.onload()}};n.src=e;if(document.head){document.head.appendChild(n)}else{document.getElementsByTagName('head')[0].appendChild(n)}};var r=function(){jQuery.getScript(e)};if(typeof jQuery==='undefined'){n(t,function(){r()})}else{r()}})();" onclick="alert('Drag this to your browser Bookmarks Bar to install.  Once installed, you can run the WCAG Bookmarklet over any page.'); return false;">SiteMaster Pa11y</a>, then click the 'HTML CodeSniffer' bookmark to run accessibility tests on any page.  Note that pages are checked at a mobile width.
</p>

<p>
    We are only reporting problems that we can verify with automated testing. <strong>Warnings and notices are being ignored and require manual checking.</strong>  Use the pa11y bookmarklet to identify warnings and notices to help manually check your page.
</p>

<p>
    This service is provided by <a href="http://squizlabs.github.io/HTML_CodeSniffer/">HTML_CodeSniffer</a> and <a href="http://pa11y.org/">pa11y</a>
</p>
