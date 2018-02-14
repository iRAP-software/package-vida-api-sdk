<?php

/**************************************************************************************************
 * This file is for internal use by the ViDA SDK. It should not be altered by users
 **************************************************************************************************
 * 
 * Controller for the Data resource. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class DataController extends AbstractResourceController
{
    protected function getResourcePath()
    {
        return 'data';
    }
    
    
    /**
     * Imports a CSV file from the specified url. The CSV file is expected to have a header
     * row that will be ignored.
     * @param int $datasetID - the ID of the dataset we wish to import for.
     * @param string $url - the url to the CSV file we wish to import. Temporary pre-signed s3 urls
     *                      recommended.
     * @return object
     */
    public function importData($datasetID, $url)
    {
        $request = new \iRAP\VidaSDK\Models\APIRequest($this->m_auth);
        $request->setUrl($this->getResourcePath(), $datasetID);
        $request->setPostData(array('url' => $url));
        $request->send();
        return $this->response($request);
    }
}