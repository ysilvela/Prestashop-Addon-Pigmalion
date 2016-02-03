{*
* 2013-2015 BeTechnology Solutions Ltd
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to info@betechnology.es so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author    BeTechnology Solutions Ltd <info@betechnology.es>
* @copyright 2013-2015 BeTechnology Solutions Ltd
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{if !empty($server_address) && !empty($account_name)}
<script type="text/javascript">
    //<![CDATA[
    (function(){
        var sa = "{$server_address|escape:"javascript":"UTF-8"}";
        {literal}function a(a){
        	var b,c,d=window.document.createElement("iframe");
        d.src="javascript:false",(d.frameElement||d).style.cssText="width: 0; height: 0; border: 0";
        var e=window.document.createElement("div");
        e.style.display="none";
        var f=window.document.createElement("div");
        e.appendChild(f),window.document.body.insertBefore(e,window.document.body.firstChild),f.appendChild(d);
        try{c=d.contentWindow.document}catch(g){
        	b=document.domain,d.src="javascript:var d=document.open();d.domain='"+b+"';void(0);",c=d.contentWindow.document}
        	return c.open()._l=function(){b&&(this.domain=b);
        		var c=this.createElement("scr".concat("ipt"));
        	c.src=a,this.body.appendChild(c)},c.write("<bo".concat('dy onload="document._l();">')),c.close(),d}var b="tiresiasjs";
        	window[b]=window[b]||function(a){(window[b].q=window[b].q||[]).push(a)},window[b].l=new Date;
        	var c=function(d,e){
        		if(!document.body)return setTimeout(function(){c(d,e)},30);
        	e=e||{},window[b].o=e;
        	console.log("Desde header_embed-script.tpl se invoca a la funcion API de include");
        var f=document.location.protocol,g=["https:"===f?f:"http:","//",e.host||sa,e.path||"/include/",d].join("");
        a(g)};window[b].init=c{/literal}
    })();
    tiresiasjs.init("{$account_name|escape:"javascript":"UTF-8"}");
    //]]>
</script>
{/if}
