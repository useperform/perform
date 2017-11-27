<template>
<div>
  <UploadButton v-if="allowUpload" @upload="upload" />
  <button v-if="!lockLayout" @click="layout = 0">Table</button>
  <button v-if="!lockLayout" @click="layout = 1">Grid</button>
  <component :is="layoutComponent" :files="files" />
</div>
</template>

<script>
import UploadButton from './UploadButton'
import FileTable from './FileTable'
import FileGrid from './FileGrid'
import upload from '../api/upload'

export default {
  props: {
    allowUpload: {
      type: Boolean,
      default: true,
    },
    initialLayout: {
      type: Number,
      // 0 for table, 1 for grid
      default: 0,
    },
    lockLayout: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      layout: 0,
    }
  },
  created() {
    this.layout = this.initialLayout;
    this.$store.dispatch('find');
  },
  components: {
    UploadButton,
    FileTable
  },
  computed: {
    files() {
      return this.$store.state.files;
    },
    layoutComponent() {
      return this.layout === 0 ? FileTable : FileGrid;
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
