<template>
  <div
    :id="cardId"
    class="row m-t-5 alert-default p-2 m-l-5 rounded-lg"
    @mouseover="isHovering = true"
    @mouseout="isHovering = false"
    :class="isHovering?'shadow-sm ':''"
  >
    <div class="col-md-4">
      <slot name="card-title"></slot>
    </div>
    <div class="small-text text-muted col-md-7">
      <slot name="card-content"></slot>
    </div>
    <div class="col-md-1 text-right" v-show="isHovering">
      <a href="#" @click.prevent="onEdit()" v-if="cardOptions.edit">
        <i class="btn-rounded text-black-50 pg-icon">edit</i>
      </a>
      <a href="#" @click.prevent="onDelete()" v-if="cardOptions.delete">
        <i class="btn-rounded text-black-50 pg-icon">trash_alt</i>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  name: "advanced-card",
  components: {},
  props: {
    cardId: {
      type: String,
      default: () => {
        const dt = new Date().getTime();
        const id = "card-".concat(dt);
        return id;
      }
    },
    options: Object
  },
  data() {
    return {
      cardOptions: {
        edit: true,
        delete: true,
        settings: true,
        collapse: true,
        refresh: true,
        resize: true,
        close: true,
        separator: true
      },
      isHovering: false
    };
  },
  mounted() {
    const self = this;
    Object.assign(self.cardOptions, self.options);
    self.init();
  },
  methods: {
    init() {
      const self = this;
      window.$("#".concat(self.cardId)).card({
        onRefresh() {
          self.onRefresh();
        },
        onCollapse() {
          self.onCollapse();
        },
        onExpand() {
          self.onExpand();
        },
        onMaximize() {
          self.onMaximize();
        },
        onRestore() {
          self.onRestore();
        },
        onClose() {
          self.onClose();
        }
      });
    },
    onEdit() {
      this.$emit("on-edit");
    },
    onDelete() {
      this.$emit("on-delete");
    },
    onRefresh() {
      this.$emit("on-refresh");
    },
    onCollapse() {},
    onExpand() {
      // Refresh while in collapsed state
      this.$emit("on-expand");
    },
    onMaximize() {
      this.$emit("on-maximize");
    },
    onRestore() {
      this.$emit("on-minimize");
    },
    onClose() {
      this.$emit("on-refresh");
    }
  }
};
</script>
