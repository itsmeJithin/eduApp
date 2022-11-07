<template>
  <div>
    <ul class="nav nav-pills justify-content-end" id="pills-tab" role="group">
      <li class="nav-item">
        <button
          class="btn btn-secondary p-1 pull-right active p-l-15 p-r-15 btn-animated from-left"
          id="pills-home-tab"
          data-toggle="pill"
          aria-selected="true"
          @click="toggleView('GRID')"
        >
          <span>Grid View</span>
          <span class="material-icons large m-r-5 hidden-block">view_module</span>
        </button>
      </li>
      <li class="nav-item p-0">
        <button
          class="btn btn-secondary p-1 pull-right p-l-15 p-r-15 btn-animated from-left"
          id="pills-contact-tab"
          data-toggle="pill"
          aria-selected="false"
          @click="toggleView('LIST')"
        >
          <span>List View</span>
          <span class="material-icons large hidden-block">view_list</span>
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: "grid-list-view",
  components: {},
  props: {},
  data() {
    return {};
  },
  mounted() {},
  methods: {
    toggleView(type) {
      this.$emit("toggle-view", type);
    }
  }
};
</script>
