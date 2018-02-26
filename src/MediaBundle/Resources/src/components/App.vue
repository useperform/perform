<template>
  <div id="app">
    <h1>{{ title }}</h1>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  data () {
    return {
      title: 'Media'
    }
  }
}
</script>
