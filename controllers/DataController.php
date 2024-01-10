<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Data resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

use Exception;
use iRAP\VidaSDK\Models\APIRequest;
use iRAP\VidaSDK\Models\ImportResponse;

class DataController extends AbstractResourceController
{
    protected function getResourcePath(): string
    {
        return 'data';
    }


    /**
     * Imports a CSV file from the specified url. The CSV file is expected to have a header
     * row that will be ignored.
     * @param int $datasetID - the ID of the dataset we wish to import for.
     * @param string $url - the url to the CSV file we wish to import. Temporary pre-signed s3 urls
     *                      recommended.
     * @return ImportResponse
     * @throws Exception
     */
    public function importData(int $datasetID, string $url): ImportResponse
    {
        $request = new APIRequest($this->m_auth);
        $args = array('import');
        $request->setUrl($this->getResourcePath(), $datasetID, $args);
        $request->setPostData(array('url' => $url));
        $request->send();
        return new ImportResponse($this->response($request));
    }
}