<template>
<div>
  <UploadButton v-if="allowUpload" @upload="upload" />
  <button v-if="!lockLayout" @click="layout = 0">Table</button>
  <button v-if="!lockLayout" @click="layout = 1">Grid</button>
  <component
     :is="layoutComponent"
     :items="items"
     :allowSelect="allowSelect"
     @toggleSelect="toggleSelect"
     />
</div>
</template>

<script>
import UploadButton from './UploadButton'
import FileTable from './FileTable'
import FileGrid from './FileGrid'
import Vue from 'vue'
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
    allowSelect: {
      type: Boolean,
      default: false,
    },
    selectLimit: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      layout: 0,
        selectedIds: {},
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
    items() {
      let listing = this;
      return this.$store.state.files.map((file) => {
        return {
          file,
          selected: !!listing.selectedIds[file.id]
        }
      });
    },
    layoutComponent() {
      return this.layout === 0 ? FileTable : FileGrid;
    },
    selectedFiles() {
      return this.items.filter((item) => {
        return !!item.selected;
      }).map(item => item.file);
    },
    selectedCount() {
      return Object.values(this.selectedIds).reduce((prev, current) => {
        return !!current ? prev + 1 : prev;
      }, 0);
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
    },
      reset() {
          this.selectedIds = {};
      },
      toggleSelect(id) {
          if (!this.allowSelect) {
              return;
          }
          const newValue = !this.selectedIds[id];
          // true is selecting a new file
          // check it is allowed
          if (newValue && this.selectedCount == this.selectLimit && this.selectLimit !== 0) {
              return;
          }

          Vue.set(this.selectedIds, id, newValue);
      }
  }
}
</script>
