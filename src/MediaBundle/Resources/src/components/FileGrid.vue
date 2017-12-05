<template>
<div class="row">
  <div class="col-lg-12 perform-media-grid">
    <FileGridItem
       v-for="item in items"
       :key="item.file.id"
       :file="item.file"
       :selected="item.selected"
       @toggleSelect="$emit('toggleSelect', item.file.id)"
       />
  </div>
</div>
</template>

<script>
import FileGridItem from './FileGridItem'

export default {
  props: ['items'],
  components: {
    FileGridItem
  },
}
</script>
