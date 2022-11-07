<?php


namespace App\Helpers;

use App\Exceptions\CoreException;
use App\Models\AptuResources;
use App\Utils\CommonUtil;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

/**
 *
 * @Date 07/06/21
 */
class S3Util
{
    protected $accessKey = null;
    protected $secretKey = null;
    protected $bucketName = null;
    protected $region = null;
    protected $version = null;

    public function __construct()
    {
        $this->bucketName = config("filesystems.disks.s3.bucket");
        $this->secretKey = config("filesystems.disks.s3.secret");
        $this->accessKey = config("filesystems.disks.s3.key");
        $this->region = config("filesystems.disks.s3.region");
        $this->version = config("filesystems.disks.s3.version");
    }

    /**
     * @param $resourceId
     * @return \stdClass
     * @throws \Exception
     */
    public function getPreSignedURL($resourceId)
    {
        try {
            $resource = AptuResources::find($resourceId);
            if (empty($resource)) {
                throw new \Exception(CoreException::INVALID_RESOURCE, "Resource not available");
            }
            $tempStorageObject = CommonUtil::convertObjectToUTF8Format($resource->storage_object);
            $storageObject = json_decode($tempStorageObject);
            $storageObject->key = htmlspecialchars_decode($storageObject->key);
            $storageObject->name = htmlspecialchars_decode($storageObject->name);
            $key = $storageObject->key;
            $bucket = $storageObject->bucket;
            $credentials = new  Credentials($this->accessKey, $this->secretKey);
            $s3Client = new S3Client([
                'version' => $this->version,
                'region' => $this->region,
                'credentials' => $credentials,
            ]);
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $key
            ]);
            $request = $s3Client->createPresignedRequest($cmd, '+30 minutes');
            $response = new \stdClass();
            $response->url = (string)$request->getUri();
            $response->name = $storageObject->name;
            return $response;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $resourceId
     * @return \Aws\Result
     * @throws \Exception
     */
    public function deleteFile($resourceId)
    {
        try {
            $resource = AptuResources::find($resourceId);
            if (empty($resource)) {
                throw new \Exception("Resource not available", 404);
            }
            $tempStorageObject = CommonUtil::convertObjectToUTF8Format($resource->storage_object);
            $storageObject = json_decode($tempStorageObject);
            $storageObject->key = htmlspecialchars_decode($storageObject->key);
            $storageObject->name = htmlspecialchars_decode($storageObject->name);
            $key = $storageObject->key;
            $bucket = $storageObject->bucket;
            $credentials = new  Credentials($this->accessKey, $this->secretKey);
            $s3Client = new S3Client([
                'version' => $this->version,
                'region' => $this->region,
                'credentials' => $credentials,
            ]);
            $status = $s3Client->deleteObject([
                'Bucket' => $bucket,
                'Key' => $key
            ]);
            if ($status) {
                return $resource->delete();
            }
        } catch (\Exception $e) {
            throw  $e;
        }
    }
}
