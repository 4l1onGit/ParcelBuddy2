


    map = new OpenLayers.Map("map");
    map.addLayer(new OpenLayers.Layer.OSM());
    epsg4326 =  new OpenLayers.Projection("EPSG:4326"); //WGS 1984 projection
    projectTo = map.getProjectionObject(); //The map projection (Spherical Mercator)
    var vectorLayer = new OpenLayers.Layer.Vector("Overlay");
    navigator.geolocation.getCurrentPosition(function(position){set_position(position.coords.latitude,position.coords.longitude);})




function set_position(x,y){
    var zoom=10;
    lonlat = new OpenLayers.LonLat(y, x).transform(epsg4326, projectTo);
    map.setCenter (lonlat, zoom);
    map.addLayer(vectorLayer);
}

function createLocation(lat, long, description, imageURL){
    var feature = new OpenLayers.Feature.Vector(
        new OpenLayers.Geometry.Point( long, lat ).transform(epsg4326, projectTo),
        {description:description + '<img id="imageID" src='+ imageURL +'>'} ,
        {externalGraphic: 'Images/marker.png', graphicHeight: 20, graphicWidth: 20, graphicXOffset:-12, graphicYOffset:-25  }
    );

    vectorLayer.addFeatures(feature);
}

//Selector control for pop up
var controls = {
    selector: new OpenLayers.Control.SelectFeature(vectorLayer, { onSelect: createPopup, onUnselect: destroyPopup })
};

function createPopup(feature) {
    feature.popup = new OpenLayers.Popup.FramedCloud("pop",
        feature.geometry.getBounds().getCenterLonLat(),
        null,
        '<div class="markerContent">'+ feature.attributes.description+'</div>',
        null,
        true,
        function() { controls['selector'].unselectAll(); }
    );
    //feature.popup.closeOnMove = true;
    map.addPopup(feature.popup);
}

function destroyPopup(feature) {
    feature.popup.destroy();
    feature.popup = null;
}

map.addControl(controls['selector']);
controls['selector'].activate();