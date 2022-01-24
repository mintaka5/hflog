<?php
require_once './init.php';

@define("FLICKR_BASE_API_URL", "http://api.flickr.com/services/rest/");
@define("FLICKR_API_KEY", "d56ec31924baf5bcb41550e09e030b9c");
@define("FLICKR_NSID", "45145385@N00");
@define("FLICKR_APP_SECRET", "02120cbd5dac8d0b");

$photoIds = array(
        "5255238625",
	"6247890567",
	"5970733778",
	"5145370657",
	"4987006177"
);

$photoCollection = new ArrayObject();

foreach($photoIds as $photoId) {
	$req = new HTTP_Request2(FLICKR_BASE_API_URL, HTTP_Request2::METHOD_GET);
	$req->getUrl()->setQueryVariable("api_key", FLICKR_API_KEY);
	$req->getUrl()->setQueryVariable("method", "flickr.photos.getInfo");
	$req->getUrl()->setQueryVariable("photo_id", $photoId);
	
	$doc = new DOMDocument();
	$doc->loadXML($req->send()->getBody());
	
	$xpath = new DOMXPath($doc);
	
	$title = $xpath->query("/*/photo/title")->item(0)->textContent;
	$desc = $xpath->query("/*/photo/description")->item(0)->textContent;
	
	$req = new HTTP_Request2(FLICKR_BASE_API_URL, HTTP_Request2::METHOD_GET);
	$req->getUrl()->setQueryVariable("api_key", FLICKR_API_KEY);
	$req->getUrl()->setQueryVariable("method", "flickr.photos.getSizes");
	$req->getUrl()->setQueryVariable("photo_id", $photoId);
	
	$doc = new DOMDocument();
	$doc->loadXML($req->send()->getBody());
	
	$xpath = new DOMXPath($doc);
	
	$thumbnail = $xpath->query("/*/sizes/size/@label[.='Thumbnail']/../@source")->item(0)->textContent;
	$large = $xpath->query("/*/sizes/size/@label[.='Large']/../@source")->item(0)->textContent;
        
        $photoObj = new stdClass();
	$photoObj->title = $title;
	$photoObj->desc = $desc;
	$photoObj->thumbnail = $thumbnail;
	$photoObj->large = $large;
	
	$photoCollection->append($photoObj);
}

$json = new Services_JSON();

$jsonAry = array();
$jsonAry['project'] = "Qualsh";

$iter = $photoCollection->getIterator();
while($iter->valid()) {
    $jsonAry['slideshow'][] = array(
        'image' => $iter->current()->large,
        'thumb' => $iter->current()->thumbnail,
        'title' => $iter->current()->title,
        'copy' => $iter->current()->desc,
        'credit' => "Chris Walsh &copy;2012"
    );
    
    $iter->next();
}

echo $json->encode($jsonAry);

exit();
?>
