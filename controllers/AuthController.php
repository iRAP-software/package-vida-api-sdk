<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 *
 * Contains the authentication methods used to request and set tokens. Also handles encryption
 * of the user password, before it is transmitted to the API
 *
 */

namespace iRAP\VidaSDK\Controllers;

use Exception;
use iRAP\VidaSDK\Defines;
use iRAP\VidaSDK\Models\AbstractAuthentication;
use iRAP\VidaSDK\Models\APIRequest;

class AuthController extends AbstractResourceController
{
    protected function getResourcePath(): string
    {
        return 'auth';
    }

    /**
     * Builds query parameters to sign, signs them and then sends the query to ViDA, so that the
     * user can view and accept/reject the permissions that the app is asking for.
     *
     * @param AbstractAuthentication $auth
     * @param string $returnUrl
     */
    public static function requestUserPermissions(AbstractAuthentication $auth, string $returnUrl)
    {
        $headers = $auth->getAuthHeaders();
        $headers['return_url'] = urlencode($returnUrl);
        $signature = $auth->getSignatures($headers);
        $query = array_merge($headers, $signature);

        if (defined('\iRAP\VidaSDK\IRAP_PERMISSIONS_URL'))
        {
            $url = \iRAP\VidaSDK\IRAP_PERMISSIONS_URL;
        }
        else
        {
            $url = Defines::IRAP_PERMISSIONS_LIVE_URL;
        }

        header('Location: ' . $url . '?' . http_build_query($query));
        die();
    }
}