<template>
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
</template>

<script>
import FileRow from './FileRow'

export default {
  props: ['files'],
  components: {
    FileRow
  },
}
</script>
