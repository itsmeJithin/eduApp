<?php


namespace App\Http\Controllers\Web;

/**
 *
 * @Date 07/06/21
 */
class HelperController extends StaffBaseController
{
    /**
     * return s3 conf
     */
    public function getS3Conf()
    {
        $conf = new \stdClass();
        $conf->serverProtocol = "https";
        $conf->sizeLimit = 1000000;
        $conf->bucketName = config("filesystems.disks.s3.bucket");
        $conf->accessKey = config("filesystems.disks.s3.key");
        $conf->region = config("filesystems.disks.s3.region");
        return $this->sendResponse($conf, "Configurations fetched successfully");

    }

}
