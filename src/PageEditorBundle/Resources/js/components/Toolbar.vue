<template>
  <div class="p--local">
    <div class="p-comp-page-editor-toolbar">
      <a class="version-menu-toggler" @click.prevent="toggleVersionMenu">
        {{currentTitle}}
      </a>
      <ul v-if="showingMenu" class="version-menu">
        <li @click.prevent="loadVersion(version.id)" v-for="version in versions">
          {{version.title}}
        </li>
      </ul>
      <a @click.prevent="save">
        Save
      </a>
      <a @click.prevent="publish">
        Publish
      </a>
      <a :href="finishUrl">
        Finish editing
      </a>
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
