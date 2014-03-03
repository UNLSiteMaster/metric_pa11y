<p>
    Pa11y can't catch all accessibility errors. It'll catch many of them, but you should do manual checking as well.
</p>
<p>
    Only distinct <?php echo $context->options['standard'] ?> errors are being reported.  Warnings and notices are bing ingored and requiare manual checking.  To locate and fix errors on your page, drag this link to your browser bookmarks bar:
    <a href="javascript:(function()%20{var%20_p='http://squizlabs.github.io/HTML_CodeSniffer/build/';var%20_i=function(s,cb)%20{var%20sc=document.createElement('script');sc.onload%20=%20function()%20{sc.onload%20=%20null;sc.onreadystatechange%20=%20null;cb.call(this);};sc.onreadystatechange%20=%20function(){if(/^(complete|loaded)$/.test(this.readyState)%20===%20true){sc.onreadystatechange%20=%20null;sc.onload();}};sc.src=s;if%20(document.head)%20{document.head.appendChild(sc);}%20else%20{document.getElementsByTagName('head')[0].appendChild(sc);}};%20var%20options={path:_p,show:{error: true,warning: false,notice: false}};_i(_p+'HTMLCS.js',function(){HTMLCSAuditor.run('WCAG2AA',null,options);});})();" onclick="alert('Drag this to your browser Bookmarks Bar to install.  Once installed, you can run the WCAG Bookmarklet over any page.'); return false;">HTML CodeSniffer</a>, then click the 'HTML CodeSniffer' bookmark to run accessibility tests on any page.
</p>

<p>
    This service is provided by <a href="http://squizlabs.github.io/HTML_CodeSniffer/">HTML_CodeSniffer</a> and <a href=="http://pa11y.org/">pa11y</a>
</p>
