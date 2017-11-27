<template>
<div>
  <UploadButton @upload="upload" />
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
              <FileRow :key="file.id" v-for="file in files" v-bind="file" />
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import UploadButton from './UploadButton'
import FileRow from './FileRow'
import upload from '../api/upload'

export default {
  created() {
    this.$store.dispatch('find');
  },
  components: {
    UploadButton,
    FileRow
  },
  computed: {
    files() {
      return this.$store.state.files;
    }
  },
  methods: {
    upload(file) {
      const taskId = Perform.base.tasks.add('Uploading '+file.name, 0, 100);
      const store = this.$store;
      upload(file, {
        progress(progress) {
          Perform.base.tasks.setProgress(taskId, progress);
        },
        complete() {
          store.dispatch('find');
        },
        error(response) {
          Perform.base.showError(response.data.message);
          Perform.base.tasks.cancel(taskId);
        }
      });
    }
  }
}
</script>
