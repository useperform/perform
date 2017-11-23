<template>
<label class="btn btn-primary">
  Add files <input type="file" @change="changed" multiple hidden>
</label>
</template>

<script>
export default {
  methods: {
    changed(e) {
      const files = e.target.files;
      for (let i=0; i < files.length; i++) {
        this.$emit('upload', files[i]);
      }
    }
  }
}
</script>
