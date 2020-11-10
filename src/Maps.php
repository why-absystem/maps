<?php
	/**
	 * Programmer: WHY
	 * Date: 11/11/20
	 * Time: 02.06
	 */
	
	namespace W;
	
	
	class Maps {
		protected $host;
		protected $keymap;
		protected $geojsondefault;
		protected $zoomdefault;
		protected $linkwscript;
		protected $linkmarkerclustererjs;
		protected $linkmapscriptjs;
		protected $linkfancyboxjs;
		protected $linksweetalertjs;
		protected $linkfancyboxcss;
		protected $linkgoogleapi;
		protected $inputcari;
		protected $iconmarker;
		
		private $listmarker;
		private $geojson;
		private $servicegoogle;
		private $div;
		private $onlyonemarker;
		private $addmarker;
		private $zoom;
		private $callbackaddmarker;
		private $centermap;
		private $posisiuser;
		
		public function __construct ($keymap) {
			$this->host              = $this->getHost();
			$this->geojsondefault    = $this->host . 'geojson/kecamatan.geojson';
			$this->zoomdefault       = 11;
			$this->servicegoogle     = 'drawing,places,geometry';
			$this->keymap            = $keymap;
			$this->geojson           = [];
			$this->listmarker        = [];
			$this->onlyonemarker     = FALSE;
			$this->addmarker         = FALSE;
			$this->zoom              = [];
			$this->callbackaddmarker = [];
			$this->inputcari         = [];
			$this->iconmarker        = [];
			
			$this->linkfancyboxcss = '<link rel="stylesheet" type="text/css" href="' . $this->host . 'css/jquery.fancybox.min.css">';
			/** @noinspection HtmlUnknownTarget */
			$this->linkwscript = '<script type="text/javascript" src="' . $this->host . 'js/w.min.js"></script>';
			/** @noinspection HtmlUnknownTarget */
			$this->linkmarkerclustererjs = '<script type="text/javascript" src="' . $this->host . 'js/markerclusterer.js"></script>';
			/** @noinspection HtmlUnknownTarget */
			$this->linkmapscriptjs = '<script type="text/javascript" src="' . $this->host . 'js/mapscript.min.js"></script>';
			/** @noinspection HtmlUnknownTarget */
			$this->linkfancyboxjs = '<script type="text/javascript" src="' . $this->host . 'js/jquery.fancybox.min.js"></script>';
			/** @noinspection HtmlUnknownTarget */
			$this->linksweetalertjs = '<script type="text/javascript" src="' . $this->host . 'js/sweetalert2.all.min.js"></script>';
			$this->linkgoogleapi    = '<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . $this->keymap . '&libraries=' . $this->servicegoogle . '&language=id&region=ID&callback=initMap"></script>';
			
		}
		
		/**
		 * @return string
		 */
		protected function getHost () {
			$protocol = "http://";
			if ($_SERVER['HTTPS']) {
				$protocol = "https://";
			}
			return $protocol . $_SERVER['SERVER_NAME'] . '/';
		}
		
		/**
		 * @param $div
		 * @return $this
		 */
		public function setDiv ($div) {
			$this->div = $div;
			return $this;
		}
		
		
		/**
		 * @param $listmarker
		 * @return $this
		 */
		public function setListmarker ($listmarker) {
			$this->listmarker[$this->div] = $listmarker;
			return $this;
		}
		
		
		/**
		 * @param $geojson
		 * @return $this
		 */
		public function setGeojson ($geojson) {
			if ($geojson === TRUE) {
				$this->geojson[$this->div] = $this->geojsondefault;
			} else {
				$this->geojson[$this->div] = $geojson;
			}
			return $this;
		}
		
		
		/**
		 *
		 * DIPANGGIL TERAKHIR DAN HANYA SATU KALI
		 */
		public function renderMap () {
			echo W::style_map();
			echo $this->linkfancyboxcss;
			?>

			<script type="text/javascript">
				/*HANDLE MULTIPLE*/
				$('#google-map').remove();
				window.google={};
				/*END HANDLE MULTIPLE*/

				/*INITIAL DATA*/
				<?php
				if (empty($this->listmarker))
					$this->listmarker[$this->div] = [];
				if (empty($this->zoom))
					$this->zoom[$this->div] = $this->zoomdefault;
				foreach ($this->listmarker as $div => $marker) {
				?>
				if ($('#<?php echo $div;?>').length === 0) {
					$('body').append('<div id="<?php echo $div;?>" class="map"></div>');
				}
				var initmarkers<?php echo $div; ?> = <?php echo json_encode($marker, JSON_NUMERIC_CHECK);?>;
				<?php
				}
				?>

				/*INIT MAP*/

				function initMap(posisicenter, modemap, zoommap) {
					resetVarGlobal();
					<?php
					if($_SESSION['id']){
					?>
					MODEMAP=modemap??false;
					CUSTOMCONTROL=true;
					<?php
					}
					?>

					/*RENDER MAP*/
					if (posisicenter) {
						MAPOPTIONS.center=GLOBALMAPCENTER;
					} else {
						MAPOPTIONS.center=INDONESIA;
						GLOBALMAPCENTER=MAPOPTIONS.center;
					}
					if (zoommap !== undefined) {
						MAPOPTIONS.zoom=GLOBALMAPZOOM;
					} else {
						MAPOPTIONS.zoom=INITZOOMMAP;
						GLOBALMAPZOOM=MAPOPTIONS.zoom;
					}

					MAPOPTIONS.styles=MAPSTYLES;
					MAPOPTIONS.mapTypeId=localStorage.getItem('getMapTypeId')?localStorage.getItem('getMapTypeId') : 'roadmap';
					<?php
					foreach ($this->listmarker as $div => $marker) {
					?>
					<?php
					if ($this->centermap[$div]) {
					?>
					MAPOPTIONS.center=<?php echo $this->centermap[$div];?>;
					<?php
					}
					?>
					<?php
					if ($this->zoom[$div]) {
					?>
					MAPOPTIONS.zoom=<?php echo $this->zoom[$div];?>;
					<?php
					}
					?>


					var autocomplete;
					<?php
					if ($this->inputcari[$div]){
					?>
					autocomplete=new google.maps.places.Autocomplete(
						document.getElementById('<?php echo $this->inputcari[$div];?>'),
						{
							componentRestrictions: {country: 'ID'}
						}
					);
					<?php
					}
					?>

					var <?php echo $div; ?> =;
					new google.maps.Map(document.getElementById('<?php echo $div; ?>'), MAPOPTIONS);
					<?php echo $div; ?>.
					set('zoomControlOptions',
						{
							position: google.maps.ControlPosition.TOP_RIGHT,
							style: google.maps.ZoomControlStyle.SMALL
						}
					);

					google.maps.event.addListener(<?php echo $div; ?>, 'dragend', function () {
						GLOBALMAPCENTER= <?php echo $div; ?>.
						getCenter();
					});

					google.maps.event.addListener(<?php echo $div; ?>, 'maptypeid_changed', function () {
						localStorage.setItem("getMapTypeId", <?php echo $div; ?>.getMapTypeId();
					)
						;
					});
					google.maps.event.addListener(<?php echo $div; ?>, 'zoom_changed', function () {
						GLOBALMAPZOOM= <?php echo $div; ?>.
						zoom;
						GLOBALMAPCENTER= <?php echo $div; ?>.
						getCenter();
					});

					/*DRAWING*/
					
					<?php
					if ($this->geojson[$div] != ''){
					?>
					/*LOAD GEOJSON*/
					var geojsonprops={
						map: <?php echo $div; ?>,
						url: '<?php echo $this->geojson[$div];?>',
						addmarker: '<?php echo $this->addmarker[$div];?>'
					};
					loadGeoJSON(geojsonprops);
					/*END LOAD GEOJSON*/
					<?php
					}
					?>
					
					<?php
					if ($this->onlyonemarker[$div]){
					?>
					var posisi=initmarkers<?php echo $div; ?>;
					var marker;
					if (posisi.lat && posisi.lng) {
						marker=new google.maps.Marker({
							position: posisi,
							map: <?php echo $div;?>,
							animation: google.maps.Animation.BOUNCE,
							icon: posisi.iconmarker??ICONMARKER
						});
						<?php echo $div;?>.
						setCenter(marker.getPosition());
					}
					
					<?php
					/*END ONLY ONE MARKER*/
					if ($this->addmarker[$div]){
					?>
					<?php echo $div;?>.
					addListener('click', function (e) {
						if (marker) {
							marker.setPosition(e.latLng);
						} else {
							marker=new google.maps.Marker({
								position: e.latLng,
								map: <?php echo $div;?>,
								animation: google.maps.Animation.BOUNCE,
								icon: '<?php echo $this->iconmarker[$div] ? $this->iconmarker[$div] : ICONMARKERPERUSAHAAN;?>'
							});
						}
						let lat=e.latLng.lat();
						let lng=e.latLng.lng();

						let latlng=lat + ',' + lng;
						$.get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' + latlng + '&key=<?php echo KEYMAP;?>&language=id&region=ID').then((data) => {
							console.log(data);
							var respon={};
							respon.nomor="";
							respon.jalan="";
							respon.dukuh="";
							respon.kodepos="";
							respon.desa="";
							respon.kecamatan="";
							respon.kabupaten="";
							respon.provinsi="";
							respon.negara="";
							data.results[0].address_components.forEach(function (item, index) {
								if (item.types[0] == "postal_code") respon.kodepos=item.long_name;
								if (item.types[0] == "street_number") respon.nomor=item.long_name;
								if (item.types[0] == "route") respon.jalan=item.long_name;
								if (item.types[0] == "administrative_area_level_5") respon.dukuh=item.long_name;
								if (item.types[0] == "administrative_area_level_4") respon.desa=item.long_name;
								if (item.types[0] == "administrative_area_level_3") respon.kecamatan=item.long_name;
								if (item.types[0] == "administrative_area_level_2") respon.kabupaten=item.long_name;
								if (item.types[0] == "administrative_area_level_1") respon.provinsi=item.long_name;
								if (item.types[0] == "country") respon.negara=item.long_name;
							});

							respon.lokasi=data.results[0].formatted_address;
							respon.lat=lat;
							respon.lng=lng;
							<?php
							foreach ($this->callbackaddmarker[$div] as $keycallback => $callback){
							?>
							if ($('#<?php echo $keycallback;?>').length) {
								$('#<?php echo $keycallback;?>').val(respon.<?php echo $callback;?>);
							} else if ($('#<?php echo $callback;?>').length) {
								$('#<?php echo $callback;?>').val(respon.<?php echo $callback;?>);
							}
							<?php
							}
							?>
						});
					});
					<?php
					}
					}else{
					?>
					/*RENDER MARKER*/
					var initmarkerprops<?php echo $div; ?> ={
						map: <?php echo $div; ?>,
						initmarkers: initmarkers<?php echo $div; ?>,
						drag: true,
						jenis: '',
						oldurl: ''
					};
					initMarker(initmarkerprops<?php echo $div; ?>);
					<?php
					}
					?>
					<?php
					/*POSISI USER*/
					if($this->posisiuser[$div]){?>
					let markerposisiuser=new google.maps.Marker;
					var divParentControl=document.createElement('div');
					var getLokasiControl=new CreateCustomControl(divParentControl, map, 'left', 'Lokasi Anda', 'getlokasi');
					divParentControl.setAttribute("id", "divparentcontrol");
					let infoWindowUser=new google.maps.InfoWindow;
					getLokasiControl.addEventListener('click', function () {
						<?php
						if ($_SERVER['HTTPS']){
						?>
						getPosisiUser(<?php echo $div;?>, markerposisiuser, infoWindowUser);
						<?php
						}else{
						?>
						Swal.fire({
							icon: 'error',
							text: 'Protocol domain harus https://',
						});
						<?php
						}
						?>
					});
					divParentControl.index=1;
					<?php echo $div;?>.
					controls[google.maps.ControlPosition.TOP_LEFT].push(divParentControl);
					<?php
					}
					/*END GET POSISI*/
					?>
					/*SEARCH AUTO COMPLETE*/
					<?php
					if ($this->inputcari[$div]){
					?>
					autocomplete.addListener('place_changed', function () {
						var place=autocomplete.getPlace();
						if (place.geometry) {
							var location=place.geometry.location;
							var latlng=new google.maps.LatLng(location.lat(), location.lng());
							google.maps.event.trigger(<?php echo $div;?>, 'click', {latLng: latlng});
						}
					});
					<?php
					}
					?>
					
					<?php
					}
					/*END FOREACH*/
					?>
				}

			</script>
			<!-- MAP -->
			<?php
			//echo $this->linkfancyboxjs;
			echo $this->linksweetalertjs;
			echo $this->linkmarkerclustererjs;
			echo $this->linkmapscriptjs;
			echo $this->linkgoogleapi;
			echo $this->linkwscript;
		}
		
		
		/**
		 * @param bool $linkgoogleapi
		 * @return $this
		 */
		public function setLinkgoogleapi ($linkgoogleapi = TRUE) {
			if ($linkgoogleapi == FALSE)
				$this->linkgoogleapi = "";
			elseif ($linkgoogleapi != TRUE)
				$this->linkgoogleapi = $linkgoogleapi;
			return $this;
		}
		
		/**
		 * @param bool $linkwscriptjs
		 * @return $this
		 */
		public function setLinkwscriptjs ($linkwscriptjs = TRUE) {
			if ($linkwscriptjs == FALSE)
				$this->linkwscriptjs = "";
			elseif ($linkwscriptjs != TRUE)
				$this->linkwscriptjs = $linkwscriptjs;
			return $this;
		}
		
		/**
		 * @param bool $linkmapscriptjs
		 * @return $this
		 */
		public function setLinkmapscriptjs ($linkmapscriptjs = TRUE) {
			if ($linkmapscriptjs == FALSE)
				$this->linkmapscriptjs = "";
			elseif ($linkmapscriptjs != TRUE)
				$this->linkmapscriptjs = $linkmapscriptjs;
			return $this;
		}
		
		/**
		 * @param bool $linkmarkerclustererjs
		 * @return $this
		 */
		public function setLinkmarkerclustererjs ($linkmarkerclustererjs = TRUE) {
			if ($linkmarkerclustererjs == FALSE)
				$this->linkmarkerclustererjs = "";
			elseif ($linkmarkerclustererjs != TRUE)
				$this->linkmarkerclustererjs = $linkmarkerclustererjs;
			return $this;
		}
		
		/**
		 * @param bool $linkfancyboxcss
		 * @return $this
		 */
		public function setLinkfancyboxcss ($linkfancyboxcss = TRUE) {
			if ($linkfancyboxcss == FALSE)
				$this->linkfancyboxcss = "";
			elseif ($linkfancyboxcss != TRUE)
				$this->linkfancyboxcss = $linkfancyboxcss;
			return $this;
		}
		
		/**
		 * @param bool $linkfancyboxjs
		 * @return $this
		 */
		public function setLinkfancyboxjs ($linkfancyboxjs = TRUE) {
			if ($linkfancyboxjs == FALSE)
				$this->linkfancyboxjs = "";
			elseif ($linkfancyboxjs != TRUE)
				$this->linkfancyboxjs = $linkfancyboxjs;
			return $this;
		}
		
		/**
		 * @param bool $onlyonemarker
		 * @return $this
		 */
		public function setOnlyonemarker ($onlyonemarker = FALSE) {
			$this->onlyonemarker[$this->div] = $onlyonemarker;
			return $this;
		}
		
		/**
		 * @param bool $addmarker
		 * @return $this
		 */
		public function setAddmarker ($addmarker = FALSE) {
			$this->addmarker[$this->div] = $addmarker;
			return $this;
		}
		
		/**
		 * @param int $zoom
		 * @return $this
		 */
		public function setZoom ($zoom) {
			$this->zoom[$this->div] = $zoom;
			return $this;
		}
		
		/**
		 * @param array $callbackaddmarker
		 * @return $this
		 */
		public function getCallbackaddmarker ($callbackaddmarker) {
			$this->callbackaddmarker[$this->div] = $callbackaddmarker;
			return $this;
		}
		
		
		/**
		 * @param $inputcari
		 * @return $this
		 */
		public function setInputcari ($inputcari) {
			$this->inputcari[$this->div] = $inputcari;
			return $this;
		}
		
		
		/**
		 * @param $iconmarker
		 * @return $this
		 */
		public function setIconmarker ($iconmarker) {
			$this->iconmarker[$this->div] = $iconmarker;
			return $this;
		}
		
		
		public function setCentermap ($lat, $lng) {
			$this->centermap[$this->div] = '{lat:' . $lat . ',lng:' . $lng . '}';
			return $this;
		}
		
		/**
		 * @param mixed $posisiuser
		 * @return $this
		 */
		public function getPosisiuser ($posisiuser) {
			$this->posisiuser[$this->div] = $posisiuser;
			return $this;
		}
	}