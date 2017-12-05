<template>
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-body">
        <table class="table">
          <thead>
            <th>Name</th>
            <th>Type</th>
            <th>Preview</th>
            <th></th>
          </thead>
          <tbody>
            <FileRow :key="item.file.id" v-for="item in items" v-bind="item.file" />
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
  props: ['items'],
  components: {
    FileRow
  },
}
</script>
