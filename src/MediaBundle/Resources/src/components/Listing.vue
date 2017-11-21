<template>
<div>
  <router-link to="/upload" class="btn btn-primary">Upload</router-link>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <table class="table media-listing">
            <thead>
              <th>Name</th>
              <th>Type</th>
              <th>Preview</th>
              <th></th>
            </thead>
            <tbody>
              <tr v-for="file in files">
                <td>
                  {{file.name}}
                </td>
                <td>
                </td>
                <td>
                </td>
                <td>
                  <a href="#" class="btn btn-danger">Delete</a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import axios from 'axios'

export default {
  created() {
    this.fetchData();
  },
  data() {
    return {
      files: []
    };
  },
  methods: {
    fetchData() {
      axios.get('/admin/media/find').then(response => {
        this.files = response.data;
      });
    }
  }
}
</script>
