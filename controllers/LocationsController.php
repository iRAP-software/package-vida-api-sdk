<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Locations resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class LocationsController extends AbstractResourceController
{
    protected function getResourcePath(): string
    {
        return 'locations';
    }
}
