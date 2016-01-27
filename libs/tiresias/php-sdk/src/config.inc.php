<?php
/**
 * Copyright (c) 2015, BeTechnology Solutions Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its contributors
 * may be used to endorse or promote products derived from this software without
 * specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author BeTechnology Solutions Ltd <info@betechnology.es>
 * @copyright 2015 BeTechnology Solutions Ltd
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 */

// Interfaces
require_once(dirname(__FILE__).'/interfaces/account/TiresiasAccountInterface.php');
require_once(dirname(__FILE__).'/interfaces/account/TiresiasAccountMetaDataBillingDetailsInterface.php');
require_once(dirname(__FILE__).'/interfaces/account/TiresiasAccountMetaDataIframeInterface.php');
require_once(dirname(__FILE__).'/interfaces/account/TiresiasAccountMetaDataInterface.php');
require_once(dirname(__FILE__).'/interfaces/account/TiresiasAccountMetaDataOwnerInterface.php');

require_once(dirname(__FILE__).'/interfaces/order/TiresiasOrderBuyerInterface.php');
require_once(dirname(__FILE__).'/interfaces/order/TiresiasOrderInterface.php');
require_once(dirname(__FILE__).'/interfaces/order/TiresiasOrderPurchasedItemInterface.php');
require_once(dirname(__FILE__).'/interfaces/order/TiresiasOrderStatusInterface.php');

require_once(dirname(__FILE__).'/interfaces/TiresiasOAuthClientMetaDataInterface.php');
require_once(dirname(__FILE__).'/interfaces/TiresiasProductInterface.php');
require_once(dirname(__FILE__).'/interfaces/TiresiasExportCollectionInterface.php');
require_once(dirname(__FILE__).'/interfaces/TiresiasValidatableInterface.php');

// Classes
require_once(dirname(__FILE__).'/classes/http/TiresiasHttpRequest.php'); // Must be loaded before `TiresiasApiRequest`
require_once(dirname(__FILE__).'/classes/TiresiasObject.php');

require_once(dirname(__FILE__).'/classes/api/TiresiasApiRequest.php');
require_once(dirname(__FILE__).'/classes/api/TiresiasApiToken.php');

require_once(dirname(__FILE__).'/classes/collection/TiresiasCollection.php');
require_once(dirname(__FILE__).'/classes/collection/TiresiasProductCollection.php');
require_once(dirname(__FILE__).'/classes/collection/TiresiasOrderCollection.php');

require_once(dirname(__FILE__).'/classes/exception/TiresiasException.php');
require_once(dirname(__FILE__).'/classes/exception/TiresiasHttpException.php');

require_once(dirname(__FILE__).'/classes/export/TiresiasExporter.php');
require_once(dirname(__FILE__).'/classes/export/TiresiasExportProductCollection.php');
require_once(dirname(__FILE__).'/classes/export/TiresiasExportOrderCollection.php');

require_once(dirname(__FILE__).'/classes/helper/TiresiasHelper.php');
require_once(dirname(__FILE__).'/classes/helper/TiresiasHelperDate.php');
require_once(dirname(__FILE__).'/classes/helper/TiresiasHelperIframe.php');
require_once(dirname(__FILE__).'/classes/helper/TiresiasHelperPrice.php');

require_once(dirname(__FILE__).'/classes/http/TiresiasHttpRequestAdapter.php');
require_once(dirname(__FILE__).'/classes/http/TiresiasHttpRequestAdapterCurl.php');
require_once(dirname(__FILE__).'/classes/http/TiresiasHttpRequestAdapterSocket.php');
require_once(dirname(__FILE__).'/classes/http/TiresiasHttpResponse.php');

require_once(dirname(__FILE__).'/classes/oauth/TiresiasOAuthClient.php');
require_once(dirname(__FILE__).'/classes/oauth/TiresiasOAuthToken.php');

require_once(dirname(__FILE__).'/classes/operation/TiresiasOperationProduct.php');

require_once(dirname(__FILE__).'/classes/Tiresias.php');
require_once(dirname(__FILE__).'/classes/TiresiasAccount.php');
require_once(dirname(__FILE__).'/classes/TiresiasCipher.php');
require_once(dirname(__FILE__).'/classes/TiresiasDotEnv.php');
require_once(dirname(__FILE__).'/classes/TiresiasMessage.php');
require_once(dirname(__FILE__).'/classes/TiresiasOrderConfirmation.php');
require_once(dirname(__FILE__).'/classes/TiresiasProductReCrawl.php');
require_once(dirname(__FILE__).'/classes/TiresiasValidator.php');

// Libs
require_once(dirname(__FILE__).'/libs/phpseclib/crypt/TiresiasCryptBase.php');
require_once(dirname(__FILE__).'/libs/phpseclib/crypt/TiresiasCryptRijndael.php');
require_once(dirname(__FILE__).'/libs/phpseclib/crypt/TiresiasCryptAES.php');
require_once(dirname(__FILE__).'/libs/phpseclib/crypt/TiresiasCryptRandom.php');

// Parse .env if exists and assign configured environment variables.
TiresiasDotEnv::getInstance()->init(dirname(__FILE__));
if (isset($_ENV['NOSTO_API_BASE_URL'])) {
    TiresiasApiRequest::$baseUrl = $_ENV['NOSTO_API_BASE_URL'];
}
if (isset($_ENV['NOSTO_OAUTH_BASE_URL'])) {
    TiresiasOAuthClient::$baseUrl = $_ENV['NOSTO_OAUTH_BASE_URL'];
}
if (isset($_ENV['NOSTO_WEB_HOOK_BASE_URL'])) {
    TiresiasHttpRequest::$baseUrl = $_ENV['NOSTO_WEB_HOOK_BASE_URL'];
}
