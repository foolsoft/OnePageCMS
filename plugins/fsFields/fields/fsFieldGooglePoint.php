<?php
class fsFieldGooglePoint extends fsField
{
  protected $_title;
  protected $_name;
  
  private $_mapDefaultCenterLat = '35.1697515'; 
  private $_mapDefaultCenterLon = '33.43660380000001';
  private $_mapDefaultZoom = '8';
  
  public function Input($htmlFormName, $value = '', $htmlAttributes = array(), $possibleValues = array(), $arrayName = 'fields')
  {
    $jsInclude = '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>';
    if (strpos($_REQUEST['includeHead'], $jsInclude) === false) {
      $_REQUEST['includeHead'] .= $jsInclude;
      $_REQUEST['includeHead'] .= '<script>'.
      'google.maps.Map.prototype.markers = [];'.
      'google.maps.Map.prototype.addMarker = function(marker) { this.markers[this.markers.length] = marker; };'.
      'google.maps.Map.prototype.getMarkers = function() { return this.markers };'.
      'google.maps.Map.prototype.clearMarkers = function() { for(var i=0; i<this.markers.length; ++i) { this.markers[i].setMap(null); } this.markers = []; };'.
      '</script>'; 
    }
    unset($jsInclude);
    
    $valueParts = explode(',', $value);
    $lat = $this->_mapDefaultCenterLat;
    $lng = $this->_mapDefaultCenterLon;
    if(count($valueParts) == 2) {
      $lat = $valueParts[0];
      $lng = $valueParts[1];  
    }
    $zoom = $this->_mapDefaultZoom;
    if(isset($htmlAttributes['width'])) {
      $zoom = is_numeric($htmlAttributes['zoom']) ? $htmlAttributes['zoom'] : $this->_mapDefaultZoom;
      unset($htmlAttributes['zoom']);
    }
    if($arrayName !== '') {
        $htmlFormName = $arrayName.'['.$htmlFormName.']';
    }
    $width = isset($htmlAttributes['width']) ? $htmlAttributes['width'] : '100%';
    $height = isset($htmlAttributes['height']) ? $htmlAttributes['height'] : '200px';
    $mapName = 'googleMap'.$htmlFormName;
    $html = fsHtml::Hidden($htmlFormName, $value, array('id' => $htmlFormName));
    $html .= '<div style="width:'.$width.';height:'.$height.';" id="canvas-'.$htmlFormName.'"></div>';
    $html .= '<script>var '.$mapName.';'.
    'function init'.$mapName.'(){var mapOptions={zoom:'.$zoom.','.
    'center: new google.maps.LatLng('.$lat.', '.$lng.') };'.
    $mapName.' = new google.maps.Map(document.getElementById("canvas-'.$htmlFormName.'"),mapOptions);';
    if(!isset($htmlAttributes['readonly']) || $htmlAttributes['readonly'] !== true) {
      $html .= 'google.maps.event.addListener('.$mapName.',"click",function(e){'.$mapName.'.clearMarkers();'.
        $mapName.'.addMarker(new google.maps.Marker({position:e.latLng,map:'.$mapName.'}));'.
        'document.getElementById("'.$htmlFormName.'").value=e.latLng.lat()+","+e.latLng.lng();'.
        $mapName.'.setCenter(e.latLng);});';  
    }  
    if(isset($htmlAttributes['readonly'])) {
      unset($htmlAttributes['readonly']);
    }
    if(!empty($value)) {
      $value = explode(',', $value);
      if(count($value) == 2) {
        $html .= $mapName.'.addMarker(new google.maps.Marker({map:'.$mapName.',position:new google.maps.LatLng('.$value[0].','.$value[1].')}));';
      }
    }
    $html .= '}google.maps.event.addDomListener(window,"load",init'.$mapName.');</script>';
    return $html;
  }
  
  public function __construct()
  {
    parent::__construct('googlepoint', 'Coordinates');
  }
}