<template>
  <div class="p-comp-media-filetable">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-5 offset-1">
            Name
          </div>
          <div class="col-2">
            Type
          </div>
        </div>
      </div>
      <div class="card-body">
        <FileRow :key="item.file.id" v-for="item in items" :file="item.file" />
        </table>
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
