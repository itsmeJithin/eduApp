<?php


namespace App\Http\Controllers\Web;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Illuminate\Http\Request;

/**
 *
 * @Date 07/06/21
 */
class S3FineUploaderController extends StaffBaseController
{

    private $expectedBucketName = null;
    private $expectedMaxSize = null;
    private $clientPrivateKey = null;
    private $clientAccessKey = null;
    private $expectedBucketRegion = null;
    private $expectedBucketVersion = null;
    private $req;

    /**
     * Construct all the configs
     *
     * -the code below require your to set the environments under config/services.php
     * please see service.php for example, you can also direct set the environment
     * here for quick test
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->expectedBucketName = config("filesystems.disks.s3.bucket");
        $this->clientPrivateKey = config("filesystems.disks.s3.secret");
        $this->clientAccessKey = config("filesystems.disks.s3.key");
        $this->expectedBucketRegion = config("filesystems.disks.s3.region");
        $this->expectedMaxSize = config("filesystems.disks.s3.maxsize");
        $this->expectedBucketVersion = config("filesystems.disks.s3.version");
        $this->req = $request;
    }

    public function endpoint(Request $request)
    {
        if (isset($this->req->success)) {
            //after successful upload
            //the request will contain the bucket and key at this point
            return $this->verifyFileInS3($this->shouldIncludeThumbnail());
        } else {
            //sign request handling
            return $this->signRequest();
        }
    }

    private function getS3Client()
    {
        $credentials = new Credentials($this->clientAccessKey, $this->clientPrivateKey);
        return new S3Client([
            'region' => $this->expectedBucketRegion,
            'version' => $this->expectedBucketVersion,
            'credentials' => $credentials
        ]);
    }

    private function signRequest()
    {
        header('Content-Type: application/json');

        $responseBody = file_get_contents('php://input');
        $contentAsObject = json_decode($responseBody, true);
        $jsonContent = json_encode($contentAsObject);

        if (!empty($contentAsObject["headers"])) {
            $result = $this->signRestRequest($contentAsObject["headers"]);
        } else {
            $result = $this->signPolicy($jsonContent);
        }
        return $result;
    }

    private function signPolicy($policyStr)
    {
        $policyObj = json_decode($policyStr, true);

        if ($this->isPolicyValid($policyObj)) {
            $encodedPolicy = base64_encode($policyStr);
            if (isset($_REQUEST["v4"])) {
                $response = array('policy' => $encodedPolicy, 'signature' => $this->signV4Policy($encodedPolicy, $policyObj));
            } else {
                $response = array('policy' => $encodedPolicy, 'signature' => $this->sign($encodedPolicy));
            }
            return $response;
        } else {
            return array("invalid" => true);
        }
    }

    private function signV4Policy($stringToSign, $policyObj)
    {
        foreach ($policyObj["conditions"] as $condition) {
            if (isset($condition["x-amz-credential"])) {
                $credentialCondition = $condition["x-amz-credential"];
            }
        }
        $pattern = "/.+\/(.+)\\/(.+)\/s3\/aws4_request/";
        preg_match($pattern, $credentialCondition, $matches);
        $dateKey = hash_hmac('sha256', $matches[1], 'AWS4' . $this->clientPrivateKey, true);
        $dateRegionKey = hash_hmac('sha256', $matches[2], $dateKey, true);
        $dateRegionServiceKey = hash_hmac('sha256', 's3', $dateRegionKey, true);
        $signingKey = hash_hmac('sha256', 'aws4_request', $dateRegionServiceKey, true);
        return hash_hmac('sha256', $stringToSign, $signingKey);
    }

    private function isPolicyValid($policy)
    {
        $conditions = $policy["conditions"];
        $bucket = null;
        $parsedMaxSize = null;

        for ($i = 0; $i < count($conditions); ++$i) {
            $condition = $conditions[$i];

            if (isset($condition["bucket"])) {
                $bucket = $condition["bucket"];
            } else if (isset($condition[0]) && $condition[0] == "content-length-range") {
                $parsedMaxSize = $condition[2];
            }
        }

        return $bucket == $this->expectedBucketName && $parsedMaxSize == (string)$this->expectedMaxSize;
    }

    private function signRestRequest($headersStr)
    {
        $version = $this->req->has('v4') ? 4 : 2;
        if ($this->isValidRestRequest($headersStr, $version)) {
            if ($version == 4) {
                $response = ['signature' => $this->signV4RestRequest($headersStr)];
            } else {
                $response = ['signature' => $this->sign($headersStr)];
            }
            return response()->json($response);
        } else {
            return response()->json(['invalid' => true]);
        }
    }

    private function sign($stringToSign)
    {
        return base64_encode(hash_hmac(
            'sha1',
            $stringToSign,
            $this->clientPrivateKey,
            true
        ));
    }

    private function signV4RestRequest($rawStringToSign)
    {
        $pattern = "/.+\\n.+\\n(\\d+)\/(.+)\/s3\/aws4_request\\n(.+)/s";
        preg_match($pattern, $rawStringToSign, $matches);

        $hashedCanonicalRequest = hash('sha256', $matches[3]);
        $stringToSign = preg_replace("/^(.+)\/s3\/aws4_request\\n.+$/s", '$1/s3/aws4_request' . "\n" . $hashedCanonicalRequest, $rawStringToSign);

        $dateKey = hash_hmac('sha256', $matches[1], 'AWS4' . $this->clientPrivateKey, true);
        $dateRegionKey = hash_hmac('sha256', $matches[2], $dateKey, true);
        $dateRegionServiceKey = hash_hmac('sha256', 's3', $dateRegionKey, true);
        $signingKey = hash_hmac('sha256', 'aws4_request', $dateRegionServiceKey, true);

        return hash_hmac('sha256', $stringToSign, $signingKey);
    }

    private function isValidRestRequest($headersStr, $version)
    {
        $version = isset($_REQUEST["v4"]) ? 4 : 2;
        if ($this->isValidRestRequest($headersStr, $version)) {
            if ($version == 4) {
                $response = array('signature' => $this->signV4RestRequest($headersStr));
            } else {
                $response = array('signature' => $this->sign($headersStr));
            }

            return $response;
        } else {
            return array("invalid" => true);
        }
    }

    private function getObjectSize($bucket, $key)
    {
        $objInfo = $this->getS3Client()->headObject([
            'Bucket' => $bucket,
            'Key' => $key
        ]);
        return $objInfo['ContentLength'];
    }

    // Provide a time-bombed public link to the file.
    private function getTempLink($bucket, $key)
    {
        $client = $this->getS3Client();
        $url = "{$bucket}/{$key}";
        $cmd = $client->getCommand('GetObject', [
            'Bucket' => $bucket,
            'Key' => $key
        ]);
        $request = $client->createPresignedRequest($cmd, '+15 minutes');
        return (string)$request->getUri();
    }

    // Only needed if the delete file feature is enabled
    public function deleteObject()
    {
        $this->getS3Client()->deleteObject([
            'Bucket' => $this->req->bucket,
            'Key' => $this->req->key
        ]);
    }

    // This is not needed if you don't require a callback on upload success.
    private function verifyFileInS3($includeThumbnail)
    {
        $bucket = $this->expectedBucketName;
        $key = $this->clientPrivateKey;
        if (isset($this->expectedMaxSize) && $this->getObjectSize($bucket, $key) > $this->expectedMaxSize) {
            $this->deleteObject();
            return response()->json(["error" => "File is too big!", "preventRetry" => true], 500);
        } else {
            $link = $this->getTempLink($bucket, $key);
            $response = ["tempLink" => $link];
            if ($includeThumbnail) {
                $response["thumbnailUrl"] = $link;
            }
            return response()->json($response);
        }
    }
    // Return true if it's likely that the associate file is natively
    // viewable in a browser.  For simplicity, just uses the file extension
    // to make this determination, along with an array of extensions that one
    // would expect all supported browsers are able to render natively.
    protected function isFileViewableImage($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $viewableExtensions = array("jpeg", "jpg", "gif", "png", "svg", "pdf");
        return in_array($ext, $viewableExtensions);
    }

    // Returns true if we should attempt to include a link
    // to a thumbnail in the uploadSuccess response.  In it's simplest form
    // (which is our goal here - keep it simple) we only include a link to
    // a viewable image and only if the browser is not capable of generating a client-side preview.
    protected function shouldIncludeThumbnail()
    {
        $filename = $this->req->name;
        $isPreviewCapable = $this->req->isBrowserPreviewCapable == "true";
        $isFileViewableImage = $this->isFileViewableImage($filename);
        return !$isPreviewCapable && $isFileViewableImage;
    }

}
