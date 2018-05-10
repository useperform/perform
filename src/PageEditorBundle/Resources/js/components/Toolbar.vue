<template>
  <div class="p--local">
    <a v-if="!visible" class="p-comp-page-editor-icon" :class="positionClasses" href="#" @click.prevent="show">
      <i class="fa fa-pencil"></i>
    </a>
    <div v-if="visible" class="p-comp-page-editor-toolbar" :class="positionClasses">
      <a @click.prevent="hide" class="control">
        <i class="fa fa-times"></i>
      </a>
      <a @click.prevent="togglePullDirection" class="control">
        <i class="fa fa-exchange"></i>
      </a>
      <a v-if="versionLoaded" class="version-title" :class="{'active': showingMenu}" @click.prevent="toggleVersionMenu">
        {{currentVersion.title}}
      </a>
      <div class="version-info">
        <p v-if="versionLoaded">
          Created
          <span>{{formatDateTime(currentVersion.createdAt)}}</span>
          <br/>
          Last save
          <span>{{formatDateTime(currentVersion.updatedAt)}}</span>
        </p>
        <p v-else>
          <i class="fa fa-spinner fa spin"></i>
        </p>
      </div>
      <div v-if="showingMenu" class="version-menu">
        <a @click.prevent="loadVersion(version.id)" v-for="version in versions">
          {{version.title}}
        </a>
      </div>
      <div class="actions">
        <a @click.prevent="save" title="Save the changes to this version">
          Save
        </a>
        <a @click.prevent="publish" :disabled="currentVersion.published" title="Use this version">
          Publish
        </a>
        <a :href="finishUrl">
          Exit page editor
        </a>
      </div>
    </div>
  </div>
</template>

<script>
 import formatDate from 'date-fns/format';

 export default {
   props: [
     'finishUrl',
   ],

   data() {
     return {
       visible: true,
       pullRight: false,
       showingMenu: false,
     };
   },

   computed: {
     currentVersion() {
       return this.$store.state.currentVersion;
     },
     versions() {
       return this.$store.state.versions;
     },
     versionLoaded() {
       return !!this.$store.state.currentVersion.id;
     },
     positionClasses() {
       return {
         left: !this.pullRight,
         right: this.pullRight,
       };
     }
   },

   methods: {
     save() {
       this.$store.dispatch('save');
     },

     publish() {
       this.$store.dispatch('publish');
     },

     hide() {
       this.visible = false;
     },

     show() {
       this.visible = true;
     },

     loadVersion(versionId) {
       this.$store.dispatch('loadVersion', {
         versionId
       });
       this.showingMenu = false;
     },

     toggleVersionMenu() {
       this.showingMenu = !this.showingMenu;
     },

     togglePullDirection() {
       this.pullRight = !this.pullRight;
     },

     formatDateTime(date) {
       return formatDate(date, 'H:m Do MMMM YYYY')
     }
   }
 };
</script>
