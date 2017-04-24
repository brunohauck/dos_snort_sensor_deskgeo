<!--

/*
Configure menu styles below
NOTE: To edit the link colors, go to the STYLE tags and edit the ssmItems colors
*/
YOffset=0; // no quotes!!
staticYOffset=0; // no quotes!!
XOffset=0; // no quotes!!
slideSpeed=20 // no quotes!!
waitTime=500; // no quotes!! this sets the time the menu stays out for after the mouse goes off it.
menuBGColor="black";
menuIsStatic="no";
menuWidth=100; // Must be a multiple of 10! no quotes!!
menuCols=1;
hdrFontFamily="arial";
hdrFontSize="1";
hdrFontColor="black";
hdrBGColor="#B59766";
hdrAlign="left";
hdrVAlign="center";
hdrHeight="20";
linkFontFamily="arial";
linkFontSize="1";
linkBGColor="white";
linkOverBGColor="#FFFF99";
linkTarget="_top";
linkAlign="left";
barBGColor="#878743";
barFontFamily="arial";
barFontSize="1";
barFontColor="white";
barVAlign="center";
barWidth=12; // no quotes!!
barText='Menu' // <IMG> tag supported, Ex: '<img src="some.gif" border=0>'

// ssmItems[...]=[name, link, target, colspan, endrow?] - leave 'link' and 'target' blank to make a header
addHdr("Snort Report");
addItem("Alerts", "alerts.php", "_top");
addItem("Histogram", "histogram.php", "_top");
addItem("Snort Home", "http://www.snort.org/", "_blank");
addItem("Snort Report Home", "http://www.symmetrixtech.com/", "_blank");

buildMenu();

//-->
