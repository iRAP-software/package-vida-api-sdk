<?php
/* * ************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 * *************************************************************************************************
 *
 * Controller for Access
 *
 */

namespace iRAP\VidaSDK\Controllers;

class AccessController extends AbstractResourceController
{

    protected function getResourcePath(): string
    {
        return 'access';
    }
}