<template>
<a href="#" @click.prevent="$emit('select', item)">
  {{item.name}}
</a>
</template>

<script>
export default {
  props: ['item'],
}
</script>
