<template>
  <div>
    <router-link to="/" class="btn btn-secondary">Cancel</router-link>
    <div class="alert alert-info">
      <p>Use this page to upload new files into the library.</p>
    </div>
    <div class="row mt">
      <div class="col-lg-3">
        <i class="fa fa-plus"></i>
        <span>Add files</span>
        <input class="file-upload" data-url="/admin/media/upload" type="file" name="files" multiple>
      </div>
      <div class="col-lg-9 file-list">
      </div>
    </div>
  </div>
</template>
