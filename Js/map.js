
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

function createLocation(name, lat, long, id, token){
    var feature = new OpenLayers.Feature.Vector(
        new OpenLayers.Geometry.Point( long, lat ).transform(epsg4326, projectTo),
        {description: `Name: ${name} <button type="button" data-bs-toggle="modal" data-bs-target="#recordModal" onclick="ajaxController.getRecord(${id}, '${token}')" class="btn btn-primary mt-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
</svg></button>`} ,
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