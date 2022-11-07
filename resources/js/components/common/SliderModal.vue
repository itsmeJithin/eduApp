<template>
  <div>
    <!-- Positions : top, center, right, fillin -->
    <div class="modal fade"
         :class="{'slide-up disable-scroll' : sliderOptions.position === 'center', 'stick-up' : sliderOptions.position === 'top', 'slide-right' : sliderOptions.position === 'right', 'fill-in' : sliderOptions.position === 'fillin'}"
         :id="id" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

      <div class="modal-dialog"
           :class="{'modal-sm':sliderOptions.size === 'small', 'modal-md':sliderOptions.size === 'medium', 'modal-lg':sliderOptions.size === 'large','modal-xl':sliderOptions.size==='extra-large'}">
        <div class="modal-content-wrapper">
          <div class="modal-content block">
            <div class="modal-header clearfix text-left">
              <slot name="modal-header">
                <h4 class="mt-0">
                  <slot name="modal-title"></slot>
                </h4>
              </slot>
              <button aria-label="" type="button" class="close" aria-hidden="true"
                      @click="onClose()">
                <i class="pg-icon">close</i>
              </button>
            </div>
            <div class="modal-body v-align-middle w-100">
              <slot name="modal-content"></slot>
            </div>
            <div class="modal-footer" :class="{'bg-white':sliderOptions.position != 'fillin'}">
              <slot name="modal-footer">
                <button type="button" class="btn btn-default btn-lg btn-xs-block"
                        :class="{'btn-block':sliderOptions.size == 'small'}"
                        @click.prevent="onClose()">
                  {{sliderOptions.cancelBtnText}}
                </button>
                <button type="button" class="btn btn-primary btn-lg btn-xs-block"
                        :class="{'btn-block':sliderOptions.size == 'small'}"
                        @click.prevent="onSubmit()">
                  {{sliderOptions.submitBtnText}}
                </button>
              </slot>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "SliderModal",
    props: {
      id: {
        type: String,
        default() {
          const randomNo = String(Math.random()).substr(2);
          return "slider-modal-".concat(randomNo);
        }
      },
      options: {
        type: Object,
        default() {
          return {}
        }
      },
      showSlider: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        sliderOptions: {
          position: "",
          size: "medium",
          submitBtnText: "Submit",
          cancelBtnText: "Cancel"
        }
      }
    },
    watch: {
      showSlider(value) {
        const self = this;
        self.sliderOptions = Object.assign(self.sliderOptions, self.options);
        if (value === true) {
          window.$("#".concat(self.id)).modal({
            backdrop: "static",
            keyboard: false
          });
        } else {
          window.$("#".concat(self.id)).modal("hide");
        }
      }
    },
    methods: {
      onSubmit() {
        this.$emit("on-submit");
      },
      onClose() {
        this.$emit("on-close");
      }
    }
  }
</script>

<style scoped>
  .modal.fade.slide-right .close {
    margin: 0;
  }

  /*Update this style based on the screen size using media query*/
  .modal-xl {
    width: 800px !important;
  }
</style>
