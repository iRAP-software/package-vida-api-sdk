<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Star Ratings Restuls Summary resource. Overrides the Abstract Resource 
 * Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class ReportFiltersController extends AbstractResourceController
{
    protected function getResourcePath()
    {
        return 'report-filters';
    }
}