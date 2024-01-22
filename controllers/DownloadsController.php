<?php

/**
 * 
 * Controller for the external download methods. Overrides the Abstract Resource Controller.
 * 
 */

namespace iRAP\VidaSDK\Controllers;

class DownloadsController extends AbstractResourceController
{
    protected function getResourcePath()
    {
        return 'downloads';
    }
    
    
    /**
     * Request for a file download
     * @param string $type could be any one of (core_before, core_before_endgps, core_after, core_after_endgps, complete_before, 
     * complete_after, fe_before, fe_after, countermeasures, countermeasures_partial, chainages, upload)
     * @param int $datasetId
     * @param string $language Name of the file (not including extension)
     * @param string $filename Language code (Defaults to en-gb)
     * @return \iRAP\VidaSDK\Models\Response
     */
    public function requestDownloadFileExternal(string $type, int $datasetId, string $filename, string $language): Response
    {
        $request = new APIRequest($this->m_auth);
        
        $data = array(
            'filename' => $filename,
            'language' => $language
        );
        
        $url = $this->getResourcePath() . "/{$type}/{$datasetId}";
        $request->setUrl($url);
        $request->setPostData($data);
        $request->send();
        return $this->response($request);
    }
}
