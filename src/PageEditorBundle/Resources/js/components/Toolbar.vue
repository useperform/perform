<template>
  <div class="p--local">
    <div class="p-comp-page-editor-toolbar">
      <div class="block">
        <a class="version-menu-toggler" @click.prevent="toggleVersionMenu">
          {{currentTitle}}
        </a>
      </div>
      <ul v-if="showingMenu" class="version-menu">
        <li @click.prevent="loadVersion(version.id)" v-for="version in versions">
          {{version.title}}
        </li>
      </ul>
      <div class="block">
        <a @click.prevent="save">
          Save
        </a>
      </div>
      <div class="block">
        <a @click.prevent="publish">
          Publish
        </a>
      </div>
      <div class="block">
        <a :href="finishUrl">
          Finish editing
        </a>
      </div>
    </div>
  </div>
</template>

<script>
 export default {
   props: [
     'finishUrl',
   ],

   data() {
     return {
       showingMenu: false,
     };
   },

   computed: {
     currentTitle() {
       return this.$store.state.versionTitle;
     },
     versions() {
       return this.$store.state.versions;
     }
   },

   methods: {
     save() {
       this.$store.dispatch('save');
     },

     publish() {
       this.$store.dispatch('publish');
     },

     loadVersion(versionId) {
       this.$store.dispatch('loadVersion', {
         versionId
       });
       this.showingMenu = false;
     },

     toggleVersionMenu() {
       this.showingMenu = !this.showingMenu;
     }
   }
 };
</script>
