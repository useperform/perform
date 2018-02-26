<template>
  <div>
    <div class="d-flex justify-content-between">
      <UploadButton v-if="allowUpload" @upload="upload" />

      <div class="btn-group" role="group" aria-label="Layouts">
        <button v-if="!lockLayout" :class="btnCssClass(0)" @click.prevent="layout = 0">
          <i class="fa fa-th-list"></i>
        </button>
        <button v-if="!lockLayout" :class="btnCssClass(1)" @click.prevent="layout = 1">
          <i class="fa fa-th"></i>
        </button>
      </div>
    </div>
    <component
      :is="layoutComponent"
      :items="items"
      @toggleSelect="toggleSelect"
    />
    <a @click="$store.dispatch('prevPage')" class="btn btn-light">Prev</a>
    <a @click="$store.dispatch('nextPage')" class="btn btn-light">Next</a>

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
    allowMultipleSelect: {
      type: Boolean,
      default: false,
    },
    selectLimit: {
      type: Number,
      default: 0,
    },
    page: {
      type: Number,
      default: 1,
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
    this.fetchData();
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
  watch: {
    '$route'() {
      this.page = this.$route.query.page;
      this.fetchData();
    }
  },
  methods: {
    upload(file) {
      const taskId = Perform.base.tasks.add('Uploading '+file.name, 0, 100);
      const that = this;
      upload(file, {
        progress(progress) {
          Perform.base.tasks.setProgress(taskId, progress);
        },
        complete() {
          that.fetchData();
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
    fetchData() {
      this.$store.dispatch('find', this.page);
    },
    toggleSelect(id) {
      if (!this.allowSelect) {
        return;
      }
      // true if selecting a new file
      const newValue = !this.selectedIds[id];

      if (newValue) {
        // deselect current selection if in single mode
        if (!this.allowMultipleSelect) {
          this.selectedIds = {};
        }
        // not allowed if the select limit has been reached
        if (this.selectedCount == this.selectLimit && this.selectLimit !== 0) {
          return;
        }
      }

      Vue.set(this.selectedIds, id, newValue);
    },
    btnCssClass(layout) {
      return {
        'btn': true,
        'btn-light': true,
        'active': this.layout === layout,
      }
    }
  }
}
</script>
