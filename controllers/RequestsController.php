<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Requests log. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class RequestsController extends AbstractResourceController
{
    protected function getResourceName()
    {
        return 'requests';
    }
}
