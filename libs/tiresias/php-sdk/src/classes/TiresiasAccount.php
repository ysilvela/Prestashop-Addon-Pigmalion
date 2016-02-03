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

/**
 * Tiresias account class for handling account related actions like, creation, OAuth2 syncing and SSO to Tiresias.
 */
class TiresiasAccount extends TiresiasObject implements TiresiasAccountInterface, TiresiasValidatableInterface
{
    /**
     * @var string the name of the Tiresias account.
     */
    protected $name;

    /**
     * @var TiresiasApiToken[] the Tiresias API tokens associated with this account.
     */
    protected $tokens = array();

    /**
     * Constructor.
     * Create a new account object with given name.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->validate();
    }

    /**
     * @inheritdoc
     */
    public function getValidationRules()
    {
        return array(
            array(array('name'), 'required')
        );
    }

    /**
     * @inheritdoc
     */
    public static function create(TiresiasAccountMetaDataInterface $meta)
    {
        //file_put_contents ( 'YSG_debug_file', 'Procedemos a crear una cuenta');
        PrestaShopLogger::addLog('Procedemos a crear una cuenta', 1);
        //PrestaShopLogger::addLog(debug_print_backtrace(), 1);
        //debug_print_backtrace();
        $params = array(
            'title' => $meta->getTitle(),
            'name' => $meta->getName(),
            'platform' => $meta->getPlatform(),
            'front_page_url' => $meta->getFrontPageUrl(),
            'currency_code' => strtoupper($meta->getCurrencyCode()),
            'language_code' => strtolower($meta->getOwnerLanguageCode()),
            'owner' => array(
                'first_name' => $meta->getOwner()->getFirstName(),
                'last_name' => $meta->getOwner()->getLastName(),
                'email' => $meta->getOwner()->getEmail(),
            ),
            'billing_details' => array(
                'country' => strtoupper($meta->getBillingDetails()->getCountry()),
            ),
            'api_tokens' => array(),
        );

        foreach (TiresiasApiToken::$tokenNames as $name) {
            $params['api_tokens'][] = 'api_'.$name;
        }

        $request = new TiresiasApiRequest();
        $request->setPath(TiresiasApiRequest::PATH_SIGN_UP);
        $request->setReplaceParams(array('{lang}' => $meta->getLanguageCode()));
        $request->setContentType('application/json');
        $request->setAuthBasic('', $meta->getSignUpApiToken());
        $response = $request->post(json_encode($params));

        PrestaShopLogger::addLog('TiresiasAccount.create. respuesta a create -> ' .$response, 1);

        if ($response->getCode() !== 200) {
            Tiresias::throwHttpException('Tiresias account could not be created.', $request, $response);
        }

        $account = new self($meta->getPlatform().'-'.$meta->getName());
        $account->tokens = TiresiasApiToken::parseTokens($response->getJsonResult(true), '', '_token');
        return $account;
    }

    /**
     * @inheritdoc
     */
    public static function syncFromTiresias(TiresiasOAuthClientMetaDataInterface $meta, $code)
    {
        $oauthClient = new TiresiasOAuthClient($meta);
        $token = $oauthClient->authenticate($code);

        if (empty($token->accessToken)) {
            throw new TiresiasException('No access token found when trying to sync account from Tiresias');
        }
        if (empty($token->merchantName)) {
            throw new TiresiasException('No merchant name found when trying to sync account from Tiresias');
        }

        $request = new TiresiasHttpRequest();
        // The request is currently not made according the the OAuth2 spec with the access token in the
        // Authorization header. This is due to the authentication server not implementing the full OAuth2 spec yet.
        $request->setUrl(TiresiasOAuthClient::$baseUrl.'/exchange');
        $request->setQueryParams(array('access_token' => $token->accessToken));
        $response = $request->get();
        $result = $response->getJsonResult(true);

        if ($response->getCode() !== 200) {
            Tiresias::throwHttpException('Failed to sync account from Tiresias.', $request, $response);
        }
        if (empty($result)) {
            throw new TiresiasException('Received invalid data from Tiresias when trying to sync account');
        }

        $account = new self($token->merchantName);
        $account->tokens = TiresiasApiToken::parseTokens($result, 'api_');
        if (!$account->isConnectedToTiresias()) {
            throw new TiresiasException('Failed to sync all account details from Tiresias');
        }
        return $account;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        PrestaShopLogger::addLog('TiresiasAccount.delete. Entramos en la funcion delete. ', 1);
        $token = $this->getApiToken('sso');
        if ($token === null) {
            throw new TiresiasException('Failed to notify Tiresias about deleted account, no "sso" token');
        }

        $request = new TiresiasHttpRequest();
        $request->setUrl(TiresiasHttpRequest::$baseUrl.TiresiasHttpRequest::PATH_ACCOUNT_DELETED);
        $request->setAuthBasic('', $token->getValue());
        $response = $request->post('');

        if ($response->getCode() !== 200) {
            Tiresias::throwHttpException('Failed to notify Tiresias about deleted account.', $request, $response);
        }
    }

    /**
     * Returns the account name.
     *
     * @return string the name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the account tokens.
     *
     * @return TiresiasApiToken[] the tokens.
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @inheritdoc
     */
    public function isConnectedToTiresias()
    {
        PrestaShopLogger::addLog('TiresiasAccount.isConnectedToTiresias. Vemos si estamos conectados. Revisamos tokens ' , 1);
        if (empty($this->tokens)) {
            return false;
        }
        $countTokens = count($this->tokens);
        $foundTokens = 0;
        foreach (TiresiasApiToken::getApiTokenNames() as $name) {
            foreach ($this->tokens as $token) {
                if ($token->name === $name) {
                    $foundTokens++;
                    break;
                }
            }
        }
        return ($countTokens === $foundTokens);
    }

    /**
     * Adds an API token to the account.
     *
     * @param TiresiasApiToken $token the token.
     */
    public function addApiToken(TiresiasApiToken $token)
    {
        $this->tokens[] = $token;
    }

    /**
     * @inheritdoc
     */
    public function getApiToken($name)
    {
        foreach ($this->tokens as $token) {
            if ($token->getName() === $name) {
                return $token;
            }
        }
        return null;
    }

    public function debug_string_backtrace() { 
        ob_start(); 
        debug_print_backtrace(); 
        $trace = ob_get_contents(); 
        ob_end_clean(); 

        // Remove first item from backtrace as it's this function which 
        // is redundant. 
        $trace = preg_replace ('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1); 

        // Renumber backtrace items. 
        $trace = preg_replace ('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace); 

        return $trace; 
    } 

    /**
     * @inheritdoc
     */
    public function getIframeUrl(TiresiasAccountMetaDataIframeInterface $meta, array $params = array())
    {        
        $url = Tiresias::helper('iframe')->getUrl($meta, $this, $params);
        PrestaShopLogger::addLog('TiresiasAccount.getIframeUrl. Url a invocar -> ' .$url, 1);
        $back = $this->debug_string_backtrace();
        PrestaShopLogger::addLog('TiresiasAccount.getIframeUrl. Stack -> ' .$back, 1);
        return $url;
    }

    /**
     * @inheritdoc
     */
    public function ssoLogin(TiresiasAccountMetaDataIframeInterface $meta){
    
        PrestaShopLogger::addLog('TiresiasAccount.ssoLogin. Lanzamos login', 1);
        $token = $this->getApiToken('sso');
        if ($token === null) {
            PrestaShopLogger::addLog('TiresiasAccount.ssoLogin. Devolvemos false', 1);
            return false;
        }

        PrestaShopLogger::addLog('TiresiasAccount.ssoLogin. Vamos a lanzar una peticion de oath', 1);
        $request = new TiresiasHttpRequest();
        $request->setUrl(TiresiasHttpRequest::$baseUrl.TiresiasHttpRequest::PATH_SSO_AUTH);
        $request->setReplaceParams(
            array(
                '{platform}' => $meta->getPlatform(),
                '{email}' => $meta->getEmail(),
            )
        );
        $request->setContentType('application/x-www-form-urlencoded');
        $request->setAuthBasic('', $token->getValue());
        $response = $request->post(
            http_build_query(
                array(
                    'fname' => $meta->getFirstName(),
                    'lname' => $meta->getLastName(),
                )
            )
        );
        $result = $response->getJsonResult();

        if ($response->getCode() !== 200) {
            PrestaShopLogger::addLog('TiresiasAccount.ssoLogin. Lanzamos excepcion por fallo en token', 1);
            Tiresias::throwHttpException('Unable to login employee to Tiresias with SSO token.', $request, $response);
        }
        if (empty($result->login_url)) {
            PrestaShopLogger::addLog('TiresiasAccount.ssoLogin. Devolvemos excepcion por no haber url', 1);
            throw new TiresiasException('No "login_url" returned when logging in employee to Tiresias');
        }else{
            PrestaShopLogger::addLog('TiresiasAccount.ssoLogin. La url devuelta es' .$result->login_url, 1);
        }

        return $result->login_url;
    }

    /**
     * Validates the account attributes.
     *
     * @throws TiresiasException if any attribute is invalid.
     */
    protected function validate()
    {
        $validator = new TiresiasValidator($this);
        if (!$validator->validate()) {
            foreach ($validator->getErrors() as $errors) {
                throw new TiresiasException(sprintf('Invalid Tiresias account. %s', $errors[0]));
            }
        }
    }
}
