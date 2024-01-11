<template>
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-md-9 col-xl-7">
        <div class="card-header px-0 mt-2 bg-transparent clearfix">
          <h4 class="float-left pt-2">
            <i class="card-icon fas fa-landmark"></i> School
          </h4>
          <div class="card-header-actions mr-1">
            <a
              class="btn btn-primary"
              href="#"
              :disabled="submitting"
              @click.prevent="updateSchool"
            >
              <i class="fas fa-spinner fa-spin" v-if="submitting"></i>
              <i class="fas fa-check" v-else></i>
              <span class="ml-1">Save</span>
            </a>
          </div>
        </div>
        <div class="card-body px-0">
          <form class="form-horizontal">
            <hr class="my-3" />
            <div class="form-group row justify-content-md-center">
              <label class="col-md-3">School Name</label>
              <div class="col-md-9">
                <input
                  class="form-control"
                  :class="{ 'is-invalid': errors.name }"
                  type="text"
                  v-model="school.name"
                  placeholder="Enter school name"
                />
                <div class="invalid-feedback" v-if="errors.name">
                  {{ errors.name[0] }}
                </div>
              </div>
            </div>
                    <content-placeholders v-show="loading">
                        <content-placeholders-text :lines="3" />
                    </content-placeholders>
            <div class="form-group row justify-content-md-center" v-if="!loading">
              <label class="col-md-3">School Address</label>
              <div class="col-md-9">
                <input
                  type="text"
                  id="address-input"
                  readonly
                  disabled
                  v-model="school.address"
                  class="form-control"
                  :class="{
                    'is-invalid':
                      errors.address || errors.latitude || errors.longitude,
                  }"
                />
                <small class="form-text text-muted">
                  Drag and drop the marker to fine tune the school address.
                </small>
                <div class="invalid-feedback" v-if="errors.address">
                  {{ errors.address[0] }}
                </div>
                <div
                  class="invalid-feedback"
                  v-else-if="errors.latitude || errors.longitude"
                >
                  Invalid address
                </div>
                <input
                  type="hidden"
                  v-model="school.latitude"
                  id="address-latitude"
                  value="0"
                />
                <input
                  type="hidden"
                  v-model="school.longitude"
                  id="address-longitude"
                  value="0"
                />
              </div>
            </div>
          </form>
          <div id="address-map-container" style="width:100%;height:400px; ">
            <div style="width: 100%; height: 100%" id="map"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import mapboxgl from "mapbox-gl";
import axios from "axios";
import MapboxGeocoder from "@mapbox/mapbox-gl-geocoder";
import "@mapbox/mapbox-gl-geocoder/dist/mapbox-gl-geocoder.css";

export default {
  data() {
    return {
      school: {
        id: "",
        name: "",
        channel: "",
        address: "",
        latitude: "",
        longitude: "",
      },
      errors: {},
      loading: true,
      submitting: false,
      preview: {},
      marker: null,
    };
  },
  props: {
    accesstoken: String,
  },
  components: {},
  mounted() {
    this.getSchool();
  },
  methods: {
    getSchool() {
      this.loading = true;
      axios
        .get(`/api/school/getSchool`)
        .then((response) => {
          if (response.data) {
            //console.log(response.data);
            this.school.id = response.data.id;
            this.school.name = response.data.name;
            this.school.channel = response.data.channel;
            this.school.address = response.data.address;
            this.school.latitude = response.data.latitude;
            this.school.longitude = response.data.longitude;
          }
          this.createMap().then(() => {
            this.loading = false;
            this.center = [this.school.longitude, this.school.latitude];
            this.map.setCenter(this.center);
            this.updateMarker();
          });
        })
        .catch((error) => {
          if (error.response) this.errors = error.response.data.errors;
          this.loading = false;
        });
    },

    updateMarker(e) {
      if (this.marker) {
        this.marker.remove();
      }
      this.marker = new mapboxgl.Marker({
        draggable: true,
        color: "#D80739",
      })
        .setLngLat(e ? e.result.center : this.center)
        .addTo(this.map)
        .on("dragend", (d) => {
          this.center = Object.values(d.target.getLngLat());
          this.marker.setLngLat(this.center);
          this.map.setCenter(this.center);
          this.getLocation().then(() => {
            this.setSchoolLocationAddress(
                this.center[1],
                this.center[0],
                this.school.address
            );
          });
        });
        this.center = Object.values(this.marker.getLngLat());
        this.map.flyTo({
          center: this.center,
          zoom: 15,
        });
        this.getLocation().then(() => {
          this.setSchoolLocationAddress(
            this.center[1],
            this.center[0],
            this.school.address
          );
        });
    },

    setSchoolLocationAddress(lat, lng, address) {
      this.school.latitude = lat;
      this.school.longitude = lng;
      this.school.address = address;
    },

    async getLocation() {
  try {
    this.loading = true;
    const response = await axios.get(
      `https://api.mapbox.com/geocoding/v5/mapbox.places/${this.center[0]},${this.center[1]}.json?access_token=${this.accesstoken}`
    );
    this.loading = false;
    this.school.address = response.data.features[0].place_name;
  } catch (err) {
    this.loading = false;
    console.log(err);
  }
},

    async createMap() {
      try {
        mapboxgl.accessToken = this.accesstoken;
        this.map = new mapboxgl.Map({
          container: "map",
          style: "mapbox://styles/mapbox/streets-v11",
          center: this.center,
          zoom: 11,
        });

        let geocoder = new MapboxGeocoder({
          accessToken: this.accesstoken,
          mapboxgl: mapboxgl,
          marker: false,
          placeholder: "Search",
        });
        this.map.addControl(geocoder);
        geocoder.on("result", (e) => {
            this.updateMarker(e);
        });
      } catch (err) {
        console.log("map error", err);
      }
    },
    updateSchool() {
      if (!this.submitting) {
        this.submitting = true;
        axios
          .put(`/api/school/updateSchool`, this.school)
          .then((response) => {
            this.errors = {};
            this.submitting = false;
            this.$toasted.global.error("School updated!");
          })
          .catch((error) => {
            if (error.response) this.errors = error.response.data.errors;
            this.submitting = false;
          });
      }
    },
  },
};
</script>
