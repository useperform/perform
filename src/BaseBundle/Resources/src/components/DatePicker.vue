<template>
  <div class="input-group">
    <span class="input-group-addon">
      <span class="fa fa-calendar"></span>
    </span>
    <input type="text" class="form-control" :value="value" />
  </div>
</template>

<script>
export default {
  props: ['value'],
  data() {
    return {
    };
  }
}
</script>
