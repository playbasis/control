<?php

//==============================================================
//==============================================================
define("_JPGRAPH_PATH", '../../jpgraph_5/jpgraph/'); // must define this before including mpdf.php file
$JpgUseSVGFormat = true;

define('_MPDF_URI','../'); 	// must be  a relative or absolute URI - not a file system path
//==============================================================
//==============================================================


ini_set("memory_limit","128M");

$html = '
<html>
<head>
</head>
<body style="font-family:Helvetica">
<table width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" class="mail-content" >
    <tbody>
    <tr>
        <td width="100%">
            <table width="100%"  bgcolor="#ffffff" border="0" cellspacing="0" cellpadding="0" class="mail-content">
                <tr>
                    <td width="2%"></td>
                    <td width="51%" height="90">
                        <img src="http://localhost/api//images/playbasis-logo.jpg" >
                    </td>
                    <td width="25%" ></td>
                    <td width="20%" align="right">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="right">
                                    <a href="https://twitter.com/playbasis" target="_blank" style=""><img src="http://localhost/api//images/tw-btn.jpg"></a>
                                    <a href="https://www.facebook.com/Playbasis" target="_blank" style=""><img src="http://localhost/api//images/fb-btn.jpg"></a>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" style="color:#666666;font-size:14px;">
                                    +66-90-098-9153
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="2%">

                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="line-height:0" width="100%">
            <img src="http://localhost/api//images/head-weekly.jpg" width="100%">
        </td>
    </tr>

    <tr>
        <td width="100%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" >
                <tr>
                    <td  width="65%%">
                        <h1 style="color:#000000;font-size:18px;padding:10px;margin:0;"><span style="font-size: 16px">Demo</span><br>Playbasis</h1>
                    </td>
                    <td  width="30%" style="color:#999999;font-size:13px;" align="right">
                        <strong style="color:#0a92d9;font-size:16px;">05 May 2014</strong>
                        to
                        <strong style="color:#0a92d9;font-size:16px;">11 May 2014</strong>
                    </td>
                    <td width="5%"></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>

        </td>
    </tr>
    </tbody>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #cccccc;">
    <tr>
        <td>
            <img src="http://localhost/api//images/icon-user.jpg"><h2 style="display:inline;color:#333333;font-size:27px;"> NEW USERS</h2>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-bottom:1px solid #cccccc;">
    <tr style="color:#999999;font-size:12px" width="100%">
        <td width="33%">Total</td>
        <td width="33%">Daily Average</td>
        <td width="33%">Best day <br>(06 May 2014)</td>
    </tr>
    <tr style="color:#999999;font-size:12px" width="100%">
        <td width="33%">
            <table cellpadding="5" cellspacing="0" border="0">
                <tr>
                    <td><strong style="color:#503091;font-size:28px">18</strong></td>
                    <td align="center" valign="baseline">
                        <img src="http://pbapp.net//images/icon-up.gif"><br><strong style="font-size:12px;color:#95cc00">28.57%</strong>
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%">
            <table cellpadding="5" cellspacing="0" border="0">
                <tr>
                    <td><strong style="color:#999999;font-size:28px">2.57</strong></td>
                    <td align="center" valign="baseline">
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%">
            <table cellpadding="5" cellspacing="0" border="0">
                <tr>
                    <td><strong style="color:#999999;font-size:28px">8</strong></td>
                    <td align="center" valign="baseline">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="100%" cellpadding="20" cellspacing="0" border="0" style="border-bottom:1px solid #cccccc">
    <tr>
        <td width="50%" style="border-right:1px solid #cccccc;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td colspan="2" height="40" valign="top">
                        <img src="http://localhost/api//images/icon-day.jpg" style="vertical-align: middle;"><strong style="font-size:15px;"> DAILY ACTIVE USER (DAU)</strong>
                    </td>
                </tr>
                <tr style="color:#999999;font-size:12px">
                    <td width="60%">Average</td>
                    <td width="40%">Best day <br>(06 May 2014)</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#503091;font-size:28px">5.29</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-up.gif"><br><strong style="font-size:12px;color:#95cc00">2.78%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="5" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:28px">12</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td width="50%"><table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="2" height="40" valign="top">
                    <img src="http://localhost/api//images/icon-month.jpg" style="vertical-align: middle;"><strong style="font-size:15px;"> MONTHLY ACTIVE USER (MAU)</strong>
                </td>
            </tr>
            <tr style="color:#999999;font-size:12px">
                <td width="60%">Average</td>
                <td width="40%">Best day <br>(09 May 2014)</td>
            </tr>
            <tr>
                <td>
                    <table cellpadding="5" cellspacing="0" border="0">
                        <tr>
                            <td><strong style="color:#503091;font-size:28px">18.86</strong></td>
                            <td align="center" valign="baseline">
                                <img src="http://pbapp.net//images/icon-down.gif"><br><strong style="font-size:12px;color:red">-14.84%</strong>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table cellpadding="5" cellspacing="0" border="0">
                        <tr>
                            <td><strong style="color:#999999;font-size:28px">25</strong></td>
                            <td align="center" valign="baseline">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table></td>
    </tr>
</table>

<img src="http://localhost/api//images/shadow.jpg" width="100%" height="15">

<h2 style="margin:0;display:inline-block;background-color:#000000;color:#ffffff;padding:5px 20px"><img src="http://localhost/api//images/icon-actions.jpg"> ACTIONS</h2>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-map-marker.gif" width="40"></td>
        <td width="35%">visit</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">404</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-up.gif"><br><strong style="font-size:12px;color:#95cc00">47.45%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">57.71</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-thumbs-up.gif" width="40"></td>
        <td width="35%">like</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">100</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-up.gif"><br><strong style="font-size:12px;color:#95cc00">5.26%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">14.29</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-share.gif" width="40"></td>
        <td width="35%">share</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">42</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-up.gif"><br><strong style="font-size:12px;color:#95cc00">162.50%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">6.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-bookmark-empty.gif" width="40"></td>
        <td width="35%">read</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">39</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-up.gif"><br><strong style="font-size:12px;color:#95cc00">69.57%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">5.57</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-upload.gif" width="40"></td>
        <td width="35%">uploadimage</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">38</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-down.gif"><br><strong style="font-size:12px;color:red">-47.95%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">5.43</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-money.gif" width="40"></td>
        <td width="35%">payment</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">32</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-up.gif"><br><strong style="font-size:12px;color:#95cc00">68.42%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">4.57</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-comment.gif" width="40"></td>
        <td width="35%">comment</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">26</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-down.gif"><br><strong style="font-size:12px;color:red">-25.71%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">3.71</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-hand-up.gif" width="40"></td>
        <td width="35%">click</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-asterisk.gif" width="40"></td>
        <td width="35%">compare</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-comments.gif" width="40"></td>
        <td width="35%">fbcomment</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-thumbs-up.gif" width="40"></td>
        <td width="35%">fblike</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-facebook.gif" width="40"></td>
        <td width="35%">fbpost</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-facebook-sign.gif" width="40"></td>
        <td width="35%">fbstatus</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-group.gif" width="40"></td>
        <td width="35%">follower</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-plus-sign.gif" width="40"></td>
        <td width="35%">following</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-signin.gif" width="40"></td>
        <td width="35%">login</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-heart.gif" width="40"></td>
        <td width="35%">love</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-asterisk.gif" width="40"></td>
        <td width="35%">menu</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-reorder.gif" width="40"></td>
        <td width="35%">order</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-film.gif" width="40"></td>
        <td width="35%">postvideo</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-ok.gif" width="40"></td>
        <td width="35%">question</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-user.gif" width="40"></td>
        <td width="35%">register</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-flag.gif" width="40"></td>
        <td width="35%">review</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-facebook.gif" width="40"></td>
        <td width="35%">shareviafb</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-twitter.gif" width="40"></td>
        <td width="35%">shareviatwitter</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-cogs.gif" width="40"></td>
        <td width="35%">timeonsite</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-twitter.gif" width="40"></td>
        <td width="35%">tweet</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fa-icon-star.gif" width="40"></td>
        <td width="35%">want</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#56b9d2;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
</table>

<img src="http://localhost/api//images/shadow.jpg" width="100%" height="15">

<h2 style="margin-top:0;display:inline-block;background-color:#000000;color:#ffffff;padding:5px 20px"><img src="http://localhost/api//images/icon-badges.jpg"> BADGES</h2>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/d52663a0fe9cd2e8e3de71e8750d7e2a.png" width="50"></td>
        <td width="35%">Super Active</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">14</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-up.gif"><br><strong style="font-size:12px;color:#95cc00">27.27%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">2.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/05e9a091af0aeb20659e355c5d4bf5ed.png" width="50"></td>
        <td width="35%">Kind Angel</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">2</strong></td>
                                <td align="center" valign="baseline">
                                    <span style="background-color:#95cc00;border-radius:4px;color:#fff;font-size:10px;padding:3px 5px">New</span><br><strong style="font-size:12px;color:#95cc00">100.00%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.29</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/90cd626b60c14c939aecfbfcfcb1d8c9.png" width="50"></td>
        <td width="35%">Magician</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">2</strong></td>
                                <td align="center" valign="baseline">
                                    <img src="http://pbapp.net//images/icon-up.gif"><br><strong style="font-size:12px;color:#95cc00">0.00%</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.29</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/9a4af48769560571ddba10e6442aa9d7.png" width="50"></td>
        <td width="35%">Baby Beginner </td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/0f2840025d83cdd0568cf33bdb05a495.png" width="50"></td>
        <td width="35%">Candy Sharer </td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/9099d760274a059344fd619748719a84.png" width="50"></td>
        <td width="35%">Check-In </td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/a96343408e0f74a5288e39099ef4dacd.png" width="50"></td>
        <td width="35%">Commentator</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/9a5fc0ec9fe44437939c642c8b251f4f.png" width="50"></td>
        <td width="35%">Fantastic</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/3dc9b2e0e6bbc929a8c4263f6c3016da.png" width="50"></td>
        <td width="35%">Friendy User</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/6fdf2fbf7fabe3874960fe4a9310c015.png" width="50"></td>
        <td width="35%">Let\'s Get Community</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/bbbde53986d5f464358b3a780ed11329.png" width="50"></td>
        <td width="35%">Like!</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/8ec11f3d7cc174f6cfe334794d44756f.png" width="50"></td>
        <td width="35%">Night Owl</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/163884415f8ef78d18810a7439814d08.png" width="50"></td>
        <td width="35%">Rising Star</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/78ad3edfe0eb92a22dcd7bd0d480fef6.png" width="50"></td>
        <td width="35%">Shine Bright</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/3d858e49d1cabb118c2d10ed061fa085.png" width="50"></td>
        <td width="35%">Super Fan</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/fb4523eb449aa22efd8798cb99602aaf.png" width="50"></td>
        <td width="35%">Super Reader</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/0c104bbf763a315f1328ea435c79f758.png" width="50"></td>
        <td width="35%">Super Star</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/da3713bb6c86581e506dbfb5b694a0f9.png" width="50"></td>
        <td width="35%">Super User</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/5c417e5cdedc8cc86b9fb2c9f0bed55e.png" width="50"></td>
        <td width="35%">Top Graduate</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="15%" height="75" align="center"><img src="../../../report/images/1ede166cf3436967329f313191f74624.png" width="50"></td>
        <td width="35%">Top Leader</td>
        <td width="50%">
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr style="color:#999999;font-size:11px">
                    <td width="60%">Total</td>
                    <td width="40%">Daily Average</td>
                </tr>
                <tr>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#339966;font-size:24px">0</strong></td>
                                <td align="center" valign="baseline">
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td><strong style="color:#999999;font-size:24px">0.00</strong></td>
                                <td align="center" valign="baseline">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
</table>

<img src="http://localhost/api//images/shadow.jpg" width="100%" height="15">

<h2 style="margin-top:0;display:inline-block;background-color:#000000;color:#ffffff;padding:5px 20px"><img src="http://localhost/api//images/icon-reward.jpg"> REWARD</h2>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="33%">
            <img src="../../../report/images/3a8b0710f93419a69c6f6a1e7d85188f.jpg" width="100">
        </td>
        <td width="66%" colspan="2">
            <h3 style="font-size:14px;margin:0;">Easy Coffee 50% Discount</h3>
            <span style="font-size:10px;color:#999999;">Start</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">29 Apr 2014</span>
            <span style="font-size:10px;color:#999999;">Expire</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">31 Dec 2014</span>
        </td>
    </tr>
    <tr style="color:#999999;font-size:10px">
        <td width="33%">Total Redeemed</td>
        <td width="33%">Eligible Players</td>
        <td width="33%">Redeemed / Total</td>
    </tr>
    <tr>
        <td width="33%"  style="padding-left:10px">
            <table cellpadding="3" cellspacing="0" border="0">
                <tr>
                    <td>
                        <strong style="color:#f7893c;font-size:24px">0</strong>
                    </td>
                    <td align="center" valign="baseline">
                        <br>
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0</strong>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">26 / Inf.</strong>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="33%">
            <img src="../../../report/images/d1d7f04bd54ac3cd3b39e5842faae8b3.jpg" width="100">
        </td>
        <td width="66%" colspan="2">
            <h3 style="font-size:14px;margin:0;">Free 1 Massage Course at Basis Spa and Massage</h3>
            <span style="font-size:10px;color:#999999;">Start</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">29 Apr 2014</span>
            <span style="font-size:10px;color:#999999;">Expire</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">31 Dec 2014</span>
        </td>
    </tr>
    <tr style="color:#999999;font-size:10px">
        <td width="33%">Total Redeemed</td>
        <td width="33%">Eligible Players</td>
        <td width="33%">Redeemed / Total</td>
    </tr>
    <tr>
        <td width="33%"  style="padding-left:10px">
            <table cellpadding="3" cellspacing="0" border="0">
                <tr>
                    <td>
                        <strong style="color:#f7893c;font-size:24px">0</strong>
                    </td>
                    <td align="center" valign="baseline">
                        <br>
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0</strong>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0 / Inf.</strong>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="33%">
            <img src="../../../report/images/dcace4698c9fc551c7c6a7935c2db10b.jpg" width="100">
        </td>
        <td width="66%" colspan="2">
            <h3 style="font-size:14px;margin:0;">Free Cup of Frappe Drink @ Easy Coffee </h3>
            <span style="font-size:10px;color:#999999;">Start</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">29 Apr 2014</span>
            <span style="font-size:10px;color:#999999;">Expire</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">30 Jun 2014</span>
        </td>
    </tr>
    <tr style="color:#999999;font-size:10px">
        <td width="33%">Total Redeemed</td>
        <td width="33%">Eligible Players</td>
        <td width="33%">Redeemed / Total</td>
    </tr>
    <tr>
        <td width="33%"  style="padding-left:10px">
            <table cellpadding="3" cellspacing="0" border="0">
                <tr>
                    <td>
                        <strong style="color:#f7893c;font-size:24px">0</strong>
                    </td>
                    <td align="center" valign="baseline">
                        <br>
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0</strong>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0 / Inf.</strong>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="33%">
            <img src="../../../report/images/9ecd8c799298e76f6d57c3dd81ab0b6f.jpg" width="100">
        </td>
        <td width="66%" colspan="2">
            <h3 style="font-size:14px;margin:0;">PB Airline 25% Discount</h3>
            <span style="font-size:10px;color:#999999;">Start</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">29 Apr 2014</span>
            <span style="font-size:10px;color:#999999;">Expire</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">31 Oct 2014</span>
        </td>
    </tr>
    <tr style="color:#999999;font-size:10px">
        <td width="33%">Total Redeemed</td>
        <td width="33%">Eligible Players</td>
        <td width="33%">Redeemed / Total</td>
    </tr>
    <tr>
        <td width="33%"  style="padding-left:10px">
            <table cellpadding="3" cellspacing="0" border="0">
                <tr>
                    <td>
                        <strong style="color:#f7893c;font-size:24px">0</strong>
                    </td>
                    <td align="center" valign="baseline">
                        <br>
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0</strong>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0 / Inf.</strong>
        </td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="33%">
            <img src="../../../report/images/388ad6d78ed95c9d97f2ad0073457b11.jpg" width="100">
        </td>
        <td width="66%" colspan="2">
            <h3 style="font-size:14px;margin:0;">PB Airline Free Ticket (with in Asia) </h3>
            <span style="font-size:10px;color:#999999;">Start</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">29 Apr 2014</span>
            <span style="font-size:10px;color:#999999;">Expire</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">30 Jun 2014</span>
        </td>
    </tr>
    <tr style="color:#999999;font-size:10px">
        <td width="33%">Total Redeemed</td>
        <td width="33%">Eligible Players</td>
        <td width="33%">Redeemed / Total</td>
    </tr>
    <tr>
        <td width="33%"  style="padding-left:10px">
            <table cellpadding="3" cellspacing="0" border="0">
                <tr>
                    <td>
                        <strong style="color:#f7893c;font-size:24px">0</strong>
                    </td>
                    <td align="center" valign="baseline">
                        <br>
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0</strong>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0 / Inf.</strong>
        </td>
    </tr>
    
    <tr  width="100%">
        <td width="33%">
            <img src="../../../report/images/d821236cf67bb30c0e3dabcbd63ffb90.jpg" width="100">
        </td>
        <td width="66%" colspan="2">
            <h3 style="font-size:14px;margin:0;">Playbasis Mall Gift Voucher (300 Baht)</h3>
            <span style="font-size:10px;color:#999999;">Start</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">29 Apr 2014</span>
            <span style="font-size:10px;color:#999999;">Expire</span>
            <span style="background-color:#0a92d9;border-radius:4px;color:#ffffff;font-size:10px;padding:3px 5px">31 Dec 2014</span>
        </td>
    </tr>
    <tr style="color:#999999;font-size:10px">
        <td width="33%">Total Redeemed</td>
        <td width="33%">Eligible Players</td>
        <td width="33%">Redeemed / Total</td>
    </tr>
    <tr>
        <td width="33%"  style="padding-left:10px">
            <table cellpadding="3" cellspacing="0" border="0">
                <tr>
                    <td>
                        <strong style="color:#f7893c;font-size:24px">0</strong>
                    </td>
                    <td align="center" valign="baseline">
                        <br>
                    </td>
                </tr>
            </table>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0</strong>
        </td>
        <td width="33%">
            <strong style="color:#999999;font-size:24px">0 / Inf.</strong>
        </td>
    </tr>
    
</table>

<img src="http://localhost/api//images/shadow.jpg" width="100%" height="15">

<h2 style="margin:0;display:inline-block;background-color:#000000;color:#ffffff;padding:5px 20px"><img src="http://localhost/api//images/icon-leaderboard.jpg"> THE MOST VALUABLE USERS</h2>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">1.</td>
        <td width="10%" align="center"><img src="../../../report/images/rei7pjiadh.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Mark Knight</td>
        <td width="20%"><span>Exp</span> 5,452</td>
        <td width="15%"><span>Lv</span> 28</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">2.</td>
        <td width="10%" align="center"><img src="../../../report/images/ul6e69n6t0.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Ryota Yasue</td>
        <td width="20%"><span>Exp</span> 40,526</td>
        <td width="15%"><span>Lv</span> 26</td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">3.</td>
        <td width="10%" align="center"><img src="../../../report/images/xdfosjrem0.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Osamu Ogasahara</td>
        <td width="20%"><span>Exp</span> 33,431</td>
        <td width="15%"><span>Lv</span> 25</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">4.</td>
        <td width="10%" align="center"><img src="../../../report/images/user_no_image.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">BOOK IS BUSY </td>
        <td width="20%"><span>Exp</span> 120</td>
        <td width="15%"><span>Lv</span> 12</td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">5.</td>
        <td width="10%" align="center"><img src="../../../report/images/am5afl0j9f.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Jirawat Tiawkittichote</td>
        <td width="20%"><span>Exp</span> 3,976</td>
        <td width="15%"><span>Lv</span> 10</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">6.</td>
        <td width="10%" align="center"><img src="../../../report/images/itg3t94cwo.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">สุข สันต์</td>
        <td width="20%"><span>Exp</span> 3,709</td>
        <td width="15%"><span>Lv</span> 9</td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">7.</td>
        <td width="10%" align="center"><img src="../../../report/images/3dhreyo3gh.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Tanawat Pratyaroongroj</td>
        <td width="20%"><span>Exp</span> 3,502</td>
        <td width="15%"><span>Lv</span> 9</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">8.</td>
        <td width="10%" align="center"><img src="../../../report/images/cat5zqt7jt.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Nat Natcha</td>
        <td width="20%"><span>Exp</span> 1,569</td>
        <td width="15%"><span>Lv</span> 5</td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">9.</td>
        <td width="10%" align="center"><img src="../../../report/images/azgviq7wj9.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Ai Ching</td>
        <td width="20%"><span>Exp</span> 1,477</td>
        <td width="15%"><span>Lv</span> 5</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">10.</td>
        <td width="10%" align="center"><img src="../../../report/images/photo.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Pedro Rodrigues</td>
        <td width="20%"><span>Exp</span> 1,435</td>
        <td width="15%"><span>Lv</span> 5</td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">11.</td>
        <td width="10%" align="center"><img src="../../../report/images/j0ppmvk5vd.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Tangkwa Branford</td>
        <td width="20%"><span>Exp</span> 1,371</td>
        <td width="15%"><span>Lv</span> 5</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">12.</td>
        <td width="10%" align="center"><img src="../../../report/images/photo.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Jenny Shen</td>
        <td width="20%"><span>Exp</span> 971</td>
        <td width="15%"><span>Lv</span> 4</td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">13.</td>
        <td width="10%" align="center"><img src="../../../report/images/c6ity8awzt.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Skakas Branford</td>
        <td width="20%"><span>Exp</span> 940</td>
        <td width="15%"><span>Lv</span> 4</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">14.</td>
        <td width="10%" align="center"><img src="../../../report/images/87g874057s.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Rob Zepeda</td>
        <td width="20%"><span>Exp</span> 896</td>
        <td width="15%"><span>Lv</span> 3</td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">15.</td>
        <td width="10%" align="center"><img src="../../../report/images/9va2socn5d.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Kit Boon</td>
        <td width="20%"><span>Exp</span> 847</td>
        <td width="15%"><span>Lv</span> 3</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">16.</td>
        <td width="10%" align="center"><img src="../../../report/images/9dcayydm4m.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Dolly Samson</td>
        <td width="20%"><span>Exp</span> 845</td>
        <td width="15%"><span>Lv</span> 3</td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">17.</td>
        <td width="10%" align="center"><img src="../../../report/images/who.png" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Harprem Doowa</td>
        <td width="20%"><span>Exp</span> 674</td>
        <td width="15%"><span>Lv</span> 3</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">18.</td>
        <td width="10%" align="center"><img src="../../../report/images/jgaj141t8v.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Pong Tredees</td>
        <td width="20%"><span>Exp</span> 674</td>
        <td width="15%"><span>Lv</span> 3</td>
    </tr>
    
    <tr bgcolor="#f5f5f5" width="100%">
        <td width="5%" align="center">19.</td>
        <td width="10%" align="center"><img src="../../../report/images/photo.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">JAVIER BORDERIAS MAROTO</td>
        <td width="20%"><span>Exp</span> 643</td>
        <td width="15%"><span>Lv</span> 3</td>
    </tr>
    
    <tr  width="100%">
        <td width="5%" align="center">20.</td>
        <td width="10%" align="center"><img src="../../../report/images/photo.jpg" width="40"></td>
        <td width="50%" align="left" style="font-size:12px;">Parkpoom Maneeyod</td>
        <td width="20%"><span>Exp</span> 631</td>
        <td width="15%"><span>Lv</span> 3</td>
    </tr>
    
</table>

<img src="http://pbapp.net//images/shadow.jpg" width="100%">

<div style="background:#424252; width:100%; padding:20px; text-align:center" >
    <a href="http://www.playbasis.com" target="_blank" style="font-family:Helvetica;font-size:12px;color:#999999;padding:20px;text-decoration:none;line-height:40px;">www.playbasis.com</a>
</div>

</body>
</html>';

//==============================================================
//==============================================================
//==============================================================

include("../mpdf.php");

$mpdf=new mPDF('s','A4','','',25,15,21,22,10,10); 

$mpdf->StartProgressBarOutput();

$mpdf->mirrorMargins = 1;
$mpdf->SetDisplayMode('fullpage','two');
$mpdf->list_number_suffix = ')';
$mpdf->hyphenate = true;

$mpdf->debug  = true;

$mpdf->WriteHTML($html);

$mpdf->Output();

exit;
//==============================================================
//==============================================================
//==============================================================
//==============================================================
//==============================================================


?>