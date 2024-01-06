<template>
<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-12 col-xl-9">
            <div class="card-header px-0 mt-2 bg-transparent clearfix">
                <h4 v-if="driver.name" class="float-left pt-2">Driver location</h4>
                <h4 v-else class="float-left pt-2">Drivers locations</h4>
            </div>
            <div v-if="driver.name" class="form-group row justify-content-md-center py-4">
                <label class="col-md-3">Driver name</label>
                <div class="col-md-9">
                    {{driver.name}}
                </div>
            </div>
            <div v-if="driver.name" class="form-group row justify-content-md-center py-4">
                <label class="col-12">Driver on map</label>
            </div>
            <div id="address-map-container" style="width:100%;height:800px; ">
                <div style="width: 100%; height: 100%" id="address-map"></div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import mapboxgl from "mapbox-gl";
export default {
    data() {
        return {
            driver: {
                marker: {},
                infowindow:{},
                last_loc_update_time:{},
                speed:{}
            },
            drivers: {},
            errors: {},
            loading: true,
            map: {},
            center: [0, 0],
        }
    },
    props: {
        accesstoken: String,
    },
    mounted() {
        this.getDriversData();
    },
    methods: {
        distance(lat1, lon1, lat2, lon2, unit) {
            if ((lat1 == lat2) && (lon1 == lon2)) {
                return 0;
            }
            else {
                var radlat1 = Math.PI * lat1/180;
                var radlat2 = Math.PI * lat2/180;
                var theta = lon1-lon2;
                var radtheta = Math.PI * theta/180;
                var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
                if (dist > 1) {
                    dist = 1;
                }
                dist = Math.acos(dist);
                dist = dist * 180/Math.PI;
                dist = dist * 60 * 1.1515;
                if (unit=="K") { dist = dist * 1.609344 }
                if (unit=="N") { dist = dist * 0.8684 }
                return dist;
            }
        },
        getDriversData() {
            let str = window.location.pathname
            let res = str.split("/")
            let driver_id = null;
            if(res.length==4)
                driver_id = res[2];
            
            if(driver_id)
            {
                this.getDriver(driver_id);
            }
            else
            {
                this.getAllDrivers();
            }
        },
        getDriver(driver_id){
            this.loading = true
            axios.get(`/api/drivers/getDriver/${driver_id}`)
                .then(response => {
                    this.driver = response.data;
                    this.center = [this.driver.last_longitude, this.driver.last_latitude];
                    // this.updateMap();
                    this.createMap().then(() => {
                        this.loading = false;
                        // this.map.setCenter(this.center);
                        this.driver.infowindow = new mapboxgl.Popup({ offset: 25 }).setText(
                            `Driver: ${this.driver.name}`
                        );
                        const el = document.createElement('div');
                        const width = 32;
                        const height = 32;
                        el.className = 'marker';
                        el.style.backgroundImage = `url(/icon/bus.png)`;
                        el.style.width = `${width}px`;
                        el.style.height = `${height}px`;
                        el.style.backgroundSize = '100%';
                        this.driver.marker = new mapboxgl.Marker(el)
                                .setLngLat(this.center)
                                .setPopup(this.driver.infowindow)
                                .addTo(this.map);
                            });
                        this.fitBounds();
                    window.Echo.channel(this.driver.channel)
                    .listen('LocationChangeEvent', (e) => {
                        var payloadData = JSON.parse(e.data);
                        this.driver.speed = parseFloat(payloadData.speed).toFixed(1);
                        this.driver.last_latitude = payloadData.lat;
                        this.driver.last_longitude = payloadData.lng;

                        // this.driver.last_loc_update_time = payloadData.time;
                        this.driver.marker.setLngLat([payloadData.lng, payloadData.lat]);
                        this.fitBounds();
                    });
                })
                .catch(error => {
                    this.$toasted.global.error('Driver does not exist!')
                })
                .then(() => {
                    this.loading = false
                })
        },
        getAllDrivers(){
            this.loading = true
            axios.get(`/api/drivers/all`)
                .then(response => {
                    this.drivers = response.data;
                    console.log(this.drivers);
                    this.updateMapAllDrivers();
                    this.drivers.forEach(d => {
                        window.Echo.channel(d.channel)
                        .listen('LocationChangeEvent', (e) => {
                            var payloadData = JSON.parse(e.data);
                            var index = this.drivers.map(function(o) { return o.bus_id; }).indexOf(payloadData.bus_id);

                            var latlng =  [payloadData.lng, payloadData.lat]
                            this.drivers[index].marker.setLngLat(latlng);
                            this.drivers[index].speed = parseFloat(payloadData.speed).toFixed(1);
                            this.drivers[index].last_latitude = payloadData.lat;
                            this.drivers[index].last_longitude = payloadData.lng;
                            // this.drivers[index].last_loc_update_time = payloadData.time;
                            this.fitBoundsAllDrivers();
                        });
                    });

                })
                .catch(error => {
                    this.$toasted.global.error('Error in retrieving drivers!')
                })
                .then(() => {
                    this.loading = false
                })
        },
        updateMapAllDrivers() {
            var init_latlng;
            this.drivers.forEach(d => {
                init_latlng = [d.last_longitude, d.last_latitude]
                return;
            });
            this.center = init_latlng;
            this.createMap().then(() => {
                for (var i = 0; i < this.drivers.length; i++) {
                    this.drivers[i].infowindow = new mapboxgl.Popup({ offset: 25 }).setText(
                        `Driver: ${this.drivers[i].name}`
                    );
                    var driver_latlng = [this.drivers[i].last_longitude, this.drivers[i].last_latitude]
                    const el = document.createElement('div');
                    const width = 32;
                    const height = 32;
                    el.className = 'marker';
                    el.style.backgroundImage = `url(/icon/bus.png)`;
                    el.style.width = `${width}px`;
                    el.style.height = `${height}px`;
                    el.style.backgroundSize = '100%';
                    this.drivers[i].marker = new mapboxgl.Marker(el)
                            .setLngLat(driver_latlng)
                            .setPopup(this.drivers[i].infowindow)
                            .addTo(this.map);
                }
                this.fitBoundsAllDrivers();
            });

        },
        async createMap() {
            try {
                mapboxgl.accessToken = this.accesstoken;
                this.map = new mapboxgl.Map({
                    container: "address-map",
                    style: "mapbox://styles/mapbox/streets-v11",
                    center: this.center,
                    zoom: 11,
                });
            } catch (err) {
                console.log("map error", err);
            }
        },
        fitBoundsAllDrivers() {
            var driver_latlng = [this.drivers[0].last_longitude, this.drivers[0].last_latitude]
            var bounds = new mapboxgl.LngLatBounds(driver_latlng, driver_latlng);
            var school_added = false;
            this.drivers.forEach(d => {
                var driver_latlng = [d.last_longitude, d.last_latitude]
                if (!bounds.contains(driver_latlng)) {
                    // marker is not within map bounds
                    bounds.extend(driver_latlng);
                }
                if(d.speed)
                {
                    d.infowindow.setText(d.name + "</br>" + d.speed + " km/h");
                }
                if(!school_added && d.school)
                {
                    school_added = true;
                    if(d.school.latitude && d.school.longitude)
                    {
                        console.log("Adding school");
                        var latlng = [d.school.longitude, d.school.latitude]
                        var infowindow = new mapboxgl.Popup({ offset: 25 }).setText(
                            `School: ${d.school.name}`
                        );
                        const el = document.createElement('div');
                        const width = 64;
                        const height = 64;
                        el.className = 'marker';
                        el.style.backgroundImage = `url(/icon/school.png)`;
                        el.style.width = `${width}px`;
                        el.style.height = `${height}px`;
                        el.style.backgroundSize = '100%';
                        new mapboxgl.Marker(el)
                            .setLngLat(latlng)
                            .setPopup(infowindow)
                            .addTo(this.map);
                        if (!bounds.contains(latlng)) {
                            // marker is not within map bounds
                            bounds.extend(latlng);
                        }
                    }
                }
            });
            // if (bounds_updated)
            this.map.fitBounds(bounds, {padding: 100});
        },
        fitBounds() {
            var driver_latlng = [this.driver.last_longitude, this.driver.last_latitude]
            var bounds = new mapboxgl.LngLatBounds(driver_latlng, driver_latlng);
            if(this.driver.speed)
            {
                this.driver.infowindow.setText(this.driver.speed + " km/h");
                // this.driver.infowindow.open(this.map, this.driver.marker);
            }

            if (!bounds.contains(driver_latlng)) {
                // marker is not within map bounds
                bounds.extend(driver_latlng);
            }
            if(this.driver.parents)
            {
                console.log("Adding parents");
                console.log(this.driver.parents);
                this.driver.parents.forEach(parent => {
                    if(parent.address_latitude && parent.address_longitude)
                    {
                        var latlng = [parent.address_longitude, parent.address_latitude]
                        console.log("Adding parent");
                        console.log(parent);
                        var infowindow = new mapboxgl.Popup({ offset: 25 }).setText(
                            `Parent: ${parent.name}`
                        );
                            new mapboxgl.Marker({
                                draggable: false,
                                color: "#D80739",
                            })
                            .setLngLat(latlng)
                            .setPopup(infowindow)
                            .addTo(this.map);
                        if (!bounds.contains(latlng)) {
                            // marker is not within map bounds
                            bounds.extend(latlng);
                        }
                        console.log("fitBounds", bounds);
                    }
                });
            }

            if(this.driver.school)
            {
                if(this.driver.school.latitude && this.driver.school.longitude)
                {
                    var latlng = [this.driver.school.longitude, this.driver.school.latitude]
                        var infowindow = new mapboxgl.Popup({ offset: 25 }).setText(
                            `School: ${this.driver.school.name}`
                        );
                        const el = document.createElement('div');
                        const width = 64;
                        const height = 64;
                        el.className = 'marker';
                        el.style.backgroundImage = `url(/icon/school.png)`;
                        el.style.width = `${width}px`;
                        el.style.height = `${height}px`;
                        el.style.backgroundSize = '100%';
                        new mapboxgl.Marker(el)
                            .setLngLat(latlng)
                            .setPopup(infowindow)
                            .addTo(this.map);
                    if (!bounds.contains(latlng)) {
                        // marker is not within map bounds
                        bounds.extend(latlng);
                    }
                }
            }
            // if (bounds_updated)
            this.map.fitBounds(bounds, {padding: 100});
        },
    },
}
</script>
