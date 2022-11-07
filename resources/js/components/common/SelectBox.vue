<template>
  <form role="form">
    <div class="form-group">
      <select class="full-width"
              :id="id"
              :multiple="selectOptions.multiple">
      </select>
    </div>
  </form>
</template>

<script>
  export default {
    name: "select-box",
    props: {
      id: {
        type: String,
        default() {
          const randomNo = Math.random().toString(36).substring(2);
          const dateTime = new Date().getTime();
          return `select-box-${dateTime}-${randomNo}`;
        }
      },
      options: {
        type: Object,
        default() {
          return {};
        }
      },
      items: {
        type: Array,
        default() {
          return [];
        }
      },
      value: null,
      disableSelect: {
        type: Boolean,
        default() {
          return false;
        }
      }
    },
    model: {
      event: 'on-change'
    },
    watch: {
      value() {
        this.selectedItems = this.value;
        this.init();
      },
      items() {
        this.init();
      },
      disableSelect() {
        window.$(`#${this.id}`).prop("disabled", this.disableSelect);
      }
    },
    updated() {
      const self = this;
      Object.assign(self.selectOptions, self.options);
      self.init();
    },
    data() {
      return {
        selectOptions: {
          multiple: false,
          placeholder: "select data",
          valueKey: "",
          textKey: ""
        },
        selectedItems: this.value,
        optionItems : []
      }
    },
    mounted() {
      const self = this;
      Object.assign(self.selectOptions, self.options);
      self.init();
    },
    methods: {
      init() {
        // Avoid applying plugin to <select> with data-init-plugin="select2"
        // Only apply on elements that don't have data-init-plugin="select2"
        const self = this;
        self.optionItems = self.items.map(item=> {
          item.id = item.id || item[self.selectOptions.valueKey]; // replace valueKey with your identifier
          item.text = item.text || item[self.selectOptions.textKey]; // replace textKey with the property used for the text
          return item;
        });
        window.$(`#${self.id}`).empty();
        window.$(`#${self.id}`).unbind();
        window.$(`#${self.id}`).prop("disabled", this.disableSelect);
        window.$(`#${self.id}`)
          .select2({
            placeholder: self.selectOptions.placeholder,
            data: self.optionItems
          })
          .val(self.selectedItems)
          .trigger("change")
          .on("change", e => {
            self.selectedItems = window.$(e.target).val();
            self.$emit("on-change", self.selectedItems);
            self.$emit("input", self.selectedItems);
          });
      }
    },
    destroyed() {
      const self = this;
      if (window.$(`#${self.id}`).data('select2'))
        window.$(`#${self.id}`).select2("destroy");
    }
  }
</script>

<style>
  .select2 {
    z-index: auto !important;
  }
</style>
