<?php


namespace prime\widgets\map;

use prime\widgets\chart\ChartBundle;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

class Map extends Widget
{
    public const TILE_LAYER = 'tileLayer';
    public $baseLayers = [
        [
            "type" => self::TILE_LAYER,
            "url" => "https://services.arcgisonline.com/arcgis/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}",
            'options' => [
                'maxZoom' => 30,
                'maxNativeZoom' => 17
            ]
        ],
    ];

    public $options = [
        'class' => [
            'map'
        ]
    ];

    public $center = [8.6753, 9.0820];
    public $zoom = 5.4;
    public $minZoom = 3;
    public $maxZoom = 5;

    public $colors;

    public $data = [];

    public $markerRadius = 12.5;
    public function init()
    {
        $this->colors = $this->colors ?? new JsExpression('chroma.brewer.OrRd');
        parent::init();
    }

    /**
     * This is the popup content that will be shown while the data is being fetched.
     * @return string
     */
    private function renderPopupLoader(): string
    {
        return <<<HTML
        <div class="loader-wrapper">
            <div class="loader-anim" style="background-image: url('/img/herams_icon.png');"></div>
            <h1>Loading project summary</h1>
            <div class="loader-anim" style="background-image: url('/img/loader.svg');"></div>
        </div>
HTML;
    }

    public function run()
    {
        $this->registerClientScript();
        $options = $this->options;
        Html::addCssClass($options, strtr(__CLASS__, ['\\' => '_']));
        $options['id'] = $this->getId();
        echo Html::beginTag('div', $options);
        //echo Html::tag('template', $this->renderPopupLoader());

        $id = Json::encode($this->getId());
        $config = Json::encode([
            'preferCanvas' => true,
            'center' => $this->center,
            'zoom' => $this->zoom,
            'zoomControl' => false,
            'maxZoom' => $this->maxZoom,
            'minZoom' => $this->minZoom
        ]);

        $baseLayers = Json::encode($this->baseLayers);
        $data = Json::encode(array_values($this->data));

        $scale = Json::encode($this->colors);
        $this->view->registerJs(<<<JS
        (function() {
            try {
                let map = L.map($id, $config);
                window.map = map;
                for (let baseLayer of $baseLayers) {
                    switch (baseLayer.type) {
                        case 'tileLayer':
                            L.tileLayer(baseLayer.url, baseLayer.options || {}).addTo(map);
                            break;
                    }
                }
                
                // /*
                let bounds = [];
                let data = $data;
                let layers = [];
                let markers = [];
                let scale = chroma.scale($scale).colors(data.length);
                var color;
                for (let set of data) {
                    color = scale.pop();
                    let layer = L.geoJSON(set.features, {
                        pointToLayer: function(feature, latlng) {
                            bounds.push(latlng);
                            let marker = L.circleMarker(latlng, {
                                radius: {$this->markerRadius},
                                color: color,
                                weight: 2.5,
                                opacity: 1,
                                fillOpacity: 0.8
                            });
                            marker.feature = feature;
                            
                            let popup = marker.bindPopup("", {
                                maxWidth: "auto",
                                closeButton: false
                            }).getPopup();

                            // On the first open fetch remote content
                            marker.renderer = new PopupRenderer(popup, feature.properties.url);
                            marker.on('popupopen', () => {
                                marker.renderer.render();
                                let event = new Event('mapPopupOpen');
                                event.id = feature.properties.id;
                                window.dispatchEvent(event);
                            });
                            marker.on('popupclose', function() {
                                let event = new Event('mapPopupClose');
                                window.dispatchEvent(event);
                            });
                            
                            let tooltip = marker.bindTooltip(feature.properties.title, {className: 'tooltip'});
                            window.addEventListener('externalPopup', function(e) {
                                if (e.id == feature.properties.id) {
                                    showPopupForMarker(marker);
                                }
                            });
                            markers[feature.properties.id] = marker;
                            return marker;
                        }, 
                        onEachFeature: function(feature, layer) {
                        }
                    });
                    layers.push(layer);
                    
                    /*let legend = document.createElement('span');
                    legend.classList.add('legend');
                    legend.style.setProperty('--color', color);
                    legend.title = set.features.length;
                    //legend.attributeStyleMap.set('--color', color);
                    legend.textContent = set.title;
                    
                    // legend.css
                    layers[legend.outerHTML] = layer;*/
                }
                
                let markerCluster = L.markerClusterGroup(
                {
                    zoomToBoundsOnClick : false,
                    spiderfyOnMaxZoom: false,
                    showCoverageOnHover: false,
                    maxClusterRadius: 10,
                    iconCreateFunction: function(cluster) {
                        return L.divIcon({ html: '<span style="background-color:'+color+'; border-color:'+color+';">' + cluster.getChildCount() + '</span>' });
                    }
                });

                var popupList = L.popup( {
                                    maxWidth: "auto",
                                    closeButton: false
                                });
                let renderer = new PopupListRenderer(popupList);                
                markerCluster.on('clusterclick', function (a) {
                    map.flyTo(a.latlng, map.getZoom(), {
                        animate: true,
                        duration: 0.5
                    });
                    popupList.setLatLng(a.latlng);
                    renderer.render(a.layer.getAllChildMarkers());
                    popupList.openOn(map);
                });


                window.addEventListener('click', function(e) {
                    if (e.target.matches('.project-list .project-item[data-id]')) {
                        let marker = markers[e.target.getAttribute('data-id')];
                        showPopupForMarker(marker);
                    }
                });

                markerCluster.addLayers(layers);
                map.addLayer(markerCluster);
                if (layers.length > 0) {
                    L.control.layers([], layers, {
                        collapsed: false,
                        position: 'bottomright'
                    }).addTo(map);
                }
                
                
                L.control.zoom({
                    position: 'bottomright'
                }).addTo(map);
                L.control.scale({
                    metric: true,
                    imperial: false
                }).addTo(map);
                let menuWidth = document.getElementById("w0").offsetWidth;
                map.fitBounds(bounds, {
                    padding: [50, 50],
                    paddingTopLeft: [menuWidth,0]
                });
            } catch(error) {
                console.error("Error in map widget JS", error);
            }

            
            function showPopupForMarker(marker) {
                let popup = marker.getPopup();
                map.once('moveend', function(){
                    popup.setLatLng(marker.getLatLng());
                    popup.openOn(map);
                } );
                map.flyTo(marker.getLatLng(), map.getZoom(), {
                    animate: true,
                    duration: 0.5
                });
            }
            
        })();

JS);

        echo Html::endTag('div');
    }


    protected function registerClientScript()
    {
        $this->view->registerAssetBundle(ChartBundle::class);
        $this->view->registerAssetBundle(MapBundle::class);
    }
}
