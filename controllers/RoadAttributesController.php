<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Road Attributes resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class RoadAttributesController extends AbstractResourceController
{
    protected function getResourceName()
    {
        return 'roadattributes';
    }
}
