<template>
<div class="row">
  <div class="col-lg-12">
    <FileGridItem :key="file.id" v-for="file in files" v-bind="file" />
  </div>
</div>
</template>

<script>
import FileGridItem from './FileGridItem'

export default {
  props: ['files'],
  components: {
    FileGridItem
  },
}
</script>
