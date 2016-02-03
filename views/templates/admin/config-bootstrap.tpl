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

<div class="tw-bs {$tiresiastagging_ps_version_class|escape:'htmlall':'UTF-8'}">
    <div class="container-fluid">
        <div class="row">
            <form class="tiresiastagging" role="form" action="{$tiresiastagging_form_action|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate="">
                <input type="hidden" id="tiresiastagging_current_language" name="tiresiastagging_current_language" value="{$tiresiastagging_current_language.id_lang|escape:'htmlall':'UTF-8'}">
                <div class="panel panel-default">
                    {if count($tiresiastagging_languages) > 1 || $tiresiastagging_account_authorized}
                        <div class="panel-heading">
                            <div class="col-xs-8">
                                {if count($tiresiastagging_languages) > 1}
                                    <label for="tiresiastagging_language">{l s='Manage accounts:' mod='tiresiastagging'}
                                        <select class="form-control" id="tiresiastagging_language">
                                            {foreach from=$tiresiastagging_languages item=language}
                                                <option value="{$language.id_lang|escape:'htmlall':'UTF-8'}" {if $language.id_lang == $tiresiastagging_current_language.id_lang}selected="selected"{/if}>
                                                    {$language.name|escape:'htmlall':'UTF-8'}
                                                </option>
                                            {/foreach}
                                        </select>
                                    </label>
                                {/if}
                            </div>
                            <div class="col-xs-4 text-right">
                                {if $tiresiastagging_account_authorized}
                                    <a href="#" id="tiresiastagging_account_setup">{l s='Account setup' mod='tiresiastagging'}
                                        <span class="glyphicon glyphicon-cog">&nbsp;</span>
                                    </a>
                                {/if}
                            </div>
                        </div>
                    {/if}
                    <div class="panel-body text-center">
                        {if $tiresiastagging_account_authorized}
                            <div id="tiresiastagging_installed" style="{if !empty($iframe_url)}display: none;{/if}">
                                <h2>{$translations.tiresiastagging_installed_heading|escape:'htmlall':'UTF-8'}</h2>
                                <p>{$translations.tiresiastagging_installed_subheading|escape:'htmlall':'UTF-8'}</p>
                                <div class="panes">
                                    <p>{l s='If you want to change the account, you need to remove the existing one first' mod='tiresiastagging'}</p>
                                    {if !empty($iframe_url)}
                                        <a id="tiresiastagging_back_to_iframe" class="btn btn-default" role="button">{l s='Back' mod='tiresiastagging'}</a>
                                    {/if}
                                    <button type="submit" onclick="return confirm('{l s='Are you sure you want to uninstall Tiresias?' mod='tiresiastagging'}');"
                                            value="1" class="btn btn-red" name="submit_tiresiastagging_reset_account">{l s='Remove Tiresias' mod='tiresiastagging'}</button>
                                </div>
                            </div>
                            {if !empty($iframe_url)}
                                <iframe id="tiresiastagging_iframe" frameborder="0" width="100%" scrolling="no" src="{$iframe_url|escape:'htmlall':'UTF-8'}"></iframe>
                            {/if}
                        {else}
                            <div class="row-fluid">
                                <div class="col-md-6 col-md-push-6 right-block">
                                    <div class="content-block">
                                        <div class="content-panel">
                                            <div class="panel panel-default panel-install">
                                                <div class="panel-body">
                                                    <div class="login-block">
                                                        <img src="https://tiresias-betechnology.rhcloud.com/img/logo.png" class="img-logo">
                                                        <h2 class="h4 content-header">{l s='Unlock Your 14-Day Free Trial' mod='tiresiastagging'}</h2>
                                                        <p class="content-subheader">{$translations.tiresiastagging_not_installed_subheading|escape:'htmlall':'UTF-8'}</p>
                                                        <div class="panes">
                                                            <div id="tiresiastagging_new_account_group">
                                                                <div class="form-group">
                                                                    <input type="text" name="tiresiastagging_account_email" placeholder="{l s='Your email address' mod='tiresiastagging'}"
                                                                           value="{$tiresiastagging_account_email|escape:'htmlall':'UTF-8'}">
                                                                </div>
                                                                <button type="submit" value="1" class="btn btn-blue" name="submit_tiresiastagging_new_account">{l s='Install' mod='tiresiastagging'}</button>
                                                            </div>
                                                            <div id="tiresiastagging_existing_account_group" class="link-wrap">
                                                                {l s='If you already have a Tiresias account,' mod='tiresiastagging'}
                                                                <button type="submit" value="1" class="btn-link" name="submit_tiresiastagging_authorize_account">{l s='click here' mod='tiresiastagging'}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="terms-block">
                                                {l s='By installing you agree to Tiresias\'s' mod='tiresiastagging'} <a href="http://www.tiresias.com/terms" target="_blank">{l s='Terms and Conditions' mod='tiresiastagging'}</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-md-pull-6">
                                    <div class="content-block">
                                        <div class="content-panel">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="row">
                                                        <div class="col-sxs-12">
                                                            <h2>{l s='Welcome to Tiresias.' mod='tiresiastagging'}</h2>
                                                            <!-- extras.platforms.install.welcomeTiresias-->
                                                            <p class="content-text">
                                                                {l s='A full personalization solution, Tiresias is the easiest way to deliver your customers personalized shopping experiences - wherever they are. ' mod='tiresiastagging'} <br><br>
                                                                {l s='Join the 10,000+ retailers, in over 100 countries, who are using Tiresias to delight their customers and grow their business.' mod='tiresiastagging'}
                                                            <!-- extras.platforms.install.installMessage -->
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sxs-12 col-sm-6">
                                                            <h6>{l s='Facebook Ads' mod='tiresiastagging'}</h6>
                                                            <!-- extras.platforms.install.facebookAds -->
                                                            <img src="https://tiresias-betechnology.rhcloud.com/img/install-feature-facebook.jpg" alt="" class="img-responsive">
                                                        </div>
                                                        <div class="col-sxs-12 col-sm-6">
                                                            <h6>{l s='Product Recommendations' mod='tiresiastagging'}</h6>
                                                            <!-- extras.platforms.install.productRecommendations -->
                                                            <img src="https://tiresias-betechnology.rhcloud.com/img/install-feature-recommendations.jpg" alt="" class="img-responsive">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sxs-12 col-sm-6">
                                                            <h6>{l s='Behavioural Pop-ups' mod='tiresiastagging'}</h6>
                                                            <!-- extras.platforms.install.behaviouralPopups -->
                                                            <img src="https://tiresias-betechnology.rhcloud.com/img/install-feature-popups.jpg" alt="" class="img-responsive">
                                                        </div>
                                                        <div class="col-sxs-12 col-sm-6">
                                                            <h6>{l s='Triggered Emails' mod='tiresiastagging'}</h6>
                                                            <!-- extras.platforms.install.triggeredEmails -->
                                                            <img src="https://tiresias-betechnology.rhcloud.com/img/install-feature-recommendations.jpg" class="img-responsive">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>