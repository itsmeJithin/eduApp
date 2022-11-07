<template>
  <div :id="id" class="input-group date full-width">
    <input type="text" class="form-control" :placeholder="format" :value="this.selectedDate">
    <span class="input-group-addon"><i class="fa fa-calendar"></i>
        </span>
  </div>
</template>

<script>
const $ = window.jQuery;
export default {
  name: "date-picker",
  props: {
    id: {
      type: String,
      default() {
        const randomNo = String(Math.random()).substr(2);
        return "date-picker-".concat(randomNo);
      }
    },
    format: {
      type: String,
      default() {
        return "dd-mm-yyyy";
      }
    },
    config: {
      autoclose: false
    },
    value: null
  },
  data() {
    return {
      selectedDate: this.value
    }
  },
  watch: {
    value() {
      this.selectedDate = this.value;
    },
    items() {
      // this.init();
    }
  },
  model: {
    event: 'on-change'
  },
  mounted() {
    const self = this;
    self.init();
  },
  methods: {
    init() {
      const self = this;
      $.fn.datepicker.defaults.format = self.format;
      $.fn.datepicker.defaults.autoclose = true;
      // Datepicker defaults
      $('#'.concat(self.id)).datepicker(this.config)
          .trigger("change")
          .on("change", e => {
            self.selectedDate = $(e.target).val();
            self.$emit("on-change", self.selectedDate);
          });
    }
  }

}
</script>
